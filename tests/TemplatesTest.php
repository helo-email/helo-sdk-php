<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Tests;

use GuzzleHttp\Psr7\Response;
use HeloEmail\Sdk\Models\PaginatedResponseOfTemplateSummaryResponse;
use HeloEmail\Sdk\Models\RenderTemplateResponse;
use HeloEmail\Sdk\Models\TemplateResponse;

final class TemplatesTest extends TestCase
{
    public function testList(): void
    {
        $client = $this->client(self::json([
            'totalCount' => 1,
            'results' => [
                [
                    'id' => '550e8400-e29b-41d4-a716-446655440000',
                    'createdAt' => '2024-01-01T00:00:00Z',
                    'updatedAt' => '2024-01-01T00:00:00Z',
                    'name' => 'example',
                    'subject' => 'example',
                    'channelIds' => ['550e8400-e29b-41d4-a716-446655440000'],
                ],
            ],
        ]));

        $result = $client->templates->list();

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/templates', $request->getUri()->getPath());
        self::assertInstanceOf(PaginatedResponseOfTemplateSummaryResponse::class, $result);
    }

    public function testCreate(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'createdAt' => '2024-01-01T00:00:00Z',
            'updatedAt' => '2024-01-01T00:00:00Z',
            'version' => 1,
            'name' => 'example',
            'subject' => 'example',
            'html' => 'example',
            'mjml' => 'example',
            'text' => 'example',
            'channelIds' => ['550e8400-e29b-41d4-a716-446655440000'],
        ]));

        $result = $client->templates->create(
            name: 'test-name',
            subject: 'test-subject',
            html: 'test-html',
            mjml: 'test-mjml',
            text: 'test-text',
            channelIds: ['550e8400-e29b-41d4-a716-446655440000'],
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/templates', $request->getUri()->getPath());
        self::assertInstanceOf(TemplateResponse::class, $result);
    }

    public function testRenderDraft(): void
    {
        $client = $this->client(self::json(['subject' => 'example', 'html' => 'example', 'text' => 'example']));

        $result = $client->templates->renderDraft(
            subject: 'test-subject',
            html: 'test-html',
            mjml: 'test-mjml',
            text: 'test-text',
            inlineStyles: true,
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/templates/render', $request->getUri()->getPath());
        self::assertInstanceOf(RenderTemplateResponse::class, $result);
    }

    public function testRetrieve(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'createdAt' => '2024-01-01T00:00:00Z',
            'updatedAt' => '2024-01-01T00:00:00Z',
            'version' => 1,
            'name' => 'example',
            'subject' => 'example',
            'html' => 'example',
            'mjml' => 'example',
            'text' => 'example',
            'channelIds' => ['550e8400-e29b-41d4-a716-446655440000'],
        ]));

        $result = $client->templates->retrieve(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/templates/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
        self::assertInstanceOf(TemplateResponse::class, $result);
    }

    public function testUpdate(): void
    {
        $client = $this->client(self::json([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'createdAt' => '2024-01-01T00:00:00Z',
            'updatedAt' => '2024-01-01T00:00:00Z',
            'version' => 1,
            'name' => 'example',
            'subject' => 'example',
            'html' => 'example',
            'mjml' => 'example',
            'text' => 'example',
            'channelIds' => ['550e8400-e29b-41d4-a716-446655440000'],
        ]));

        $result = $client->templates->update(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'test-name',
            subject: 'test-subject',
            html: 'test-html',
            mjml: 'test-mjml',
            text: 'test-text',
            channelIds: ['550e8400-e29b-41d4-a716-446655440000'],
            version: 10,
        );

        $request = $this->lastRequest();
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame('/templates/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
        self::assertInstanceOf(TemplateResponse::class, $result);
    }

    public function testDelete(): void
    {
        $client = $this->client(new Response(204));

        $client->templates->delete(
            id: '550e8400-e29b-41d4-a716-446655440000',
        );

        $request = $this->lastRequest();
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame('/templates/550e8400-e29b-41d4-a716-446655440000', $request->getUri()->getPath());
    }

    public function testRender(): void
    {
        $client = $this->client(self::json(['subject' => 'example', 'html' => 'example', 'text' => 'example']));

        $result = $client->templates->render(
            id: '550e8400-e29b-41d4-a716-446655440000',
            inlineStyles: true,
        );

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/templates/550e8400-e29b-41d4-a716-446655440000/render', $request->getUri()->getPath());
        self::assertInstanceOf(RenderTemplateResponse::class, $result);
    }
}
