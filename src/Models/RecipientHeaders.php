<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

final class RecipientHeaders
{
    public function __construct(
        /** @var list<MailAddress> */
        public readonly array $to,
        /** @var list<MailAddress>|null */
        public readonly ?array $cc = null,
        /** @var list<MailAddress>|null */
        public readonly ?array $bcc = null,
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
            to: array_map(static fn ($item) => MailAddress::fromArray($item), $data['to']),
            cc: isset($data['cc']) ? array_map(static fn ($item) => MailAddress::fromArray($item), $data['cc']) : null,
            bcc: isset($data['bcc']) ? array_map(static fn ($item) => MailAddress::fromArray($item), $data['bcc']) : null,
        );
    }
}
