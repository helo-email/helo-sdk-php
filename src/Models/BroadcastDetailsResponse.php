<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class BroadcastDetailsResponse
{
    public function __construct(
        public readonly string $id,
        public readonly \DateTimeImmutable $createdAt,
        public readonly BroadcastStatus $status,
        public readonly string $subject,
        public readonly string $completion,
        public readonly int $messages,
        /**
         * Number of messages that failed permanently. Retrieve details via `GET
         * /broadcasts/{id}/failures`.
         */
        public readonly int $failed,
        /**
         * Number of recipients skipped because they appear on a suppression list. Retrieve details
         * via `GET /broadcasts/{id}/suppressions`.
         */
        public readonly int $suppressed,
        public readonly BroadcastContent $content,
        public readonly BroadcastTracking $tracking,
        public readonly BroadcastStatistics $statistics,
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
            failed: $data['failed'],
            suppressed: $data['suppressed'],
            content: BroadcastContent::fromArray($data['content']),
            tracking: BroadcastTracking::fromArray($data['tracking']),
            statistics: BroadcastStatistics::fromArray($data['statistics']),
        );
    }
}
