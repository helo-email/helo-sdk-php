<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** Details of a `recipient-unsubscribed` event. */
final class UnsubscribedDetails
{
    public function __construct(
        /** IP address the unsubscribe was made from. */
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
