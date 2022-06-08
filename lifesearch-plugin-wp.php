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
use SortMyCash\LifeSearch\XMLBuilder;

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Below is the hook to fire the plugin when a forminator submission occurs.
add_action('forminator_custom_form_submit_before_set_fields', 'receive_form_data_for_life_search');

const LIFE_SEARCH_FORM_ID = 13977;

/**
 * Action called as a result of registering the hook, above.
 *
 * @param $entry - the entry model
 * @param int $form_id - the form id
 * @param array $field_data_array - the entry data
 *
 */
function receive_form_data_for_life_search($entry, int $form_id, array $field_data_array)
{
  if (LIFE_SEARCH_FORM_ID != $form_id || !$entry) {
    return;
  }

  $xmlBuilder = new XMLBuilder($field_data_array);
  $xml = $xmlBuilder->buildXml();

  // Create a new client, so that the XML can be transmitted to LifeSearch.
  $client = new LifeSearchClient(new Client(), $xml);
  $result = $client->sendRequest();

  // Output the result.
  print_r(json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL);
}
