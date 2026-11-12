<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Resources;

use HeloEmail\Sdk\Exceptions\ApiException;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
use HeloEmail\Sdk\Internal\HttpClient;
use HeloEmail\Sdk\Models\StatisticsDailyResponse;
use HeloEmail\Sdk\Models\StatisticsHourlyResponse;
use HeloEmail\Sdk\Models\StatisticsTotalsResponse;

final class StatisticsResource
{
    /** @internal constructed by the client */
    public function __construct(private readonly HttpClient $http)
    {
    }

    /**
     * Retrieve hourly statistics
     *
     * @param list<string>|null $tags
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function retrieveHourly(
        string $from,
        string $to,
        ?string $channelId = null,
        ?array $tags = null,
    ): StatisticsHourlyResponse {
        $data = $this->http->request(
            'GET',
            '/statistics/hourly',
            query: [
                'from' => $from,
                'to' => $to,
                'channelId' => $channelId,
                'tags' => HttpClient::commaJoin($tags),
            ],
        );

        return StatisticsHourlyResponse::fromArray($data);
    }

    /**
     * Retrieve daily statistics
     *
     * @param list<string>|null $tags
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function retrieveDaily(
        string $from,
        string $to,
        string $timezone,
        ?string $channelId = null,
        ?array $tags = null,
    ): StatisticsDailyResponse {
        $data = $this->http->request(
            'GET',
            '/statistics/daily',
            query: [
                'from' => $from,
                'to' => $to,
                'timezone' => $timezone,
                'channelId' => $channelId,
                'tags' => HttpClient::commaJoin($tags),
            ],
        );

        return StatisticsDailyResponse::fromArray($data);
    }

    /**
     * Retrieve all time statistics
     *
     * @param list<string>|null $tags
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function retrieveTotals(
        string $from,
        string $to,
        ?string $channelId = null,
        ?array $tags = null,
    ): StatisticsTotalsResponse {
        $data = $this->http->request(
            'GET',
            '/statistics/totals',
            query: [
                'from' => $from,
                'to' => $to,
                'channelId' => $channelId,
                'tags' => HttpClient::commaJoin($tags),
            ],
        );

        return StatisticsTotalsResponse::fromArray($data);
    }
}
