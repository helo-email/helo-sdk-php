<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/**
 * Current processing state of a broadcast. `accepted` — queued, waiting for the channel to become
 * available. `processing` — actively sending messages. `completed` — all messages have been
 * processed. `canceled` — stopped before completing.
 */
enum BroadcastStatus: string
{
    case Accepted = 'accepted';
    case Processing = 'processing';
    case Completed = 'completed';
    case Canceled = 'canceled';
}
