<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class SendMessageResponse
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $messageId = null,
        /** @var list<string>|null */
        public readonly ?array $suppressions = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $errorMessage = null,
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
            status: $data['status'],
            messageId: $data['messageId'] ?? null,
            suppressions: $data['suppressions'] ?? null,
            errorCode: $data['errorCode'] ?? null,
            errorMessage: $data['errorMessage'] ?? null,
        );
    }
}
