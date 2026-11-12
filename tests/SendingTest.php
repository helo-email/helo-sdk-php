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
            template: [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'subject' => 'test-subject',
                'html' => 'test-html',
                'text' => 'test-text',
                'inlineStyles' => true,
            ],
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
        self::assertInstanceOf(SendMessageAcceptedResponse::class, $result);
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
        self::assertInstanceOf(SendMessageBatchResponse::class, $result);
    }

    public function testSendBroadcast(): void
    {
        $client = $this->client(self::json(['status' => 'example', 'broadcastId' => '550e8400-e29b-41d4-a716-446655440000']));

        $result = $client->sending->sendBroadcast(
            from: ['email' => 'test@example.com', 'name' => 'test-name'],
            template: [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'subject' => 'test-subject',
                'html' => 'test-html',
                'text' => 'test-text',
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
        self::assertInstanceOf(SendBroadcastResponse::class, $result);
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
            template: [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'subject' => 'test-subject',
                'html' => 'test-html',
                'text' => 'test-text',
                'inlineStyles' => true,
            ],
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
        self::assertInstanceOf(SendMessageAcceptedResponse::class, $result);
    }
}
