<?php

namespace Loops;

use GuzzleHttp\Client;

class Contacts
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create(string $email, ?array $properties = [], ?array $mailing_lists = []): mixed
    {
        $payload = [
            'email' => $email,
            'mailingLists' => $mailing_lists
        ];
        $payload = array_merge($payload, $properties);

        return $this->client->query('POST', 'v1/contacts/create', [
            'json' => $payload
        ]);
    }

    public function update(string $email, ?array $properties = [], ?array $mailing_lists = []): mixed
    {
        $payload = [
            'email' => $email,
            'mailingLists' => $mailing_lists
        ];
        $payload = array_merge($payload, $properties);

        return $this->client->query('PUT', 'v1/contacts/update', [
            'json' => $payload
        ]);
    }

    public function find(?string $email = null, ?string $user_id = null): mixed
    {
        if ($email && $user_id) {
            throw new \InvalidArgumentException(message: 'Only one parameter is permitted.');
        }
        if (!$email && !$user_id) {
            throw new \InvalidArgumentException(message: 'You must provide an email or user_id value.');
        }
        $query = [];
        if ($email)
            $query['email'] = $email;
        if ($user_id)
            $query['userId'] = $user_id;

        return $this->client->query('GET', 'v1/contacts/find', [
            'query' => $query
        ]);
    }

    public function delete(?string $email = null, ?string $user_id = null): mixed
    {
        if ($email && $user_id) {
            throw new \InvalidArgumentException(message: 'Only one parameter is permitted.');
        }
        if (!$email && !$user_id) {
            throw new \InvalidArgumentException(message: 'You must provide an email or user_id value.');
        }

        $payload = [];
        if ($email)
            $payload['email'] = $email;
        if ($user_id)
            $payload['userId'] = $user_id;

        return $this->client->query('POST', 'v1/contacts/delete', [
            'json' => $payload
        ]);
    }
}