<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Webhooks;

use HeloEmail\Sdk\Exceptions\WebhookSignatureException;
use HeloEmail\Sdk\Models\DomainKeyVerificationFailedPayload;
use HeloEmail\Sdk\Models\DomainKeyVerifiedPayload;
use HeloEmail\Sdk\Models\EmailBouncedWebhookPayload;
use HeloEmail\Sdk\Models\EmailDeliveredWebhookPayload;
use HeloEmail\Sdk\Models\EmailOpenedWebhookPayload;
use HeloEmail\Sdk\Models\LinkClickedWebhookPayload;
use HeloEmail\Sdk\Models\MessageAcceptedWebhookPayload;
use HeloEmail\Sdk\Models\MessageProcessedWebhookPayload;
use HeloEmail\Sdk\Models\RecipientComplainedWebhookPayload;
use HeloEmail\Sdk\Models\RecipientResubscribedWebhookPayload;
use HeloEmail\Sdk\Models\RecipientUnsubscribedWebhookPayload;
use HeloEmail\Sdk\Models\ReturnPathDomainVerificationFailedPayload;
use HeloEmail\Sdk\Models\ReturnPathDomainVerifiedPayload;

/**
 * Turns an incoming webhook request into its typed payload, after verifying
 * its signature.
 */
final class WebhookParser
{
    /**
     * Verifies the delivery's signature, then decodes its body into the payload
     * class for its event type. Returns null for an event type this SDK version
     * does not know (one the API added later), so you can still acknowledge it.
     *
     * @param string|null $signatureHeader value of the X-Helo-Webhook-Signature header
     * @param string $requestBody raw (unparsed) request body
     * @param string $signingKey signing key for the webhook endpoint
     *
     * @return MessageAcceptedWebhookPayload|MessageProcessedWebhookPayload|EmailDeliveredWebhookPayload|EmailBouncedWebhookPayload|EmailOpenedWebhookPayload|LinkClickedWebhookPayload|RecipientComplainedWebhookPayload|RecipientUnsubscribedWebhookPayload|RecipientResubscribedWebhookPayload|DomainKeyVerifiedPayload|DomainKeyVerificationFailedPayload|ReturnPathDomainVerifiedPayload|ReturnPathDomainVerificationFailedPayload|null
     *
     * @throws WebhookSignatureException when the signature is rejected
     * @throws \JsonException when the body is not valid JSON
     */
    public static function parse(?string $signatureHeader, string $requestBody, string $signingKey): ?object
    {
        WebhookSignature::verify($signatureHeader, $requestBody, $signingKey);

        $data = json_decode($requestBody, true, flags: JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            return null;
        }

        return match ($data['eventType'] ?? null) {
            'message-accepted' => MessageAcceptedWebhookPayload::fromArray($data),
            'message-processed' => MessageProcessedWebhookPayload::fromArray($data),
            'email-delivered' => EmailDeliveredWebhookPayload::fromArray($data),
            'email-bounced' => EmailBouncedWebhookPayload::fromArray($data),
            'email-opened' => EmailOpenedWebhookPayload::fromArray($data),
            'link-clicked' => LinkClickedWebhookPayload::fromArray($data),
            'recipient-complained' => RecipientComplainedWebhookPayload::fromArray($data),
            'recipient-unsubscribed' => RecipientUnsubscribedWebhookPayload::fromArray($data),
            'recipient-resubscribed' => RecipientResubscribedWebhookPayload::fromArray($data),
            'domain-key-verified' => DomainKeyVerifiedPayload::fromArray($data),
            'domain-key-verification-failed' => DomainKeyVerificationFailedPayload::fromArray($data),
            'return-path-domain-verified' => ReturnPathDomainVerifiedPayload::fromArray($data),
            'return-path-domain-verification-failed' => ReturnPathDomainVerificationFailedPayload::fromArray($data),
            default => null,
        };
    }
}
