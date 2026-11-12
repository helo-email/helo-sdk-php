<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

enum DnsRecordType: string
{
    case Txt = 'txt';
    case Cname = 'cname';
}
