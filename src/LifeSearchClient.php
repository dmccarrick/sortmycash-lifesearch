<?php

namespace SortMyCash\LifeSearch;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SimpleXMLElement;

class LifeSearchClient implements Contracts\LifeSearchClientInterface
{
  /**
   * @var Client
   */
  private $client;

  /**
   * @var SimpleXMLElement
   */
  private $xml;

  /**
   * @var string[]
   */
  private $headers = [
    'headers' => [
      'Content-Type' => 'application/xml',
      'Accept' => 'application/xml'
    ]
  ];

  private $method = 'POST';

  /**
   * LifeSearchClient constructor.
   * @param Client $client
   * @param SimpleXMLElement $xml
   */
  public function __construct(Client $client, SimpleXMLElement $xml)
  {
    $this->client = $client;
    $this->xml = $xml;
  }

  /**
   * Attempt to send the XML message to LifeSearch.
   *
   * @return array
   */
  public function sendRequest(): array
  {
    try {
      $response = $this->client->request($this->method, $this->getUri(), $this->getOptions());
    } catch (GuzzleException $e) {
      return [
        'response_code' => $e->getCode(),
        'message' => $e->getMessage()
      ];
    }

    return [
      'response_code' => $response->getStatusCode(),
      'message' => $response->getBody()->getContents()
    ];
  }

  /**
   * Returns the correct URI, based on the .env setup.
   *
   * @return mixed
   */
  private function getUri()
  {
    if ("STAGING" == $_ENV['MODE']) {
      return $_ENV['LIFESEARCH_ENDPOINT_STAGING'];
    }

    return $_ENV['LIFESEARCH_ENDPOINT_PRODUCTION'];
  }


  /**
   * Returns the headers and body for the request.
   *
   * @return array
   */
  private function getOptions()
  {
    return array_merge($this->headers, ['body' => $this->xml->asXML()]);
  }
}
