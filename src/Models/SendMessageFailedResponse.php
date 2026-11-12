<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class SendMessageFailedResponse
{
    public function __construct(
        public readonly string $status,
        public readonly string $errorCode,
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
            status: $data['status'],
            errorCode: $data['errorCode'],
            errorMessage: $data['errorMessage'],
        );
    }
}
