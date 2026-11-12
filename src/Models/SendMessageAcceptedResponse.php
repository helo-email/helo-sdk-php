<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class SendMessageAcceptedResponse
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?string $messageId = null,
        /** @var list<string>|null */
        public readonly ?array $suppressions = null,
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
            messageId: $data['messageId'] ?? null,
            suppressions: $data['suppressions'] ?? null,
        );
    }
}
