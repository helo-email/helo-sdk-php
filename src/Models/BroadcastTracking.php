<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class BroadcastTracking
{
    public function __construct(
        /** Whether open tracking is enabled for this broadcast. */
        public readonly bool $opens,
        /** Whether link click tracking is enabled for this broadcast. */
        public readonly bool $links,
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
            opens: $data['opens'],
            links: $data['links'],
        );
    }
}
