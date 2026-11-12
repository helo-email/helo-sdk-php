<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/**
 * Delivery mode for a channel. `live` sends real emails, whereas `sandbox`
 * accepts and processes messages without delivering them to recipients.
 */
enum DeliveryType: string
{
    case Live = 'live';
    case Sandbox = 'sandbox';
}
