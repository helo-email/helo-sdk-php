<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Exceptions;

/** The request could not reach the API (network error), after retries. */
class ApiConnectionException extends HeloException
{
}
