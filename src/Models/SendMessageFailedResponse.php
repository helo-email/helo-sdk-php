<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class SendMessageFailedResponse
{
    public function __construct(
        public readonly string $status,
        public readonly string $errorCode,
        public readonly string $errorMessage,
        /**
         * Field-level validation errors for this message, keyed by field (relative to the message).
         * Only present when `errorCode` is `validation_failed`, which happens in a `relaxed` batch.
         * @var array<string, list<ValidationError>>|null
         */
        public readonly ?array $errors = null,
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
            errors: isset($data['errors']) ? array_map(static fn ($item) => array_map(static fn ($item) => ValidationError::fromArray($item), $item), $data['errors']) : null,
        );
    }
}
