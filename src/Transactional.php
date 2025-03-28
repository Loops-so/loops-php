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
    ): mixed {
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
    public function get(?int $per_page = 20, ?string $cursor = null): mixed
    {

        $query = [
            'per_page' => $per_page
        ];
        if ($cursor)
            $query['cursor'] = $cursor;

        return $this->client->query('GET', 'v1/transactional', [
            'query' => $query
        ]);
    }
}