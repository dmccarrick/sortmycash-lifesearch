<?php

namespace SortMyCash\LifeSearch;

use DateTime;
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
  private const DEFAULT_DEATH_BENEFIT = "True";
  private const DEFAULT_TERM_YEARS = 20;
  private const DEFAULT_COVER_AMOUNT = 200000;
  private const DEFAULT_NUMBER_OF_APPLICANTS = 1;
  private const DEFAULT_LIFE_VALUE = 'first';
  private const DEFAULT_LIVES_COVERED = 'First';
  private const DEFAULT_SMOKER_VALUE = 'false';
  private const DEFAULT_TERM_TYPE = 'Years';
  private const DEFAULT_BUSINESS_SOURCE = 'SortMyCash';
  private const DEFAULT_ENQUIRY_REF = 'TESTREF';
  private const DEFAULT_AD_CODE = 'Web';
  private const DEFAULT_ADDRESS = '1 XXXXXXXXX';

  /** @var [] */
  private $formData = [];

  /** @var [] */
  private $applicantMappings = [
    'Title' => 'select-1',
    'FirstName' => 'name-1',
    'LastName' => 'name-2',
    'DOB' => 'date-1'
  ];

  /** @var [] */
  private $contactMappings = [
    'Telephone' => 'phone-1',
    'Email' => 'email-1',
    'Postcode' => 'zip',
    'OptOut' => 'consent-opt-out'
  ];

  /** @var string */
  private $baseXmlString = '<Message type="Lead"></Message>';

  /** @var SimpleXMLElement */
  private $xml;

  public function __construct(array $formData)
  {
    $this->setFormData($formData);
    $this->setXML(new SimpleXMLElement($this->getBaseXmlString()));
  }

  /**
   * @return SimpleXMLElement
   */
  public function buildXml(): SimpleXMLElement
  {
    $xml = $this->getXml();

    $this->addApplicants($xml, $this->getFormData());
    $this->addQuoteRequestDefaults($xml);
    $this->addAffiliateDetails($xml);

    return $xml;
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
    $contactSubNode->addChild('Address', self::DEFAULT_ADDRESS);
    $applicantSubNode = $subNode->addChild('Applicant');
    $applicantSubNode->addAttribute('life', self::DEFAULT_LIFE_VALUE);
    $applicantSubNode->addChild('smoker', self::DEFAULT_SMOKER_VALUE);

    foreach ($this->contactMappings as $key => $target) {
      $contactSubNode->addChild($key, $formData[$target]);
    }

    foreach ($this->applicantMappings as $key => $target) {
      if ($target == 'date-1') {
        $formData[$target] = DateTime::createFromFormat('d-m-Y', $formData[$target])->format('Y-m-d');
      }

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
    $subNode->addChild('LivesCovered', self::DEFAULT_LIVES_COVERED);
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
    $subNode->addChild('BusinessSource', self::DEFAULT_BUSINESS_SOURCE);
    $subNode->addChild('AdCode', self::DEFAULT_AD_CODE);
    $subNode->addChild('Ref', $enquiryRef ? $enquiryRef : self::DEFAULT_ENQUIRY_REF);

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
