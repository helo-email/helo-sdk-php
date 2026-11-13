<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class MessageDetailsResponseEvent
{
    public function __construct(
        public readonly EventType|string $eventType,
        public readonly \DateTimeImmutable $timestamp,
        /** @var list<string> */
        public readonly array $recipients,
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
            eventType: EventType::tryFrom($data['eventType']) ?? $data['eventType'],
            timestamp: new \DateTimeImmutable($data['timestamp']),
            recipients: $data['recipients'],
            details: $data['details'] ?? null,
        );
    }
}
