<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class StatisticsHourlyResponseResult
{
    public function __construct(
        public readonly ?\DateTimeImmutable $timestamp = null,
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
            timestamp: isset($data['timestamp']) ? new \DateTimeImmutable($data['timestamp']) : null,
            transactional: isset($data['transactional']) ? DeliveryStats::fromArray($data['transactional']) : null,
            broadcast: isset($data['broadcast']) ? DeliveryStats::fromArray($data['broadcast']) : null,
        );
    }
}
