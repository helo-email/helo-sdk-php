# Changelog

## 1.1.0

- Fixed an empty `metadata`, `tracking`, `headers` or template `data` being sent as `[]`
  instead of `{}`, which the API rejects. A map with numeric keys is now sent as an object too.
- Fixed retries repeating requests that could be acted on twice: a `POST` without an
  idempotency key is no longer retried after a network error or a `5xx`. A `Retry-After` of more
  than 8 seconds is now returned as the error rather than retried early.
- Fixed a request the HTTP client can't send (an invalid request, say) being retried and reported
  as `ApiConnectionException`; it is now thrown as `HeloException` straight away. Only network
  failures are retried.
- `sendTransactional`, `sendTransactionalBatch`, `sendBroadcast` and `sendBroadcastMessage` now
  generate an idempotency key when you don't pass `idempotencyKey`, and send the same key on
  every retry, so a send whose response was lost is never sent twice.
- Added `nullFields` to `templates->update()` and `webhooks->update()`, to clear a field:
  `$helo->templates->update(id: $templateId, nullFields: ['text'])`.
- An enum property on a response model now holds the raw string for a value added to the API
  after this release, instead of throwing `ValueError`, so its type is `Enum|string`. Compare
  against cases with `===`; check `instanceof` before reading `->value`.

## 1.0.0

- Initial release of the PHP SDK.
