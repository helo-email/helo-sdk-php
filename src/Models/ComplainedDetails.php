<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** Details of a `recipient-complained` event. */
final class ComplainedDetails
{
    public function __construct(
        /** Complaint feedback type reported by the receiving server. */
        public readonly ?string $type = null,
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
            type: $data['type'] ?? null,
        );
    }
}
