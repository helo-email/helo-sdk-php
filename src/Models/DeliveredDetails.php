<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** Details of an `email-delivered` event. */
final class DeliveredDetails
{
    public function __construct(
        /** SMTP response returned by the receiving server. */
        public readonly ?string $response = null,
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
            response: $data['response'] ?? null,
        );
    }
}
