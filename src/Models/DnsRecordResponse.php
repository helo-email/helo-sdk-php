<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class DnsRecordResponse
{
    public function __construct(
        public readonly DnsRecordType|string|null $type = null,
        public readonly ?string $host = null,
        public readonly ?string $value = null,
        public readonly DnsRecordStatus|string|null $status = null,
        public readonly ?\DateTimeImmutable $lastCheckedAt = null,
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
            type: isset($data['type']) ? DnsRecordType::tryFrom($data['type']) ?? $data['type'] : null,
            host: $data['host'] ?? null,
            value: $data['value'] ?? null,
            status: isset($data['status']) ? DnsRecordStatus::tryFrom($data['status']) ?? $data['status'] : null,
            lastCheckedAt: isset($data['lastCheckedAt']) ? new \DateTimeImmutable($data['lastCheckedAt']) : null,
        );
    }
}
