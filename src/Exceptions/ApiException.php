<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Exceptions;

/**
 * The API returned a non-success status. Each status the API documents has its
 * own subclass (NotFoundException for 404, and so on).
 */
class ApiException extends HeloException
{
    /**
     * @param array<mixed> $responseData the decoded error body
     */
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly ?string $errorCode = null,
        public readonly ?string $detail = null,
        public readonly ?string $requestId = null,
        public readonly array $responseData = [],
    ) {
        parent::__construct($message, $statusCode);
    }
}
