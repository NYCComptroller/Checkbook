<?php

/**
 * @file
 * date_all_day_test_feature.features.inc
 */

/**
 * Implements hook_node_info().
 */
function date_all_day_test_feature_node_info() {
  $items = array(
    'test_datetime' => array(
      'name' => t('Date All Day: Datetime'),
      'base' => 'node_content',
      'description' => t('This content type is used for testing the Date All Day module using the Datetime field type.'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
    'test_iso_date' => array(
      'name' => t('Date All Day: ISO Date'),
      'base' => 'node_content',
      'description' => t('This content type is used for testing the Date All Day module using the ISO Date field type.'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
    'test_timestamp' => array(
      'name' => t('Date All Day: Timestamp'),
      'base' => 'node_content',
      'description' => t('This content type is used for testing the Date All Day module using the UNIX Timestamp field type.'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
  );

  // Trigger hook_node_info_alter().
  drupal_alter('node_info', $items);

  return $items;
}
