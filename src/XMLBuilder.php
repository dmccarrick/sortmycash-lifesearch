<?php

namespace SortMyCash\LifeSearch;

use SimpleXMLElement;

/**
 * A class responsible for building a simple XML Document based on a received form submission.
 *
 * Class XMLBuilder
 * @package SortMyCash\LifeSearch
 */
class XMLBuilder implements Contracts\XMLBuilderInterface
{
  private const DEFAULT_PRODUCT_TYPE = "Term";
  private const DEFAULT_DEATH_BENEFIT = true;
  private const DEFAULT_TERM_YEARS = 20;
  private const DEFAULT_COVER_AMOUNT = 200000;
  private const DEFAULT_NUMBER_OF_APPLICANTS = 1;
  private const DEFAULT_LIFE_VALUE = 'first';
  private const DEFAULT_TERM_TYPE = 'Years';

  /** @var [] */
  private $formData = [];

  /** @var [] */
  private $applicantMappings = [
    'Title' => 'title',
    'FirstName' => 'first_name',
    'LastName' => 'last_name',
    'DOB' => 'date_of_birth'
  ];

  /** @var [] */
  private $contactMappings = [
    'Telephone' => 'tel_no',
    'Email' => 'email',
    'OptOut' => 'consent'
  ];

  /** @var string */
  private $baseXmlString = '<?xml version="1.0"?><Message type="Lead"></Message>';

  /** @var SimpleXMLElement */
  private $xml;

  public function __construct(array $formData)
  {
    $this->setFormData($formData);
    $this->setXML(new SimpleXMLElement($this->getBaseXmlString()));
  }

  /**
   * @return string
   */
  public function buildXml(): string
  {
    $xml = $this->getXml();

    $this->addApplicants($xml, $this->getFormData());
    $this->addQuoteRequestDefaults($xml);
    $this->addAffiliateDetails($xml);

    return $xml->asXML();
  }

  /**
   * @param SimpleXMLElement $xml
   *
   * @param array $formData
   * @return SimpleXMLElement
   */
  private function addApplicants(SimpleXMLElement $xml, array $formData): SimpleXMLElement
  {
    $subNode = $xml->addChild('Applicants');
    $contactSubNode = $subNode->addChild('Contact');
    $applicantSubNode = $subNode->addChild('Applicant');
    $applicantSubNode->addAttribute('life', self::DEFAULT_LIFE_VALUE);

    foreach ($this->contactMappings as $key => $target) {
      $contactSubNode->addChild($key, $formData[$target]);
    }

    foreach ($this->applicantMappings as $key => $target) {
      $applicantSubNode->addChild($key, $formData[$target]);
    }

    return $xml;
  }

  /**
   * @param SimpleXMLElement $xml
   *
   * @return SimpleXMLElement
   */
  private function addQuoteRequestDefaults(SimpleXMLElement $xml): SimpleXMLElement
  {
    $subNode = $xml->addChild('QuoteRequests');
    $subNode = $subNode->addChild('QuoteRequest');
    $subNode->addAttribute('number', self::DEFAULT_NUMBER_OF_APPLICANTS);
    $subNode = $subNode->addChild('Products');
    $subNode = $subNode->addChild('Product');
    $subNode->addAttribute('Type', self::DEFAULT_PRODUCT_TYPE);
    $subNode->addChild('CoverType', self::DEFAULT_PRODUCT_TYPE);
    $subNode->addChild('DeathBenefit', self::DEFAULT_DEATH_BENEFIT);
    $termNode = $subNode->addChild('Term', self::DEFAULT_TERM_YEARS);
    $termNode->addAttribute('Type', self::DEFAULT_TERM_TYPE);
    $subNode->addChild('CoverAmount', self::DEFAULT_COVER_AMOUNT);

    return $xml;
  }

  /**
   * @param SimpleXMLElement $xml
   * @param string $enquiryRef
   *
   * @return SimpleXMLElement
   */
  private function addAffiliateDetails(SimpleXMLElement $xml, string $enquiryRef = ''): SimpleXMLElement
  {
    $subNode = $xml->addChild('Partner');
    $subNode->addChild('BusinessSource', '');
    $subNode->addChild('AdCode', '');
    $subNode->addChild('Ref', $enquiryRef);

    return $xml;
  }

  /**
   * @param array $formData
   * @return XMLBuilder
   */
  public function setFormData(array $formData): XMLBuilder
  {
    $this->formData = $formData;
    return $this;
  }

  /**
   * @return array
   */
  public function getFormData(): array
  {
    return $this->formData;
  }

  /**
   * @return string
   */
  private function getBaseXmlString(): string
  {
    return $this->baseXmlString;
  }

  /**
   * @param SimpleXMLElement $xml
   * @return XMLBuilder
   */
  private function setXML(SimpleXMLElement $xml): XMLBuilder
  {
    $this->xml = $xml;
    return $this;
  }

  /**
   * @return SimpleXMLElement
   */
  public function getXml(): SimpleXMLElement
  {
    return $this->xml;
  }
}
