<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class TemplateResponse
{
    public function __construct(
        public readonly string $id,
        public readonly \DateTimeImmutable $createdAt,
        public readonly \DateTimeImmutable $updatedAt,
        /**
         * Opaque version, changing on every update. Pass it back as the version of an update to
         * have the update rejected if the template has been modified in the meantime.
         */
        public readonly int $version,
        public readonly string $name,
        public readonly string $subject,
        public readonly ?string $html = null,
        public readonly ?string $mjml = null,
        public readonly ?string $text = null,
        /** @var list<string>|null */
        public readonly ?array $channelIds = null,
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
            id: $data['id'],
            createdAt: new \DateTimeImmutable($data['createdAt']),
            updatedAt: new \DateTimeImmutable($data['updatedAt']),
            version: $data['version'],
            name: $data['name'],
            subject: $data['subject'],
            html: $data['html'] ?? null,
            mjml: $data['mjml'] ?? null,
            text: $data['text'] ?? null,
            channelIds: $data['channelIds'] ?? null,
        );
    }
}
