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

    $this->apiKey = new ApiKey(client: $this->httpClient);
    $this->contacts = new Contacts(client: $this->httpClient);
    $this->events = new Events(client: $this->httpClient);
    $this->mailingLists = new MailingLists(client: $this->httpClient);
    $this->transactional = new Transactional(client: $this->httpClient);
    $this->customFields = new CustomFields(client: $this->httpClient);
  }
}