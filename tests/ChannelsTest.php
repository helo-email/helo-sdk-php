<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Models\ChannelDetailsResponse;
use HeloEmail\Sdk\Models\DeliveryType;
use HeloEmail\Sdk\Models\PaginationResultOfChannelBasicResponse;

final class ChannelsTest extends TestCase
{
    public function testList(): void
    {
        $client = $this->client(self::json([
            'results' => [
                [
                    'id' => '550e8400-e29b-41d4-a716-446655440000',
                    'name' => 'example',
                    'deliveryType' => 'live',
                    'createdAt' => '2024-01-01T00:00:00Z',
                ],
            ],
            'totalCount' => 1,
        ]));

        $result = $client->channels->list();

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/channels', $request->getUri()->getPath());
        self::assertInstanceOf(PaginationResultOfChannelBasicResponse::class, $result);
    }

    public function testCreate(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'example',
            'deliveryType' => 'live',
            'createdAt' => '2024-01-01T00:00:00Z',
            'updatedAt' => '2024-01-01T00:00:00Z',
            'tracking' => ['links' => true, 'opens' => true],
        ]));

        $result = $client->channels->create(
            name: 'test-name',
            deliveryType: DeliveryType::Live,
            tracking: ['links' => true, 'opens' => true],
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/channels', $request->getUri()->getPath());
        self::assertInstanceOf(ChannelDetailsResponse::class, $result);
    }

    public function testRetrieve(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'example',
            'deliveryType' => 'live',
            'createdAt' => '2024-01-01T00:00:00Z',
            'updatedAt' => '2024-01-01T00:00:00Z',
            'tracking' => ['links' => true, 'opens' => true],
        ]));

        $result = $client->channels->retrieve(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/channels/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
        self::assertInstanceOf(ChannelDetailsResponse::class, $result);
    }

    public function testUpdate(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'example',
            'deliveryType' => 'live',
            'createdAt' => '2024-01-01T00:00:00Z',
            'updatedAt' => '2024-01-01T00:00:00Z',
            'tracking' => ['links' => true, 'opens' => true],
        ]));

        $result = $client->channels->update(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'test-name',
            deliveryType: DeliveryType::Live,
            tracking: ['links' => true, 'opens' => true],
        );

        $request = $this->lastRequest();
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame('/channels/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
        self::assertInstanceOf(ChannelDetailsResponse::class, $result);
    }

    public function testDelete(): void
    {
        $client = $this->client(new Response(204));

        $client->channels->delete(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame('/channels/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
    }
}
