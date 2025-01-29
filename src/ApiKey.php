<?php

namespace Loops;

use GuzzleHttp\Client;

class ApiKey
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function test(): mixed
    {
        return $this->client->query('GET', 'v1/api-key');
    }
}
