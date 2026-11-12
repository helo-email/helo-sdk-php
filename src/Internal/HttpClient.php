<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Internal;

use GuzzleHttp\Exception\ConnectException;
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
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Sends requests to the API: JSON in and out, retries with jittered backoff
 * (honouring Retry-After), and the status-code-to-exception mapping.
 *
 * @internal
 */
final class HttpClient
{
    private const INITIAL_RETRY_DELAY = 0.5;
    private const MAX_RETRY_DELAY = 8.0;

    /** Rate limiting and transient server/gateway errors. */
    private const RETRY_STATUSES = [408, 429, 500, 502, 503, 504];

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

    /**
     * Sends a request and returns the decoded JSON body, or null when there is none.
     * Null query values, body fields and headers are left out.
     *
     * @param array<string, mixed> $query
     * @param array<string, mixed>|null $body
     * @param array<string, string|null> $headers
     */
    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
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
            // An empty PHP array encodes as `[]`, but the body is always a JSON object.
            $payload = $fields === []
                ? '{}'
                : json_encode($fields, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        }

        $uri = rtrim($this->baseUrl, '/') . $path . self::queryString($query);

        for ($attempt = 0; ; $attempt++) {
            $canRetry = $attempt < $this->maxRetries;
            try {
                $response = $this->client->sendRequest(new Request($method, $uri, $headers, $payload));
            } catch (ClientExceptionInterface $exception) {
                if ($canRetry) {
                    self::sleep(self::backoffDelay($attempt, null));
                    continue;
                }
                if (self::isTimeout($exception)) {
                    throw new ApiTimeoutException('Request timed out.', 0, $exception);
                }
                throw new ApiConnectionException('Connection error.', 0, $exception);
            }

            if ($canRetry && in_array($response->getStatusCode(), self::RETRY_STATUSES, true)) {
                self::sleep(self::backoffDelay($attempt, self::retryAfter($response)));
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

    private static function backoffDelay(int $attempt, ?float $retryAfter): float
    {
        if ($retryAfter !== null) {
            return min($retryAfter, self::MAX_RETRY_DELAY);
        }

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
