<?php

/**
 * Plugin Name:  SortMyCash - LifeSearch Plugin
 * Plugin URI:   https://github.com/dmccarrick/sortmycash-lifesearch
 * Description:  A WordPress plugin to facilitate integration with LifeSearch's API.
 * Version:      0.1.0
 * Author:       Daniel McCarrick
 * Author URI:   https://github.com/dmccarrick/
 */

use SortMyCash\LifeSearch\XMLBuilder;

add_action('forminator_custom_form_submit_before_set_fields', 'receive_form_data_for_life_search');

const LIFE_SEARCH_FORM_ID = 1;

/**
 * Action called before setting fields to database
 *
 * @since 1.0.2
 *
 * @param $entry - the entry model
 * @param int $form_id - the form id
 * @param array $field_data_array - the entry data
 *
 */
function receive_form_data_for_life_search($entry, int $form_id, array $field_data_array) {
  if (LIFE_SEARCH_FORM_ID != $form_id) {
    return;
  }

  $xmlBuilder = new XMLBuilder($field_data_array);
  $xmlString = $xmlBuilder->buildXml();
  // Print out to the CLI, for now, will eventually be passed into a new client, to be transmitted to LifeSearch.
  print_r($xmlString);
}