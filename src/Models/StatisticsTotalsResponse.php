<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class StatisticsTotalsResponse
{
    public function __construct(
        public readonly ?DeliveryStats $transactional = null,
        public readonly ?DeliveryStats $broadcast = null,
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
            transactional: isset($data['transactional']) ? DeliveryStats::fromArray($data['transactional']) : null,
            broadcast: isset($data['broadcast']) ? DeliveryStats::fromArray($data['broadcast']) : null,
        );
    }
}
