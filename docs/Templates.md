# Templates

Manage templates for sending.

| Method | HTTP request | Description |
| ------ | ------------ | ----------- |
| [**templates->list()**](#list) | **GET** /templates | List all templates |
| [**templates->create()**](#create) | **POST** /templates | Create a template |
| [**templates->renderDraft()**](#renderdraft) | **POST** /templates/render | Render template content |
| [**templates->retrieve()**](#retrieve) | **GET** /templates/{id} | Retrieve a template |
| [**templates->update()**](#update) | **PATCH** /templates/{id} | Update a template |
| [**templates->delete()**](#delete) | **DELETE** /templates/{id} | Delete a template |
| [**templates->render()**](#render) | **POST** /templates/{id}/render | Render a template |

## list

`GET /templates`

Retrieves a paginated list of templates, optionally filtered by name or channel.

```php Templates_list
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$paginatedResponseOfTemplateSummary = $helo->templates->list(limit: 10, offset: 10);
```

## create

`POST /templates`

Creates a new template from HTML, MJML, or plain-text content. MJML is compiled to HTML when the template is saved.

```php Templates_create
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$template = $helo->templates->create(
    name: 'test-name',
    subject: 'test-subject',
    html: 'test-html',
    mjml: 'test-mjml',
    text: 'test-text',
    channelIds: ['550e8400-e29b-41d4-a716-446655440000'],
);
```

## renderDraft

`POST /templates/render`

Renders unsaved template content (subject, HTML or MJML, and text) with sample data, without persisting anything.

```php Templates_renderDraft
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$renderTemplate = $helo->templates->renderDraft(
    subject: 'test-subject',
    html: 'test-html',
    mjml: 'test-mjml',
    text: 'test-text',
    inlineStyles: true,
);
```

## retrieve

`GET /templates/{id}`

Fetches the details and content of a specific template.

```php Templates_retrieve
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$template = $helo->templates->retrieve(id: '550e8400-e29b-41d4-a716-446655440000');
```

## update

`PATCH /templates/{id}`

Modifies an existing template. Only the provided fields are changed.

```php Templates_update
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$template = $helo->templates->update(
    id: '550e8400-e29b-41d4-a716-446655440000',
    name: 'test-name',
    subject: 'test-subject',
    html: 'test-html',
    mjml: 'test-mjml',
    text: 'test-text',
    channelIds: ['550e8400-e29b-41d4-a716-446655440000'],
    version: 10,
);
```

## delete

`DELETE /templates/{id}`

Permanently removes a template. This action cannot be undone.

```php Templates_delete
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$helo->templates->delete(id: '550e8400-e29b-41d4-a716-446655440000');
```

## render

`POST /templates/{id}/render`

Renders a saved template with sample data, to preview the output before sending.

```php Templates_render
use HeloEmail\Sdk\Helo;

$helo = new Helo(); // reads HELO_API_KEY from the environment

$renderTemplate = $helo->templates->render(
    id: '550e8400-e29b-41d4-a716-446655440000',
    inlineStyles: true,
);
```
