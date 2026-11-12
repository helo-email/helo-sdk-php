<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

enum SuppressionReason: string
{
    case Bounce = 'bounce';
    case Complaint = 'complaint';
    case Unsubscribe = 'unsubscribe';
    case Manual = 'manual';
}
