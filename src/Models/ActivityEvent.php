<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class ActivityEvent
{
    public function __construct(
        public readonly string $messageId,
        public readonly string $channelId,
        public readonly MailType $mailType,
        public readonly EventType $eventType,
        public readonly \DateTimeImmutable $timestamp,
        public readonly string $subject,
        /** @var list<string> */
        public readonly array $recipients,
        public readonly ?MailSource $mailSource = null,
        /** @var list<string>|null */
        public readonly ?array $tags = null,
        /** @var array<string, string>|null */
        public readonly ?array $metadata = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $details = null,
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
            messageId: $data['messageId'],
            channelId: $data['channelId'],
            mailType: MailType::from($data['mailType']),
            eventType: EventType::from($data['eventType']),
            timestamp: new \DateTimeImmutable($data['timestamp']),
            subject: $data['subject'],
            recipients: $data['recipients'],
            mailSource: isset($data['mailSource']) ? MailSource::from($data['mailSource']) : null,
            tags: $data['tags'] ?? null,
            metadata: $data['metadata'] ?? null,
            details: $data['details'] ?? null,
        );
    }
}
