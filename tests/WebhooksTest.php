<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Models\PaginationResultOfWebhookResponse;
use HeloEmail\Sdk\Models\WebhookEvent;
use HeloEmail\Sdk\Models\WebhookResponse;

final class WebhooksTest extends TestCase
{
    public function testList(): void
    {
        $client = $this->client(self::json([
            'results' => [
                [
                    'id' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'url' => 'example',
                    'payloadSigningKey' => 'example',
                    'enabled' => true,
                    'additionalHeaders' => [['name' => 'example', 'value' => 'example']],
                    'events' => ['message-accepted'],
                    'lastResponse' => ['statusCode' => 1, 'error' => 'example', 'at' => '2024-01-01T00:00:00Z'],
                ],
            ],
            'totalCount' => 1,
        ]));

        $result = $client->webhooks->list();

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/webhooks', $request->getUri()->getPath());
        self::assertInstanceOf(PaginationResultOfWebhookResponse::class, $result);
    }

    public function testCreate(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'channelId' => '550e8400-e29b-41d4-a716-446655440000',
            'url' => 'example',
            'payloadSigningKey' => 'example',
            'enabled' => true,
            'additionalHeaders' => [['name' => 'example', 'value' => 'example']],
            'events' => ['message-accepted'],
            'lastResponse' => ['statusCode' => 1, 'error' => 'example', 'at' => '2024-01-01T00:00:00Z'],
        ]));

        $result = $client->webhooks->create(
            url: 'test-url',
            events: [WebhookEvent::MessageAccepted],
            channelId: '550e8400-e29b-41d4-a716-446655440000',
            additionalHeaders: [['name' => 'test-name', 'value' => 'test-value']],
            enabled: true,
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/webhooks', $request->getUri()->getPath());
        self::assertEquals(
            json_decode(json_encode([
            'url' => 'test-url',
            'events' => [WebhookEvent::MessageAccepted],
            'channelId' => '550e8400-e29b-41d4-a716-446655440000',
            'additionalHeaders' => [['name' => 'test-name', 'value' => 'test-value']],
            'enabled' => true,
        ]), true),
            json_decode((string) $request->getBody(), true),
        );
        self::assertInstanceOf(WebhookResponse::class, $result);
    }

    public function testRetrieve(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'channelId' => '550e8400-e29b-41d4-a716-446655440000',
            'url' => 'example',
            'payloadSigningKey' => 'example',
            'enabled' => true,
            'additionalHeaders' => [['name' => 'example', 'value' => 'example']],
            'events' => ['message-accepted'],
            'lastResponse' => ['statusCode' => 1, 'error' => 'example', 'at' => '2024-01-01T00:00:00Z'],
        ]));

        $result = $client->webhooks->retrieve(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/webhooks/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
        self::assertInstanceOf(WebhookResponse::class, $result);
    }

    public function testUpdate(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'channelId' => '550e8400-e29b-41d4-a716-446655440000',
            'url' => 'example',
            'payloadSigningKey' => 'example',
            'enabled' => true,
            'additionalHeaders' => [['name' => 'example', 'value' => 'example']],
            'events' => ['message-accepted'],
            'lastResponse' => ['statusCode' => 1, 'error' => 'example', 'at' => '2024-01-01T00:00:00Z'],
        ]));

        $result = $client->webhooks->update(
            id: '550e8400-e29b-41d4-a716-446655440000',
            url: 'test-url',
            events: [WebhookEvent::MessageAccepted],
            channelId: '550e8400-e29b-41d4-a716-446655440000',
            additionalHeaders: [['name' => 'test-name', 'value' => 'test-value']],
            enabled: true,
        );

        $request = $this->lastRequest();
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame('/webhooks/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
        self::assertEquals(
            json_decode(json_encode([
            'url' => 'test-url',
            'events' => [WebhookEvent::MessageAccepted],
            'channelId' => '550e8400-e29b-41d4-a716-446655440000',
            'additionalHeaders' => [['name' => 'test-name', 'value' => 'test-value']],
            'enabled' => true,
        ]), true),
            json_decode((string) $request->getBody(), true),
        );
        self::assertInstanceOf(WebhookResponse::class, $result);
    }

    public function testDelete(): void
    {
        $client = $this->client(new Response(204));

        $client->webhooks->delete(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame('/webhooks/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
    }

    public function testRegenerateSigningKey(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'channelId' => '550e8400-e29b-41d4-a716-446655440000',
            'url' => 'example',
            'payloadSigningKey' => 'example',
            'enabled' => true,
            'additionalHeaders' => [['name' => 'example', 'value' => 'example']],
            'events' => ['message-accepted'],
            'lastResponse' => ['statusCode' => 1, 'error' => 'example', 'at' => '2024-01-01T00:00:00Z'],
        ]));

        $result = $client->webhooks->regenerateSigningKey(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/webhooks/550e8400-e29b-41d4-a716-446655440000/regenerate-signing-key', $request->getUri()->getPath());
        self::assertInstanceOf(WebhookResponse::class, $result);
    }
}
