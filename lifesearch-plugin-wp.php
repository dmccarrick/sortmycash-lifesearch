<?php

/**
 * Plugin Name:  SortMyCash - LifeSearch Plugin
 * Plugin URI:   https://github.com/dmccarrick/sortmycash-lifesearch
 * Description:  A WordPress plugin to facilitate integration with LifeSearch's API.
 * Version:      0.1.0
 * Author:       Daniel McCarrick
 * Author URI:   https://github.com/dmccarrick/
 */

use GuzzleHttp\Client;
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
    $logger->writeLifeSearchLog("Incorrect Form ID: " . $formId);
    return;
  }

  $dataArray = [];
  foreach($fieldDataArray as $fieldData) {
    if (is_array($fieldData['value'])) {
      foreach ($fieldData['value'] as  $key => $subFieldDataValue) {
        if ($subFieldDataValue = 'Yes,-I-agree-that-these-details-can-be-shared-with-SortMyCashu2019s-Life-Insurance-partner,-LifeSearch-in-order-for-them-to-contact-me') {
          $dataArray['consent'] = 'True';
        } else {
          $dataArray[$key] = $subFieldDataValue;
        }
      }
    } else {
      $dataArray[$fieldData['name']] = $fieldData['value'];
    }
  }

  $logger->writeLifeSearchLog("Field Data: " . substr(json_encode($dataArray, JSON_PRETTY_PRINT), 0, 1000));

  $xmlBuilder = new XMLBuilder($fieldDataArray);
  $xml = $xmlBuilder->buildXml();

  // Create a new client, so that the XML can be transmitted to LifeSearch.
  $client = new LifeSearchClient(new Client(), $xml);
  $result = $client->sendRequest();

  // Log the result.
  $logger->writeLifeSearchLog(json_encode($result, JSON_PRETTY_PRINT));
}
