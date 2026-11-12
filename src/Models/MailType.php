<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

enum MailType: string
{
    case Transactional = 'transactional';
    case Broadcast = 'broadcast';
}
