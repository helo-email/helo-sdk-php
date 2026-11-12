# Statistics

Access activity statistics.

| Method | HTTP request | Description |
| ------ | ------------ | ----------- |
| [**statistics->retrieveHourly()**](#retrievehourly) | **GET** /statistics/hourly | Retrieve hourly statistics |
| [**statistics->retrieveDaily()**](#retrievedaily) | **GET** /statistics/daily | Retrieve daily statistics |
| [**statistics->retrieveTotals()**](#retrievetotals) | **GET** /statistics/totals | Retrieve all time statistics |

## retrieveHourly

`GET /statistics/hourly`

Fetches hourly aggregated statistics.

```php Statistics_retrieveHourly
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$statisticsHourly = $helo->statistics->retrieveHourly(
    from: '2024-01-01T00:00:00Z',
    to: '2024-01-01T00:00:00Z',
    channelId: '550e8400-e29b-41d4-a716-446655440000',
);
```

## retrieveDaily

`GET /statistics/daily`

Fetches daily aggregated statistics.

```php Statistics_retrieveDaily
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$statisticsDaily = $helo->statistics->retrieveDaily(
    from: '2024-01-01',
    to: '2024-01-01',
    timezone: 'America/New_York',
    channelId: '550e8400-e29b-41d4-a716-446655440000',
);
```

## retrieveTotals

`GET /statistics/totals`

Fetches cumulative statistics.

```php Statistics_retrieveTotals
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$statisticsTotals = $helo->statistics->retrieveTotals(
    from: '2024-01-01T00:00:00Z',
    to: '2024-01-01T00:00:00Z',
    channelId: '550e8400-e29b-41d4-a716-446655440000',
);
```
