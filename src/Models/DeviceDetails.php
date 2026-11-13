<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** The device the recipient engaged with the message from. */
final class DeviceDetails
{
    public function __construct(
        public readonly ?string $brand = null,
        public readonly ?string $family = null,
        public readonly ?string $model = null,
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
            brand: $data['brand'] ?? null,
            family: $data['family'] ?? null,
            model: $data['model'] ?? null,
        );
    }
}
