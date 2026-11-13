<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Models\DnsRecordResponse;
use HeloEmail\Sdk\Models\DnsRecordsResponse;
use HeloEmail\Sdk\Models\DomainResponse;
use HeloEmail\Sdk\Models\DomainWithDnsResponse;
use HeloEmail\Sdk\Models\PaginatedResponseOfDomainResponse;

final class DomainsTest extends TestCase
{
    public function testList(): void
    {
        $client = $this->client(self::json([
            'totalCount' => 1,
            'results' => [
                [
                    'id' => '550e8400-e29b-41d4-a716-446655440000',
                    'createdAt' => '2024-01-01T00:00:00Z',
                    'name' => 'example',
                    'verified' => true,
                    'channels' => [
                        [
                            'id' => '550e8400-e29b-41d4-a716-446655440000',
                            'name' => 'example',
                            'deliveryType' => 'live',
                            'deleted' => true,
                        ],
                    ],
                ],
            ],
        ]));

        $result = $client->domains->list();

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/domains', $request->getUri()->getPath());
        self::assertInstanceOf(PaginatedResponseOfDomainResponse::class, $result);
    }

    public function testCreate(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'createdAt' => '2024-01-01T00:00:00Z',
            'name' => 'example',
            'verified' => true,
            'channels' => [
                [
                    'id' => '550e8400-e29b-41d4-a716-446655440000',
                    'name' => 'example',
                    'deliveryType' => 'live',
                    'deleted' => true,
                ],
            ],
            'dnsRecords' => [
                'domainKeyActive' => [
                    'type' => 'txt',
                    'host' => 'example',
                    'value' => 'example',
                    'status' => 'pending',
                    'lastCheckedAt' => '2024-01-01T00:00:00Z',
                ],
                'domainKeyPending' => [
                    'type' => 'txt',
                    'host' => 'example',
                    'value' => 'example',
                    'status' => 'pending',
                    'lastCheckedAt' => '2024-01-01T00:00:00Z',
                ],
                'returnPath' => [
                    [
                        'type' => 'txt',
                        'host' => 'example',
                        'value' => 'example',
                        'status' => 'pending',
                        'lastCheckedAt' => '2024-01-01T00:00:00Z',
                    ],
                ],
                'tracking' => [
                    'type' => 'txt',
                    'host' => 'example',
                    'value' => 'example',
                    'status' => 'pending',
                    'lastCheckedAt' => '2024-01-01T00:00:00Z',
                ],
            ],
        ]));

        $result = $client->domains->create(
            name: 'test-name',
            channelIds: ['550e8400-e29b-41d4-a716-446655440000'],
            enableCustomTracking: true,
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/domains', $request->getUri()->getPath());
        self::assertEquals(
            json_decode(json_encode([
            'name' => 'test-name',
            'channelIds' => ['550e8400-e29b-41d4-a716-446655440000'],
            'enableCustomTracking' => true,
        ]), true),
            json_decode((string) $request->getBody(), true),
        );
        self::assertInstanceOf(DomainWithDnsResponse::class, $result);
    }

    public function testRetrieve(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'createdAt' => '2024-01-01T00:00:00Z',
            'name' => 'example',
            'verified' => true,
            'channels' => [
                [
                    'id' => '550e8400-e29b-41d4-a716-446655440000',
                    'name' => 'example',
                    'deliveryType' => 'live',
                    'deleted' => true,
                ],
            ],
            'dnsRecords' => [
                'domainKeyActive' => [
                    'type' => 'txt',
                    'host' => 'example',
                    'value' => 'example',
                    'status' => 'pending',
                    'lastCheckedAt' => '2024-01-01T00:00:00Z',
                ],
                'domainKeyPending' => [
                    'type' => 'txt',
                    'host' => 'example',
                    'value' => 'example',
                    'status' => 'pending',
                    'lastCheckedAt' => '2024-01-01T00:00:00Z',
                ],
                'returnPath' => [
                    [
                        'type' => 'txt',
                        'host' => 'example',
                        'value' => 'example',
                        'status' => 'pending',
                        'lastCheckedAt' => '2024-01-01T00:00:00Z',
                    ],
                ],
                'tracking' => [
                    'type' => 'txt',
                    'host' => 'example',
                    'value' => 'example',
                    'status' => 'pending',
                    'lastCheckedAt' => '2024-01-01T00:00:00Z',
                ],
            ],
        ]));

        $result = $client->domains->retrieve(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/domains/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
        self::assertInstanceOf(DomainWithDnsResponse::class, $result);
    }

    public function testUpdate(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'createdAt' => '2024-01-01T00:00:00Z',
            'name' => 'example',
            'verified' => true,
            'channels' => [
                [
                    'id' => '550e8400-e29b-41d4-a716-446655440000',
                    'name' => 'example',
                    'deliveryType' => 'live',
                    'deleted' => true,
                ],
            ],
        ]));

        $result = $client->domains->update(
            id: '550e8400-e29b-41d4-a716-446655440000',
            channelIds: ['550e8400-e29b-41d4-a716-446655440000'],
            enableCustomTracking: true,
        );

        $request = $this->lastRequest();
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame('/domains/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
        self::assertEquals(
            json_decode(json_encode([
            'channelIds' => ['550e8400-e29b-41d4-a716-446655440000'],
            'enableCustomTracking' => true,
        ]), true),
            json_decode((string) $request->getBody(), true),
        );
        self::assertInstanceOf(DomainResponse::class, $result);
    }

    public function testDelete(): void
    {
        $client = $this->client(new Response(204));

        $client->domains->delete(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame('/domains/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
    }

    public function testVerify(): void
    {
        $client = $this->client(self::json([
            'domainKeyActive' => [
                'type' => 'txt',
                'host' => 'example',
                'value' => 'example',
                'status' => 'pending',
                'lastCheckedAt' => '2024-01-01T00:00:00Z',
            ],
            'domainKeyPending' => [
                'type' => 'txt',
                'host' => 'example',
                'value' => 'example',
                'status' => 'pending',
                'lastCheckedAt' => '2024-01-01T00:00:00Z',
            ],
            'returnPath' => [
                [
                    'type' => 'txt',
                    'host' => 'example',
                    'value' => 'example',
                    'status' => 'pending',
                    'lastCheckedAt' => '2024-01-01T00:00:00Z',
                ],
            ],
            'tracking' => [
                'type' => 'txt',
                'host' => 'example',
                'value' => 'example',
                'status' => 'pending',
                'lastCheckedAt' => '2024-01-01T00:00:00Z',
            ],
        ]));

        $result = $client->domains->verify(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/domains/550e8400-e29b-41d4-a716-446655440000/verify', $request->getUri()->getPath());
        self::assertInstanceOf(DnsRecordsResponse::class, $result);
    }

    public function testRotateKey(): void
    {
        $client = $this->client(self::json([
            'type' => 'txt',
            'host' => 'example',
            'value' => 'example',
            'status' => 'pending',
            'lastCheckedAt' => '2024-01-01T00:00:00Z',
        ]));

        $result = $client->domains->rotateKey(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/domains/550e8400-e29b-41d4-a716-446655440000/rotate-key', $request->getUri()->getPath());
        self::assertInstanceOf(DnsRecordResponse::class, $result);
    }
}
