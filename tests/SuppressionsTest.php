<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Models\CreateSuppressionsResponse;
use HeloEmail\Sdk\Models\MailType;
use HeloEmail\Sdk\Models\PaginatedResponseOfSuppressionResponse;
use HeloEmail\Sdk\Models\RemoveSuppressionsResponse;

final class SuppressionsTest extends TestCase
{
    public function testList(): void
    {
        $client = $this->client(self::json([
            'totalCount' => 1,
            'results' => [['email' => 'example', 'reason' => 'bounce', 'createdAt' => '2024-01-01T00:00:00Z']],
        ]));

        $result = $client->suppressions->list(
            channelId: '550e8400-e29b-41d4-a716-446655440000',
            mailType: MailType::Transactional,
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/suppressions', $request->getUri()->getPath());
        self::assertInstanceOf(PaginatedResponseOfSuppressionResponse::class, $result);
    }

    public function testCreate(): void
    {
        $client = $this->client(self::json(['results' => [['email' => 'example', 'success' => true, 'message' => 'example']]]));

        $result = $client->suppressions->create(
            channelId: '550e8400-e29b-41d4-a716-446655440000',
            mailType: MailType::Transactional,
            emails: ['test@example.com'],
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/suppressions', $request->getUri()->getPath());
        self::assertInstanceOf(CreateSuppressionsResponse::class, $result);
    }

    public function testRemove(): void
    {
        $client = $this->client(self::json(['results' => [['email' => 'example', 'success' => true, 'message' => 'example']]]));

        $result = $client->suppressions->remove(
            channelId: '550e8400-e29b-41d4-a716-446655440000',
            mailType: MailType::Transactional,
            emails: ['test@example.com'],
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/suppressions/remove', $request->getUri()->getPath());
        self::assertInstanceOf(RemoveSuppressionsResponse::class, $result);
    }
}
