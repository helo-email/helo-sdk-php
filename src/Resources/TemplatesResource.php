<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Resources;

use HeloEmail\Sdk\Exceptions\ApiException;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
use HeloEmail\Sdk\Internal\HttpClient;
use HeloEmail\Sdk\Models\PaginatedResponseOfTemplateSummaryResponse;
use HeloEmail\Sdk\Models\RenderTemplateResponse;
use HeloEmail\Sdk\Models\TemplateResponse;

final class TemplatesResource
{
    /** @internal constructed by the client */
    public function __construct(private readonly HttpClient $http)
    {
    }

    /**
     * List all templates
     *
     * @param list<string>|null $channelIds
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?string $name = null,
        ?array $channelIds = null,
    ): PaginatedResponseOfTemplateSummaryResponse {
        $data = $this->http->request(
            'GET',
            '/templates',
            query: [
                'limit' => $limit,
                'offset' => $offset,
                'name' => $name,
                'channelIds' => HttpClient::commaJoin($channelIds),
            ],
        );

        return PaginatedResponseOfTemplateSummaryResponse::fromArray($data);
    }

    /**
     * Create a template
     *
     * @param list<string>|null $channelIds
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function create(
        string $name,
        string $subject,
        ?string $html = null,
        ?string $mjml = null,
        ?string $text = null,
        ?array $channelIds = null,
    ): TemplateResponse {
        $data = $this->http->request(
            'POST',
            '/templates',
            body: [
                'name' => $name,
                'subject' => $subject,
                'html' => $html,
                'mjml' => $mjml,
                'text' => $text,
                'channelIds' => $channelIds,
            ],
        );

        return TemplateResponse::fromArray($data);
    }

    /**
     * Render template content
     *
     * @param array<string, mixed>|null $data
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function renderDraft(
        string $subject,
        ?string $html = null,
        ?string $mjml = null,
        ?string $text = null,
        ?bool $inlineStyles = null,
        ?array $data = null,
    ): RenderTemplateResponse {
        $data = $this->http->request(
            'POST',
            '/templates/render',
            body: [
                'subject' => $subject,
                'html' => $html,
                'mjml' => $mjml,
                'text' => $text,
                'inlineStyles' => $inlineStyles,
                'data' => $data,
            ],
        );

        return RenderTemplateResponse::fromArray($data);
    }

    /**
     * Retrieve a template
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function retrieve(
        string $id,
    ): TemplateResponse {
        $data = $this->http->request(
            'GET',
            '/templates/' . HttpClient::pathSegment($id),
        );

        return TemplateResponse::fromArray($data);
    }

    /**
     * Update a template
     *
     * @param list<string>|null $channelIds
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function update(
        string $id,
        ?string $name = null,
        ?string $subject = null,
        ?string $html = null,
        ?string $mjml = null,
        ?string $text = null,
        ?array $channelIds = null,
        ?int $version = null,
    ): TemplateResponse {
        $data = $this->http->request(
            'PATCH',
            '/templates/' . HttpClient::pathSegment($id),
            body: [
                'name' => $name,
                'subject' => $subject,
                'html' => $html,
                'mjml' => $mjml,
                'text' => $text,
                'channelIds' => $channelIds,
                'version' => $version,
            ],
        );

        return TemplateResponse::fromArray($data);
    }

    /**
     * Delete a template
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function delete(
        string $id,
    ): void {
        $this->http->request(
            'DELETE',
            '/templates/' . HttpClient::pathSegment($id),
        );
    }

    /**
     * Render a template
     *
     * @param array<string, mixed>|null $data
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function render(
        string $id,
        ?bool $inlineStyles = null,
        ?array $data = null,
    ): RenderTemplateResponse {
        $data = $this->http->request(
            'POST',
            '/templates/' . HttpClient::pathSegment($id) . '/render',
            body: [
                'inlineStyles' => $inlineStyles,
                'data' => $data,
            ],
        );

        return RenderTemplateResponse::fromArray($data);
    }
}
