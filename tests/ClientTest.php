<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Exceptions\HeloException;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
use HeloEmail\Sdk\Exceptions\InternalServerException;
use HeloEmail\Sdk\Exceptions\NotFoundException;
use HeloEmail\Sdk\Exceptions\RateLimitException;
use HeloEmail\Sdk\Exceptions\UnprocessableEntityException;
use HeloEmail\Sdk\Internal\HttpClient;
use Psr\Http\Message\RequestInterface;

/** The hand-written transport every resource goes through. */
final class ClientTest extends \PHPUnit\Framework\TestCase
{
    /** @var list<array{request: RequestInterface}> */
    private array $history = [];

    private function http(Response|\Throwable ...$responses): HttpClient
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->history));

        return new HttpClient(new Client(['handler' => $stack]), 'test-key', 'https://api.example.test/', 1);
    }

    public function testSendsCredentialsAndAUserAgentNamingThePackageAndRuntime(): void
    {
        $this->http(new Response(204))->request('GET', '/things');

        $request = $this->history[0]['request'];
        self::assertSame('Bearer test-key', $request->getHeaderLine('Authorization'));
        self::assertSame('https://api.example.test/things', (string) $request->getUri());
        self::assertMatchesRegularExpression(
            '#^helo\-email\-sdk/' . preg_quote(Helo::VERSION, '#') . ' \(php [^;]+; [a-z]+/[^)]+\)$#',
            $request->getHeaderLine('User-Agent'),
        );
    }

    public function testLeavesOutNullsAndEncodesEnumsArraysAndBooleans(): void
    {
        $this->http(new Response(204))->request(
            'POST',
            '/things',
            query: ['ids' => HttpClient::commaJoin(['a', 'b']), 'tag' => ['x', 'y'], 'flag' => false, 'skip' => null],
            body: ['name' => 'n', 'skip' => null],
            headers: ['X-Skip' => null],
        );

        $request = $this->history[0]['request'];
        self::assertSame('ids=a%2Cb&tag=x&tag=y&flag=false', $request->getUri()->getQuery());
        self::assertSame('{"name":"n"}', (string) $request->getBody());
        self::assertFalse($request->hasHeader('X-Skip'));
    }

    public function testSendsAnEmptyBodyAsAJsonObject(): void
    {
        $this->http(new Response(204))->request('POST', '/things', body: ['skip' => null]);

        self::assertSame('{}', (string) $this->history[0]['request']->getBody());
    }

    public function testSendsTheArraysTheSchemaCallsObjectsAsJsonObjects(): void
    {
        $this->http(new Response(204))->request(
            'POST',
            '/things',
            body: ['meta' => [], 'tags' => [], 'items' => [['data' => [], 'ids' => [1]]], 'labels' => ['0' => 'a']],
            objects: ['meta', 'items.*', 'items.*.data', 'labels'],
        );

        self::assertSame(
            '{"meta":{},"tags":[],"items":[{"data":{},"ids":[1]}],"labels":{"0":"a"}}',
            (string) $this->history[0]['request']->getBody(),
        );
    }

    public function testMapsAnErrorResponseToItsException(): void
    {
        $body = json_encode(['code' => 'not_found', 'detail' => 'No such thing', 'requestId' => 'req_1']);

        try {
            $this->http(new Response(404, [], $body))->request('GET', '/things/1');
            self::fail('expected an exception');
        } catch (NotFoundException $e) {
            self::assertSame(404, $e->statusCode);
            self::assertSame('not_found', $e->errorCode);
            self::assertSame('req_1', $e->requestId);
            self::assertSame('No such thing', $e->getMessage());
        }
    }

    public function testListsFieldErrorsInTheMessage(): void
    {
        $body = json_encode(['errors' => ['subject' => [['message' => 'is required']]]]);

        $this->expectException(UnprocessableEntityException::class);
        $this->expectExceptionMessage('subject: is required');
        $this->http(new Response(422, [], $body))->request('POST', '/things', body: []);
    }

    public function testRetriesATransientFailureThenSucceeds(): void
    {
        $result = $this->http(new Response(503, ['Retry-After' => '0']), new Response(200, [], '{"ok":true}'))
            ->request('GET', '/things');

        self::assertSame(['ok' => true], $result);
        self::assertCount(2, $this->history);
    }

    public function testGivesUpAfterTheLastRetry(): void
    {
        try {
            $this->http(new Response(429, ['Retry-After' => '0']), new Response(429, ['Retry-After' => '3']))
                ->request('GET', '/things');
            self::fail('expected an exception');
        } catch (RateLimitException $e) {
            self::assertSame(3.0, $e->retryAfter);
            self::assertCount(2, $this->history);
        }
    }

    public function testRetriesAPostCarryingAnIdempotencyKey(): void
    {
        $this->http(new Response(502, ['Retry-After' => '0']), new Response(204))
            ->request('POST', '/things', body: [], headers: ['X-Example-Idempotency-Key' => 'key-1']);

        self::assertCount(2, $this->history);
        self::assertSame('key-1', $this->history[1]['request']->getHeaderLine('X-Example-Idempotency-Key'));
    }

    /** Without a key, the server may have acted on the first attempt before failing. */
    public function testDoesNotRetryAPostWithoutAnIdempotencyKey(): void
    {
        try {
            $this->http(new Response(503, ['Retry-After' => '0']))->request('POST', '/things', body: []);
            self::fail('expected an exception');
        } catch (InternalServerException) {
            self::assertCount(1, $this->history);
        }
    }

    public function testDoesNotRetryAPostWithoutAKeyAfterANetworkError(): void
    {
        $this->expectException(ApiConnectionException::class);
        $this->http(new \GuzzleHttp\Exception\ConnectException('refused', new \GuzzleHttp\Psr7\Request('POST', '/')))
            ->request('POST', '/things', body: []);
    }

    /** A 429 means the server refused the request without acting on it. */
    public function testRetriesARateLimitedPostEvenWithoutAKey(): void
    {
        $result = $this->http(new Response(429, ['Retry-After' => '0']), new Response(200, [], '{"ok":true}'))
            ->request('POST', '/things', body: []);

        self::assertSame(['ok' => true], $result);
    }

    public function testDoesNotRetryEarlyWhenRetryAfterIsLongerThanItWaits(): void
    {
        try {
            $this->http(new Response(429, ['Retry-After' => '60']))->request('GET', '/things');
            self::fail('expected an exception');
        } catch (RateLimitException $e) {
            self::assertSame(60.0, $e->retryAfter);
            self::assertCount(1, $this->history);
        }
    }

    /** Not a network failure, so sending it again would fail the same way. */
    public function testDoesNotRetryARequestTheClientCouldNotSend(): void
    {
        $request = new \GuzzleHttp\Psr7\Request('GET', '/');
        $invalid = new class ('invalid', $request) extends \RuntimeException implements \Psr\Http\Client\RequestExceptionInterface {
            public function __construct(string $message, private readonly \Psr\Http\Message\RequestInterface $request)
            {
                parent::__construct($message);
            }

            public function getRequest(): \Psr\Http\Message\RequestInterface
            {
                return $this->request;
            }
        };

        try {
            $this->http($invalid, new Response(204))->request('GET', '/things');
            self::fail('expected an exception');
        } catch (HeloException $e) {
            self::assertNotInstanceOf(ApiConnectionException::class, $e);
            self::assertCount(1, $this->history);
        }
    }

    public function testSendsTheNamedNullFieldsAsNull(): void
    {
        $nulls = HttpClient::nullFields(['channelIds'], ['text' => 'text', 'channelIds' => 'channelIds']);
        $this->http(new Response(204))->request('PATCH', '/things', body: ['name' => 'n', 'text' => null], nulls: $nulls);

        self::assertSame('{"name":"n","channelIds":null}', (string) $this->history[0]['request']->getBody());
    }

    public function testRejectsANullFieldTheApiCannotClear(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("nullFields: name can't be null (only text can)");
        HttpClient::nullFields(['name'], ['text' => 'text']);
    }

    public function testGeneratesRandomUuidIdempotencyKeys(): void
    {
        $key = HttpClient::idempotencyKey();

        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $key);
        self::assertNotSame($key, HttpClient::idempotencyKey());
    }

    /** A value the API added after this SDK version was released is kept, not rejected. */
    public function testKeepsAnEnumValueThisVersionDoesNotKnow(): void
    {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'example',
            'deliveryType' => 'live',
            'createdAt' => '2024-01-01T00:00:00Z',
            'updatedAt' => '2024-01-01T00:00:00Z',
            'tracking' => ['links' => true, 'opens' => true],
        ];
        $data['deliveryType'] = 'added-later';

        self::assertSame('added-later', \HeloEmail\Sdk\Models\ChannelDetailsResponse::fromArray($data)->deliveryType);
    }

    public function testWrapsANetworkFailure(): void
    {
        $failure = fn () => new \GuzzleHttp\Exception\ConnectException('refused', new \GuzzleHttp\Psr7\Request('GET', '/'));

        $this->expectException(ApiConnectionException::class);
        $this->http($failure(), $failure())->request('GET', '/things');
    }

    public function testRequiresAnApiKey(): void
    {
        $previous = getenv('HELO_API_KEY');
        putenv('HELO_API_KEY');

        try {
            $this->expectException(\InvalidArgumentException::class);
            new Helo();
        } finally {
            if ($previous !== false) {
                putenv('HELO_API_KEY=' . $previous);
            }
        }
    }
}
