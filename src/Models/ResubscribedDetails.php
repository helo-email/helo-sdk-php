<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** Details of a `recipient-resubscribed` event. */
final class ResubscribedDetails
{
    public function __construct(
        /** IP address the resubscribe was made from. */
        public readonly ?string $ip = null,
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
            ip: $data['ip'] ?? null,
        );
    }
}
