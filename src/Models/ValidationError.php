<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** A single field validation error. */
final class ValidationError
{
    public function __construct(
        /** Description of the validation failure. */
        public readonly string $message,
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
            message: $data['message'],
        );
    }
}
