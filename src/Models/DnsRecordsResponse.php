<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class DnsRecordsResponse
{
    public function __construct(
        public readonly ?DnsRecordResponse $domainKeyActive = null,
        public readonly ?DnsRecordResponse $domainKeyPending = null,
        /** @var list<DnsRecordResponse>|null */
        public readonly ?array $returnPath = null,
        public readonly ?DnsRecordResponse $tracking = null,
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
            domainKeyActive: isset($data['domainKeyActive']) ? DnsRecordResponse::fromArray($data['domainKeyActive']) : null,
            domainKeyPending: isset($data['domainKeyPending']) ? DnsRecordResponse::fromArray($data['domainKeyPending']) : null,
            returnPath: isset($data['returnPath']) ? array_map(static fn ($item) => DnsRecordResponse::fromArray($item), $data['returnPath']) : null,
            tracking: isset($data['tracking']) ? DnsRecordResponse::fromArray($data['tracking']) : null,
        );
    }
}
