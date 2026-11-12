<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Resources;

use HeloEmail\Sdk\Exceptions\ApiException;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
use HeloEmail\Sdk\Internal\HttpClient;
use HeloEmail\Sdk\Models\PaginationResultOfWebhookResponse;
use HeloEmail\Sdk\Models\WebhookEvent;
use HeloEmail\Sdk\Models\WebhookResponse;

final class WebhooksResource
{
    /** @internal constructed by the client */
    public function __construct(private readonly HttpClient $http)
    {
    }

    /**
     * List all webhooks
     *
     * @param list<string>|null $channelIds
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?array $channelIds = null,
    ): PaginationResultOfWebhookResponse {
        $data = $this->http->request(
            'GET',
            '/webhooks',
            query: [
                'limit' => $limit,
                'offset' => $offset,
                'channelIds' => HttpClient::commaJoin($channelIds),
            ],
        );

        return PaginationResultOfWebhookResponse::fromArray($data);
    }

    /**
     * Create a webhook
     *
     * @param list<WebhookEvent> $events
     * @param list<array<string, mixed>>|null $additionalHeaders
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function create(
        string $url,
        array $events,
        ?string $channelId = null,
        ?array $additionalHeaders = null,
        ?bool $enabled = null,
    ): WebhookResponse {
        $data = $this->http->request(
            'POST',
            '/webhooks',
            body: [
                'url' => $url,
                'events' => $events,
                'channelId' => $channelId,
                'additionalHeaders' => $additionalHeaders,
                'enabled' => $enabled,
            ],
        );

        return WebhookResponse::fromArray($data);
    }

    /**
     * Retrieve a webhook
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function retrieve(
        string $id,
    ): WebhookResponse {
        $data = $this->http->request(
            'GET',
            '/webhooks/' . HttpClient::pathSegment($id),
        );

        return WebhookResponse::fromArray($data);
    }

    /**
     * Update a webhook
     *
     * @param list<WebhookEvent>|null $events
     * @param list<array<string, mixed>>|null $additionalHeaders
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function update(
        string $id,
        ?string $url = null,
        ?array $events = null,
        ?string $channelId = null,
        ?array $additionalHeaders = null,
        ?bool $enabled = null,
    ): WebhookResponse {
        $data = $this->http->request(
            'PATCH',
            '/webhooks/' . HttpClient::pathSegment($id),
            body: [
                'url' => $url,
                'events' => $events,
                'channelId' => $channelId,
                'additionalHeaders' => $additionalHeaders,
                'enabled' => $enabled,
            ],
        );

        return WebhookResponse::fromArray($data);
    }

    /**
     * Delete a webhook
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function delete(
        string $id,
    ): void {
        $this->http->request(
            'DELETE',
            '/webhooks/' . HttpClient::pathSegment($id),
        );
    }

    /**
     * Regenerate webhook signing key
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function regenerateSigningKey(
        string $id,
    ): WebhookResponse {
        $data = $this->http->request(
            'POST',
            '/webhooks/' . HttpClient::pathSegment($id) . '/regenerate-signing-key',
        );

        return WebhookResponse::fromArray($data);
    }
}
