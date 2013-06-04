<?php
/**
 * @file
 * checkbook_agencies.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function checkbook_agencies_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "feeds" && $api == "feeds_importer_default") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_views_api().
 */
function checkbook_agencies_views_api() {
  list($module, $api) = func_get_args();
  if ($module == "views" && $api == "views_default") {
    return array("version" => "3.0");
  }
}

/**
 * Implements hook_node_info().
 */
function checkbook_agencies_node_info() {
  $items = array(
    'checkbook_agencies' => array(
      'name' => t('Checkbook Agencies'),
      'base' => 'node_content',
      'description' => t('Stores the agency id, agency code and agency name'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
  );
  return $items;
}
