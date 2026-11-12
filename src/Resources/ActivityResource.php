<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Resources;

use HeloEmail\Sdk\Exceptions\ApiException;
use HeloEmail\Sdk\Exceptions\ApiConnectionException;
use HeloEmail\Sdk\Internal\HttpClient;
use HeloEmail\Sdk\Models\EventType;
use HeloEmail\Sdk\Models\MailType;
use HeloEmail\Sdk\Models\MessageDetailsResponse;
use HeloEmail\Sdk\Models\MessageStatus;
use HeloEmail\Sdk\Models\PaginatedEventsResponse;
use HeloEmail\Sdk\Models\PaginatedMessagesResponse;

final class ActivityResource
{
    /** @internal constructed by the client */
    public function __construct(private readonly HttpClient $http)
    {
    }

    /**
     * List activity events
     *
     * @param list<string>|null $tags
     * @param list<EventType>|null $eventTypes
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function listEvents(
        ?string $channelId = null,
        ?string $messageId = null,
        ?int $after = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $limit = null,
        ?string $recipient = null,
        ?string $subject = null,
        ?array $tags = null,
        ?MailType $mailType = null,
        ?array $eventTypes = null,
    ): PaginatedEventsResponse {
        $data = $this->http->request(
            'GET',
            '/activity/events',
            query: [
                'channelId' => $channelId,
                'messageId' => $messageId,
                'after' => $after,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'limit' => $limit,
                'recipient' => $recipient,
                'subject' => $subject,
                'tags' => HttpClient::commaJoin($tags),
                'mailType' => $mailType,
                'eventTypes' => HttpClient::commaJoin($eventTypes),
            ],
        );

        return PaginatedEventsResponse::fromArray($data);
    }

    /**
     * List messages
     *
     * @param list<string>|null $tags
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function listMessages(
        ?string $channelId = null,
        ?int $after = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $limit = null,
        ?string $recipient = null,
        ?string $subject = null,
        ?array $tags = null,
        ?MailType $mailType = null,
        ?MessageStatus $status = null,
    ): PaginatedMessagesResponse {
        $data = $this->http->request(
            'GET',
            '/activity/messages',
            query: [
                'channelId' => $channelId,
                'after' => $after,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'limit' => $limit,
                'recipient' => $recipient,
                'subject' => $subject,
                'tags' => HttpClient::commaJoin($tags),
                'mailType' => $mailType,
                'status' => $status,
            ],
        );

        return PaginatedMessagesResponse::fromArray($data);
    }

    /**
     * Retrieve message details
     *
     * @throws ApiException
     * @throws ApiConnectionException
     */
    public function retrieveMessage(
        string $id,
    ): MessageDetailsResponse {
        $data = $this->http->request(
            'GET',
            '/activity/messages/' . HttpClient::pathSegment($id),
        );

        return MessageDetailsResponse::fromArray($data);
    }
}
