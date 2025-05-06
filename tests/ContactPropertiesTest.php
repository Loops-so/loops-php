<?php

namespace Tests;

use Loops\LoopsClient;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class ContactPropertiesTest extends TestCase
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


  public function testCreateContactProperty(): void
  {
    // Mock a successful property creation
    $property = [
      'name' => 'company',
      'type' => 'string'
    ];
    $expectedResponse = [
      'success' => true,
      'property' => $property
    ];

    $this->mockHttpClient
      ->expects($this->once())
      ->method('post')
      ->with(
        'v1/contacts/properties',
        $this->callback(function ($options) use ($property) {
          // The json option is already an array, no need to decode
          $payload = $options['json'];
          return $payload['name'] === $property['name'] && $payload['type'] === $property['type'];
        })
      )
      ->willReturn(new Response(
        status: 200,
        headers: ['Content-Type' => 'application/json'],
        body: json_encode($expectedResponse)
      ));

    $result = $this->client->contactProperties->create($property['name'], $property['type']);
    $this->assertEquals($expectedResponse, $result);
  }

}
