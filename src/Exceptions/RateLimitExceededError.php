<?php

namespace Loops\Exceptions;

class RateLimitExceededError extends \Exception
{
  private $limit;
  private $remaining;

  public function __construct(int $limit, int $remaining)
  {
    parent::__construct(message: 'Rate limit of ' . $limit . ' requests per second exceeded.');
    $this->limit = $limit;
    $this->remaining = $remaining;
  }

  public function getLimit(): int
  {
    return $this->limit;
  }

  public function getRemaining(): int
  {
    return $this->remaining;
  }
}