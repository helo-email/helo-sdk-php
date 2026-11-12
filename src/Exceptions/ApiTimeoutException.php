<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Exceptions;

/** The request timed out, after retries. */
class ApiTimeoutException extends ApiConnectionException
{
}
