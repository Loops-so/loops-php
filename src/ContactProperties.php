<?php

namespace Loops;

use GuzzleHttp\Client;

class ContactProperties
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create(string $name, string $type = 'string' | 'number' | 'boolean' | 'date'): mixed
    {
        $payload = [
            'name' => $name,
            'type' => $type
        ];

        return $this->client->query('POST', 'v1/contacts/properties', [
            'json' => $payload
        ]);
    }
    public function get(string $list = null): mixed
    {
        $query = [];
        if ($list) {
            $query['list'] = $list;
        }
        return $this->client->query('GET', 'v1/contacts/properties', [
            'query' => $query
        ]);
    }
}