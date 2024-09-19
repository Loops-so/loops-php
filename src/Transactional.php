<?php

namespace Loops;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Transactional
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function send(
        string $transactional_id,
        string $email,
        bool $add_to_audience = false,
        array $data_variables = [],
        array $attachments = []
    ) {
        $payload = [
            'transactional_id' => $transactional_id,
            'email' => $email,
            'add_to_audience' => $add_to_audience,
            'data_variables' => $data_variables,
            'attachments' => $attachments,
        ];

        try {
            $response = $this->client->post('v1/transactional', ['json' => $payload]);
            return json_decode(json: $response->getBody()->getContents(), associative: true);
        } catch (GuzzleException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}