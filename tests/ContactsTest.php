<?php

namespace Tests;

use Loops\LoopsClient;
use PHPUnit\Framework\TestCase;

class ContactsTest extends TestCase
{
    private LoopsClient $client;

    protected function setUp(): void
    {
        $this->client = new LoopsClient('dummy-api-key');
    }

    public function test_createMethodWorksWithLoopsClient(): void
    {
        $email = 'test@example.com';
        $properties = ['name' => 'Test User'];
        $mailingLists = ['newsletter'];

        $this->client->contacts->create(
            email: $email,
            properties: $properties,
            mailing_lists: $mailingLists
        );
    }
} 