<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Models\BroadcastDetailsResponse;
use HeloEmail\Sdk\Models\PaginatedResponseOfBroadcast;
use HeloEmail\Sdk\Models\PaginatedResponseOfBroadcastFailure;
use HeloEmail\Sdk\Models\PaginatedResponseOfBroadcastSuppression;

final class BroadcastsTest extends TestCase
{
    public function testList(): void
    {
        $client = $this->client(self::json([
            'totalCount' => 1,
            'results' => [
                [
                    'id' => '550e8400-e29b-41d4-a716-446655440000',
                    'createdAt' => '2024-01-01T00:00:00Z',
                    'status' => 'accepted',
                    'subject' => 'example',
                    'completion' => 'example',
                    'messages' => 1,
                ],
            ],
        ]));

        $result = $client->broadcasts->list(
            channelId: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/broadcasts', $request->getUri()->getPath());
        self::assertInstanceOf(PaginatedResponseOfBroadcast::class, $result);
    }

    public function testRetrieve(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'createdAt' => '2024-01-01T00:00:00Z',
            'status' => 'accepted',
            'subject' => 'example',
            'completion' => 'example',
            'messages' => 1,
            'failed' => 1,
            'suppressed' => 1,
            'content' => [
                'from' => ['email' => 'example', 'name' => 'example'],
                'replyTo' => [['email' => 'example', 'name' => 'example']],
                'template' => ['subject' => 'example', 'html' => 'example', 'text' => 'example'],
                'attachments' => [['fileName' => 'example', 'disposition' => 'attachment', 'size' => 1]],
                'tags' => ['example'],
                'headers' => [],
                'metadata' => [],
            ],
            'tracking' => ['opens' => true, 'links' => true],
            'statistics' => [
                'sent' => 1,
                'delivered' => 1,
                'bounced' => 1,
                'opened' => 1,
                'clicked' => 1,
                'complained' => 1,
                'unsubscribed' => 1,
            ],
        ]));

        $result = $client->broadcasts->retrieve(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/broadcasts/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
        self::assertInstanceOf(BroadcastDetailsResponse::class, $result);
    }

    public function testListFailures(): void
    {
        $client = $this->client(self::json([
            'totalCount' => 1,
            'results' => [
                [
                    'recipients' => [
                        'to' => [['email' => 'example', 'name' => 'example']],
                        'cc' => [['email' => 'example', 'name' => 'example']],
                        'bcc' => [['email' => 'example', 'name' => 'example']],
                    ],
                    'messageIndex' => 1,
                    'errorCode' => 'example',
                    'errorMessage' => 'example',
                ],
            ],
        ]));

        $result = $client->broadcasts->listFailures(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/broadcasts/550e8400-e29b-41d4-a716-446655440000/failures', $request->getUri()->getPath());
        self::assertInstanceOf(PaginatedResponseOfBroadcastFailure::class, $result);
    }

    public function testListSuppressions(): void
    {
        $client = $this->client(self::json(['totalCount' => 1, 'results' => ['example']]));

        $result = $client->broadcasts->listSuppressions(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/broadcasts/550e8400-e29b-41d4-a716-446655440000/suppressions', $request->getUri()->getPath());
        self::assertInstanceOf(PaginatedResponseOfBroadcastSuppression::class, $result);
    }
}
