<?php


namespace SortMyCash\LifeSearch;

use SimpleXMLElement;

/**
 * A class responsible for building a simple XML Document based on a received form submission.
 *
 * Class XMLBuilder
 * @package SortMyCash\LifeSearch
 */
class XMLBuilder implements \SortMyCash\LifeSearch\Contracts\XMLBuilder
{
  const DEFAULT_PRODUCT_TYPE = "Term";
  const DEFAULT_DEATH_BENEFIT = true;
  const DEFAULT_TERM_YEARS = 20;
  const DEFAULT_COVER_AMOUNT = 200000;

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

  public function __construct(array $formData) {
    $this->setFormData($formData);
    $this->setXML(new SimpleXMLElement($this->getBaseXmlString()));
  }

  public function buildXml() {
    $xml = $this->getXml();

    $this->addApplicants($xml, $this->getFormData());
    $this->addQuoteRequestDefaults($xml);
    $this->addAffiliateDetails($xml);
  }

  /**
   * @param SimpleXMLElement $xml
   *
   * @param array $formData
   * @return SimpleXMLElement
   */
  private function addApplicants(SimpleXMLElement $xml, array $formData) {
    $subNode = $xml->addChild('Applicants');
    $contactSubNode = $subNode->addChild('Contact');
    $applicantSubNode = $subNode->addChild('Applicant life="first"');

    foreach($this->contactMappings as $key => $target) {
      $contactSubNode->addChild($key, $formData[$target]);
    }

    foreach($this->applicantMappings as $key => $target) {
      $contactSubNode->addChild($key, $formData[$target]);
    }

    return $xml;
  }

  /**
   * @param SimpleXMLElement $xml
   *
   * @return SimpleXMLElement
   */
  private function addQuoteRequestDefaults(SimpleXMLElement $xml) {
    $subNode = $xml->addChild('QuoteRequest');
    $subNode = $subNode->addChild('QuoteRequest number="1"');
    $subNode = $subNode->addChild('Products');
    $subNode = $subNode->addChild(sprintf('Product type="%s"', self::DEFAULT_PRODUCT_TYPE));
    $subNode->addChild('CoverType', self::DEFAULT_PRODUCT_TYPE);
    $subNode->addChild('DeathBenefit', self::DEFAULT_DEATH_BENEFIT);
    $subNode->addChild('Term type="Years"', self::DEFAULT_TERM_YEARS);
    $subNode->addChild('CoverAmount', self::DEFAULT_COVER_AMOUNT);

    return $xml;
  }

  /**
   * @param SimpleXMLElement $xml
   * @param string $enquiryRef
   *
   * @return SimpleXMLElement
   */
  private function addAffiliateDetails(SimpleXMLElement $xml, string $enquiryRef = '') {
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
  private function setXML(SimpleXMLElement $xml): XMLBuilder {
    $this->xml = $xml;
    return $this;
  }

  /**
   * @param bool $asXml
   * @return SimpleXMLElement
   */
  public function getXml($asXml = false): ?SimpleXMLElement
  {
    return $asXml ? $this->xml->asXML() : $this->xml;
  }
}