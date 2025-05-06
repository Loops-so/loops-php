<?php

namespace Loops;

use Loops\LoopsClient;

class MailingLists
{
    private $client;

    public function __construct(LoopsClient $client)
    {
        $this->client = $client;
    }

    public function get()
    {
        return $this->client->query(method: 'GET', endpoint: 'v1/lists');
    }
}
