<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Models\MessageDetailsResponse;
use HeloEmail\Sdk\Models\PaginatedEventsResponse;
use HeloEmail\Sdk\Models\PaginatedMessagesResponse;

final class ActivityTest extends TestCase
{
    public function testListEvents(): void
    {
        $client = $this->client(self::json([
            'after' => 1,
            'totalCount' => 1.5,
            'results' => [
                [
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'mailType' => 'transactional',
                    'mailSource' => 'api',
                    'eventType' => 'accepted',
                    'timestamp' => '2024-01-01T00:00:00Z',
                    'subject' => 'example',
                    'recipients' => ['example'],
                    'tags' => ['example'],
                    'metadata' => [],
                    'details' => [],
                ],
            ],
        ]));

        $result = $client->activity->listEvents();

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/activity/events', $request->getUri()->getPath());
        self::assertInstanceOf(PaginatedEventsResponse::class, $result);
    }

    public function testListMessages(): void
    {
        $client = $this->client(self::json([
            'after' => 1,
            'totalCount' => 1.5,
            'results' => [
                [
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'timestamp' => '2024-01-01T00:00:00Z',
                    'mailType' => 'transactional',
                    'mailSource' => 'api',
                    'deliveryType' => 'live',
                    'status' => 'queued',
                    'subject' => 'example',
                    'recipients' => ['example'],
                    'tags' => ['example'],
                    'statistics' => [
                        'delivered' => 1,
                        'bounced' => 1,
                        'opened' => 1,
                        'clicked' => 1,
                        'complained' => 1,
                        'unsubscribed' => 1,
                    ],
                ],
            ],
        ]));

        $result = $client->activity->listMessages();

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/activity/messages', $request->getUri()->getPath());
        self::assertInstanceOf(PaginatedMessagesResponse::class, $result);
    }

    public function testRetrieveMessage(): void
    {
        $client = $this->client(self::json([
            'messageId' => '550e8400-e29b-41d4-a716-446655440000',
            'channelId' => '550e8400-e29b-41d4-a716-446655440000',
            'timestamp' => '2024-01-01T00:00:00Z',
            'mailType' => 'transactional',
            'mailSource' => 'api',
            'deliveryType' => 'live',
            'status' => 'queued',
            'subject' => 'example',
            'from' => ['email' => 'test@example.com', 'name' => 'example'],
            'to' => [['email' => 'test@example.com', 'name' => 'example']],
            'cc' => [['email' => 'test@example.com', 'name' => 'example']],
            'bcc' => [['email' => 'test@example.com', 'name' => 'example']],
            'replyTo' => [['email' => 'test@example.com', 'name' => 'example']],
            'text' => 'example',
            'html' => 'example',
            'body' => 'example',
            'tags' => ['example'],
            'headers' => [],
            'metadata' => [],
            'attachments' => [['fileName' => 'example', 'disposition' => 'inline', 'size' => 1.5]],
            'tracking' => ['links' => true, 'opens' => true],
            'events' => [
                [
                    'eventType' => 'accepted',
                    'timestamp' => '2024-01-01T00:00:00Z',
                    'recipients' => ['example'],
                    'details' => [],
                ],
            ],
            'statistics' => [
                'delivered' => 1,
                'bounced' => 1,
                'opened' => 1,
                'clicked' => 1,
                'complained' => 1,
                'unsubscribed' => 1,
            ],
        ]));

        $result = $client->activity->retrieveMessage(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/activity/messages/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
        self::assertInstanceOf(MessageDetailsResponse::class, $result);
    }
}
