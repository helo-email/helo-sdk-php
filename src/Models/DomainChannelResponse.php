<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class DomainChannelResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly DeliveryType $deliveryType,
        public readonly ?bool $deleted = null,
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
            id: $data['id'],
            name: $data['name'],
            deliveryType: DeliveryType::from($data['deliveryType']),
            deleted: $data['deleted'] ?? null,
        );
    }
}
