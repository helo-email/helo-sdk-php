<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** Payload delivered when a domain key verification fails. */
final class DomainKeyVerificationFailedPayload
{
    public function __construct(
        public readonly WebhookEvent|string $eventType,
        /** Unique identifier for the domain. */
        public readonly string $domainId,
        /** The name of the domain. */
        public readonly string $domainName,
        /** The host for the DNS record. */
        public readonly string $dnsRecordHost,
        /** When the event occurred (ISO 8601 / RFC 3339). */
        public readonly \DateTimeImmutable $timestamp,
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
            eventType: WebhookEvent::tryFrom($data['eventType']) ?? $data['eventType'],
            domainId: $data['domainId'],
            domainName: $data['domainName'],
            dnsRecordHost: $data['dnsRecordHost'],
            timestamp: new \DateTimeImmutable($data['timestamp']),
        );
    }
}
