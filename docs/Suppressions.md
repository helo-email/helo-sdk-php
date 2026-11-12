# Suppressions

| Method | HTTP request | Description |
| ------ | ------------ | ----------- |
| [**suppressions->list()**](#list) | **GET** /suppressions | List suppressions |
| [**suppressions->create()**](#create) | **POST** /suppressions | Create suppressions |
| [**suppressions->remove()**](#remove) | **POST** /suppressions/remove | Remove suppressions |

## list

`GET /suppressions`

Retrieves a list of suppressed email addresses for a channel.

```php Suppressions_list
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Models\MailType;
use HeloEmail\Sdk\Models\SuppressionReason;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$paginatedResponseOfSuppression = $helo->suppressions->list(
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    mailType: MailType::Transactional,
    reason: SuppressionReason::Bounce,
    email: 'test@example.com',
);
```

## create

`POST /suppressions`

Adds email addresses to the suppression list to prevent future sends.

```php Suppressions_create
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Models\MailType;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$createSuppressions = $helo->suppressions->create(
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    mailType: MailType::Transactional,
    emails: ['test@example.com'],
);
```

## remove

`POST /suppressions/remove`

Removes email addresses from the suppression list to allow future sends.

```php Suppressions_remove
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Models\MailType;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$removeSuppressions = $helo->suppressions->remove(
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    mailType: MailType::Transactional,
    emails: ['test@example.com'],
);
```
