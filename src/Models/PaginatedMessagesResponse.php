<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class PaginatedMessagesResponse
{
    public function __construct(
        public readonly float $totalCount,
        /** @var list<Message> */
        public readonly array $results,
        public readonly ?int $after = null,
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
            totalCount: $data['totalCount'],
            results: array_map(static fn ($item) => Message::fromArray($item), $data['results']),
            after: $data['after'] ?? null,
        );
    }
}
