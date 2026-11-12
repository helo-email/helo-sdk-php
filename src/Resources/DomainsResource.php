<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Resources;

use HeloEmail\Sdk\Exceptions\ApiException;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
use HeloEmail\Sdk\Internal\HttpClient;
use HeloEmail\Sdk\Models\DnsRecordResponse;
use HeloEmail\Sdk\Models\DnsRecordsResponse;
use HeloEmail\Sdk\Models\DomainResponse;
use HeloEmail\Sdk\Models\DomainWithDnsResponse;
use HeloEmail\Sdk\Models\PaginatedResponseOfDomainResponse;

final class DomainsResource
{
    /** @internal constructed by the client */
    public function __construct(private readonly HttpClient $http)
    {
    }

    /**
     * List all domains
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
    ): PaginatedResponseOfDomainResponse {
        $data = $this->http->request(
            'GET',
            '/domains',
            query: [
                'limit' => $limit,
                'offset' => $offset,
                'name' => $name,
                'channelIds' => HttpClient::commaJoin($channelIds),
            ],
        );

        return PaginatedResponseOfDomainResponse::fromArray($data);
    }

    /**
     * Create a domain
     *
     * @param list<string>|null $channelIds
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function create(
        string $name,
        ?array $channelIds = null,
        ?bool $enableCustomTracking = null,
    ): DomainWithDnsResponse {
        $data = $this->http->request(
            'POST',
            '/domains',
            body: [
                'name' => $name,
                'channelIds' => $channelIds,
                'enableCustomTracking' => $enableCustomTracking,
            ],
        );

        return DomainWithDnsResponse::fromArray($data);
    }

    /**
     * Retrieve a domain
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function retrieve(
        string $id,
    ): DomainWithDnsResponse {
        $data = $this->http->request(
            'GET',
            '/domains/' . HttpClient::pathSegment($id),
        );

        return DomainWithDnsResponse::fromArray($data);
    }

    /**
     * Update a domain
     *
     * @param list<string>|null $channelIds
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function update(
        string $id,
        ?array $channelIds = null,
        ?bool $enableCustomTracking = null,
    ): DomainResponse {
        $data = $this->http->request(
            'PATCH',
            '/domains/' . HttpClient::pathSegment($id),
            body: [
                'channelIds' => $channelIds,
                'enableCustomTracking' => $enableCustomTracking,
            ],
        );

        return DomainResponse::fromArray($data);
    }

    /**
     * Delete a domain
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function delete(
        string $id,
    ): void {
        $this->http->request(
            'DELETE',
            '/domains/' . HttpClient::pathSegment($id),
        );
    }

    /**
     * Verify a domain
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function verify(
        string $id,
    ): DnsRecordsResponse {
        $data = $this->http->request(
            'POST',
            '/domains/' . HttpClient::pathSegment($id) . '/verify',
        );

        return DnsRecordsResponse::fromArray($data);
    }

    /**
     * Rotate a domain key
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function rotateKey(
        string $id,
    ): DnsRecordResponse {
        $data = $this->http->request(
            'POST',
            '/domains/' . HttpClient::pathSegment($id) . '/rotate-key',
        );

        return DnsRecordResponse::fromArray($data);
    }
}
