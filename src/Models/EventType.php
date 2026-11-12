<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

enum EventType: string
{
    case Accepted = 'accepted';
    case Processed = 'processed';
    case Delivered = 'delivered';
    case Bounced = 'bounced';
    case Opened = 'opened';
    case Clicked = 'clicked';
    case Complained = 'complained';
    case Unsubscribed = 'unsubscribed';
    case Resubscribed = 'resubscribed';
}
