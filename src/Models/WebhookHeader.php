<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** A custom HTTP header to include in webhook deliveries. */
final class WebhookHeader
{
    public function __construct(
        /** Header name, without the X-Customer- prefix. */
        public readonly string $name,
        /** Header value. */
        public readonly string $value,
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
            name: $data['name'],
            value: $data['value'],
        );
    }
}
