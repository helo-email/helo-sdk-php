<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class DeliveryStats
{
    public function __construct(
        public readonly ?int $sent = null,
        public readonly ?int $delivered = null,
        public readonly ?int $opened = null,
        public readonly ?int $clicked = null,
        public readonly ?int $bounced = null,
        public readonly ?int $unsubscribed = null,
        public readonly ?int $complained = null,
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
            sent: $data['sent'] ?? null,
            delivered: $data['delivered'] ?? null,
            opened: $data['opened'] ?? null,
            clicked: $data['clicked'] ?? null,
            bounced: $data['bounced'] ?? null,
            unsubscribed: $data['unsubscribed'] ?? null,
            complained: $data['complained'] ?? null,
        );
    }
}
