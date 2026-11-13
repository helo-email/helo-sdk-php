<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Internal;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
use HeloEmail\Sdk\Exceptions\ApiException;
use HeloEmail\Sdk\Exceptions\ApiTimeoutException;
use HeloEmail\Sdk\Exceptions\AuthenticationException;
use HeloEmail\Sdk\Exceptions\BadRequestException;
use HeloEmail\Sdk\Exceptions\ConflictException;
use HeloEmail\Sdk\Exceptions\InternalServerException;
use HeloEmail\Sdk\Exceptions\NotFoundException;
use HeloEmail\Sdk\Exceptions\PermissionDeniedException;
use HeloEmail\Sdk\Exceptions\RateLimitException;
use HeloEmail\Sdk\Exceptions\UnprocessableEntityException;
use HeloEmail\Sdk\Exceptions\HeloException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Sends requests to the API: JSON in and out, retries, and the
 * status-code-to-exception mapping.
 *
 * A request is retried (up to $maxRetries times, with jittered backoff) after a
 * network failure or a 408, 429 or 5xx response, but only when repeating it
 * cannot do anything twice: its method is idempotent, or it carries an
 * idempotency key. A 429 is retried regardless, since the server refused it
 * without acting on it. A Retry-After longer than the SDK waits is not retried
 * early; the error goes to the caller.
 *
 * @internal
 */
final class HttpClient
{
    private const INITIAL_RETRY_DELAY = 0.5;
    private const MAX_RETRY_DELAY = 8.0;

    /** Rate limiting and transient server/gateway errors. */
    private const RETRY_STATUSES = [408, 429, 500, 502, 503, 504];

    /** Methods that can be repeated without doing anything twice. */
    private const IDEMPOTENT_METHODS = ['GET', 'HEAD', 'OPTIONS', 'PUT', 'DELETE'];

    private const CURLE_OPERATION_TIMEDOUT = 28;

    private readonly string $userAgent;

    public function __construct(
        private readonly ClientInterface $client,
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly int $maxRetries,
    ) {
        $this->userAgent = self::userAgent();
    }

    /**
     * `<product>/<version> (php <version>; <os>/<arch>)`, describing the PHP running
     * the SDK rather than the machine that generated it.
     */
    public static function userAgent(): string
    {
        return sprintf(
            'helo-email-sdk/%s (php %s; %s/%s)',
            Helo::VERSION,
            PHP_VERSION,
            strtolower(PHP_OS_FAMILY),
            php_uname('m') ?: 'unknown',
        );
    }

    /**
     * One comma-separated value for an `explode: false` array query parameter.
     *
     * @param list<mixed>|null $values
     */
    public static function commaJoin(?array $values): ?string
    {
        if (!$values) {
            return null;
        }

        return implode(',', array_map(static fn ($value) => self::queryValue($value), $values));
    }

    public static function pathSegment(mixed $value): string
    {
        return rawurlencode(self::queryValue($value));
    }

    /** A random (version 4) UUID, for an idempotency key the caller didn't supply. */
    public static function idempotencyKey(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }

    /**
     * The wire names of the arguments named in $nullFields, checked against the ones
     * the API accepts null for, so a misspelled name fails instead of being sent.
     *
     * @param list<string>|null $nullFields
     * @param array<string, string> $nullable argument name => wire name
     * @return list<string>
     */
    public static function nullFields(?array $nullFields, array $nullable): array
    {
        $unknown = array_diff($nullFields ?? [], array_keys($nullable));
        if ($unknown !== []) {
            throw new \InvalidArgumentException(sprintf(
                "nullFields: %s can't be null (only %s can)",
                implode(', ', $unknown),
                implode(', ', array_keys($nullable)),
            ));
        }

        return array_values(array_map(static fn (string $name) => $nullable[$name], $nullFields ?? []));
    }

    /**
     * Sends a request and returns the decoded JSON body, or null when there is none.
     * Null query values, body fields and headers are left out; the body fields
     * named in $nulls are sent as null, which clears them.
     *
     * @param array<string, mixed> $query
     * @param array<string, mixed>|null $body
     * @param array<string, string|null> $headers
     * @param list<string> $nulls
     * @param list<string> $objects dotted paths of the body's JSON objects ('*' for any list item or map value)
     */
    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        array $nulls = [],
        array $objects = [],
    ): mixed {
        $headers = array_filter($headers, static fn ($value) => $value !== null);
        $headers += [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'User-Agent' => $this->userAgent,
            'Accept' => 'application/json',
        ];

        $payload = null;
        if ($body !== null) {
            $headers['Content-Type'] = 'application/json';
            $fields = self::wireValue(array_filter($body, static fn ($value) => $value !== null));
            foreach ($nulls as $name) {
                $fields[$name] = null;
            }
            // An empty PHP array encodes as `[]`, but the body is always a JSON object.
            $payload = json_encode(
                (object) self::objects($fields, $objects, ''),
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION,
            );
        }

        $uri = rtrim($this->baseUrl, '/') . $path . self::queryString($query);

        $canRepeat = self::canRepeat($method, $headers);

        for ($attempt = 0; ; $attempt++) {
            try {
                $response = $this->client->sendRequest(new Request($method, $uri, $headers, $payload));
            } catch (ClientExceptionInterface $exception) {
                if (!self::isNetworkFailure($exception)) {
                    // An invalid request, say: sending it again would fail the same way.
                    throw new HeloException('The request could not be sent: ' . $exception->getMessage(), 0, $exception);
                }
                if ($canRepeat && $attempt < $this->maxRetries) {
                    self::sleep(self::backoffDelay($attempt));
                    continue;
                }
                if (self::isTimeout($exception)) {
                    throw new ApiTimeoutException('Request timed out.', 0, $exception);
                }
                throw new ApiConnectionException('Connection error.', 0, $exception);
            }

            $delay = $this->retryDelay($response, $attempt, $canRepeat);
            if ($delay !== null) {
                self::sleep($delay);
                continue;
            }

            return self::handleResponse($response);
        }
    }

    private static function handleResponse(ResponseInterface $response): mixed
    {
        $status = $response->getStatusCode();
        $content = (string) $response->getBody();

        if ($status < 200 || $status >= 300) {
            throw self::apiException($response, $content);
        }
        if ($status === 204 || $content === '') {
            return null;
        }

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    private static function apiException(ResponseInterface $response, string $content): ApiException
    {
        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $data = null;
        }
        $data = is_array($data) ? $data : [];

        $status = $response->getStatusCode();
        $arguments = [
            self::errorMessage($data, $status),
            $status,
            is_string($data['code'] ?? null) ? $data['code'] : null,
            is_string($data['detail'] ?? null) ? $data['detail'] : null,
            is_string($data['requestId'] ?? null) ? $data['requestId'] : null,
            $data,
        ];

        return match (true) {
            $status === 400 => new BadRequestException(...$arguments),
            $status === 401 => new AuthenticationException(...$arguments),
            $status === 403 => new PermissionDeniedException(...$arguments),
            $status === 404 => new NotFoundException(...$arguments),
            $status === 409 => new ConflictException(...$arguments),
            $status === 422 => new UnprocessableEntityException(...$arguments),
            $status === 429 => new RateLimitException(...[...$arguments, self::retryAfter($response)]),
            $status >= 500 => new InternalServerException(...$arguments),
            default => new ApiException(...$arguments),
        };
    }

    /** @param array<mixed> $data */
    private static function errorMessage(array $data, int $status): string
    {
        if (is_array($data['errors'] ?? null) && $data['errors'] !== []) {
            $messages = [];
            foreach ($data['errors'] as $field => $problems) {
                $message = is_array($problems) ? ($problems[0]['message'] ?? null) : null;
                if (is_string($message)) {
                    $messages[] = "{$field}: {$message}";
                }
            }
            if ($messages !== []) {
                return implode(', ', $messages);
            }
        }

        $detail = $data['detail'] ?? $data['title'] ?? null;

        return is_string($detail) && $detail !== '' ? $detail : "HTTP {$status}";
    }

    /** The server-suggested delay in seconds, from a Retry-After of seconds or an HTTP date. */
    private static function retryAfter(ResponseInterface $response): ?float
    {
        $value = $response->getHeaderLine('Retry-After');
        if ($value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return max(0.0, (float) $value);
        }

        $time = strtotime($value);
        if ($time === false) {
            return null;
        }
        $delay = $time - time();

        return $delay > 0 ? (float) $delay : null;
    }

    /**
     * Whether sending the request again cannot do anything twice.
     *
     * @param array<string, string> $headers
     */
    private static function canRepeat(string $method, array $headers): bool
    {
        if (in_array(strtoupper($method), self::IDEMPOTENT_METHODS, true)) {
            return true;
        }
        foreach (array_keys($headers) as $name) {
            if (str_ends_with(strtolower($name), 'idempotency-key')) {
                return true;
            }
        }

        return false;
    }

    /** How long to wait before retrying this response, or null not to retry it. */
    private function retryDelay(ResponseInterface $response, int $attempt, bool $canRepeat): ?float
    {
        $status = $response->getStatusCode();
        if ($attempt >= $this->maxRetries || !in_array($status, self::RETRY_STATUSES, true)) {
            return null;
        }
        if ($status !== 429 && !$canRepeat) {
            return null;
        }

        $retryAfter = self::retryAfter($response);
        if ($retryAfter === null) {
            return self::backoffDelay($attempt);
        }

        return $retryAfter <= self::MAX_RETRY_DELAY ? $retryAfter : null;
    }

    private static function backoffDelay(int $attempt): float
    {
        // Exponential backoff with full jitter.
        $ceiling = min(self::MAX_RETRY_DELAY, self::INITIAL_RETRY_DELAY * (2 ** $attempt));

        return $ceiling * mt_rand() / mt_getrandmax();
    }

    private static function sleep(float $seconds): void
    {
        if ($seconds > 0) {
            usleep((int) ($seconds * 1_000_000));
        }
    }

    /**
     * PSR-18's network exception, or Guzzle's RequestException without a response,
     * which Guzzle throws for transfer failures like a connection reset mid-response.
     */
    private static function isNetworkFailure(ClientExceptionInterface $exception): bool
    {
        return $exception instanceof NetworkExceptionInterface
            || ($exception instanceof RequestException && !$exception->hasResponse());
    }

    private static function isTimeout(ClientExceptionInterface $exception): bool
    {
        return $exception instanceof ConnectException
            && ($exception->getHandlerContext()['errno'] ?? null) === self::CURLE_OPERATION_TIMEDOUT;
    }

    /**
     * Repeats the key for an array value (`explode: true`, OpenAPI's default).
     *
     * @param array<string, mixed> $query
     */
    private static function queryString(array $query): string
    {
        $pairs = [];
        foreach ($query as $key => $value) {
            if ($value === null) {
                continue;
            }
            foreach (is_array($value) ? $value : [$value] as $item) {
                $pairs[] = rawurlencode($key) . '=' . rawurlencode(self::queryValue($item));
            }
        }

        return $pairs === [] ? '' : '?' . implode('&', $pairs);
    }

    private static function queryValue(mixed $value): string
    {
        $value = self::wireValue($value);

        return is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
    }

    /**
     * Casts the arrays at $paths to objects, since PHP encodes an empty or
     * integer-keyed array as a JSON list (`metadata: []` would otherwise send `[]`).
     *
     * @param list<string> $paths
     */
    private static function objects(array $value, array $paths, string $path): array|\stdClass
    {
        foreach ($value as $key => $item) {
            $child = ltrim("$path.$key", '.');
            if (!self::covers($paths, $child)) {
                $child = ltrim("$path.*", '.');
            }
            if (is_array($item) && self::covers($paths, $child)) {
                $value[$key] = self::objects($item, $paths, $child);
            }
        }

        return in_array($path, $paths, true) ? (object) $value : $value;
    }

    /** @param list<string> $paths */
    private static function covers(array $paths, string $path): bool
    {
        foreach ($paths as $candidate) {
            if ($candidate === $path || str_starts_with($candidate, "$path.")) {
                return true;
            }
        }

        return false;
    }

    /** Enums become their wire values and dates RFC 3339 strings, at any depth. */
    private static function wireValue(mixed $value): mixed
    {
        return match (true) {
            $value instanceof \BackedEnum => $value->value,
            $value instanceof \DateTimeInterface => $value->format(\DateTimeInterface::RFC3339),
            is_array($value) => array_map(self::wireValue(...), $value),
            default => $value,
        };
    }
}
