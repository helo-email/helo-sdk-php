<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class BroadcastResponse
{
    public function __construct(
        /** Unique broadcast identifier. */
        public readonly string $id,
        /** When the broadcast was submitted. */
        public readonly \DateTimeImmutable $createdAt,
        public readonly BroadcastStatus $status,
        /** Email subject line. */
        public readonly string $subject,
        /** Processing progress as a percentage (e.g. `"45%"`). */
        public readonly string $completion,
        /** Total number of messages in the broadcast. */
        public readonly int $messages,
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
            status: BroadcastStatus::from($data['status']),
            subject: $data['subject'],
            completion: $data['completion'],
            messages: $data['messages'],
        );
    }
}
