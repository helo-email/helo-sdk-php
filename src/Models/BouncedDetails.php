<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** Details of an `email-bounced` event. */
final class BouncedDetails
{
    public function __construct(
        /** Bounce classification, e.g. `Permanent` or `Transient`. */
        public readonly ?string $type = null,
        /** More specific bounce classification, e.g. `General` or `Suppressed`. */
        public readonly ?string $subType = null,
        /** Diagnostic code returned by the receiving server. */
        public readonly ?string $code = null,
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
            subType: $data['subType'] ?? null,
            code: $data['code'] ?? null,
        );
    }
}
