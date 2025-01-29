<?php

namespace Loops;

use GuzzleHttp\Client;

class MailingLists
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get()
    {
        return $this->client->query('GET', 'v1/lists');
    }
}
