<?php

declare(strict_types=1);

namespace HeloEmail\Sdk;

use GuzzleHttp\Client;
use HeloEmail\Sdk\Internal\HttpClient;
use HeloEmail\Sdk\Resources\ActivityResource;
use HeloEmail\Sdk\Resources\BroadcastsResource;
use HeloEmail\Sdk\Resources\ChannelsResource;
use HeloEmail\Sdk\Resources\DomainsResource;
use HeloEmail\Sdk\Resources\SendingResource;
use HeloEmail\Sdk\Resources\StatisticsResource;
use HeloEmail\Sdk\Resources\SuppressionsResource;
use HeloEmail\Sdk\Resources\TemplatesResource;
use HeloEmail\Sdk\Resources\WebhooksResource;
use Psr\Http\Client\ClientInterface;

/** Client for the Helo API. */
final class Helo
{
    public const VERSION = '1.3.0';
    public const DEFAULT_BASE_URL = 'https://api.helohq.com';
    public const DEFAULT_TIMEOUT = 30.0;
    public const DEFAULT_MAX_RETRIES = 2;

    public readonly ActivityResource $activity;
    public readonly BroadcastsResource $broadcasts;
    public readonly ChannelsResource $channels;
    public readonly DomainsResource $domains;
    public readonly SendingResource $sending;
    public readonly StatisticsResource $statistics;
    public readonly SuppressionsResource $suppressions;
    public readonly TemplatesResource $templates;
    public readonly WebhooksResource $webhooks;

    /**
     * @param string|null $apiKey read from HELO_API_KEY when not given
     * @param float $timeout seconds; ignored when you pass your own $httpClient
     * @param int $maxRetries retries for connection errors, timeouts, 408, 429 and 5xx
     * @param ClientInterface|null $httpClient any PSR-18 client, instead of the default Guzzle one
     */
    public function __construct(
        ?string $apiKey = null,
        string $baseUrl = self::DEFAULT_BASE_URL,
        float $timeout = self::DEFAULT_TIMEOUT,
        int $maxRetries = self::DEFAULT_MAX_RETRIES,
        ?ClientInterface $httpClient = null,
    ) {
        $apiKey ??= getenv('HELO_API_KEY') ?: null;
        if ($apiKey === null || $apiKey === '') {
            throw new \InvalidArgumentException(
                'No API key provided. Pass $apiKey or set the HELO_API_KEY environment variable.',
            );
        }

        $http = new HttpClient(
            $httpClient ?? new Client(['timeout' => $timeout]),
            $apiKey,
            $baseUrl,
            $maxRetries,
        );
        $this->activity = new ActivityResource($http);
        $this->broadcasts = new BroadcastsResource($http);
        $this->channels = new ChannelsResource($http);
        $this->domains = new DomainsResource($http);
        $this->sending = new SendingResource($http);
        $this->statistics = new StatisticsResource($http);
        $this->suppressions = new SuppressionsResource($http);
        $this->templates = new TemplatesResource($http);
        $this->webhooks = new WebhooksResource($http);
    }
}
