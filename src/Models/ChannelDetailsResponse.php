<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class ChannelDetailsResponse
{
    public function __construct(
        /** The unique channel ID. */
        public readonly ?string $id = null,
        /** The display name of the channel. */
        public readonly ?string $name = null,
        public readonly ?DeliveryType $deliveryType = null,
        /** When the channel was created. */
        public readonly ?\DateTimeImmutable $createdAt = null,
        /** When the channel was last updated. */
        public readonly ?\DateTimeImmutable $updatedAt = null,
        public readonly ?ChannelTracking $tracking = null,
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
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            deliveryType: isset($data['deliveryType']) ? DeliveryType::from($data['deliveryType']) : null,
            createdAt: isset($data['createdAt']) ? new \DateTimeImmutable($data['createdAt']) : null,
            updatedAt: isset($data['updatedAt']) ? new \DateTimeImmutable($data['updatedAt']) : null,
            tracking: isset($data['tracking']) ? ChannelTracking::fromArray($data['tracking']) : null,
        );
    }
}
