<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** The most recent delivery outcome recorded for a webhook. */
final class WebhookLastResponse
{
    public function __construct(
        /**
         * HTTP status code returned by the endpoint, or null when no HTTP response was received
         * (network error, timeout, or blocked host).
         */
        public readonly ?int $statusCode = null,
        /**
         * Reason a delivery failed without an HTTP response (e.g. a connection error or
         * BlockedHost). Null when an HTTP response was received.
         */
        public readonly ?string $error = null,
        /** Timestamp of the most recent delivery attempt. */
        public readonly ?\DateTimeImmutable $at = null,
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
            statusCode: $data['statusCode'] ?? null,
            error: $data['error'] ?? null,
            at: isset($data['at']) ? new \DateTimeImmutable($data['at']) : null,
        );
    }
}
