<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Exceptions;

/** HTTP 429. `$retryAfter` is the server-suggested delay in seconds, if any. */
class RateLimitException extends ApiException
{
    /**
     * @param array<mixed> $responseData the decoded error body
     */
    public function __construct(
        string $message,
        int $statusCode,
        ?string $errorCode = null,
        ?string $detail = null,
        ?string $requestId = null,
        array $responseData = [],
        public readonly ?float $retryAfter = null,
    ) {
        parent::__construct($message, $statusCode, $errorCode, $detail, $requestId, $responseData);
    }
}
