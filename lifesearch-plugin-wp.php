<?php

/**
 * Plugin Name:  SortMyCash - LifeSearch Plugin
 * Plugin URI:   https://github.com/dmccarrick/sortmycash-lifesearch
 * Description:  A WordPress plugin to facilitate integration with LifeSearch's API.
 * Version:      1.0.2
 * Author:       Daniel McCarrick
 * Author URI:   https://github.com/dmccarrick/
 */

use GuzzleHttp\Client;
use SortMyCash\LifeSearch\ForminatorFormParser;
use SortMyCash\LifeSearch\LifeSearchClient;
use SortMyCash\LifeSearch\LifeSearchLogger;
use SortMyCash\LifeSearch\XMLBuilder;

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Below is the hook to fire the plugin when a forminator submission occurs.
add_action('forminator_custom_form_submit_before_set_fields', 'receive_form_data_for_life_search', 10, 3);

/**
 * Action called as a result of registering the hook, above.
 *
 * @param $entry - the entry model
 * @param int $formId
 * @param array $fieldDataArray
 */
function receive_form_data_for_life_search($entry, int $formId, array $fieldDataArray)
{
  $logger = new LifeSearchLogger();

  if ($_ENV['LIFESEARCH_FORM_ID'] != $formId || !$entry) {
    return;
  }

  $parser = new ForminatorFormParser($fieldDataArray);
  $dataArray = $parser->parse();

  $xmlBuilder = new XMLBuilder($dataArray);
  $xml = $xmlBuilder->buildXml();

  // Create a new client, so that the XML can be transmitted to LifeSearch.
  $client = new LifeSearchClient(new Client(), $xml);
  $result = $client->sendRequest();

  // Log the result.
  $logger->writeLifeSearchLog(json_encode($result, JSON_PRETTY_PRINT));
}
