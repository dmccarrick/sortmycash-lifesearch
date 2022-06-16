<?php

namespace SortMyCash\LifeSearch\Contracts;

use SortMyCash\LifeSearch\ForminatorFormParser;

interface ForminatorFormParserInterface
{
  /**
   * Parse the field data array to return sensible key value pairs from the forminator submission.
   *
   * @return array
   */
  public function parse(): array;

  /**
   * @param mixed $fieldDataArray
   * @return ForminatorFormParser
   */
  public function setFieldDataArray($fieldDataArray): ForminatorFormParser;

  /**
   * @return mixed
   */
  public function getFieldDataArray();
}