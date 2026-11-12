# Broadcasts

Manage and track broadcast email campaigns.

| Method | HTTP request | Description |
| ------ | ------------ | ----------- |
| [**broadcasts->list()**](#list) | **GET** /broadcasts | List broadcasts |
| [**broadcasts->retrieve()**](#retrieve) | **GET** /broadcasts/{id} | Retrieve a broadcast |
| [**broadcasts->listFailures()**](#listfailures) | **GET** /broadcasts/{id}/failures | List failed broadcast messages |
| [**broadcasts->listSuppressions()**](#listsuppressions) | **GET** /broadcasts/{id}/suppressions | List broadcast suppressed recipients |

## list

`GET /broadcasts`

Retrieves a paginated list of sent broadcasts with summary statistics.

```php Broadcasts_list
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Models\BroadcastStatus;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$paginatedResponseOfBroadcast = $helo->broadcasts->list(
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    status: BroadcastStatus::Accepted,
    subject: 'test-subject',
);
```

## retrieve

`GET /broadcasts/{id}`

Fetches details and statistics for a specific broadcast.

```php Broadcasts_retrieve
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$broadcastDetails = $helo->broadcasts->retrieve(id: '550e8400-e29b-41d4-a716-446655440000');
```

## listFailures

`GET /broadcasts/{id}/failures`

Returns messages that could not be delivered due to permanent errors (e.g. invalid addresses, domain issues). Transient errors that were retried successfully do not appear here.

```php Broadcasts_listFailures
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$paginatedResponseOfBroadcastFailure = $helo->broadcasts->listFailures(
    id: '550e8400-e29b-41d4-a716-446655440000',
    limit: 10,
    offset: 10,
);
```

## listSuppressions

`GET /broadcasts/{id}/suppressions`

Returns recipients that were skipped because they appear on a suppression list (e.g. previous bounces or unsubscribes).

```php Broadcasts_listSuppressions
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$paginatedResponseOfBroadcastSuppression = $helo->broadcasts->listSuppressions(
    id: '550e8400-e29b-41d4-a716-446655440000',
    limit: 10,
    offset: 10,
);
```
