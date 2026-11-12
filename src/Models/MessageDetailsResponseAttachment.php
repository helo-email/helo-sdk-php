<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class MessageDetailsResponseAttachment
{
    public function __construct(
        public readonly string $fileName,
        public readonly AttachmentDisposition $disposition,
        public readonly float $size,
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
            fileName: $data['fileName'],
            disposition: AttachmentDisposition::from($data['disposition']),
            size: $data['size'],
        );
    }
}
