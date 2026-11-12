<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Exceptions;

/** The header was not in the documented `t={timestamp},v{version}={signature}` form. */
class WebhookSignatureMalformedHeaderException extends WebhookSignatureException
{
}
