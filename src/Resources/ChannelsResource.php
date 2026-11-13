<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Resources;

use HeloEmail\Sdk\Exceptions\ApiException;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
use HeloEmail\Sdk\Internal\HttpClient;
use HeloEmail\Sdk\Models\ChannelDetailsResponse;
use HeloEmail\Sdk\Models\DeliveryType;
use HeloEmail\Sdk\Models\PaginationResultOfChannelBasicResponse;

final class ChannelsResource
{
    /** @internal constructed by the client */
    public function __construct(private readonly HttpClient $http)
    {
    }

    /**
     * List all channels
     *
     * @param list<string>|null $channelIds
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?string $name = null,
        ?array $channelIds = null,
        ?DeliveryType $deliveryType = null,
    ): PaginationResultOfChannelBasicResponse {
        $data = $this->http->request(
            'GET',
            '/channels',
            query: [
                'limit' => $limit,
                'offset' => $offset,
                'name' => $name,
                'channelIds' => HttpClient::commaJoin($channelIds),
                'deliveryType' => $deliveryType,
            ],
        );

        return PaginationResultOfChannelBasicResponse::fromArray($data);
    }

    /**
     * Create a channel
     *
     * @param array<string, mixed>|null $tracking
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function create(
        string $name,
        DeliveryType $deliveryType,
        ?array $tracking = null,
    ): ChannelDetailsResponse {
        $data = $this->http->request(
            'POST',
            '/channels',
            body: [
                'name' => $name,
                'deliveryType' => $deliveryType,
                'tracking' => $tracking,
            ],
            objects: ['tracking'],
        );

        return ChannelDetailsResponse::fromArray($data);
    }

    /**
     * Retrieve a channel
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function retrieve(
        string $id,
    ): ChannelDetailsResponse {
        $data = $this->http->request(
            'GET',
            '/channels/' . HttpClient::pathSegment($id),
        );

        return ChannelDetailsResponse::fromArray($data);
    }

    /**
     * Update a channel
     *
     * @param array<string, mixed>|null $tracking
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function update(
        string $id,
        ?string $name = null,
        ?DeliveryType $deliveryType = null,
        ?array $tracking = null,
    ): ChannelDetailsResponse {
        $data = $this->http->request(
            'PATCH',
            '/channels/' . HttpClient::pathSegment($id),
            body: [
                'name' => $name,
                'deliveryType' => $deliveryType,
                'tracking' => $tracking,
            ],
            objects: ['tracking'],
        );

        return ChannelDetailsResponse::fromArray($data);
    }

    /**
     * Delete a channel
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function delete(
        string $id,
    ): void {
        $this->http->request(
            'DELETE',
            '/channels/' . HttpClient::pathSegment($id),
        );
    }
}
