<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Exceptions;

/** Base class for every reason a webhook signature is rejected; catch it when you do not care why. */
class WebhookSignatureException extends HeloException
{
}
