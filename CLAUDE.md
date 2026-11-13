# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this
repository.

## This repository is generated

Every file here except `.git` is produced by the `helo-sdk-generator-php` gem, which lives in the
`helo-sdk-generator` repo (checked out next to this one) under `vendor/gems/helo-sdk-generator-php`.
Regenerating sweeps the working tree first, so **any edit made here is lost on the next run**. Fix
the generator's Ruby or ERB templates instead, then regenerate:

```bash
cd ../helo-sdk-generator && ./exe/helo-sdk-generator public
```

## Commands

```bash
composer install
composer test           # PHPUnit (mocked HTTP, no server needed)
```

## Releasing

There is no publish step: Packagist publishes a version when it sees the tag. `release.yml` tags
`v<version>` for each new `CHANGELOG.md` version pushed to `main`, after the tests pass.
`composer.json` deliberately has no `version` field, since Packagist takes it from the tag.

## Architecture

- `src/Helo.php`: the client. It exposes one resource per API tag and resolves the API key
  from `HELO_API_KEY` when not passed. `Helo::VERSION` is the package version.
- `src/Resources/<Tag>Resource.php`: one method per operation. Request bodies are flattened into
  named arguments; object-shaped values are arrays keyed by wire name.
- `src/Internal/HttpClient.php`: the PSR-18 transport (Guzzle unless the caller passes a client),
  retries with jittered backoff (honouring `Retry-After`), the `User-Agent`, and the
  status-code-to-exception mapping. Null arguments are left out of the request.
- `src/Models/`: readonly response models, each with `fromArray()`, and string-backed enums.
- `src/Exceptions/`: `ApiException` and its per-status subclasses, the connection exceptions, and
  the `WebhookSignature*Exception` classes, all extending `HeloException`.
- `src/Webhooks/WebhookSignature.php`: HMAC-SHA256 verification of the
  `X-Helo-Webhook-Signature` header (`verify`, `isValid`, `generate`).
- `src/Webhooks/WebhookParser.php`: `parse` verifies a delivery, then decodes it into the payload
  model for its `eventType` (null for one this version does not know).

## Documentation & code samples

`docs/*.md` holds one file per tag. Each example is a fenced block whose info string is the
operation's OpenAPI `operationId`:

    ```php Activity_listEvents
    ...
    ```

`helo-sdk-generator` extracts those blocks and embeds them into the published OpenAPI description
as the PHP `x-codeSamples` entry for that operation, so the info string must stay an exact
`operationId`.
