<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use HeloEmail\Sdk\Exceptions\HeloException;
use HeloEmail\Sdk\Exceptions\WebhookSignatureException;
use HeloEmail\Sdk\Exceptions\WebhookSignatureMalformedHeaderException;
use HeloEmail\Sdk\Exceptions\WebhookSignatureMismatchException;
use HeloEmail\Sdk\Exceptions\WebhookSignatureTimestampSkewException;
use HeloEmail\Sdk\Exceptions\WebhookSignatureUnsupportedVersionException;
use HeloEmail\Sdk\Webhooks\WebhookSignature;
use PHPUnit\Framework\Attributes\DataProvider;

final class WebhookSignatureTest extends \PHPUnit\Framework\TestCase
{
    private const SIGNING_KEY = 'whsec_test';
    private const BODY = '{"event":"message.delivered"}';

    private static function validHeader(?int $timestamp = null): string
    {
        $timestamp ??= time();

        return "t={$timestamp},v1=" . WebhookSignature::generate(self::BODY, self::SIGNING_KEY, $timestamp);
    }

    public function testAcceptsAValidSignature(): void
    {
        $header = self::validHeader();

        WebhookSignature::verify($header, self::BODY, self::SIGNING_KEY);

        self::assertTrue(WebhookSignature::isValid($header, self::BODY, self::SIGNING_KEY));
    }

    /**
     * A sender rolling out a new signing scheme emits every version at once. This SDK
     * must keep verifying the versions it knows and ignore the rest, otherwise the
     * rollout breaks every receiver that has not upgraded yet.
     *
     * @return array<string, array{string}>
     */
    public static function headersWithExtras(): array
    {
        return [
            'newer version after' => ['t={t},v1={sig},v2=8badf00d'],
            'newer version before' => ['t={t},v2=8badf00d,v1={sig}'],
            'unknown element' => ['t={t},v1={sig},alg=sha512'],
            'whitespace' => ['t={t}, v1={sig}'],
            'timestamp last' => ['v1={sig},t={t}'],
            'several v1' => ['t={t},v1=8badf00d,v1={sig}'],
        ];
    }

    #[DataProvider('headersWithExtras')]
    public function testIgnoresUnknownVersionsAndElements(string $format): void
    {
        $timestamp = time();
        $signature = WebhookSignature::generate(self::BODY, self::SIGNING_KEY, $timestamp);
        $header = strtr($format, ['{t}' => (string) $timestamp, '{sig}' => $signature]);

        self::assertTrue(WebhookSignature::isValid($header, self::BODY, self::SIGNING_KEY), $header);
    }

    public function testRejectsASignatureFromADifferentKey(): void
    {
        $header = self::validHeader();

        self::assertFalse(WebhookSignature::isValid($header, self::BODY, 'wrong-key'));
        $this->expectException(WebhookSignatureMismatchException::class);
        WebhookSignature::verify($header, self::BODY, 'wrong-key');
    }

    public function testRejectsATamperedBody(): void
    {
        $this->expectException(WebhookSignatureMismatchException::class);
        WebhookSignature::verify(self::validHeader(), '{"event":"message.bounced"}', self::SIGNING_KEY);
    }

    public function testRejectsAStaleTimestamp(): void
    {
        $this->expectException(WebhookSignatureTimestampSkewException::class);
        $this->expectExceptionMessage('tolerance');
        WebhookSignature::verify(self::validHeader(time() - 600), self::BODY, self::SIGNING_KEY);
    }

    public function testRejectsAHeaderCarryingOnlyUnknownVersions(): void
    {
        $timestamp = time();
        $signature = WebhookSignature::generate(self::BODY, self::SIGNING_KEY, $timestamp);

        $this->expectException(WebhookSignatureUnsupportedVersionException::class);
        $this->expectExceptionMessage('v2');
        WebhookSignature::verify("t={$timestamp},v2={$signature}", self::BODY, self::SIGNING_KEY);
    }

    /** @return array<string, array{string|null}> */
    public static function malformedHeaders(): array
    {
        return [
            'garbage' => ['garbage'],
            'empty' => [''],
            'missing' => [null],
            'uppercase hex' => ['t={t},v1=ABCDEF'],
            'no signature' => ['t={t}'],
            'no timestamp' => ['v1={sig}'],
            'non-numeric timestamp' => ['t=yesterday,v1={sig}'],
        ];
    }

    #[DataProvider('malformedHeaders')]
    public function testRejectsMalformedHeaders(?string $format): void
    {
        $timestamp = time();
        $signature = WebhookSignature::generate(self::BODY, self::SIGNING_KEY, $timestamp);
        $header = $format === null ? null : strtr($format, ['{t}' => (string) $timestamp, '{sig}' => $signature]);

        $this->expectException(WebhookSignatureMalformedHeaderException::class);
        WebhookSignature::verify($header, self::BODY, self::SIGNING_KEY);
    }

    public function testTreatsAShortHexSignatureAsAMismatchNotMalformed(): void
    {
        $this->expectException(WebhookSignatureMismatchException::class);
        WebhookSignature::verify('t=' . time() . ',v1=abc', self::BODY, self::SIGNING_KEY);
    }

    public function testEveryRejectionSharesOneBaseClass(): void
    {
        self::assertFalse(WebhookSignature::isValid('garbage', self::BODY, self::SIGNING_KEY));
        self::assertTrue(is_subclass_of(WebhookSignatureException::class, HeloException::class));

        $this->expectException(WebhookSignatureException::class);
        WebhookSignature::verify('garbage', self::BODY, self::SIGNING_KEY);
    }

    public function testGenerateMatchesTheDocumentedScheme(): void
    {
        $expected = hash_hmac('sha256', '1700000000.' . self::BODY, self::SIGNING_KEY);

        self::assertSame($expected, WebhookSignature::generate(self::BODY, self::SIGNING_KEY, '1700000000'));
        self::assertSame($expected, WebhookSignature::generate(self::BODY, self::SIGNING_KEY, 1700000000));
    }
}
