<?php

namespace Loops;

use Loops\LoopsClient;

class ApiKey
{
    private $client;

    public function __construct(LoopsClient $client)
    {
        $this->client = $client;
    }

    public function test(): mixed
    {
        return $this->client->query(method: 'GET', endpoint: 'v1/api-key');
    }
}
