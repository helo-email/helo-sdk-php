<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class Message
{
    public function __construct(
        public readonly string $messageId,
        public readonly string $channelId,
        public readonly \DateTimeImmutable $timestamp,
        public readonly MailType|string $mailType,
        public readonly MailSource|string $mailSource,
        public readonly DeliveryType|string $deliveryType,
        public readonly MessageStatus|string $status,
        public readonly string $subject,
        /** @var list<string> */
        public readonly array $recipients,
        public readonly MessageStatistics $statistics,
        /** @var list<string>|null */
        public readonly ?array $tags = null,
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
            timestamp: new \DateTimeImmutable($data['timestamp']),
            mailType: MailType::tryFrom($data['mailType']) ?? $data['mailType'],
            mailSource: MailSource::tryFrom($data['mailSource']) ?? $data['mailSource'],
            deliveryType: DeliveryType::tryFrom($data['deliveryType']) ?? $data['deliveryType'],
            status: MessageStatus::tryFrom($data['status']) ?? $data['status'],
            subject: $data['subject'],
            recipients: $data['recipients'],
            statistics: MessageStatistics::fromArray($data['statistics']),
            tags: $data['tags'] ?? null,
        );
    }
}
