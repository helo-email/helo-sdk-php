<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
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
