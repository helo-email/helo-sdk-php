<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Helo;
use Psr\Http\Message\RequestInterface;

/** Drives the client against queued responses, so no server is needed. */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var list<array{request: RequestInterface}> */
    private array $history = [];

    protected function client(Response ...$responses): Helo
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->history));

        return new Helo(
            apiKey: 'test-key',
            baseUrl: 'https://api.example.test',
            maxRetries: 1,
            httpClient: new Client(['handler' => $stack]),
        );
    }

    /** @param array<mixed> $body */
    protected static function json(array $body, int $status = 200): Response
    {
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($body, JSON_THROW_ON_ERROR));
    }

    protected function lastRequest(): RequestInterface
    {
        self::assertNotEmpty($this->history, 'no request was sent');

        return $this->history[array_key_last($this->history)]['request'];
    }

    /** @return list<RequestInterface> */
    protected function requests(): array
    {
        return array_map(static fn (array $entry) => $entry['request'], $this->history);
    }
}
