<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/** Details of a `link-clicked` event. */
final class ClickedDetails
{
    public function __construct(
        /** The URL that was clicked. */
        public readonly ?string $link = null,
        /** IP address the recipient engaged from. */
        public readonly ?string $ip = null,
        /** Country the recipient engaged from. */
        public readonly ?string $country = null,
        /** ISO 3166-1 alpha-2 country code the recipient engaged from. */
        public readonly ?string $countryCode = null,
        public readonly ?ClientDetails $client = null,
        public readonly ?DeviceDetails $device = null,
    ) {
    }

    /**
     * Builds the model from a decoded JSON response.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            link: $data['link'] ?? null,
            ip: $data['ip'] ?? null,
            country: $data['country'] ?? null,
            countryCode: $data['countryCode'] ?? null,
            client: isset($data['client']) ? ClientDetails::fromArray($data['client']) : null,
            device: isset($data['device']) ? DeviceDetails::fromArray($data['device']) : null,
        );
    }
}
