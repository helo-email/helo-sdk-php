<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class PaginationResultOfChannelBasicResponse
{
    public function __construct(
        /** @var list<ChannelBasicResponse> */
        public readonly array $results,
        public readonly int $totalCount,
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
            results: array_map(static fn ($item) => ChannelBasicResponse::fromArray($item), $data['results']),
            totalCount: $data['totalCount'],
        );
    }
}
