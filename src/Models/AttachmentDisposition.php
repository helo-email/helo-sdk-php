<?php

declare(strict_types=1);

namespace HeloEmail\Sdk\Models;

/**
 * How the attachment is presented. Use `attachment` for downloadable files and `inline` for
 * embedded content (e.g. images referenced in HTML).
 */
enum AttachmentDisposition: string
{
    case Attachment = 'attachment';
    case Inline = 'inline';
}
