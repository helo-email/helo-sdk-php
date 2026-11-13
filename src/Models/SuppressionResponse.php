<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class SuppressionResponse
{
    public function __construct(
        public readonly string $email,
        public readonly SuppressionReason|string $reason,
        public readonly \DateTimeImmutable $createdAt,
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
            email: $data['email'],
            reason: SuppressionReason::tryFrom($data['reason']) ?? $data['reason'],
            createdAt: new \DateTimeImmutable($data['createdAt']),
        );
    }
}
