<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use HeloEmail\Sdk\Exceptions\WebhookSignatureMismatchException;
use HeloEmail\Sdk\Models\DomainKeyVerificationFailedPayload;
use HeloEmail\Sdk\Models\DomainKeyVerifiedPayload;
use HeloEmail\Sdk\Models\EmailBouncedWebhookPayload;
use HeloEmail\Sdk\Models\EmailDeliveredWebhookPayload;
use HeloEmail\Sdk\Models\EmailOpenedWebhookPayload;
use HeloEmail\Sdk\Models\LinkClickedWebhookPayload;
use HeloEmail\Sdk\Models\MessageAcceptedWebhookPayload;
use HeloEmail\Sdk\Models\MessageProcessedWebhookPayload;
use HeloEmail\Sdk\Models\RecipientComplainedWebhookPayload;
use HeloEmail\Sdk\Models\RecipientResubscribedWebhookPayload;
use HeloEmail\Sdk\Models\RecipientUnsubscribedWebhookPayload;
use HeloEmail\Sdk\Models\ReturnPathDomainVerificationFailedPayload;
use HeloEmail\Sdk\Models\ReturnPathDomainVerifiedPayload;
use HeloEmail\Sdk\Webhooks\WebhookParser;
use HeloEmail\Sdk\Webhooks\WebhookSignature;
use PHPUnit\Framework\Attributes\DataProvider;

final class WebhookParserTest extends \PHPUnit\Framework\TestCase
{
    private const SIGNING_KEY = 'whsec_test';

    private static function header(string $body): string
    {
        $timestamp = time();

        return "t={$timestamp},v1=" . WebhookSignature::generate($body, self::SIGNING_KEY, $timestamp);
    }

    /** @return array<string, array{class-string, array<string, mixed>}> */
    public static function payloads(): array
    {
        return [
            'message-accepted' => [
                MessageAcceptedWebhookPayload::class,
                [
                    'eventType' => 'message-accepted',
                    'recipients' => ['example'],
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'mailType' => 'transactional',
                    'subject' => 'example',
                    'tags' => ['example'],
                    'metadata' => [],
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'message-processed' => [
                MessageProcessedWebhookPayload::class,
                [
                    'eventType' => 'message-processed',
                    'recipients' => ['example'],
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'mailType' => 'transactional',
                    'subject' => 'example',
                    'tags' => ['example'],
                    'metadata' => [],
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'email-delivered' => [
                EmailDeliveredWebhookPayload::class,
                [
                    'eventType' => 'email-delivered',
                    'details' => ['response' => 'example'],
                    'recipient' => 'example',
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'mailType' => 'transactional',
                    'subject' => 'example',
                    'tags' => ['example'],
                    'metadata' => [],
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'email-bounced' => [
                EmailBouncedWebhookPayload::class,
                [
                    'eventType' => 'email-bounced',
                    'details' => ['type' => 'example', 'subType' => 'example', 'code' => 'example'],
                    'recipient' => 'example',
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'mailType' => 'transactional',
                    'subject' => 'example',
                    'tags' => ['example'],
                    'metadata' => [],
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'email-opened' => [
                EmailOpenedWebhookPayload::class,
                [
                    'eventType' => 'email-opened',
                    'details' => [
                        'ip' => 'example',
                        'country' => 'example',
                        'countryCode' => 'example',
                        'client' => ['family' => 'example', 'version' => 'example'],
                        'device' => ['brand' => 'example', 'family' => 'example', 'model' => 'example'],
                    ],
                    'recipient' => 'example',
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'mailType' => 'transactional',
                    'subject' => 'example',
                    'tags' => ['example'],
                    'metadata' => [],
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'link-clicked' => [
                LinkClickedWebhookPayload::class,
                [
                    'eventType' => 'link-clicked',
                    'details' => [
                        'link' => 'example',
                        'ip' => 'example',
                        'country' => 'example',
                        'countryCode' => 'example',
                        'client' => ['family' => 'example', 'version' => 'example'],
                        'device' => ['brand' => 'example', 'family' => 'example', 'model' => 'example'],
                    ],
                    'recipient' => 'example',
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'mailType' => 'transactional',
                    'subject' => 'example',
                    'tags' => ['example'],
                    'metadata' => [],
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'recipient-complained' => [
                RecipientComplainedWebhookPayload::class,
                [
                    'eventType' => 'recipient-complained',
                    'details' => ['type' => 'example'],
                    'recipient' => 'example',
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'mailType' => 'transactional',
                    'subject' => 'example',
                    'tags' => ['example'],
                    'metadata' => [],
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'recipient-unsubscribed' => [
                RecipientUnsubscribedWebhookPayload::class,
                [
                    'eventType' => 'recipient-unsubscribed',
                    'details' => ['ip' => 'example'],
                    'recipient' => 'example',
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'mailType' => 'transactional',
                    'subject' => 'example',
                    'tags' => ['example'],
                    'metadata' => [],
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'recipient-resubscribed' => [
                RecipientResubscribedWebhookPayload::class,
                [
                    'eventType' => 'recipient-resubscribed',
                    'details' => ['ip' => 'example'],
                    'recipient' => 'example',
                    'messageId' => '550e8400-e29b-41d4-a716-446655440000',
                    'channelId' => '550e8400-e29b-41d4-a716-446655440000',
                    'mailType' => 'transactional',
                    'subject' => 'example',
                    'tags' => ['example'],
                    'metadata' => [],
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'domain-key-verified' => [
                DomainKeyVerifiedPayload::class,
                [
                    'eventType' => 'domain-key-verified',
                    'domainId' => '550e8400-e29b-41d4-a716-446655440000',
                    'domainName' => 'example',
                    'dnsRecordHost' => 'example',
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'domain-key-verification-failed' => [
                DomainKeyVerificationFailedPayload::class,
                [
                    'eventType' => 'domain-key-verification-failed',
                    'domainId' => '550e8400-e29b-41d4-a716-446655440000',
                    'domainName' => 'example',
                    'dnsRecordHost' => 'example',
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'return-path-domain-verified' => [
                ReturnPathDomainVerifiedPayload::class,
                [
                    'eventType' => 'return-path-domain-verified',
                    'domainId' => '550e8400-e29b-41d4-a716-446655440000',
                    'domainName' => 'example',
                    'dnsRecordHost' => 'example',
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
            'return-path-domain-verification-failed' => [
                ReturnPathDomainVerificationFailedPayload::class,
                [
                    'eventType' => 'return-path-domain-verification-failed',
                    'domainId' => '550e8400-e29b-41d4-a716-446655440000',
                    'domainName' => 'example',
                    'dnsRecordHost' => 'example',
                    'timestamp' => '2024-01-01T00:00:00Z',
                ],
            ],
        ];
    }

    /**
     * @param class-string $class
     * @param array<string, mixed> $data
     */
    #[DataProvider('payloads')]
    public function testParsesEachEventIntoItsPayload(string $class, array $data): void
    {
        $body = json_encode($data, JSON_THROW_ON_ERROR);

        self::assertInstanceOf($class, WebhookParser::parse(self::header($body), $body, self::SIGNING_KEY));
    }

    public function testReturnsNullForAnEventThisVersionDoesNotKnow(): void
    {
        $body = '{"eventType":"something-new"}';

        self::assertNull(WebhookParser::parse(self::header($body), $body, self::SIGNING_KEY));
    }

    public function testRejectsABodyThatDoesNotMatchItsSignature(): void
    {
        $this->expectException(WebhookSignatureMismatchException::class);

        WebhookParser::parse(self::header('{"eventType":"other"}'), '{"eventType":"tampered"}', self::SIGNING_KEY);
    }
}
