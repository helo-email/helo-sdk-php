<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class RemoveSuppressionsResponse
{
    public function __construct(
        /** @var list<RemoveSuppressionResult> */
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
            results: array_map(static fn ($item) => RemoveSuppressionResult::fromArray($item), $data['results']),
        );
    }
}
