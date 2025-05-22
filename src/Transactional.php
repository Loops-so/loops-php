<?php

namespace Loops;

use Loops\LoopsClient;

class Transactional
{
    private $client;

    public function __construct(LoopsClient $client)
    {
        $this->client = $client;
    }

    public function send(
        string $transactional_id,
        string $email,
        ?bool $add_to_audience = false,
        ?array $data_variables = [],
        ?array $attachments = [], /** @var array<array{filename: string, content_type: string, data: string}> */
        ?array $headers = []
    ): mixed {
        $payload = [
            'transactional_id' => $transactional_id,
            'email' => $email,
            'add_to_audience' => $add_to_audience,
            'data_variables' => $data_variables,
            'attachments' => $attachments,
        ];

        return $this->client->query(method: 'POST', endpoint: 'v1/transactional', options: [
            'json' => $payload,
            'headers' => $headers
        ]);
    }

    public function get(?int $per_page = 20, ?string $cursor = null): mixed
    {

        $query = [
            'per_page' => $per_page
        ];
        if ($cursor)
            $query['cursor'] = $cursor;

        return $this->client->query(method: 'GET', endpoint: 'v1/transactional', options: [
            'query' => $query
        ]);
    }
}