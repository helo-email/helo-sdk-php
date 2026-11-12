<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Resources;

use HeloEmail\Sdk\Exceptions\ApiException;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
use HeloEmail\Sdk\Internal\HttpClient;
use HeloEmail\Sdk\Models\BroadcastDetailsResponse;
use HeloEmail\Sdk\Models\BroadcastStatus;
use HeloEmail\Sdk\Models\PaginatedResponseOfBroadcast;
use HeloEmail\Sdk\Models\PaginatedResponseOfBroadcastFailure;
use HeloEmail\Sdk\Models\PaginatedResponseOfBroadcastSuppression;

final class BroadcastsResource
{
    /** @internal constructed by the client */
    public function __construct(private readonly HttpClient $http)
    {
    }

    /**
     * List broadcasts
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function list(
        string $channelId,
        ?BroadcastStatus $status = null,
        ?string $subject = null,
        ?string $from = null,
        ?string $to = null,
        ?int $limit = null,
        ?int $offset = null,
    ): PaginatedResponseOfBroadcast {
        $data = $this->http->request(
            'GET',
            '/broadcasts',
            query: [
                'channelId' => $channelId,
                'status' => $status,
                'subject' => $subject,
                'from' => $from,
                'to' => $to,
                'limit' => $limit,
                'offset' => $offset,
            ],
        );

        return PaginatedResponseOfBroadcast::fromArray($data);
    }

    /**
     * Retrieve a broadcast
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function retrieve(
        string $id,
    ): BroadcastDetailsResponse {
        $data = $this->http->request(
            'GET',
            '/broadcasts/' . HttpClient::pathSegment($id),
        );

        return BroadcastDetailsResponse::fromArray($data);
    }

    /**
     * List failed broadcast messages
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function listFailures(
        string $id,
        ?int $limit = null,
        ?int $offset = null,
    ): PaginatedResponseOfBroadcastFailure {
        $data = $this->http->request(
            'GET',
            '/broadcasts/' . HttpClient::pathSegment($id) . '/failures',
            query: [
                'limit' => $limit,
                'offset' => $offset,
            ],
        );

        return PaginatedResponseOfBroadcastFailure::fromArray($data);
    }

    /**
     * List broadcast suppressed recipients
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function listSuppressions(
        string $id,
        ?int $limit = null,
        ?int $offset = null,
    ): PaginatedResponseOfBroadcastSuppression {
        $data = $this->http->request(
            'GET',
            '/broadcasts/' . HttpClient::pathSegment($id) . '/suppressions',
            query: [
                'limit' => $limit,
                'offset' => $offset,
            ],
        );

        return PaginatedResponseOfBroadcastSuppression::fromArray($data);
    }
}
