<?php

namespace Loops\Exceptions;

use GuzzleHttp\Exception\GuzzleException;

class APIError extends \Exception
{
  private $statusCode;
  private $json;

  public function __construct(int $statusCode, ?array $json = null)
  {
    parent::__construct(message: $json['message'] ?? 'API Error');
    $this->statusCode = $statusCode;
    $this->json = $json;
  }

  public function getStatusCode(): int
  {
    return $this->statusCode;
  }

  public function getJson(): ?array
  {
    return $this->json;
  }
}