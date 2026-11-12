<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class MailAddress
{
    public function __construct(
        /** Email address. Max 254 characters. */
        public readonly string $email,
        /** Display name shown to recipients in their email client. */
        public readonly ?string $name = null,
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
            email: $data['email'],
            name: $data['name'] ?? null,
        );
    }
}
