<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Resources;

use HeloEmail\Sdk\Exceptions\ApiException;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
use HeloEmail\Sdk\Internal\HttpClient;
use HeloEmail\Sdk\Models\SendBroadcastResponse;
use HeloEmail\Sdk\Models\SendMessageAcceptedResponse;
use HeloEmail\Sdk\Models\SendMessageBatchResponse;

final class SendingResource
{
    /** @internal constructed by the client */
    public function __construct(private readonly HttpClient $http)
    {
    }

    /**
     * Send a transactional email
     *
     * @param array<string, mixed> $from
     * @param list<array<string, mixed>> $to
     * @param list<array<string, mixed>>|null $cc
     * @param list<array<string, mixed>>|null $bcc
     * @param list<array<string, mixed>>|null $replyTo
     * @param array<string, mixed>|null $template
     * @param array<string, mixed>|null $tracking
     * @param list<array<string, mixed>>|null $attachments
     * @param list<string>|null $tags
     * @param array<string, string>|null $headers
     * @param array<string, string>|null $metadata
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function sendTransactional(
        array $from,
        array $to,
        ?array $cc = null,
        ?array $bcc = null,
        ?array $replyTo = null,
        ?string $subject = null,
        ?string $html = null,
        ?string $text = null,
        ?array $template = null,
        ?array $tracking = null,
        ?array $attachments = null,
        ?array $tags = null,
        ?array $headers = null,
        ?array $metadata = null,
        ?string $channelId = null,
        ?string $idempotencyKey = null,
    ): SendMessageAcceptedResponse {
        $data = $this->http->request(
            'POST',
            '/send/transactional',
            body: [
                'from' => $from,
                'to' => $to,
                'cc' => $cc,
                'bcc' => $bcc,
                'replyTo' => $replyTo,
                'subject' => $subject,
                'html' => $html,
                'text' => $text,
                'template' => $template,
                'tracking' => $tracking,
                'attachments' => $attachments,
                'tags' => $tags,
                'headers' => $headers,
                'metadata' => $metadata,
            ],
            headers: [
                'X-Helo-Channel-Id' => $channelId,
                'X-Helo-Idempotency-Key' => $idempotencyKey,
            ],
        );

        return SendMessageAcceptedResponse::fromArray($data);
    }

    /**
     * Send transactional emails in batch
     *
     * @param list<array<string, mixed>> $requests
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function sendTransactionalBatch(
        array $requests,
        ?string $channelId = null,
        ?string $idempotencyKey = null,
    ): SendMessageBatchResponse {
        $data = $this->http->request(
            'POST',
            '/send/transactional/batch',
            body: [
                'requests' => $requests,
            ],
            headers: [
                'X-Helo-Channel-Id' => $channelId,
                'X-Helo-Idempotency-Key' => $idempotencyKey,
            ],
        );

        return SendMessageBatchResponse::fromArray($data);
    }

    /**
     * Send a broadcast
     *
     * @param array<string, mixed> $from
     * @param array<string, mixed> $template
     * @param list<array<string, mixed>> $messages
     * @param list<array<string, mixed>>|null $replyTo
     * @param array<string, mixed>|null $tracking
     * @param list<array<string, mixed>>|null $attachments
     * @param list<string>|null $tags
     * @param array<string, string>|null $headers
     * @param array<string, string>|null $metadata
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function sendBroadcast(
        array $from,
        array $template,
        array $messages,
        ?array $replyTo = null,
        ?array $tracking = null,
        ?array $attachments = null,
        ?array $tags = null,
        ?array $headers = null,
        ?array $metadata = null,
        ?string $channelId = null,
        ?string $idempotencyKey = null,
    ): SendBroadcastResponse {
        $data = $this->http->request(
            'POST',
            '/send/broadcast',
            body: [
                'from' => $from,
                'template' => $template,
                'messages' => $messages,
                'replyTo' => $replyTo,
                'tracking' => $tracking,
                'attachments' => $attachments,
                'tags' => $tags,
                'headers' => $headers,
                'metadata' => $metadata,
            ],
            headers: [
                'X-Helo-Channel-Id' => $channelId,
                'X-Helo-Idempotency-Key' => $idempotencyKey,
            ],
        );

        return SendBroadcastResponse::fromArray($data);
    }

    /**
     * Send a single broadcast email
     *
     * @param array<string, mixed> $from
     * @param list<array<string, mixed>> $to
     * @param list<array<string, mixed>>|null $cc
     * @param list<array<string, mixed>>|null $bcc
     * @param list<array<string, mixed>>|null $replyTo
     * @param array<string, mixed>|null $template
     * @param array<string, mixed>|null $tracking
     * @param list<array<string, mixed>>|null $attachments
     * @param list<string>|null $tags
     * @param array<string, string>|null $headers
     * @param array<string, string>|null $metadata
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function sendBroadcastMessage(
        array $from,
        array $to,
        ?array $cc = null,
        ?array $bcc = null,
        ?array $replyTo = null,
        ?string $subject = null,
        ?string $html = null,
        ?string $text = null,
        ?array $template = null,
        ?array $tracking = null,
        ?array $attachments = null,
        ?array $tags = null,
        ?array $headers = null,
        ?array $metadata = null,
        ?string $channelId = null,
        ?string $idempotencyKey = null,
    ): SendMessageAcceptedResponse {
        $data = $this->http->request(
            'POST',
            '/send/broadcast/message',
            body: [
                'from' => $from,
                'to' => $to,
                'cc' => $cc,
                'bcc' => $bcc,
                'replyTo' => $replyTo,
                'subject' => $subject,
                'html' => $html,
                'text' => $text,
                'template' => $template,
                'tracking' => $tracking,
                'attachments' => $attachments,
                'tags' => $tags,
                'headers' => $headers,
                'metadata' => $metadata,
            ],
            headers: [
                'X-Helo-Channel-Id' => $channelId,
                'X-Helo-Idempotency-Key' => $idempotencyKey,
            ],
        );

        return SendMessageAcceptedResponse::fromArray($data);
    }
}
