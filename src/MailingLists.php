<?php

namespace Loops;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MailingLists
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getAll()
    {
        try {
            $response = $this->client->get('v1/lists');
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
