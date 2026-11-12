<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class PaginatedResponseOfBroadcastSuppression
{
    public function __construct(
        public readonly int $totalCount,
        /** @var list<string> */
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
            results: $data['results'],
        );
    }
}
