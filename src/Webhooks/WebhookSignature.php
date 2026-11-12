<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Webhooks;

use HeloEmail\Sdk\Exceptions\WebhookSignatureException;
use HeloEmail\Sdk\Exceptions\WebhookSignatureMalformedHeaderException;
use HeloEmail\Sdk\Exceptions\WebhookSignatureMismatchException;
use HeloEmail\Sdk\Exceptions\WebhookSignatureTimestampSkewException;
use HeloEmail\Sdk\Exceptions\WebhookSignatureUnsupportedVersionException;

/**
 * Verifies the signature on an incoming webhook request, sent in the
 * `X-Helo-Webhook-Signature` header as `t={timestamp},v1={hex signature}`.
 */
final class WebhookSignature
{
    /**
     * Signing schemes this SDK can verify. The header may carry several versions at
     * once (`t=...,v1=...,v2=...`) so a new scheme can be rolled out while receivers
     * upgrade; verification uses the newest version present that appears here, and
     * ignores the rest.
     */
    public const SUPPORTED_VERSIONS = [1];

    /** How far a signature's timestamp may be from now, in seconds, before it is rejected. */
    public const MAX_TIMESTAMP_SKEW_SECONDS = 300;

    /**
     * Returns when the signature is valid, and otherwise throws a
     * WebhookSignatureException subclass saying why it was rejected.
     *
     * @param string|null $signatureHeader value of the X-Helo-Webhook-Signature header
     * @param string $requestBody raw (unparsed) request body
     * @param string $signingKey signing key for the webhook endpoint
     *
     * @throws WebhookSignatureException
     */
    public static function verify(?string $signatureHeader, string $requestBody, string $signingKey): void
    {
        [$timestamp, $signatures] = self::parseHeader($signatureHeader);

        $version = self::newestSupportedVersion($signatures);
        if ($version === null) {
            $present = array_keys($signatures);
            sort($present);
            throw new WebhookSignatureUnsupportedVersionException(
                'Unsupported webhook signature version: header carries only '
                . implode(', ', array_map(static fn (int $v) => "v{$v}", $present)),
            );
        }

        $skew = abs(time() - (int) $timestamp);
        if ($skew > self::MAX_TIMESTAMP_SKEW_SECONDS) {
            throw new WebhookSignatureTimestampSkewException(sprintf(
                'Webhook signature timestamp outside tolerance: off by %ds, tolerance is %ds',
                $skew,
                self::MAX_TIMESTAMP_SKEW_SECONDS,
            ));
        }

        $computed = self::signatureForVersion($version, $requestBody, $signingKey, $timestamp);
        foreach ($signatures[$version] as $signature) {
            if (hash_equals($computed, $signature)) {
                return;
            }
        }

        throw new WebhookSignatureMismatchException('Webhook signature mismatch');
    }

    /** Like verify(), returning false instead of throwing. */
    public static function isValid(?string $signatureHeader, string $requestBody, string $signingKey): bool
    {
        try {
            self::verify($signatureHeader, $requestBody, $signingKey);
        } catch (WebhookSignatureException) {
            return false;
        }

        return true;
    }

    /**
     * The hex-encoded HMAC-SHA256 signature for a payload (the v1 scheme), for
     * signing test fixtures.
     *
     * @param string $payload raw (unparsed) request body
     * @param string $signingKey signing key for the webhook endpoint
     * @param int|string $timestamp unix timestamp in seconds, as sent in the header
     */
    public static function generate(string $payload, string $signingKey, int|string $timestamp): string
    {
        return hash_hmac('sha256', "{$timestamp}.{$payload}", $signingKey);
    }

    /** The one place a new signing scheme needs to be added. */
    private static function signatureForVersion(int $version, string $payload, string $signingKey, string $timestamp): string
    {
        if ($version === 1) {
            return self::generate($payload, $signingKey, $timestamp);
        }

        throw new WebhookSignatureUnsupportedVersionException("Unsupported webhook signature version: v{$version}");
    }

    /**
     * The header's timestamp and its signatures keyed by version. Elements that are
     * not recognized are ignored, so a sender adding new ones does not break this.
     *
     * @return array{string, array<int, list<string>>}
     */
    private static function parseHeader(?string $signatureHeader): array
    {
        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $signatureHeader ?? '') as $element) {
            $parts = explode('=', trim($element), 2);
            if (count($parts) !== 2) {
                continue;
            }
            [$key, $value] = $parts;

            if ($key === 't') {
                if (preg_match('/\A\d+\z/', $value) !== 1) {
                    throw new WebhookSignatureMalformedHeaderException('Malformed webhook signature header');
                }
                $timestamp = $value;
                continue;
            }

            if (preg_match('/\Av(\d+)\z/', $key, $match) !== 1) {
                continue;
            }
            $version = (int) $match[1];

            // Only versions this SDK verifies have a format it can insist on;
            // anything else is recorded but left unchecked.
            $supported = in_array($version, self::SUPPORTED_VERSIONS, true);
            if ($supported && preg_match('/\A[a-f0-9]+\z/', $value) !== 1) {
                throw new WebhookSignatureMalformedHeaderException('Malformed webhook signature header');
            }

            $signatures[$version][] = $value;
        }

        if ($timestamp === null || $signatures === []) {
            throw new WebhookSignatureMalformedHeaderException('Malformed webhook signature header');
        }

        return [$timestamp, $signatures];
    }

    /**
     * The highest version present that this SDK can verify. Once a sender emits a
     * newer scheme, the older one stops being honored here.
     *
     * @param array<int, list<string>> $signatures
     */
    private static function newestSupportedVersion(array $signatures): ?int
    {
        $supported = array_intersect(array_keys($signatures), self::SUPPORTED_VERSIONS);

        return $supported === [] ? null : max($supported);
    }
}
