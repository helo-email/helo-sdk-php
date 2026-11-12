# Webhooks

Create and manage webhooks for event notifications.

| Method | HTTP request | Description |
| ------ | ------------ | ----------- |
| [**webhooks->list()**](#list) | **GET** /webhooks | List all webhooks |
| [**webhooks->create()**](#create) | **POST** /webhooks | Create a webhook |
| [**webhooks->retrieve()**](#retrieve) | **GET** /webhooks/{id} | Retrieve a webhook |
| [**webhooks->update()**](#update) | **PATCH** /webhooks/{id} | Update a webhook |
| [**webhooks->delete()**](#delete) | **DELETE** /webhooks/{id} | Delete a webhook |
| [**webhooks->regenerateSigningKey()**](#regeneratesigningkey) | **POST** /webhooks/{id}/regenerate-signing-key | Regenerate webhook signing key |

## list

`GET /webhooks`

Retrieves all webhooks configured for the account.

```php Webhooks_list
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$paginationResultOfWebhook = $helo->webhooks->list(limit: 10, offset: 10);
```

## create

`POST /webhooks`

Registers a new webhook to receive event notifications.

```php Webhooks_create
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Models\WebhookEvent;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$webhook = $helo->webhooks->create(
    url: 'test-url',
    events: [WebhookEvent::MessageAccepted],
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    additionalHeaders: [['name' => 'test-name', 'value' => 'test-value']],
    enabled: true,
);
```

## retrieve

`GET /webhooks/{id}`

Fetches the details and configuration of a specific webhook.

```php Webhooks_retrieve
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$webhook = $helo->webhooks->retrieve(id: '550e8400-e29b-41d4-a716-446655440000');
```

## update

`PATCH /webhooks/{id}`

Modifies an existing webhook by ID.

```php Webhooks_update
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Models\WebhookEvent;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$webhook = $helo->webhooks->update(
    id: '550e8400-e29b-41d4-a716-446655440000',
    url: 'test-url',
    events: [WebhookEvent::MessageAccepted],
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    additionalHeaders: [['name' => 'test-name', 'value' => 'test-value']],
    enabled: true,
);
```

## delete

`DELETE /webhooks/{id}`

Permanently removes a webhook.

```php Webhooks_delete
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$helo->webhooks->delete(id: '550e8400-e29b-41d4-a716-446655440000');
```

## regenerateSigningKey

`POST /webhooks/{id}/regenerate-signing-key`

Regenerate the signing key used for the webhook signature. This operation replaces the old key.

```php Webhooks_regenerateSigningKey
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$webhook = $helo->webhooks->regenerateSigningKey(id: '550e8400-e29b-41d4-a716-446655440000');
```
