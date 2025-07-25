# Loops PHP Package

[![Packagist Total Downloads](https://img.shields.io/packagist/dt/loops-so/loops?style=social)](https://packagist.org/packages/loops-so/loops)

## Introduction

This is the official PHP package for [Loops](https://loops.so), an email platform for modern software companies.

## Installation

Install the Loops package [using Composer](https://packagist.org/packages/loops-so/loops):

```bash
composer require loops-so/loops
```

## Usage

You will need a Loops API key to use the package.

In your Loops account, go to the [API Settings page](https://app.loops.so/settings?page=api) and click **Generate key**.

Copy this key and save it in your application code (for example, in an environment variable).

See the API documentation to learn more about [rate limiting](https://loops.so/docs/api-reference#rate-limiting) and [error handling](https://loops.so/docs/api-reference#debugging).

To use the package, first initialise the client with your API key, then you can call one of the methods.

API rate limits can be handled with the included `RateLimitExceededError` exception.

```php
use Loops\LoopsClient;

$loops = new LoopsClient(env('LOOPS_API_KEY'));

// Test API key
$result = $loops->apiKey->test();

// Create a contact and catch errors
try {
    $result = $loops->contacts->create('user@example.com', [
      'firstName' => 'John'
    ]);
} catch (Loops\Exceptions\APIError $e) {
    // Handle API errors (400, 401, 403, etc)
    echo $e->getMessage();
    $returnedJson = $e->getJson(); // JSON returned by the API
    $statusCode = $e->getStatusCode(); // HTTP status code from the response
} catch (\Exception $e) {
    // Handle any other unexpected errors
    echo "Unexpected error: " . $e->getMessage();
}
```

## Handling rate limits

You can use the check for rate limit issues with your requests.

You can access details about the rate limits from the `getLimit` and `getRemaining` functions.

```php
try {
    $result = $loops->contacts->create('user@example.com', [
      'firstName' => 'John'
    ]);
} catch (Loops\Exceptions\RateLimitExceededError $e) {
    // Handle rate limiting
    echo "Rate limit hit. Limit: " . $e->getLimit() . ", requests remaining: " . $e->getRemaining();
} catch (Loops\Exceptions\APIError $e) {
    // Handle API errors (400, 401, 403, etc)
    echo $e->getMessage();
} catch (\Exception $e) {
    // Handle any other unexpected errors
    echo "Unexpected error: " . $e->getMessage();
}
```

## Default contact properties

Each contact in Loops has a set of default properties. These will always be returned in API results.

- `id`
- `email`
- `firstName`
- `lastName`
- `source`
- `subscribed`
- `userGroup`
- `userId`

## Custom contact properties

You can use custom contact properties in API calls. Please make sure to [add custom properties](https://loops.so/docs/contacts/properties#custom-contact-properties) in your Loops account before using them with the SDK.

## Methods

- [apiKey->test()](#apikey-test)
- [contacts->create()](#contacts-create)
- [contacts->update()](#contacts-update)
- [contacts->find()](#contacts-find)
- [contacts->delete()](#contacts-delete)
- [contactProperties->create()](#contactproperties-create)
- [contactProperties->get()](#contactproperties-get)
- [mailingLists->get()](#mailinglists-get)
- [events->send()](#events-send)
- [transactional->send()](#transactional-send)
- [transactional->get()](#transactional-get)

---

### apiKey->test()

Test if your API key is valid.

[API Reference](https://loops.so/docs/api-reference/api-key)

#### Parameters

None

#### Example

```php
$result = $loops->apiKey->test();
```

#### Response

This method will return a success or error message:

```json
{
  "success": true,
  "teamName": "Company name"
}
```

```json
{
  "error": "Invalid API key"
}
```

---

### contacts->create()

Create a new contact.

[API Reference](https://loops.so/docs/api-reference/create-contact)

#### Parameters

| Name             | Type   | Required | Notes                                                                                                                                                                                                                                                                                                                                                                                                               |
| ---------------- | ------ | -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `$email`         | string | Yes      | If a contact already exists with this email address, an `APIError` will be thrown.                                                                                                                                                                                                                                                                                                                                  |
| `$properties`    | array  | No       | An array containing default and any custom properties for your contact.<br />Please [add custom properties](https://loops.so/docs/contacts/properties#custom-contact-properties) in your Loops account before using them with the SDK.<br />Values can be of type `string`, `number`, `null` (to reset a value), `boolean` or `date` ([see allowed date formats](https://loops.so/docs/contacts/properties#dates)). |
| `$mailing_lists` | array  | No       | An array of mailing list IDs and boolean subscription statuses.                                                                                                                                                                                                                                                                                                                                                     |

#### Examples

```php
$result = $loops->contacts->create("hello@gmail.com");

$contact_properties = [
  'firstName' => "Bob" /* Default property */,
  'favoriteColor' => "Red" /* Custom property */,
];
$mailing_lists = [
  'cm06f5v0e45nf0ml5754o9cix' => TRUE,
  'cm16k73gq014h0mmj5b6jdi9r' => FALSE,
];
$result = $loops->contacts->create(
  email: "hello@gmail.com",
  properties: $contact_properties,
  mailing_lists: $mailing_lists
);
```

#### Response

```json
{
  "success": true,
  "id": "id_of_contact"
}
```

Error handling is done through the `APIError` class, which provides `getStatusCode()` and `getJson()` methods for retrieving the API's error response details. For implementation examples, see the [Usage section](#usage).

```json
// HTTP 400 Bad Request
{
  "success": false,
  "message": "An error message here."
}
```

---

### contacts->update()

Update a contact.

Note: To update a contact's email address, the contact requires a `userId` value. Then you can make a request with their `userId` and an updated email address.

[API Reference](https://loops.so/docs/api-reference/update-contact)

#### Parameters

| Name             | Type   | Required | Notes                                                                                                                                                                                                                                                                                                                                                                                                               |
| ---------------- | ------ | -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `$email`         | string | Yes      | The email address of the contact to update. If there is no contact with this email address, a new contact will be created using the email and properties in this request.                                                                                                                                                                                                                                           |
| `$properties`    | array  | No       | An array containing default and any custom properties for your contact.<br />Please [add custom properties](https://loops.so/docs/contacts/properties#custom-contact-properties) in your Loops account before using them with the SDK.<br />Values can be of type `string`, `number`, `null` (to reset a value), `boolean` or `date` ([see allowed date formats](https://loops.so/docs/contacts/properties#dates)). |
| `$mailing_lists` | array  | No       | An array of mailing list IDs and boolean subscription statuses.                                                                                                                                                                                                                                                                                                                                                     |

#### Example

```php
$contact_properties = [
  'firstName' => 'Bob', /* Default property */
  'favoriteColor' => 'Blue' /* Custom property */
];
$response = $loops->contacts->update(
  email: 'hello@gmail.com',
  properties: $contact_properties
);

// Updating a contact's email address using userId
$results = $loops->contacts->update(
  email: 'newemail@gmail.com',
  properties: [
    'userId' => '1234'
  ]
);
```

#### Response

```json
{
  "success": true,
  "id": "id_of_contact"
}
```

Error handling is done through the `APIError` class, which provides `getStatusCode()` and `getJson()` methods for retrieving the API's error response details. For implementation examples, see the [Usage section](#usage).

```json
// HTTP 400 Bad Request
{
  "success": false,
  "message": "An error message here."
}
```

---

### contacts->find()

Find a contact.

[API Reference](https://loops.so/docs/api-reference/find-contact)

#### Parameters

You must use one parameter in the request.

| Name       | Type   | Required | Notes |
| ---------- | ------ | -------- | ----- |
| `$email`   | string | No       |       |
| `$user_id` | string | No       |       |

#### Examples

```php
$result = $loops->contacts->find(email: 'hello@gmail.com');

$result = $loops->contacts->find(user_id: '12345');
```

#### Response

This method will return a list containing a single contact object, which will include all default properties and any custom properties.

If no contact is found, an empty list will be returned.

```json
[
  {
    "id": "cll6b3i8901a9jx0oyktl2m4u",
    "email": "hello@gmail.com",
    "firstName": "Bob",
    "lastName": null,
    "source": "API",
    "subscribed": true,
    "userGroup": "",
    "userId": "12345",
    "mailingLists": {
      "cm06f5v0e45nf0ml5754o9cix": true
    },
    "favoriteColor": "Blue" /* Custom property */
  }
]
```

---

### contacts->delete()

Delete a contact.

[API Reference](https://loops.so/docs/api-reference/delete-contact)

#### Parameters

You must use one parameter in the request.

| Name       | Type   | Required | Notes |
| ---------- | ------ | -------- | ----- |
| `$email`   | string | No       |       |
| `$user_id` | string | No       |       |

#### Example

```php
$result = $loops->contacts->delete(email: 'hello@gmail.com')

$result = $loops->contacts->delete(user_id: '12345')
```

#### Response

```json
{
  "success": true,
  "message": "Contact deleted."
}
```

Error handling is done through the `APIError` class, which provides `getStatusCode()` and `getJson()` methods for retrieving the API's error response details. For implementation examples, see the [Usage section](#usage).

```json
// HTTP 400 Bad Reuqest
{
  "success": false,
  "message": "An error message here."
}
```

```json
// HTTP 404 Not Found
{
  "success": false,
  "message": "An error message here."
}
```

---

### contactProperties->create()

Create a new contact property.

[API Reference](https://loops.so/docs/api-reference/create-contact-property)

#### Parameters

| Name    | Type   | Required | Notes                                                                                  |
| ------- | ------ | -------- | -------------------------------------------------------------------------------------- |
| `$name` | string | Yes      | The name of the property. Should be in camelCase, like `planName` or `favouriteColor`. |
| `$type` | string | Yes      | The property's value type.<br />Can be one of `string`, `number`, `boolean` or `date`. |

#### Examples

```php
$result = $loops->contactProperties->create("planName", "string");
```

#### Response

```json
{
  "success": true
}
```

Error handling is done through the `APIError` class, which provides `getStatusCode()` and `getJson()` methods for retrieving the API's error response details. For implementation examples, see the [Usage section](#usage).

```json
// HTTP 400 Bad Request
{
  "success": false,
  "message": "An error message here."
}
```

---

### contactProperties->get()

Get a list of your account's contact properties.

[API Reference](https://loops.so/docs/api-reference/list-contact-properties)

#### Parameters

| Name    | Type   | Required | Notes                                                           |
| ------- | ------ | -------- | --------------------------------------------------------------- |
| `$list` | string | No       | Use "custom" to retrieve only your account's custom properties. |

#### Example

```php
$result = $loops->contactProperties->get();

$result = $loops->contactProperties->get(list: "custom");
```

#### Response

This method will return a list of contact property objects containing `key`, `label` and `type` attributes.

```json
[
  {
    "key": "firstName",
    "label": "First Name",
    "type": "string"
  },
  {
    "key": "lastName",
    "label": "Last Name",
    "type": "string"
  },
  {
    "key": "email",
    "label": "Email",
    "type": "string"
  },
  {
    "key": "notes",
    "label": "Notes",
    "type": "string"
  },
  {
    "key": "source",
    "label": "Source",
    "type": "string"
  },
  {
    "key": "userGroup",
    "label": "User Group",
    "type": "string"
  },
  {
    "key": "userId",
    "label": "User Id",
    "type": "string"
  },
  {
    "key": "subscribed",
    "label": "Subscribed",
    "type": "boolean"
  },
  {
    "key": "createdAt",
    "label": "Created At",
    "type": "date"
  },
  {
    "key": "favoriteColor",
    "label": "Favorite Color",
    "type": "string"
  },
  {
    "key": "plan",
    "label": "Plan",
    "type": "string"
  }
]
```

---

### mailingLists->get()

Get a list of your account's mailing lists. [Read more about mailing lists](https://loops.so/docs/contacts/mailing-lists)

[API Reference](https://loops.so/docs/api-reference/list-mailing-lists)

#### Parameters

None

#### Example

```php
$result = $loops->mailingLists->get();
```

#### Response

This method will return a list of mailing list objects containing `id`, `name`, `description` and `isPublic` attributes.

If your account has no mailing lists, an empty list will be returned.

```json
[
  {
    "id": "cm06f5v0e45nf0ml5754o9cix",
    "name": "Main list",
    "description": "All customers.",
    "isPublic": true
  },
  {
    "id": "cm16k73gq014h0mmj5b6jdi9r",
    "name": "Investors",
    "description": null,
    "isPublic": false
  }
]
```

---

### events->send()

Send an event to trigger an email in Loops. [Read more about events](https://loops.so/docs/events)

[API Reference](https://loops.so/docs/api-reference/send-event)

#### Parameters

| Name                  | Type   | Required | Notes                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| --------------------- | ------ | -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `$event_name`         | string | Yes      |                                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| `$email`              | string | No       | The contact's email address. Required if `$user_id` is not present.                                                                                                                                                                                                                                                                                                                                                                                           |
| `$user_id`            | string | No       | The contact's unique user ID. If you use `$user_id` without `$email`, this value must have already been added to your contact in Loops. Required if `$email` is not present.                                                                                                                                                                                                                                                                                  |
| `$contact_properties` | array  | No       | An array containing contact properties, which will be updated or added to the contact when the event is received.<br />Please [add custom properties](https://loops.so/docs/contacts/properties#custom-contact-properties) in your Loops account before using them with the SDK.<br />Values can be of type `string`, `number`, `null` (to reset a value), `boolean` or `date` ([see allowed date formats](https://loops.so/docs/contacts/properties#dates)). |
| `$event_properties`   | array  | No       | An array containing event properties, which will be made available in emails that are triggered by this event.<br />Values can be of type `string`, `number`, `boolean` or `date` ([see allowed date formats](https://loops.so/docs/events/properties#important-information-about-event-properties)).                                                                                                                                                         |
| `$mailing_lists`      | array  | No       | An array of mailing list IDs and boolean subscription statuses.                                                                                                                                                                                                                                                                                                                                                                                               |
| `$headers`            | array  | No       | Additional headers to send with the request.                                                                                                                                                                                                                                                                                                                                                                                                                  |

#### Examples

```php
$result = $loops->events->send(
  event_name: 'signup',
  email: 'hello@gmail.com'
);

$result = $loops->events->send(
  event_name: 'signup',
  email: 'hello@gmail.com',
  event_properties: [
    'username' => 'user1234',
    'signupDate' => '2024-03-21T10:09:23Z'
  ],
  mailing_lists: [
    'cm06f5v0e45nf0ml5754o9cix' => true,
    'cm16k73gq014h0mmj5b6jdi9r' => false
  ]
;

# In this case with both email and userId present, the system will look for a contact with either a
#  matching `email` or `user_id` value.
# If a contact is found for one of the values (e.g. `email`), the other value (e.g. `user_id`) will be updated.
# If a contact is not found, a new contact will be created using both `email` and `user_id` values.
# Any values added in `contact_properties` will also be updated on the contact.
$result = $loops->events->send(
  event_name: 'signup',
  email: 'hello@gmail.com',
  user_id: '1234567890',
  contact_properties: [
    'firstName' => 'Bob',
    'plan' => 'pro',
  }]
});

# Example with Idempotency-Key header
$result = $loops->events->send(
  event_name: 'signup',
  email: 'hello@gmail.com',
  headers: [
    'Idempotency-Key' => '550e8400-e29b-41d4-a716-446655440000'
  ]
);
```

#### Response

```json
{
  "success": true
}
```

Error handling is done through the `APIError` class, which provides `getStatusCode()` and `getJson()` methods for retrieving the API's error response details. For implementation examples, see the [Usage section](#usage).

```json
// HTTP 400 Bad Request
{
  "success": false,
  "message": "An error message here."
}
```

---

### transactional->send()

Send a transactional email to a contact. [Learn about sending transactional email](https://loops.so/docs/transactional/guide)

[API Reference](https://loops.so/docs/api-reference/send-transactional-email)

#### Parameters

| Name                             | Type    | Required | Notes                                                                                                                                                                                            |
| -------------------------------- | ------- | -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `$transactional_id`              | string  | Yes      | The ID of the transactional email to send.                                                                                                                                                       |
| `$email`                         | string  | Yes      | The email address of the recipient.                                                                                                                                                              |
| `$add_to_audience`               | boolean | No       | If `true`, a contact will be created in your audience using the `$email` value (if a matching contact doesn’t already exist).                                                                    |
| `$data_variables`                | array   | No       | An array containing data as defined by the data variables added to the transactional email template.<br />Values can be of type `string` or `number`.                                            |
| `$attachments`                   | array[] | No       | A list of attachments objects.<br />**Please note**: Attachments need to be enabled on your account before using them with the API. [Read more](https://loops.so/docs/transactional/attachments) |
| `$attachments[]["filename"]`     | string  | No       | The name of the file, shown in email clients.                                                                                                                                                    |
| `$attachments[]["content_type"]` | string  | No       | The MIME type of the file.                                                                                                                                                                       |
| `$attachments[]["data"]`         | string  | No       | The base64-encoded content of the file.                                                                                                                                                          |
| `$headers`                       | array   | No       | Additional headers to send with the request.                                                                                                                                                     |

#### Examples

```php
$result = $loops->transactional->send(
  transactional_id: 'clfq6dinn000yl70fgwwyp82l',
  email: 'hello@gmail.com',
  data_variables: [
    'loginUrl' => 'https://myapp.com/login/',
  ]
);

# Example with Idempotency-Key header
$result = $loops->transactional->send(
  transactional_id: 'clfq6dinn000yl70fgwwyp82l',
  email: 'hello@gmail.com',
  data_variables: [
    'loginUrl' => 'https://myapp.com/login/',
  ],
  headers: [
    'Idempotency-Key' => '550e8400-e29b-41d4-a716-446655440000'
  ]
);

# Please contact us to enable attachments on your account.
$result = $loops->transactional->send(
  transactional_id: 'clfq6dinn000yl70fgwwyp82l',
  email: 'hello@gmail.com',
  data_variables: [
    'loginUrl' => 'https://myapp.com/login/',
  ],
  attachments: [
    [
      'filename' => 'presentation.pdf',
      'content_type' => 'application/pdf',
      'data' => base64_encode(file_get_contents('path/to/presentation.pdf'))
    ]
  ]
);
```

#### Response

```json
{
  "success": true
}
```

Error handling is done through the `APIError` class, which provides `getStatusCode()` and `getJson()` methods for retrieving the API's error response details. For implementation examples, see the [Usage section](#usage).

If there is a problem with the request, a descriptive error message will be returned:

```json
// HTTP 400 Bad Request
{
  "success": false,
  "path": "dataVariables",
  "message": "There are required fields for this email. You need to include a 'dataVariables' object with the required fields."
}
```

```json
// HTTP 400 Bad Request
{
  "success": false,
  "error": {
    "path": "dataVariables",
    "message": "Missing required fields: login_url"
  },
  "transactionalId": "clfq6dinn000yl70fgwwyp82l"
}
```

---

### transactional->get()

Get a list of published transactional emails.

[API Reference](https://loops.so/docs/api-reference/list-transactional-emails)

#### Parameters

| Name        | Type    | Required | Notes                                                                                                                         |
| ----------- | ------- | -------- | ----------------------------------------------------------------------------------------------------------------------------- |
| `$per_page` | integer | No       | How many results to return per page. Must be between 10 and 50. Defaults to 20 if omitted.                                    |
| `$cursor`   | string  | No       | A cursor, to return a specific page of results. Cursors can be found from the `pagination.nextCursor` value in each response. |

#### Example

```php
$result = $loops->transactional->get();

$result = $loops->transactional->get(per_page: 15);
```

#### Response

```json
{
  "pagination": {
    "totalResults": 23,
    "returnedResults": 20,
    "perPage": 20,
    "totalPages": 2,
    "nextCursor": "clyo0q4wo01p59fsecyxqsh38",
    "nextPage": "https://app.loops.so/api/v1/transactional?cursor=clyo0q4wo01p59fsecyxqsh38&perPage=20"
  },
  "data": [
    {
      "id": "clfn0k1yg001imo0fdeqg30i8",
      "lastUpdated": "2023-11-06T17:48:07.249Z",
      "dataVariables": []
    },
    {
      "id": "cll42l54f20i1la0lfooe3z12",
      "lastUpdated": "2025-02-02T02:56:28.845Z",
      "dataVariables": [
        "confirmationUrl"
      ]
    },
    {
      "id": "clw6rbuwp01rmeiyndm80155l",
      "lastUpdated": "2024-05-14T19:02:52.000Z",
      "dataVariables": [
        "firstName",
        "lastName",
        "inviteLink"
      ]
    },
    ...
  ]
}
```

---

## Testing

```bash
vendor/bin/phpunit
```

---

## Contributing

Bug reports and pull requests are welcome. Please read our [Contributing Guidelines](CONTRIBUTING.md).
