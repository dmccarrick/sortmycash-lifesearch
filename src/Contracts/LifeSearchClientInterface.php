<?php

namespace SortMyCash\LifeSearch\Contracts;

interface LifeSearchClientInterface
{
  /**
   * Attempt to send the XML message to LifeSearch.
   *
   * @return array
   */
  public function sendRequest(): array;
}
