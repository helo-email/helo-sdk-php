# Domains

Register, verify, and manage domains for email sending.

| Method | HTTP request | Description |
| ------ | ------------ | ----------- |
| [**domains->list()**](#list) | **GET** /domains | List all domains |
| [**domains->create()**](#create) | **POST** /domains | Create a domain |
| [**domains->retrieve()**](#retrieve) | **GET** /domains/{id} | Retrieve a domain |
| [**domains->update()**](#update) | **PATCH** /domains/{id} | Update a domain |
| [**domains->delete()**](#delete) | **DELETE** /domains/{id} | Delete a domain |
| [**domains->verify()**](#verify) | **POST** /domains/{id}/verify | Verify a domain |
| [**domains->rotateKey()**](#rotatekey) | **POST** /domains/{id}/rotate-key | Rotate a domain key |

## list

`GET /domains`

Retrieves all domains associated with the current account, including their verification status.

```php Domains_list
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$paginatedResponseOfDomain = $helo->domains->list(limit: 10, offset: 10);
```

## create

`POST /domains`

Registers a new domain for sending emails. The domain must be verified before it can be used.

```php Domains_create
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$domainWithDns = $helo->domains->create(
    name: 'test-name',
    channelIds: ['550e8400-e29b-41d4-a716-446655440000'],
    enableCustomTracking: true,
);
```

## retrieve

`GET /domains/{id}`

Gets detailed information about a specific domain, including verification status and configuration.

```php Domains_retrieve
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$domainWithDns = $helo->domains->retrieve(id: '550e8400-e29b-41d4-a716-446655440000');
```

## update

`PATCH /domains/{id}`

Modifies an existing domain by ID.

```php Domains_update
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$domain = $helo->domains->update(
    id: '550e8400-e29b-41d4-a716-446655440000',
    channelIds: ['550e8400-e29b-41d4-a716-446655440000'],
    enableCustomTracking: true,
);
```

## delete

`DELETE /domains/{id}`

Removes a domain from the account. This will stop all email sending from this domain.

```php Domains_delete
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$helo->domains->delete(id: '550e8400-e29b-41d4-a716-446655440000');
```

## verify

`POST /domains/{id}/verify`

Initiates the domain verification process by checking DNS records.

```php Domains_verify
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$dnsRecords = $helo->domains->verify(id: '550e8400-e29b-41d4-a716-446655440000');
```

## rotateKey

`POST /domains/{id}/rotate-key`

Generates new DKIM keys for the domain. This is recommended for security best practices.

```php Domains_rotateKey
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$dnsRecord = $helo->domains->rotateKey(id: '550e8400-e29b-41d4-a716-446655440000');
```
