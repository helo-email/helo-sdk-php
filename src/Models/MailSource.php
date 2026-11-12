<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

enum MailSource: string
{
    case Api = 'api';
    case Smtp = 'smtp';
}
