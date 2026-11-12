<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class ChannelTracking
{
    public function __construct(
        /** Whether link-click tracking is enabled. */
        public readonly bool $links,
        /** Whether open tracking is enabled. */
        public readonly bool $opens,
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
            links: $data['links'],
            opens: $data['opens'],
        );
    }
}
