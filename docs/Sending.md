# Sending

Send transactional and broadcast emails.

| Method | HTTP request | Description |
| ------ | ------------ | ----------- |
| [**sending->sendTransactional()**](#sendtransactional) | **POST** /send/transactional | Send a transactional email |
| [**sending->sendTransactionalBatch()**](#sendtransactionalbatch) | **POST** /send/transactional/batch | Send transactional emails in batch |
| [**sending->sendBroadcast()**](#sendbroadcast) | **POST** /send/broadcast | Send a broadcast |
| [**sending->sendBroadcastMessage()**](#sendbroadcastmessage) | **POST** /send/broadcast/message | Send a single broadcast email |

## sendTransactional

`POST /send/transactional`

Sends a single transactional email such as receipts, confirmations, or notifications.

```php Sending_sendTransactional
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Models\AttachmentDisposition;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$sendMessageAccepted = $helo->sending->sendTransactional(
    from: ['email' => 'from@yourdomain.com', 'name' => 'From name'],
    to: [['email' => 'to@example.com', 'name' => 'To name']],
    cc: [['email' => 'cc@example.com', 'name' => 'Cc name']],
    bcc: [['email' => 'bcc@example.com', 'name' => 'Bcc name']],
    replyTo: [['email' => 'reply-to@example.com', 'name' => 'Reply-To name']],
    subject: 'Hello from Helo',
    html: '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
    text: 'This is a test message, delivered with <3 by Helo.',
    template: [
        'id' => '550e8400-e29b-41d4-a716-446655440000',
        'subject' => 'test-subject',
        'html' => 'test-html',
        'text' => 'test-text',
        'inlineStyles' => true,
    ],
    tracking: ['opens' => true, 'links' => true],
    attachments: [
        [
            'content' => 'SGVsbG8gd29ybGQ=',
            'contentId' => 'test-contentId',
            'contentType' => 'test-contentType',
            'fileName' => 'test-fileName',
            'disposition' => AttachmentDisposition::Attachment,
        ],
    ],
    tags: ['welcome', 'onboarding'],
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    idempotencyKey: 'test-idempotencyKey',
);
```

## sendTransactionalBatch

`POST /send/transactional/batch`

Sends multiple transactional emails in a single API request for better performance.

```php Sending_sendTransactionalBatch
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$sendMessageBatch = $helo->sending->sendTransactionalBatch(
    requests: [
        [
            'from' => ['email' => 'from@yourdomain.com', 'name' => 'From name'],
            'to' => [['email' => 'to@example.com', 'name' => 'To name']],
            'subject' => 'Hello from Helo',
            'html' => '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
            'text' => 'This is a test message, delivered with <3 by Helo.',
            'tags' => ['welcome', 'onboarding'],
        ],
    ],
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    idempotencyKey: 'test-idempotencyKey',
);
```

## sendBroadcast

`POST /send/broadcast`

Sends a broadcast of multiple messages for marketing or announcement purposes.

```php Sending_sendBroadcast
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Models\AttachmentDisposition;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$sendBroadcast = $helo->sending->sendBroadcast(
    from: ['email' => 'test@example.com', 'name' => 'test-name'],
    template: [
        'id' => '550e8400-e29b-41d4-a716-446655440000',
        'subject' => 'test-subject',
        'html' => 'test-html',
        'text' => 'test-text',
        'inlineStyles' => true,
    ],
    messages: [['to' => [['email' => 'test@example.com', 'name' => 'test-name']], 'tags' => ['test-tag']]],
    replyTo: [['email' => 'test@example.com', 'name' => 'test-name']],
    tracking: ['opens' => true, 'links' => true],
    attachments: [
        [
            'content' => 'SGVsbG8gd29ybGQ=',
            'contentId' => 'test-contentId',
            'contentType' => 'test-contentType',
            'fileName' => 'test-fileName',
            'disposition' => AttachmentDisposition::Attachment,
        ],
    ],
    tags: ['test-tag'],
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    idempotencyKey: 'test-idempotencyKey',
);
```

## sendBroadcastMessage

`POST /send/broadcast/message`

Sends a single broadcast email message.

```php Sending_sendBroadcastMessage
use HeloEmail\Sdk\Helo;
use HeloEmail\Sdk\Models\AttachmentDisposition;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$sendMessageAccepted = $helo->sending->sendBroadcastMessage(
    from: ['email' => 'from@yourdomain.com', 'name' => 'From name'],
    to: [['email' => 'to@example.com', 'name' => 'To name']],
    cc: [['email' => 'cc@example.com', 'name' => 'Cc name']],
    bcc: [['email' => 'bcc@example.com', 'name' => 'Bcc name']],
    replyTo: [['email' => 'reply-to@example.com', 'name' => 'Reply-To name']],
    subject: 'Hello from Helo',
    html: '<html><body><h1>Hi there, new friend.</h1><p>This is a test message, delivered with <3 by Helo. </p></body></html>',
    text: 'This is a test message, delivered with <3 by Helo.',
    template: [
        'id' => '550e8400-e29b-41d4-a716-446655440000',
        'subject' => 'test-subject',
        'html' => 'test-html',
        'text' => 'test-text',
        'inlineStyles' => true,
    ],
    tracking: ['opens' => true, 'links' => true],
    attachments: [
        [
            'content' => 'SGVsbG8gd29ybGQ=',
            'contentId' => 'test-contentId',
            'contentType' => 'test-contentType',
            'fileName' => 'test-fileName',
            'disposition' => AttachmentDisposition::Attachment,
        ],
    ],
    tags: ['welcome', 'onboarding'],
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    idempotencyKey: 'test-idempotencyKey',
);
```
