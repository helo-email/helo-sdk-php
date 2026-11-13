<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** Payload delivered for the `link-clicked` event. */
final class LinkClickedWebhookPayload
{
    public function __construct(
        public readonly WebhookEvent|string $eventType,
        /** The single recipient the event relates to. */
        public readonly string $recipient,
        /** Unique identifier of the message the event relates to. */
        public readonly string $messageId,
        /** Unique identifier of the channel the message was sent from. */
        public readonly string $channelId,
        public readonly MailType|string $mailType,
        /** When the event occurred (ISO 8601 / RFC 3339). */
        public readonly \DateTimeImmutable $timestamp,
        public readonly ?ClickedDetails $details = null,
        /** Subject line of the message. */
        public readonly ?string $subject = null,
        /**
         * Tags associated with the message.
         * @var list<string>|null
         */
        public readonly ?array $tags = null,
        /**
         * Arbitrary key/value metadata associated with the message.
         * @var array<string, string>|null
         */
        public readonly ?array $metadata = null,
    ) {
    }

    /**
     * Builds the model from a decoded JSON response.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            eventType: WebhookEvent::tryFrom($data['eventType']) ?? $data['eventType'],
            recipient: $data['recipient'],
            messageId: $data['messageId'],
            channelId: $data['channelId'],
            mailType: MailType::tryFrom($data['mailType']) ?? $data['mailType'],
            timestamp: new \DateTimeImmutable($data['timestamp']),
            details: isset($data['details']) ? ClickedDetails::fromArray($data['details']) : null,
            subject: $data['subject'] ?? null,
            tags: $data['tags'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }
}
