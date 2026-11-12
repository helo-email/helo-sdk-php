<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Models\StatisticsDailyResponse;
use HeloEmail\Sdk\Models\StatisticsHourlyResponse;
use HeloEmail\Sdk\Models\StatisticsTotalsResponse;

final class StatisticsTest extends TestCase
{
    public function testRetrieveHourly(): void
    {
        $client = $this->client(self::json([
            'results' => [
                [
                    'timestamp' => '2024-01-01T00:00:00Z',
                    'transactional' => [
                        'sent' => 1,
                        'delivered' => 1,
                        'opened' => 1,
                        'clicked' => 1,
                        'bounced' => 1,
                        'unsubscribed' => 1,
                        'complained' => 1,
                    ],
                    'broadcast' => [
                        'sent' => 1,
                        'delivered' => 1,
                        'opened' => 1,
                        'clicked' => 1,
                        'bounced' => 1,
                        'unsubscribed' => 1,
                        'complained' => 1,
                    ],
                ],
            ],
        ]));

        $result = $client->statistics->retrieveHourly(
            from: '2024-01-01T00:00:00Z',
            to: '2024-01-01T00:00:00Z',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/statistics/hourly', $request->getUri()->getPath());
        self::assertInstanceOf(StatisticsHourlyResponse::class, $result);
    }

    public function testRetrieveDaily(): void
    {
        $client = $this->client(self::json([
            'results' => [
                [
                    'timestamp' => '2024-01-01',
                    'transactional' => [
                        'sent' => 1,
                        'delivered' => 1,
                        'opened' => 1,
                        'clicked' => 1,
                        'bounced' => 1,
                        'unsubscribed' => 1,
                        'complained' => 1,
                    ],
                    'broadcast' => [
                        'sent' => 1,
                        'delivered' => 1,
                        'opened' => 1,
                        'clicked' => 1,
                        'bounced' => 1,
                        'unsubscribed' => 1,
                        'complained' => 1,
                    ],
                ],
            ],
        ]));

        $result = $client->statistics->retrieveDaily(
            from: '2024-01-01',
            to: '2024-01-01',
            timezone: 'America/New_York',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/statistics/daily', $request->getUri()->getPath());
        self::assertInstanceOf(StatisticsDailyResponse::class, $result);
    }

    public function testRetrieveTotals(): void
    {
        $client = $this->client(self::json([
            'transactional' => [
                'sent' => 1,
                'delivered' => 1,
                'opened' => 1,
                'clicked' => 1,
                'bounced' => 1,
                'unsubscribed' => 1,
                'complained' => 1,
            ],
            'broadcast' => [
                'sent' => 1,
                'delivered' => 1,
                'opened' => 1,
                'clicked' => 1,
                'bounced' => 1,
                'unsubscribed' => 1,
                'complained' => 1,
            ],
        ]));

        $result = $client->statistics->retrieveTotals(
            from: '2024-01-01T00:00:00Z',
            to: '2024-01-01T00:00:00Z',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/statistics/totals', $request->getUri()->getPath());
        self::assertInstanceOf(StatisticsTotalsResponse::class, $result);
    }
}
