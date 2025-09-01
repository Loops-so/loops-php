<?php

namespace Tests;

use Loops\LoopsClient;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class ContactsTest extends TestCase
{
  private LoopsClient $client;

  private \GuzzleHttp\Client $mockHttpClient;

  protected function setUp(): void
  {
    // Create a mock HTTP client
    $this->mockHttpClient = $this->createMock(\GuzzleHttp\Client::class);

    // Create the LoopsClient with the mock
    $this->client = new LoopsClient('test_api_key');
    $this->client->setHttpClient($this->mockHttpClient);
  }


  public function testCreateContact(): void
  {
    $email = 'test@example.com';
    $properties = ['name' => 'Test User'];
    $mailingLists = ['123214' => true];

    // Configure mock to expect the correct API call
    $this->mockHttpClient
      ->expects($this->once())
      ->method('post')
      ->with(
        'v1/contacts/create',
        $this->callback(function ($options) use ($email, $properties, $mailingLists) {
          $payload = $options['json'];
          return $payload['email'] === $email
            && $payload['name'] === $properties['name']
            && $payload['mailingLists'] === $mailingLists;
        })
      )
      ->willReturn(new Response(
        status: 200,
        body: json_encode(['success' => true, 'id' => '12345'])
      ));

    // Make the API call
    $result = $this->client->contacts->create(
      email: $email,
      properties: $properties,
      mailing_lists: $mailingLists
    );

    // Assert the response
    $this->assertEquals(['success' => true, 'id' => '12345'], $result);
  }

  public function testFindContact(): void
  {
    $email = 'test@example.com';

    // Configure mock to expect the correct API call
    $this->mockHttpClient
      ->expects($this->once())
      ->method('get')
      ->with(
        'v1/contacts/find',
        $this->callback(function ($options) use ($email) {
          return $options['query']['email'] === $email;
        })
      )
      ->willReturn(new Response(
        status: 200,
        body: json_encode([
          [
            'id' => '12345',
            'email' => 'test@example.com',
            'name' => 'Test User',
            'mailing_lists' => ['list1' => true]
          ]
        ])
      ));

    // Make the API call
    $result = $this->client->contacts->find(
      email: $email
    );

    // Assert the response
    $this->assertEquals(expected: [
      [
        'id' => '12345',
        'email' => 'test@example.com',
        'name' => 'Test User',
        'mailing_lists' => ['list1' => true]
      ]
    ], actual: $result);
  }

  public function testUpdateContact(): void
  {
    $email = 'test@example.com';
    $properties = ['name' => 'Updated User'];
    $mailingLists = ['list2' => true];

    // Configure mock to expect the correct API call
    $this->mockHttpClient
      ->expects($this->once())
      ->method('put')
      ->with(
        'v1/contacts/update',
        $this->callback(function ($options) use ($email, $properties, $mailingLists) {
          $payload = $options['json'];
          return $payload['email'] === $email
            && $payload['name'] === $properties['name']
            && $payload['mailingLists'] === $mailingLists;
        })
      )
      ->willReturn(new Response(
        status: 200,
        body: json_encode(['success' => true, 'id' => '12345'])
      ));

    // Make the API call
    $result = $this->client->contacts->update(
      email: $email,
      properties: $properties,
      mailing_lists: $mailingLists
    );

    // Assert the response
    $this->assertEquals(['success' => true, 'id' => '12345'], $result);
  }

  public function testUpdateContactWithUserId(): void
  {
    $userId = 'abcdef123456';
    $properties = ['name' => 'Updated User'];
    $mailingLists = ['list2' => true];

    // Configure mock to expect the correct API call
    $this->mockHttpClient
      ->expects($this->once())
      ->method('put')
      ->with(
        'v1/contacts/update',
        $this->callback(callback: function (array $options) use ($userId, $properties, $mailingLists): bool {
          $payload = $options['json'];
          return $payload['userId'] === $userId
            && $payload['name'] === $properties['name']
            && $payload['mailingLists'] === $mailingLists;
        })
      )
      ->willReturn(new Response(
        status: 200,
        body: json_encode(['success' => true, 'id' => '12345'])
      ));

    // Make the API call
    $result = $this->client->contacts->update(
      user_id: $userId,
      properties: $properties,
      mailing_lists: $mailingLists
    );

    // Assert the response
    $this->assertEquals(['success' => true, 'id' => '12345'], $result);
  }

  public function testUpdateContactWithoutEmailAndUserId(): void
  {
    $this->expectException(exception: \InvalidArgumentException::class);
    $this->expectExceptionMessage(message: 'You must provide an email or user_id value.');

    // Make the API call without email or user_id
    $this->client->contacts->update(
      email: null,
      user_id: null
    );
  }

  public function testDeleteContact(): void
  {
    $email = 'test@example.com';

    // Configure mock to expect the correct API call
    $this->mockHttpClient
      ->expects($this->once())
      ->method('post')
      ->with(
        'v1/contacts/delete',
        $this->callback(function ($options) use ($email) {
          $payload = $options['json'];
          return $payload['email'] === $email;
        })
      )
      ->willReturn(new Response(
        status: 200,
        body: json_encode(['success' => true])
      ));

    // Make the API call
    $result = $this->client->contacts->delete(
      email: $email
    );

    // Assert the response
    $this->assertEquals(['success' => true], $result);
  }

}
