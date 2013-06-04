<?php
/**
 * @file
 * checkbook_homepage.features.inc
 */

/**
 * Implements hook_views_api().
 */
function checkbook_homepage_views_api() {
  list($module, $api) = func_get_args();
  if ($module == "views" && $api == "views_default") {
    return array("version" => "3.0");
  }
}

/**
 * Implements hook_node_info().
 */
function checkbook_homepage_node_info() {
  $items = array(
    'analysis' => array(
      'name' => t('Analysis'),
      'base' => 'node_content',
      'description' => t('Use this content type to create nodes for the homepage <em>Analysis</em> section.'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
    'spotlight' => array(
      'name' => t('Spotlight'),
      'base' => 'node_content',
      'description' => t('Use this content type to create nodes for the homepage <em>Spotlight</em> section.'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
    'video' => array(
      'name' => t('Video'),
      'base' => 'node_content',
      'description' => t('Use this content type to create videos for the homepage <em>Comptroller\'s Message</em> section.'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
  );
  return $items;
}
