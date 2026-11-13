<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Models\AttachmentDisposition;
use HeloEmail\Sdk\Models\SendBroadcastResponse;
use HeloEmail\Sdk\Models\SendMessageAcceptedResponse;
use HeloEmail\Sdk\Models\SendMessageBatchResponse;

final class SendingTest extends TestCase
{
    public function testSendTransactional(): void
    {
        $client = $this->client(self::json([
            'status' => 'example',
            'messageId' => '550e8400-e29b-41d4-a716-446655440000',
            'suppressions' => ['example'],
        ]));

        $result = $client->sending->sendTransactional(
            from: ['email' => 'from@yourdomain.com', 'name' => 'From name'],
            to: [['email' => 'to@example.com', 'name' => 'To name']],
            cc: [['email' => 'cc@example.com', 'name' => 'Cc name']],
            bcc: [['email' => 'bcc@example.com', 'name' => 'Bcc name']],
            replyTo: [['email' => 'reply-to@example.com', 'name' => 'Reply-To name']],
            subject: 'Hello from Helo',
            html: '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
            text: 'This is a test message, delivered with <3 by Helo.',
            tracking: ['opens' => true, 'links' => true],
            attachments: [
                [
                    'content' => 'SGVsbG8gd29ybGQ=',
                    'contentId' => 'test-contentId',
                    'contentType' => 'test-contentType',
                    'fileName' => 'test-fileName',
                    'disposition' => AttachmentDisposition::Attachment,
                ],
            ],
            tags: ['welcome', 'onboarding'],
            channelId: '550e8400-e29b-41d4-a716-446655440000',
            idempotencyKey: 'test-idempotencyKey',
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/send/transactional', $request->getUri()->getPath());
        self::assertTrue($request->hasHeader('X-Helo-Channel-Id'));
        self::assertTrue($request->hasHeader('X-Helo-Idempotency-Key'));
        self::assertEquals(
            json_decode(json_encode([
            'from' => ['email' => 'from@yourdomain.com', 'name' => 'From name'],
            'to' => [['email' => 'to@example.com', 'name' => 'To name']],
            'cc' => [['email' => 'cc@example.com', 'name' => 'Cc name']],
            'bcc' => [['email' => 'bcc@example.com', 'name' => 'Bcc name']],
            'replyTo' => [['email' => 'reply-to@example.com', 'name' => 'Reply-To name']],
            'subject' => 'Hello from Helo',
            'html' => '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
            'text' => 'This is a test message, delivered with <3 by Helo.',
            'tracking' => ['opens' => true, 'links' => true],
            'attachments' => [
                [
                    'content' => 'SGVsbG8gd29ybGQ=',
                    'contentId' => 'test-contentId',
                    'contentType' => 'test-contentType',
                    'fileName' => 'test-fileName',
                    'disposition' => AttachmentDisposition::Attachment,
                ],
            ],
            'tags' => ['welcome', 'onboarding'],
        ]), true),
            json_decode((string) $request->getBody(), true),
        );
        self::assertInstanceOf(SendMessageAcceptedResponse::class, $result);
    }

    /** A send whose response is lost must not be sent twice: the retry reuses the key the SDK generated. */
    public function testSendTransactionalGeneratesOneIdempotencyKeyPerCall(): void
    {
        $client = $this->client(
            new Response(503, ['Retry-After' => '0']),
            self::json([
            'status' => 'example',
            'messageId' => '550e8400-e29b-41d4-a716-446655440000',
            'suppressions' => ['example'],
        ]),
        );

        $client->sending->sendTransactional(
            from: ['email' => 'from@yourdomain.com', 'name' => 'From name'],
            to: [['email' => 'to@example.com', 'name' => 'To name']],
            cc: [['email' => 'cc@example.com', 'name' => 'Cc name']],
            bcc: [['email' => 'bcc@example.com', 'name' => 'Bcc name']],
            replyTo: [['email' => 'reply-to@example.com', 'name' => 'Reply-To name']],
            subject: 'Hello from Helo',
            html: '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
            text: 'This is a test message, delivered with <3 by Helo.',
            tracking: ['opens' => true, 'links' => true],
            attachments: [
                [
                    'content' => 'SGVsbG8gd29ybGQ=',
                    'contentId' => 'test-contentId',
                    'contentType' => 'test-contentType',
                    'fileName' => 'test-fileName',
                    'disposition' => AttachmentDisposition::Attachment,
                ],
            ],
            tags: ['welcome', 'onboarding'],
            channelId: '550e8400-e29b-41d4-a716-446655440000',
        );

        $keys = array_map(static fn ($request) => $request->getHeaderLine('X-Helo-Idempotency-Key'), $this->requests());
        self::assertCount(2, $keys);
        self::assertSame($keys[0], $keys[1]);
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $keys[0]);
    }

    public function testSendTransactionalBatch(): void
    {
        $client = $this->client(self::json([
            'responses' => [
                [
                    'status' => 'example',
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'suppressions' => ['example'],
                    'errorCode' => 'example',
                    'errorMessage' => 'example',
                ],
            ],
        ]));

        $result = $client->sending->sendTransactionalBatch(
            requests: [
                [
                    'from' => ['email' => 'from@yourdomain.com', 'name' => 'From name'],
                    'to' => [['email' => 'to@example.com', 'name' => 'To name']],
                    'subject' => 'Hello from Helo',
                    'html' => '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
                    'text' => 'This is a test message, delivered with <3 by Helo.',
                    'tags' => ['welcome', 'onboarding'],
                ],
            ],
            channelId: '550e8400-e29b-41d4-a716-446655440000',
            idempotencyKey: 'test-idempotencyKey',
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/send/transactional/batch', $request->getUri()->getPath());
        self::assertTrue($request->hasHeader('X-Helo-Channel-Id'));
        self::assertTrue($request->hasHeader('X-Helo-Idempotency-Key'));
        self::assertEquals(
            json_decode(json_encode([
            'requests' => [
                [
                    'from' => ['email' => 'from@yourdomain.com', 'name' => 'From name'],
                    'to' => [['email' => 'to@example.com', 'name' => 'To name']],
                    'subject' => 'Hello from Helo',
                    'html' => '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
                    'text' => 'This is a test message, delivered with <3 by Helo.',
                    'tags' => ['welcome', 'onboarding'],
                ],
            ],
        ]), true),
            json_decode((string) $request->getBody(), true),
        );
        self::assertInstanceOf(SendMessageBatchResponse::class, $result);
    }

    /** A send whose response is lost must not be sent twice: the retry reuses the key the SDK generated. */
    public function testSendTransactionalBatchGeneratesOneIdempotencyKeyPerCall(): void
    {
        $client = $this->client(
            new Response(503, ['Retry-After' => '0']),
            self::json([
            'responses' => [
                [
                    'status' => 'example',
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'suppressions' => ['example'],
                    'errorCode' => 'example',
                    'errorMessage' => 'example',
                ],
            ],
        ]),
        );

        $client->sending->sendTransactionalBatch(
            requests: [
                [
                    'from' => ['email' => 'from@yourdomain.com', 'name' => 'From name'],
                    'to' => [['email' => 'to@example.com', 'name' => 'To name']],
                    'subject' => 'Hello from Helo',
                    'html' => '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
                    'text' => 'This is a test message, delivered with <3 by Helo.',
                    'tags' => ['welcome', 'onboarding'],
                ],
            ],
            channelId: '550e8400-e29b-41d4-a716-446655440000',
        );

        $keys = array_map(static fn ($request) => $request->getHeaderLine('X-Helo-Idempotency-Key'), $this->requests());
        self::assertCount(2, $keys);
        self::assertSame($keys[0], $keys[1]);
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $keys[0]);
    }

    public function testSendBroadcast(): void
    {
        $client = $this->client(self::json(['status' => 'example', 'broadcastId' => '550e8400-e29b-41d4-a716-446655440000']));

        $result = $client->sending->sendBroadcast(
            from: ['email' => 'test@example.com', 'name' => 'test-name'],
            template: [
                'subject' => 'Hello, {{name}}',
                'html' => '<html><body><h1>Hi {{name}}.</h1><p>This is a broadcast, delivered with <3 by Helo.</p></body></html>',
                'text' => 'Hi {{name}}. This is a broadcast, delivered with <3 by Helo.',
                'inlineStyles' => true,
            ],
            messages: [
                [
                    'to' => [['email' => 'test@example.com', 'name' => 'test-name']],
                    'tags' => ['test-tag'],
                ],
            ],
            replyTo: [['email' => 'test@example.com', 'name' => 'test-name']],
            tracking: ['opens' => true, 'links' => true],
            attachments: [
                [
                    'content' => 'SGVsbG8gd29ybGQ=',
                    'contentId' => 'test-contentId',
                    'contentType' => 'test-contentType',
                    'fileName' => 'test-fileName',
                    'disposition' => AttachmentDisposition::Attachment,
                ],
            ],
            tags: ['test-tag'],
            channelId: '550e8400-e29b-41d4-a716-446655440000',
            idempotencyKey: 'test-idempotencyKey',
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/send/broadcast', $request->getUri()->getPath());
        self::assertTrue($request->hasHeader('X-Helo-Channel-Id'));
        self::assertTrue($request->hasHeader('X-Helo-Idempotency-Key'));
        self::assertEquals(
            json_decode(json_encode([
            'from' => ['email' => 'test@example.com', 'name' => 'test-name'],
            'template' => [
                'subject' => 'Hello, {{name}}',
                'html' => '<html><body><h1>Hi {{name}}.</h1><p>This is a broadcast, delivered with <3 by Helo.</p></body></html>',
                'text' => 'Hi {{name}}. This is a broadcast, delivered with <3 by Helo.',
                'inlineStyles' => true,
            ],
            'messages' => [
                [
                    'to' => [['email' => 'test@example.com', 'name' => 'test-name']],
                    'tags' => ['test-tag'],
                ],
            ],
            'replyTo' => [['email' => 'test@example.com', 'name' => 'test-name']],
            'tracking' => ['opens' => true, 'links' => true],
            'attachments' => [
                [
                    'content' => 'SGVsbG8gd29ybGQ=',
                    'contentId' => 'test-contentId',
                    'contentType' => 'test-contentType',
                    'fileName' => 'test-fileName',
                    'disposition' => AttachmentDisposition::Attachment,
                ],
            ],
            'tags' => ['test-tag'],
        ]), true),
            json_decode((string) $request->getBody(), true),
        );
        self::assertInstanceOf(SendBroadcastResponse::class, $result);
    }

    /** A send whose response is lost must not be sent twice: the retry reuses the key the SDK generated. */
    public function testSendBroadcastGeneratesOneIdempotencyKeyPerCall(): void
    {
        $client = $this->client(
            new Response(503, ['Retry-After' => '0']),
            self::json(['status' => 'example', 'broadcastId' => '550e8400-e29b-41d4-a716-446655440000']),
        );

        $client->sending->sendBroadcast(
            from: ['email' => 'test@example.com', 'name' => 'test-name'],
            template: [
                'subject' => 'Hello, {{name}}',
                'html' => '<html><body><h1>Hi {{name}}.</h1><p>This is a broadcast, delivered with <3 by Helo.</p></body></html>',
                'text' => 'Hi {{name}}. This is a broadcast, delivered with <3 by Helo.',
                'inlineStyles' => true,
            ],
            messages: [
                [
                    'to' => [['email' => 'test@example.com', 'name' => 'test-name']],
                    'tags' => ['test-tag'],
                ],
            ],
            replyTo: [['email' => 'test@example.com', 'name' => 'test-name']],
            tracking: ['opens' => true, 'links' => true],
            attachments: [
                [
                    'content' => 'SGVsbG8gd29ybGQ=',
                    'contentId' => 'test-contentId',
                    'contentType' => 'test-contentType',
                    'fileName' => 'test-fileName',
                    'disposition' => AttachmentDisposition::Attachment,
                ],
            ],
            tags: ['test-tag'],
            channelId: '550e8400-e29b-41d4-a716-446655440000',
        );

        $keys = array_map(static fn ($request) => $request->getHeaderLine('X-Helo-Idempotency-Key'), $this->requests());
        self::assertCount(2, $keys);
        self::assertSame($keys[0], $keys[1]);
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $keys[0]);
    }

    public function testSendBroadcastMessage(): void
    {
        $client = $this->client(self::json([
            'status' => 'example',
            'messageId' => '550e8400-e29b-41d4-a716-446655440000',
            'suppressions' => ['example'],
        ]));

        $result = $client->sending->sendBroadcastMessage(
            from: ['email' => 'from@yourdomain.com', 'name' => 'From name'],
            to: [['email' => 'to@example.com', 'name' => 'To name']],
            cc: [['email' => 'cc@example.com', 'name' => 'Cc name']],
            bcc: [['email' => 'bcc@example.com', 'name' => 'Bcc name']],
            replyTo: [['email' => 'reply-to@example.com', 'name' => 'Reply-To name']],
            subject: 'Hello from Helo',
            html: '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
            text: 'This is a test message, delivered with <3 by Helo.',
            tracking: ['opens' => true, 'links' => true],
            attachments: [
                [
                    'content' => 'SGVsbG8gd29ybGQ=',
                    'contentId' => 'test-contentId',
                    'contentType' => 'test-contentType',
                    'fileName' => 'test-fileName',
                    'disposition' => AttachmentDisposition::Attachment,
                ],
            ],
            tags: ['welcome', 'onboarding'],
            channelId: '550e8400-e29b-41d4-a716-446655440000',
            idempotencyKey: 'test-idempotencyKey',
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/send/broadcast/message', $request->getUri()->getPath());
        self::assertTrue($request->hasHeader('X-Helo-Channel-Id'));
        self::assertTrue($request->hasHeader('X-Helo-Idempotency-Key'));
        self::assertEquals(
            json_decode(json_encode([
            'from' => ['email' => 'from@yourdomain.com', 'name' => 'From name'],
            'to' => [['email' => 'to@example.com', 'name' => 'To name']],
            'cc' => [['email' => 'cc@example.com', 'name' => 'Cc name']],
            'bcc' => [['email' => 'bcc@example.com', 'name' => 'Bcc name']],
            'replyTo' => [['email' => 'reply-to@example.com', 'name' => 'Reply-To name']],
            'subject' => 'Hello from Helo',
            'html' => '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
            'text' => 'This is a test message, delivered with <3 by Helo.',
            'tracking' => ['opens' => true, 'links' => true],
            'attachments' => [
                [
                    'content' => 'SGVsbG8gd29ybGQ=',
                    'contentId' => 'test-contentId',
                    'contentType' => 'test-contentType',
                    'fileName' => 'test-fileName',
                    'disposition' => AttachmentDisposition::Attachment,
                ],
            ],
            'tags' => ['welcome', 'onboarding'],
        ]), true),
            json_decode((string) $request->getBody(), true),
        );
        self::assertInstanceOf(SendMessageAcceptedResponse::class, $result);
    }

    /** A send whose response is lost must not be sent twice: the retry reuses the key the SDK generated. */
    public function testSendBroadcastMessageGeneratesOneIdempotencyKeyPerCall(): void
    {
        $client = $this->client(
            new Response(503, ['Retry-After' => '0']),
            self::json([
            'status' => 'example',
            'messageId' => '550e8400-e29b-41d4-a716-446655440000',
            'suppressions' => ['example'],
        ]),
        );

        $client->sending->sendBroadcastMessage(
            from: ['email' => 'from@yourdomain.com', 'name' => 'From name'],
            to: [['email' => 'to@example.com', 'name' => 'To name']],
            cc: [['email' => 'cc@example.com', 'name' => 'Cc name']],
            bcc: [['email' => 'bcc@example.com', 'name' => 'Bcc name']],
            replyTo: [['email' => 'reply-to@example.com', 'name' => 'Reply-To name']],
            subject: 'Hello from Helo',
            html: '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
            text: 'This is a test message, delivered with <3 by Helo.',
            tracking: ['opens' => true, 'links' => true],
            attachments: [
                [
                    'content' => 'SGVsbG8gd29ybGQ=',
                    'contentId' => 'test-contentId',
                    'contentType' => 'test-contentType',
                    'fileName' => 'test-fileName',
                    'disposition' => AttachmentDisposition::Attachment,
                ],
            ],
            tags: ['welcome', 'onboarding'],
            channelId: '550e8400-e29b-41d4-a716-446655440000',
        );

        $keys = array_map(static fn ($request) => $request->getHeaderLine('X-Helo-Idempotency-Key'), $this->requests());
        self::assertCount(2, $keys);
        self::assertSame($keys[0], $keys[1]);
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $keys[0]);
    }
}
