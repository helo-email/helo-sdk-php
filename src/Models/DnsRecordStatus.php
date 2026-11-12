<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

enum DnsRecordStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Failing = 'failing';
    case Failed = 'failed';
}
