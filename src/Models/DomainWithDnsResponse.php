<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class DomainWithDnsResponse
{
    public function __construct(
        public readonly string $id,
        public readonly \DateTimeImmutable $createdAt,
        public readonly string $name,
        public readonly bool $verified,
        public readonly DnsRecordsResponse $dnsRecords,
        /** @var list<DomainChannelResponse>|null */
        public readonly ?array $channels = null,
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
            id: $data['id'],
            createdAt: new \DateTimeImmutable($data['createdAt']),
            name: $data['name'],
            verified: $data['verified'],
            dnsRecords: DnsRecordsResponse::fromArray($data['dnsRecords']),
            channels: isset($data['channels']) ? array_map(static fn ($item) => DomainChannelResponse::fromArray($item), $data['channels']) : null,
        );
    }
}
