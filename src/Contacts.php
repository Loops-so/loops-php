<?php

namespace Loops;

use Loops\LoopsClient;

class Contacts
{
    private $client;

    public function __construct(LoopsClient $client)
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

        return $this->client->query(method: 'POST', endpoint: 'v1/contacts/create', options: [
            'json' => $payload
        ]);
    }

    public function update(?string $email = null, ?string $user_id = null, ?array $properties = [], ?array $mailing_lists = []): mixed
    {
        if (!$email && !$user_id) {
            throw new \InvalidArgumentException(message: 'You must provide an email or user_id value.');
        }
        $payload = [
            'email' => $email,
            'userId' => $user_id,
            'mailingLists' => $mailing_lists
        ];
        $payload = array_merge($payload, $properties);

        return $this->client->query(method: 'PUT', endpoint: 'v1/contacts/update', options: [
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

        return $this->client->query(method: 'GET', endpoint: 'v1/contacts/find', options: [
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

        return $this->client->query(method: 'POST', endpoint: 'v1/contacts/delete', options: [
            'json' => $payload
        ]);
    }
}