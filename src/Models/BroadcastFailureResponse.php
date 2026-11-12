<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class BroadcastFailureResponse
{
    public function __construct(
        public readonly RecipientHeaders $recipients,
        /** Zero-based index of the failed message within the original `messages` array. */
        public readonly int $messageIndex,
        /** Machine-readable error code (e.g. `invalid_recipient`, `domain_unverified`). */
        public readonly string $errorCode,
        /** Human-readable description of the failure. */
        public readonly string $errorMessage,
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
            recipients: RecipientHeaders::fromArray($data['recipients']),
            messageIndex: $data['messageIndex'],
            errorCode: $data['errorCode'],
            errorMessage: $data['errorMessage'],
        );
    }
}
