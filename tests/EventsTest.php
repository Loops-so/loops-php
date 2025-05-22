<?php

namespace Tests;

use Loops\LoopsClient;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class EventsTest extends TestCase
{
  private LoopsClient $client;
  private \GuzzleHttp\Client $mockHttpClient;

  protected function setUp(): void
  {
    // Create a mock HTTP client with default configuration
    $this->mockHttpClient = $this->createMock(\GuzzleHttp\Client::class);
    $this->mockHttpClient->method('getConfig')
      ->with('headers')
      ->willReturn([
        'Authorization' => 'Bearer test_api_key',
        'Content-Type' => 'application/json',
      ]);

    // Create the LoopsClient with the mock
    $this->client = new LoopsClient('test_api_key');
    $this->client->setHttpClient($this->mockHttpClient);
  }

  public function testSendEventWithEmail(): void
  {
    $event_name = 'test_event';
    $email = 'test@example.com';
    $contact_properties = ['name' => 'Test User'];
    $event_properties = ['value' => 100];
    $mailing_lists = ['list1' => true];
    $custom_headers = ['X-Custom-Header' => 'value'];

    // Configure mock to expect the correct API call
    $this->mockHttpClient
      ->expects($this->once())
      ->method('post')
      ->with(
        'v1/events/send',
        $this->callback(function ($options) use ($event_name, $email, $contact_properties, $event_properties, $mailing_lists, $custom_headers) {
          $payload = $options['json'];
          $expected_headers = array_merge([
            'Authorization' => 'Bearer test_api_key',
            'Content-Type' => 'application/json',
          ], $custom_headers);

          return $payload['eventName'] === $event_name
            && $payload['email'] === $email
            && $payload['name'] === $contact_properties['name']
            && $payload['eventProperties'] === $event_properties
            && $payload['mailingLists'] === $mailing_lists
            && $options['headers'] === $expected_headers;
        })
      )
      ->willReturn(new Response(
        status: 200,
        body: json_encode(['success' => true])
      ));

    // Make the API call
    $result = $this->client->events->send(
      event_name: $event_name,
      email: $email,
      contact_properties: $contact_properties,
      event_properties: $event_properties,
      mailing_lists: $mailing_lists,
      headers: $custom_headers
    );

    // Assert the response
    $this->assertEquals(['success' => true], $result);
  }

  public function testSendEventWithUserId(): void
  {
    $event_name = 'test_event';
    $user_id = 'user_123';
    $contact_properties = ['name' => 'Test User'];
    $event_properties = ['value' => 100];
    $mailing_lists = ['list1' => true];

    // Configure mock to expect the correct API call
    $this->mockHttpClient
      ->expects($this->once())
      ->method('post')
      ->with(
        'v1/events/send',
        $this->callback(function ($options) use ($event_name, $user_id, $contact_properties, $event_properties, $mailing_lists) {
          $payload = $options['json'];
          return $payload['eventName'] === $event_name
            && $payload['userId'] === $user_id
            && $payload['name'] === $contact_properties['name']
            && $payload['eventProperties'] === $event_properties
            && $payload['mailingLists'] === $mailing_lists;
        })
      )
      ->willReturn(new Response(
        status: 200,
        body: json_encode(['success' => true])
      ));

    // Make the API call
    $result = $this->client->events->send(
      event_name: $event_name,
      user_id: $user_id,
      contact_properties: $contact_properties,
      event_properties: $event_properties,
      mailing_lists: $mailing_lists
    );

    // Assert the response
    $this->assertEquals(['success' => true], $result);
  }

  public function testSendEventWithoutEmailOrUserId(): void
  {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('You must provide an email or user_id value.');

    $this->client->events->send(
      event_name: 'test_event'
    );
  }
}