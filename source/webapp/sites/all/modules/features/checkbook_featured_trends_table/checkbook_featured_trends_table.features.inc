<?php
/**
 * @file
 * checkbook_featured_trends_table.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function checkbook_featured_trends_table_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "page_manager" && $api == "pages_default") {
    return array("version" => "1");
  }
}
