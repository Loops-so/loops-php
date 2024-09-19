<?php

namespace Loops;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiKey
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function test()
    {
        try {
            $response = $this->client->get('v1/api-key');
            return json_decode(json: $response->getBody()->getContents(), associative: true);
        } catch (GuzzleException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
