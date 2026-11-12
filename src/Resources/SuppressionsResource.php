<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Resources;

use HeloEmail\Sdk\Exceptions\ApiException;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
use HeloEmail\Sdk\Internal\HttpClient;
use HeloEmail\Sdk\Models\CreateSuppressionsResponse;
use HeloEmail\Sdk\Models\MailType;
use HeloEmail\Sdk\Models\PaginatedResponseOfSuppressionResponse;
use HeloEmail\Sdk\Models\RemoveSuppressionsResponse;
use HeloEmail\Sdk\Models\SuppressionReason;

final class SuppressionsResource
{
    /** @internal constructed by the client */
    public function __construct(private readonly HttpClient $http)
    {
    }

    /**
     * List suppressions
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function list(
        string $channelId,
        MailType $mailType,
        ?SuppressionReason $reason = null,
        ?string $email = null,
        ?int $limit = null,
        ?int $offset = null,
    ): PaginatedResponseOfSuppressionResponse {
        $data = $this->http->request(
            'GET',
            '/suppressions',
            query: [
                'channelId' => $channelId,
                'mailType' => $mailType,
                'reason' => $reason,
                'email' => $email,
                'limit' => $limit,
                'offset' => $offset,
            ],
        );

        return PaginatedResponseOfSuppressionResponse::fromArray($data);
    }

    /**
     * Create suppressions
     *
     * @param list<string> $emails
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function create(
        string $channelId,
        MailType $mailType,
        array $emails,
    ): CreateSuppressionsResponse {
        $data = $this->http->request(
            'POST',
            '/suppressions',
            body: [
                'channelId' => $channelId,
                'mailType' => $mailType,
                'emails' => $emails,
            ],
        );

        return CreateSuppressionsResponse::fromArray($data);
    }

    /**
     * Remove suppressions
     *
     * @param list<string> $emails
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function remove(
        string $channelId,
        MailType $mailType,
        array $emails,
    ): RemoveSuppressionsResponse {
        $data = $this->http->request(
            'POST',
            '/suppressions/remove',
            body: [
                'channelId' => $channelId,
                'mailType' => $mailType,
                'emails' => $emails,
            ],
        );

        return RemoveSuppressionsResponse::fromArray($data);
    }
}
