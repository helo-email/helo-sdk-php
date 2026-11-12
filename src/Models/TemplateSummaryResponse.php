<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class TemplateSummaryResponse
{
    public function __construct(
        public readonly string $id,
        public readonly \DateTimeImmutable $createdAt,
        public readonly \DateTimeImmutable $updatedAt,
        public readonly string $name,
        public readonly string $subject,
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
            name: $data['name'],
            subject: $data['subject'],
            channelIds: $data['channelIds'] ?? null,
        );
    }
}
