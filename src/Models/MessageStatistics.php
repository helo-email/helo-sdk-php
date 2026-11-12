<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class MessageStatistics
{
    public function __construct(
        public readonly int $delivered,
        public readonly int $bounced,
        public readonly int $opened,
        public readonly int $clicked,
        public readonly int $complained,
        public readonly int $unsubscribed,
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
            delivered: $data['delivered'],
            bounced: $data['bounced'],
            opened: $data['opened'],
            clicked: $data['clicked'],
            complained: $data['complained'],
            unsubscribed: $data['unsubscribed'],
        );
    }
}
