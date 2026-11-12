<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class SendMessageBatchResponse
{
    public function __construct(
        /** @var list<SendMessageResponse> */
        public readonly array $responses,
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
            responses: array_map(static fn ($item) => SendMessageResponse::fromArray($item), $data['responses']),
        );
    }
}
