<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class CreateSuppressionsResponse
{
    public function __construct(
        /** @var list<SuppressionResult> */
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
            results: array_map(static fn ($item) => SuppressionResult::fromArray($item), $data['results']),
        );
    }
}
