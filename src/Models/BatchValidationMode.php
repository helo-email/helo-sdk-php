<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/**
 * How the batch handles messages that fail validation. `strict` — any validation failure rejects
 * the whole batch with a 422, and no messages are sent. `relaxed` — valid messages are sent, and
 * each invalid message is returned as `failed`. Suppressed recipients and the send limit are not
 * validation, so they fail only the affected message in either mode.
 */
enum BatchValidationMode: string
{
    case Strict = 'strict';
    case Relaxed = 'relaxed';
}
