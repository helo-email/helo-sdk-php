<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class BroadcastStatistics
{
    public function __construct(
        /** Total messages submitted to the mail transfer agent. */
        public readonly int $sent,
        /** Messages accepted by the recipient mail server. */
        public readonly int $delivered,
        /** Messages rejected by the recipient mail server. */
        public readonly int $bounced,
        /** Unique recipients who opened the email (requires open tracking). */
        public readonly int $opened,
        /** Unique recipients who clicked a tracked link (requires link tracking). */
        public readonly int $clicked,
        /** Recipients who marked the email as spam. */
        public readonly int $complained,
        /** Recipients who clicked the unsubscribe link. */
        public readonly int $unsubscribed,
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
            sent: $data['sent'],
            delivered: $data['delivered'],
            bounced: $data['bounced'],
            opened: $data['opened'],
            clicked: $data['clicked'],
            complained: $data['complained'],
            unsubscribed: $data['unsubscribed'],
        );
    }
}
