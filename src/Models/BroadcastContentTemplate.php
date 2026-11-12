<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class BroadcastContentTemplate
{
    public function __construct(
        /** Subject line. */
        public readonly string $subject,
        /** HTML body. */
        public readonly ?string $html = null,
        /** Plain-text fallback body. */
        public readonly ?string $text = null,
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
            subject: $data['subject'],
            html: $data['html'] ?? null,
            text: $data['text'] ?? null,
        );
    }
}
