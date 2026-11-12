<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class RenderTemplateResponse
{
    public function __construct(
        public readonly string $subject,
        public readonly ?string $html = null,
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
