<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class SendBroadcastResponse
{
    public function __construct(
        /**
         * `accepted` — broadcast queued successfully. `failed` — validation or account error (see
         * `errorCode`). `delayed` — transient submission error, will be retried automatically.
         */
        public readonly ?string $status = null,
        /**
         * Unique identifier for the broadcast. Use this to track progress via `GET
         * /broadcasts/{id}`.
         */
        public readonly ?string $broadcastId = null,
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
            status: $data['status'] ?? null,
            broadcastId: $data['broadcastId'] ?? null,
        );
    }
}
