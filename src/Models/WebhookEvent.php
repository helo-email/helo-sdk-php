<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** Email lifecycle event type that can trigger a webhook delivery. */
enum WebhookEvent: string
{
    case MessageAccepted = 'message-accepted';
    case MessageProcessed = 'message-processed';
    case EmailDelivered = 'email-delivered';
    case EmailBounced = 'email-bounced';
    case EmailOpened = 'email-opened';
    case LinkClicked = 'link-clicked';
    case RecipientComplained = 'recipient-complained';
    case RecipientUnsubscribed = 'recipient-unsubscribed';
    case RecipientResubscribed = 'recipient-resubscribed';
    case DomainKeyVerified = 'domain-key-verified';
    case DomainKeyVerificationFailed = 'domain-key-verification-failed';
    case ReturnPathDomainVerified = 'return-path-domain-verified';
    case ReturnPathDomainVerificationFailed = 'return-path-domain-verification-failed';
}
