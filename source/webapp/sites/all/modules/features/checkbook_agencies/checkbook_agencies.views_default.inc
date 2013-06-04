<?php
/**
 * @file
 * checkbook_agencies.views_default.inc
 */

/**
 * Implements hook_views_default_views().
 */
function checkbook_agencies_views_default_views() {
  $export = array();

  $view = new view;
  $view->name = 'checkbook_agencies';
  $view->description = 'Displays all the available agencies with link to respective pages. view is exposed as a block and jquery is used for displaying.';
  $view->tag = 'default';
  $view->base_table = 'node';
  $view->human_name = 'Checkbook Agencies';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Checkbook Agencies';
  $handler->display->display_options['access']['type'] = 'perm';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['query']['options']['query_comment'] = FALSE;
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'none';
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['style_plugin'] = 'list';
  $handler->display->display_options['row_plugin'] = 'fields';
  /* Field: Content: Agency ID */
  $handler->display->display_options['fields']['field_checkbook_agency_id']['id'] = 'field_checkbook_agency_id';
  $handler->display->display_options['fields']['field_checkbook_agency_id']['table'] = 'field_data_field_checkbook_agency_id';
  $handler->display->display_options['fields']['field_checkbook_agency_id']['field'] = 'field_checkbook_agency_id';
  $handler->display->display_options['fields']['field_checkbook_agency_id']['label'] = '';
  $handler->display->display_options['fields']['field_checkbook_agency_id']['exclude'] = TRUE;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_checkbook_agency_id']['settings'] = array(
    'thousand_separator' => ' ',
    'prefix_suffix' => 1,
  );
  $handler->display->display_options['fields']['field_checkbook_agency_id']['field_api_classes'] = 0;
  /* Field: Content: Agency Name */
  $handler->display->display_options['fields']['field_checkbook_agency_name']['id'] = 'field_checkbook_agency_name';
  $handler->display->display_options['fields']['field_checkbook_agency_name']['table'] = 'field_data_field_checkbook_agency_name';
  $handler->display->display_options['fields']['field_checkbook_agency_name']['field'] = 'field_checkbook_agency_name';
  $handler->display->display_options['fields']['field_checkbook_agency_name']['label'] = '';
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['make_link'] = 1;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['path'] = 'abc/[field_checkbook_agency_id]';
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_checkbook_agency_name']['field_api_classes'] = 0;
  /* Sort criterion: Content: Agency Name (field_checkbook_agency_name) */
  $handler->display->display_options['sorts']['field_checkbook_agency_name_value']['id'] = 'field_checkbook_agency_name_value';
  $handler->display->display_options['sorts']['field_checkbook_agency_name_value']['table'] = 'field_data_field_checkbook_agency_name';
  $handler->display->display_options['sorts']['field_checkbook_agency_name_value']['field'] = 'field_checkbook_agency_name_value';
  /* Filter criterion: Content: Published */
  $handler->display->display_options['filters']['status']['id'] = 'status';
  $handler->display->display_options['filters']['status']['table'] = 'node';
  $handler->display->display_options['filters']['status']['field'] = 'status';
  $handler->display->display_options['filters']['status']['value'] = 1;
  $handler->display->display_options['filters']['status']['group'] = 0;
  $handler->display->display_options['filters']['status']['expose']['operator'] = FALSE;
  /* Filter criterion: Content: Type */
  $handler->display->display_options['filters']['type']['id'] = 'type';
  $handler->display->display_options['filters']['type']['table'] = 'node';
  $handler->display->display_options['filters']['type']['field'] = 'type';
  $handler->display->display_options['filters']['type']['value'] = array(
    'checkbook_agencies' => 'checkbook_agencies',
  );

  /* Display: Block */
  $handler = $view->new_display('block', 'Block', 'block');
  $handler->display->display_options['block_description'] = 'Agencies List';
  $export['checkbook_agencies'] = $view;

  return $export;
}
