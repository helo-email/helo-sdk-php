<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class BroadcastContent
{
    public function __construct(
        public readonly ?MailAddress $from = null,
        /**
         * Reply-to addresses.
         * @var list<MailAddress>|null
         */
        public readonly ?array $replyTo = null,
        public readonly ?BroadcastContentTemplate $template = null,
        /**
         * File attachments included with the broadcast.
         * @var list<BroadcastContentAttachment>|null
         */
        public readonly ?array $attachments = null,
        /**
         * Tags associated with the broadcast.
         * @var list<string>|null
         */
        public readonly ?array $tags = null,
        /**
         * Custom email headers applied to every message.
         * @var array<string, string>|null
         */
        public readonly ?array $headers = null,
        /**
         * Custom key/value metadata stored with the broadcast.
         * @var array<string, string>|null
         */
        public readonly ?array $metadata = null,
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
            from: isset($data['from']) ? MailAddress::fromArray($data['from']) : null,
            replyTo: isset($data['replyTo']) ? array_map(static fn ($item) => MailAddress::fromArray($item), $data['replyTo']) : null,
            template: isset($data['template']) ? BroadcastContentTemplate::fromArray($data['template']) : null,
            attachments: isset($data['attachments']) ? array_map(static fn ($item) => BroadcastContentAttachment::fromArray($item), $data['attachments']) : null,
            tags: $data['tags'] ?? null,
            headers: $data['headers'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }
}
