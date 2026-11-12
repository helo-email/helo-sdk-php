# Helo PHP SDK

Helo Email API (https://helohq.com)

## Installation

```bash
composer require helo-email/sdk
```

Requires PHP 8.3 or later, and installs Guzzle.

## Quick start

```php
use HeloEmail\Sdk\Helo;

// The API key is read from HELO_API_KEY when not passed explicitly.
$helo = new Helo();

$paginatedEvents = $helo->activity->listEvents(
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    messageId: '550e8400-e29b-41d4-a716-446655440000',
);
```

Pass the key directly when you manage several accounts or read it from a secrets manager:

```php
$helo = new Helo(apiKey: 'your-api-key');
```

Override the base URL, timeout or retry count (defaults: `https://api.helohq.com`, 30 seconds,
2 retries):

```php
$helo = new Helo(
    baseUrl: 'https://api.helohq.com',
    timeout: 60.0,
    maxRetries: 2,
);
```

Request bodies are flattened into named arguments. Object-shaped values are arrays keyed by the
API's field names, and responses are readonly model objects (`HeloEmail\Sdk\Models`).

### Bringing your own HTTP client

Any [PSR-18](https://www.php-fig.org/psr/psr-18/) client can replace the default Guzzle one, to
add a proxy, logging middleware or a mock in tests. Configure its timeout yourself; `timeout:`
only applies to the default client.

```php
$helo = new Helo(httpClient: new \GuzzleHttp\Client(['timeout' => 10, 'proxy' => 'http://proxy:8080']));
```

Every request carries a `User-Agent` naming this package and the PHP running it, e.g.
`helo-email-sdk/1.0.0 (php 8.3.0; linux/x86_64)`.

## Automatic retries

Connection errors, timeouts, `408`, `429` and `5xx` responses are retried with exponential backoff
and full jitter. A `Retry-After` header is honoured when present. Pass `maxRetries: 0` to disable.

## Resources

| Property | Documentation |
| -------- | ------------- |
| `$helo->activity` | [docs/Activity.md](docs/Activity.md) |
| `$helo->broadcasts` | [docs/Broadcasts.md](docs/Broadcasts.md) |
| `$helo->channels` | [docs/Channels.md](docs/Channels.md) |
| `$helo->domains` | [docs/Domains.md](docs/Domains.md) |
| `$helo->sending` | [docs/Sending.md](docs/Sending.md) |
| `$helo->statistics` | [docs/Statistics.md](docs/Statistics.md) |
| `$helo->suppressions` | [docs/Suppressions.md](docs/Suppressions.md) |
| `$helo->templates` | [docs/Templates.md](docs/Templates.md) |
| `$helo->webhooks` | [docs/Webhooks.md](docs/Webhooks.md) |

## Error handling

Every API error extends `ApiException` and carries `statusCode`, `errorCode`, `detail` and
`requestId`. `RateLimitException` also has `retryAfter`.

```php
use HeloEmail\Sdk\Exceptions\ApiException;
use HeloEmail\Sdk\Exceptions\AuthenticationException;

try {
    $paginatedEvents = $helo->activity->listEvents(
        channelId: '550e8400-e29b-41d4-a716-446655440000',
        messageId: '550e8400-e29b-41d4-a716-446655440000',
    );
} catch (AuthenticationException $e) {
    echo "auth failed: {$e->getMessage()} (status {$e->statusCode})\n";
} catch (ApiException $e) {
    echo "API error {$e->statusCode}: {$e->getMessage()} (request {$e->requestId})\n";
}
```

| Exception | HTTP status |
| --------- | ----------- |
| `BadRequestException` | 400 |
| `AuthenticationException` | 401 |
| `PermissionDeniedException` | 403 |
| `NotFoundException` | 404 |
| `ConflictException` | 409 |
| `UnprocessableEntityException` | 422 |
| `RateLimitException` | 429 |
| `InternalServerException` | 5xx |

Network failures throw `ApiConnectionException` (or its subclass `ApiTimeoutException`) once
retries are exhausted. All of these extend `HeloException`, the base of every exception
this library throws, under `HeloEmail\Sdk\Exceptions`.

## Webhook signature verification

Webhook deliveries are signed with the endpoint's signing key. Verify every delivery before acting
on it, against the **raw** request body: decoding and re-encoding the JSON changes the bytes, and
the signature will not match.

```php
use HeloEmail\Sdk\Exceptions\WebhookSignatureException;
use HeloEmail\Sdk\Webhooks\WebhookSignature;

$body = file_get_contents('php://input'); // raw body, exactly as received

try {
    WebhookSignature::verify(
        $_SERVER['HTTP_X_HELO_WEBHOOK_SIGNATURE'] ?? null,
        $body,
        getenv('HELO_WEBHOOK_SIGNING_KEY'),
    );
} catch (WebhookSignatureException) {
    http_response_code(400);
    exit;
}

$event = json_decode($body, true);
// ... handle the event, then acknowledge quickly
http_response_code(204);
```

In Laravel or Symfony, pass `$request->header('X-Helo-Webhook-Signature')` (or
`$request->headers->get(...)`) and `$request->getContent()`.

`WebhookSignature::verify()` returns when the signature is valid and throws otherwise. Each
rejection has its own class, so a stale delivery can be treated differently from a genuinely bad
one:

| Exception | Meaning |
| --- | --- |
| `WebhookSignatureMalformedHeaderException` | The header was not in the expected format |
| `WebhookSignatureUnsupportedVersionException` | The delivery used a signing scheme this SDK version cannot verify: upgrade the package |
| `WebhookSignatureTimestampSkewException` | Correctly signed, but too old to accept: possibly a replay, or clock drift |
| `WebhookSignatureMismatchException` | Wrong signing key, or the body was modified in transit |

All four extend `WebhookSignatureException`, so catch that one class to handle any rejection. If
you only want a boolean, use `WebhookSignature::isValid()` with the same arguments.

The signature header may carry several versions at once (`t=...,v1=...,v2=...`) while a new
signing scheme is being rolled out. This SDK verifies against the newest version it supports
(`WebhookSignature::SUPPORTED_VERSIONS`) and ignores elements it does not recognize, so a rollout
will not break this integration.

To compute a signature yourself, to sign a fixture in tests for example, use
`WebhookSignature::generate($payload, $signingKey, $timestamp)`.

## Development

```bash
composer install
composer test
```
