<?php

namespace Loops;

use GuzzleHttp\Client;

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
        ?bool $add_to_audience = false,
        ?array $data_variables = [],
        ?array $attachments = [] /** @var array<array{filename: string, content_type: string, data: string}> */
    ) {
        $payload = [
            'transactional_id' => $transactional_id,
            'email' => $email,
            'add_to_audience' => $add_to_audience,
            'data_variables' => $data_variables,
            'attachments' => $attachments,
        ];

        return $this->client->query('POST', 'v1/transactional', [
            'json' => $payload
        ]);
    }
}