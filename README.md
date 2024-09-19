# Loops PHP Package

## Introduction

This is the official PHP package for [Loops](https://loops.so), an email platform for modern software companies.

## Installation

Install the Loops package using Composer:

```bash
composer require loops/loops-php
```

## Usage

You will need a Loops API key to use the package.

In your Loops account, go to the [API Settings page](https://app.loops.so/settings?page=api) and click **Generate key**.

Copy this key and save it in your application code (for example, in an environment variable).

See the API documentation to learn more about [rate limiting](https://loops.so/docs/api-reference#rate-limiting) and [error handling](https://loops.so/docs/api-reference#debugging).

To use the package, first initialise the client with your API key, then you can call one of the methods.

```php
use Loops\LoopsClient;

$loops = new LoopsClient(env('LOOPS_API_KEY'));
$result = $loops->apiKey->test();
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
- [mailingLists->getAll()](#mailinglists-getall)
- [events->send()](#events-send)
- [transactional->send()](#transactional-send)
- [customFields->getAll()](#customfields-getall)

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
| `$email`         | string | Yes      | If a contact already exists with this email address, an error response will be returned.                                                                                                                                                                                                                                                                                                                            |
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
  "hello@gmail.com",
  properties: $contact_properties,
  mailing_lists: $mailing_lists
);
```

#### Response

This method will return a success or error message:

```json
{
  "success": true,
  "id": "id_of_contact"
}
```

```json
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
  'hello@gmail.com',
  properties: $contact_properties
);

// Updating a contact's email address using userId
$results = $loops->contacts->update(
  'newemail@gmail.com',
  properties: [
    'userId' => '1234'
  ]
);
```

#### Response

This method will return a success or error message:

```json
{
  "success": true,
  "id": "id_of_contact"
}
```

```json
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
$result = $loops->contacts->find('hello@gmail.com');

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
$result = $loops->contacts->delete('hello@gmail.com')

$result = $loops->contacts->delete(user_id: '12345')
```

#### Response

This method will return a success or error message:

```json
{
  "success": true,
  "message": "Contact deleted."
}
```

```json
{
  "success": false,
  "message": "An error message here."
}
```

---

### mailingLists->getAll()

Get a list of your account's mailing lists. [Read more about mailing lists](https://loops.so/docs/contacts/mailing-lists)

[API Reference](https://loops.so/docs/api-reference/list-mailing-lists)

#### Parameters

None

#### Example

```php
$result = $loops->mailingLists->getAll();
```

#### Response

This method will return a list of mailing list objects containing `id`, `name` and `isPublic` attributes.

If your account has no mailing lists, an empty list will be returned.

```json
[
  {
    "id": "cm06f5v0e45nf0ml5754o9cix",
    "name": "Main list",
    "isPublic": true
  },
  {
    "id": "cm16k73gq014h0mmj5b6jdi9r",
    "name": "Investors",
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

#### Examples

```php
$result = $loops->events->send(
  'signup',
  email: 'hello@gmail.com'
);

$result = $loops->events->send(
  'signup',
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
  'signup',
  email: 'hello@gmail.com',
  user_id: '1234567890',
  contact_properties: [
    'firstName' => 'Bob',
    'plan' => 'pro',
  }]
});
```

#### Response

This method will return a success or error:

```json
{
  "success": true
}
```

```json
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

| Name                          | Type    | Required | Notes                                                                                                                                                                                            |
| ----------------------------- | ------- | -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `$transactional_id`           | string  | Yes      | The ID of the transactional email to send.                                                                                                                                                       |
| `$email`                      | string  | Yes      | The email address of the recipient.                                                                                                                                                              |
| `$add_to_audience`            | boolean | No       | If `true`, a contact will be created in your audience using the `$email` value (if a matching contact doesn’t already exist).                                                                    |
| `$data_variables`             | array   | No       | An array containing data as defined by the data variables added to the transactional email template.<br />Values can be of type `string` or `number`.                                            |
| `$attachments`                | array[] | No       | A list of attachments objects.<br />**Please note**: Attachments need to be enabled on your account before using them with the API. [Read more](https://loops.so/docs/transactional/attachments) |
| `$attachments[].filename`     | string  | No       | The name of the file, shown in email clients.                                                                                                                                                    |
| `$attachments[].content_type` | string  | No       | The MIME type of the file.                                                                                                                                                                       |
| `$attachments[].data`         | string  | No       | The base64-encoded content of the file.                                                                                                                                                          |

#### Examples

```php
$result = $loops->transactional->send(
  transactional_id: 'clfq6dinn000yl70fgwwyp82l',
  email: 'hello@gmail.com',
  data_variables: [
    'loginUrl' => 'https://myapp.com/login/',
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

This method will return a success or error message.

```json
{
  "success": true
}
```

If there is a problem with the request, a descriptive error message will be returned:

```json
{
  "success": false,
  "path": "dataVariables",
  "message": "There are required fields for this email. You need to include a 'dataVariables' array with the required fields."
}
```

```json
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

### customFields->getAll()

Get a list of your account's custom fields. These are custom properties that can be added to contacts to store extra data. [Read more about contact properties](https://loops.so/docs/contacts/properties)

[API Reference](https://loops.so/docs/api-reference/list-custom-fields)

#### Parameters

None

#### Example

```php
$result = $loops->customFields->getAll();
```

#### Response

This method will return a list of custom field objects containing `key`, `label` and `type` attributes.

If your account has no custom fields, an empty list will be returned.

```json
[
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

## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/Loops-so/loops-php.
