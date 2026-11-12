<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Exceptions;

/** Correctly formed, but the timestamp is too far from the current time. It may be a replay. */
class WebhookSignatureTimestampSkewException extends WebhookSignatureException
{
}
