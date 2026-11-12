<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** Paginated list of webhooks. */
final class PaginationResultOfWebhookResponse
{
    public function __construct(
        /**
         * The page of results.
         * @var list<WebhookResponse>
         */
        public readonly array $results,
        /** Total number of webhooks matching the query. */
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
            results: array_map(static fn ($item) => WebhookResponse::fromArray($item), $data['results']),
            totalCount: $data['totalCount'],
        );
    }
}
