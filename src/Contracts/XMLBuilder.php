<?php

namespace SortMyCash\LifeSearch\Contracts;


use SimpleXMLElement;
use \SortMyCash\LifeSearch\XMLBuilder;

/**
 * A class responsible for building a simple XML Document based on a received form submission.
 *
 * Class XMLBuilder
 * @package SortMyCash\LifeSearch
 */
interface XMLBuilderInterface
{
  public function buildXml();

  /**
   * @param array $formData
   * @return XMLBuilder
   */
  public function setFormData(array $formData): XMLBuilder;

  /**
   * @return array
   */
  public function getFormData(): array;

  /**
   * @param bool $asXml
   * @return SimpleXMLElement
   */
  public function getXml($asXml = false): ?SimpleXMLElement;
}