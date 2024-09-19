<?php

namespace Loops;

use GuzzleHttp\Client;

class LoopsClient
{
    private $httpClient;
    public $apiKey;
    public $contacts;
    public $events;
    public $mailingLists;
    public $transactional;
    public $customFields;

    public function __construct(string $api_key)
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://app.loops.so/api/',
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->apiKey = new ApiKey($this->httpClient);
        $this->contacts = new Contacts($this->httpClient);
        $this->events = new Events($this->httpClient);
        $this->mailingLists = new MailingLists($this->httpClient);
        $this->transactional = new Transactional($this->httpClient);
        $this->customFields = new CustomFields($this->httpClient);
    }
}