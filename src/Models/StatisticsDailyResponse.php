<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class StatisticsDailyResponse
{
    public function __construct(
        /** @var list<StatisticsDailyResponseResult>|null */
        public readonly ?array $results = null,
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
            results: isset($data['results']) ? array_map(static fn ($item) => StatisticsDailyResponseResult::fromArray($item), $data['results']) : null,
        );
    }
}
