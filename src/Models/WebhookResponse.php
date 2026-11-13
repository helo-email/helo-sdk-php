<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** Webhook configuration properties. */
final class WebhookResponse
{
    public function __construct(
        /** Unique identifier for the webhook. */
        public readonly ?string $id = null,
        /** Channel this webhook is scoped to, or null for account-wide. */
        public readonly ?string $channelId = null,
        /** The HTTPS URL events are delivered to. */
        public readonly ?string $url = null,
        /**
         * HMAC-SHA256 signing key used to verify webhook authenticity. Sent as the
         * X-Helo-Webhook-Signature request header on each delivery.
         */
        public readonly ?string $payloadSigningKey = null,
        /** Whether the webhook is active and will receive events. */
        public readonly ?bool $enabled = null,
        /**
         * Custom headers sent with each webhook delivery.
         * @var list<WebhookHeader>|null
         */
        public readonly ?array $additionalHeaders = null,
        /**
         * Event types that trigger this webhook.
         * @var list<WebhookEvent|string>|null
         */
        public readonly ?array $events = null,
        /**
         * The most recent delivery outcome for this webhook, or null if no delivery has been
         * attempted yet.
         */
        public readonly ?WebhookLastResponse $lastResponse = null,
    ) {
    }

    /**
     * Builds the model from a decoded JSON response.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            channelId: $data['channelId'] ?? null,
            url: $data['url'] ?? null,
            payloadSigningKey: $data['payloadSigningKey'] ?? null,
            enabled: $data['enabled'] ?? null,
            additionalHeaders: isset($data['additionalHeaders']) ? array_map(static fn ($item) => WebhookHeader::fromArray($item), $data['additionalHeaders']) : null,
            events: isset($data['events']) ? array_map(static fn ($item) => WebhookEvent::tryFrom($item) ?? $item, $data['events']) : null,
            lastResponse: isset($data['lastResponse']) ? WebhookLastResponse::fromArray($data['lastResponse']) : null,
        );
    }
}
