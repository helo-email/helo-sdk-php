<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** The mail client the recipient engaged with the message from. */
final class ClientDetails
{
    public function __construct(
        public readonly ?string $family = null,
        public readonly ?string $version = null,
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
            family: $data['family'] ?? null,
            version: $data['version'] ?? null,
        );
    }
}
