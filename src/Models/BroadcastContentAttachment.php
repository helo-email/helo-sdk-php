<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class BroadcastContentAttachment
{
    public function __construct(
        /** File name including extension. */
        public readonly string $fileName,
        public readonly AttachmentDisposition $disposition,
        /** File size in bytes. */
        public readonly int $size,
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
