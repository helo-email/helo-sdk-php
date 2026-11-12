<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Exceptions;

/** The header carried only signing schemes this SDK cannot verify; upgrading the SDK is the fix. */
class WebhookSignatureUnsupportedVersionException extends WebhookSignatureException
{
}
