<?php

namespace SortMyCash\LifeSearch;

class LifeSearchLogger
{
  private const BASE_LOG = "LifeSearch Plugin Log - ";

  /**
   * Write to the WordPress log.
   *
   * @param string $log
   */
  public function writeLifeSearchLog(string $log)
  {
    if ($_ENV['LOGGING_ENABLED']) {
      if (is_array($log) || is_object($log)) {
        error_log(self::BASE_LOG . json_encode($log, JSON_PRETTY_PRINT));
      } else {
        error_log(self::BASE_LOG . $log);
      }
    }
  }
}
