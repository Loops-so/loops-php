<?php

namespace Loops\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Loops\LoopsClient;
use Loops\Exceptions\APIError;
use Loops\Exceptions\RateLimitExceededError;
use PHPUnit\Framework\TestCase;

class LoopsClientTest extends TestCase
{
  private LoopsClient $client;
  private Client $mockHttpClient;

  protected function setUp(): void
  {
    // Create a mock HTTP client
    $this->mockHttpClient = $this->createMock(Client::class);

    // Create the LoopsClient with the mock
    $this->client = new LoopsClient('test_api_key');
    $this->client->setHttpClient($this->mockHttpClient);
  }

  public function testSuccessfulApiCall(): void
  {
    // Mock a successful API response
    $expectedResponse = ['success' => true, 'id' => '1234567890'];
    $responseBody = json_encode($expectedResponse);

    $this->mockHttpClient
      ->expects($this->once())
      ->method('post')
      ->with(
        'v1/contacts/create',
        $this->callback(function ($options) {
          // The json option is already an array, no need to decode
          $payload = $options['json'];
          return $payload['email'] === 'test@example.com'
            && $payload['name'] === 'Test User';
        })
      )
      ->willReturn(new Response(
        status: 200,
        headers: ['Content-Type' => 'application/json'],
        body: $responseBody
      ));

    // Make the API call
    $result = $this->client->contacts->create(
      email: 'test@example.com',
      properties: ['name' => 'Test User']
    );

    // Assert the response
    $this->assertEquals($expectedResponse, $result);
  }

  public function testApiErrorHandling(): void
  {
    // Mock an API error response
    $errorResponse = ['error' => 'Invalid request', 'message' => 'Bad request'];
    $responseBody = json_encode($errorResponse);

    $this->mockHttpClient
      ->expects($this->once())
      ->method('post')
      ->willReturn(new Response(
        status: 400,
        headers: ['Content-Type' => 'application/json'],
        body: $responseBody
      ));

    // Assert that the API error is thrown
    $this->expectException(APIError::class);
    $this->expectExceptionCode(400);

    $this->client->contacts->create(
      email: 'test@example.com',
      properties: ['name' => 'Test User']
    );
  }

  public function testRateLimitHandling(): void
  {
    // Mock a rate limit response
    $this->mockHttpClient
      ->expects($this->once())
      ->method('post')
      ->willReturn(new Response(
        status: 429,
        headers: [
          'Content-Type' => 'application/json',
          'x-ratelimit-limit' => ['10'],
          'x-ratelimit-remaining' => ['0']
        ],
        body: json_encode(['error' => 'Rate limit exceeded'])
      ));

    // Assert that the rate limit error is thrown
    $this->expectException(RateLimitExceededError::class);

    $this->client->contacts->create(
      email: 'test@example.com',
      properties: ['name' => 'Test User']
    );
  }

}