# Channels

Create and manage communication channels for organizing messages.

| Method | HTTP request | Description |
| ------ | ------------ | ----------- |
| [**channels->list()**](#list) | **GET** /channels | List all channels |
| [**channels->create()**](#create) | **POST** /channels | Create a channel |
| [**channels->retrieve()**](#retrieve) | **GET** /channels/{id} | Retrieve a channel |
| [**channels->update()**](#update) | **PATCH** /channels/{id} | Update a channel |
| [**channels->delete()**](#delete) | **DELETE** /channels/{id} | Delete a channel |

## list

`GET /channels`

Retrieves a list of all channels accessible to the current user.

```php Channels_list
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$paginationResultOfChannelBasic = $helo->channels->list(limit: 10, offset: 10);
```

## create

`POST /channels`

Creates a new communication channel for organizing and routing messages.

```php Channels_create
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Models\DeliveryType;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$channelDetails = $helo->channels->create(
    name: 'test-name',
    deliveryType: DeliveryType::Live,
    tracking: ['links' => true, 'opens' => true],
);
```

## retrieve

`GET /channels/{id}`

Fetches the details and configuration of a specific channel.

```php Channels_retrieve
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$channelDetails = $helo->channels->retrieve(id: '550e8400-e29b-41d4-a716-446655440000');
```

## update

`PATCH /channels/{id}`

Modifies an existing channel by ID.

```php Channels_update
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Models\DeliveryType;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$channelDetails = $helo->channels->update(
    id: '550e8400-e29b-41d4-a716-446655440000',
    name: 'test-name',
    deliveryType: DeliveryType::Live,
    tracking: ['links' => true, 'opens' => true],
);
```

## delete

`DELETE /channels/{id}`

Permanently removes a channel and all associated data.

```php Channels_delete
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$helo->channels->delete(id: '550e8400-e29b-41d4-a716-446655440000');
```
