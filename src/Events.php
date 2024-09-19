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

    public function send(string $event_name, string $email = null, string $user_id = null, array $contact_properties = [], array $event_properties = [], array $mailing_lists = [])
    {
        if (!$email && !$user_id) {
            throw new \InvalidArgumentException('You must provide an email or user_id value.');
        }

        $payload = [
            'eventName' => $event_name,
            'email' => $email,
            'userId' => $user_id,
            'eventProperties' => $event_properties,
            'mailingLists' => $mailing_lists,
        ];

        $payload = array_merge($payload, $contact_properties);

        try {
            $response = $this->client->post('v1/events/send', ['json' => $payload]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}