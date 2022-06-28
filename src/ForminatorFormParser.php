<?php

namespace SortMyCash\LifeSearch;

use SortMyCash\LifeSearch\Contracts\ForminatorFormParserInterface;

class ForminatorFormParser implements ForminatorFormParserInterface
{
  protected const CONSENT_KEY_STRING = 'Yes,-I-agree-that-these-details-can-be-shared-with-SortMyCash';

  /** @var [] */
  protected $fieldDataArray;

  /**
   * ForminatorFormParser constructor.
   * @param array $fieldDataArray
   */
  public function __construct(array $fieldDataArray)
  {
    $this->setFieldDataArray($fieldDataArray);
  }

  /**
   * Parse the field data array to return sensible key value pairs from the forminator submission.
   *
   * @return array
   */
  public function parse(): array
  {
    $logger = new LifeSearchLogger();
    $dataArray = [];

    foreach ($this->getFieldDataArray() as $fieldData) {
      if (is_array($fieldData['value'])) {
        foreach ($fieldData['value'] as $key => $subFieldDataValue) {
          if (str_contains($subFieldDataValue, self::CONSENT_KEY_STRING)) {
            $dataArray['consent-opt-out'] = 'false';
          } else {
            $dataArray[$key] = $subFieldDataValue;
          }
        }
      } else {
        $dataArray[$fieldData['name']] = $fieldData['value'];
      }
    }

    return $dataArray;
  }

  /**
   * @param mixed $fieldDataArray
   * @return ForminatorFormParser
   */
  public function setFieldDataArray($fieldDataArray): ForminatorFormParser
  {
    $this->fieldDataArray = $fieldDataArray;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getFieldDataArray()
  {
    return $this->fieldDataArray;
  }
}
