<?php

namespace Loops;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ContactProperties
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws GuzzleException
     */
    public function create(string $name, string $type = 'string' | 'number' | 'boolean' | 'date'): mixed
    {
        $payload = [
            'name' => $name,
            'type' => $type
        ];

        return $this->client->request('POST', 'v1/contacts/properties', [
            'json' => $payload
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function get(string $list = null): mixed
    {
        $query = [];
        if ($list) {
            $query['list'] = $list;
        }
        return $this->client->request('GET', 'v1/contacts/properties', [
            'query' => $query
        ]);
    }
}