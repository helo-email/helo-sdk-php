<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class RemoveSuppressionResult
{
    public function __construct(
        public readonly string $email,
        public readonly bool $success,
        public readonly ?string $message = null,
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
            success: $data['success'],
            message: $data['message'] ?? null,
        );
    }
}
