<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class MessageDetailsResponse
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
        public readonly ActivityMailAddress $from,
        /** @var list<ActivityMailAddress> */
        public readonly array $to,
        public readonly MessageDetailsResponseTracking $tracking,
        /** @var list<MessageDetailsResponseEvent> */
        public readonly array $events,
        /** @var list<ActivityMailAddress>|null */
        public readonly ?array $cc = null,
        /** @var list<ActivityMailAddress>|null */
        public readonly ?array $bcc = null,
        /** @var list<ActivityMailAddress>|null */
        public readonly ?array $replyTo = null,
        public readonly ?string $text = null,
        public readonly ?string $html = null,
        public readonly ?string $body = null,
        /** @var list<string>|null */
        public readonly ?array $tags = null,
        /** @var array<string, string>|null */
        public readonly ?array $headers = null,
        /** @var array<string, string>|null */
        public readonly ?array $metadata = null,
        /** @var list<MessageDetailsResponseAttachment>|null */
        public readonly ?array $attachments = null,
        public readonly ?MessageStatistics $statistics = null,
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
            from: ActivityMailAddress::fromArray($data['from']),
            to: array_map(static fn ($item) => ActivityMailAddress::fromArray($item), $data['to']),
            tracking: MessageDetailsResponseTracking::fromArray($data['tracking']),
            events: array_map(static fn ($item) => MessageDetailsResponseEvent::fromArray($item), $data['events']),
            cc: isset($data['cc']) ? array_map(static fn ($item) => ActivityMailAddress::fromArray($item), $data['cc']) : null,
            bcc: isset($data['bcc']) ? array_map(static fn ($item) => ActivityMailAddress::fromArray($item), $data['bcc']) : null,
            replyTo: isset($data['replyTo']) ? array_map(static fn ($item) => ActivityMailAddress::fromArray($item), $data['replyTo']) : null,
            text: $data['text'] ?? null,
            html: $data['html'] ?? null,
            body: $data['body'] ?? null,
            tags: $data['tags'] ?? null,
            headers: $data['headers'] ?? null,
            metadata: $data['metadata'] ?? null,
            attachments: isset($data['attachments']) ? array_map(static fn ($item) => MessageDetailsResponseAttachment::fromArray($item), $data['attachments']) : null,
            statistics: isset($data['statistics']) ? MessageStatistics::fromArray($data['statistics']) : null,
        );
    }
}
