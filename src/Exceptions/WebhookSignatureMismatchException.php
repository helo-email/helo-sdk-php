<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Exceptions;

/** The signature did not match the body: it was tampered with, or the signing key is wrong. */
class WebhookSignatureMismatchException extends WebhookSignatureException
{
}
