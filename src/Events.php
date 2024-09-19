<?php

namespace Loops;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Events
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function send(array $payload)
    {
        try {
            $response = $this->client->post('v1/events/send', ['json' => $payload]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}