<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class PaginatedResponseOfBroadcast
{
    public function __construct(
        public readonly int $totalCount,
        /** @var list<BroadcastResponse> */
        public readonly array $results,
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
            results: array_map(static fn ($item) => BroadcastResponse::fromArray($item), $data['results']),
        );
    }
}
