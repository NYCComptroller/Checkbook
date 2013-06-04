<?php

/**
 * @file
 * Hooks provided by Path Breadcrumbs module.
 */

/**
 * Expose Path Breadcrumbs settings.
 *
 * This hook is called by CTools. For this hook to work, you need
 * hook_ctools_plugin_api(). The values of this hook can be overridden
 * and reverted through the UI.
 */
function hook_path_breadcrumb_settings_info() {
  $path_breadcrumb = new stdClass();
  $path_breadcrumb->api_version = 1;
  $path_breadcrumb->path_id = '1';
  $path_breadcrumb->machine_name = 'example_breadcrumb';
  $path_breadcrumb->name = 'Example breadcrumb';
  $path_breadcrumb->path = 'node/%node';
  $path_breadcrumb->data = array(
    'titles' => '%node:title',
    'paths' => '%node:url',
    'home' => 1,
    'translatable' => 1,
    'arguments' => array(),
    'accesss' => array(),
  );
  $path_breadcrumb->weight = 0;
  $path_breadcrumb->disabled = 0;
  return $path_breadcrumb;
}
