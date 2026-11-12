# Activity

Track and retrieve message activity, including messages, delivery and engagement events.

| Method | HTTP request | Description |
| ------ | ------------ | ----------- |
| [**activity->listEvents()**](#listevents) | **GET** /activity/events | List activity events |
| [**activity->listMessages()**](#listmessages) | **GET** /activity/messages | List messages |
| [**activity->retrieveMessage()**](#retrievemessage) | **GET** /activity/messages/{id} | Retrieve message details |

## listEvents

`GET /activity/events`

Retrieves activity events for messages, including delivery status, opens, clicks, bounces, unsubscribes and complaints.

```php Activity_listEvents
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$paginatedEvents = $helo->activity->listEvents(
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    messageId: '550e8400-e29b-41d4-a716-446655440000',
);
```

## listMessages

`GET /activity/messages`

Retrieves a paginated list of sent messages with basic tracking information.

```php Activity_listMessages
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$paginatedMessages = $helo->activity->listMessages(
    channelId: '550e8400-e29b-41d4-a716-446655440000',
    after: 10,
);
```

## retrieveMessage

`GET /activity/messages/{id}`

Fetches detailed tracking information for a specific message, including all associated events.

```php Activity_retrieveMessage
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$messageDetails = $helo->activity->retrieveMessage(id: '550e8400-e29b-41d4-a716-446655440000');
```
