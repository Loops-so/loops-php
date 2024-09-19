<?php

namespace Loops;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Contacts
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create(string $email, array $properties = [], array $mailing_lists = [])
    {   
        $payload = [
            'email' => $email,
            'mailingLists' => $mailing_lists
        ];
        $payload = array_merge($payload, $properties);
        try {
            $response = $this->client->post('v1/contacts/create', ['json' => $payload]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update(string $email, array $properties = [], array $mailing_lists = [])
    {
        $payload = [
            'email' => $email,
            'mailingLists' => $mailing_lists
        ];
        $payload = array_merge($payload, $properties);
        error_log(json_encode($payload));
        try {
            $response = $this->client->put('v1/contacts/update', ['json' => $payload]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function find(string $email = null, string $user_id = null)
    {
        if ($email && $user_id) {
            throw new \InvalidArgumentException('Only one parameter is permitted.');
        }
        if (!$email && !$user_id) {
            throw new \InvalidArgumentException('You must provide an email or user_id value.');
        }
        $query = [];
        if ($email) $query['email'] = $email;
        if ($user_id) $query['userId'] = $user_id;

        try {
            $response = $this->client->get('v1/contacts/find', ['query' => $query]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete(string $email = null, string $user_id = null)
    {
        if ($email && $user_id) {
            throw new \InvalidArgumentException('Only one parameter is permitted.');
        }
        if (!$email && !$user_id) {
            throw new \InvalidArgumentException('You must provide an email or user_id value.');
        }
        $payload = [];
        if ($email) $payload['email'] = $email;
        if ($user_id) $payload['userId'] = $user_id;

        try {
            $response = $this->client->post('v1/contacts/delete', ['json' => $payload]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}