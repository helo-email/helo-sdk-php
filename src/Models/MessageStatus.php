<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

enum MessageStatus: string
{
    case Queued = 'queued';
    case Sent = 'sent';
}
