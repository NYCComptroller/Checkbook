<?php
/**
 * @file
 * checkbook_homepage.views_default.inc
 */

/**
 * Implements hook_views_default_views().
 */
function checkbook_homepage_views_default_views() {
  $export = array();

  $view = new view;
  $view->name = 'analysis';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'node';
  $view->human_name = 'Analysis';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Analysis';
  $handler->display->display_options['access']['type'] = 'perm';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['query']['options']['query_comment'] = FALSE;
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'some';
  $handler->display->display_options['pager']['options']['items_per_page'] = '2';
  $handler->display->display_options['style_plugin'] = 'default';
  $handler->display->display_options['row_plugin'] = 'fields';
  $handler->display->display_options['row_options']['hide_empty'] = 0;
  $handler->display->display_options['row_options']['default_field_elements'] = 1;
  /* Field: Content: Description */
  $handler->display->display_options['fields']['field_analysis_description']['id'] = 'field_analysis_description';
  $handler->display->display_options['fields']['field_analysis_description']['table'] = 'field_data_field_analysis_description';
  $handler->display->display_options['fields']['field_analysis_description']['field'] = 'field_analysis_description';
  $handler->display->display_options['fields']['field_analysis_description']['label'] = '';
  $handler->display->display_options['fields']['field_analysis_description']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_analysis_description']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_analysis_description']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_analysis_description']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_analysis_description']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_analysis_description']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_analysis_description']['field_api_classes'] = 0;
  /* Field: Content: Link */
  $handler->display->display_options['fields']['field_analysis_link']['id'] = 'field_analysis_link';
  $handler->display->display_options['fields']['field_analysis_link']['table'] = 'field_data_field_analysis_link';
  $handler->display->display_options['fields']['field_analysis_link']['field'] = 'field_analysis_link';
  $handler->display->display_options['fields']['field_analysis_link']['label'] = '';
  $handler->display->display_options['fields']['field_analysis_link']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_analysis_link']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_analysis_link']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_analysis_link']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_analysis_link']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_analysis_link']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_analysis_link']['click_sort_column'] = 'url';
  $handler->display->display_options['fields']['field_analysis_link']['field_api_classes'] = 0;
  /* Sort criterion: Content: Post date */
  $handler->display->display_options['sorts']['created']['id'] = 'created';
  $handler->display->display_options['sorts']['created']['table'] = 'node';
  $handler->display->display_options['sorts']['created']['field'] = 'created';
  $handler->display->display_options['sorts']['created']['order'] = 'DESC';
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
    'analysis' => 'analysis',
  );

  /* Display: Block */
  $handler = $view->new_display('block', 'Block', 'analysis');
  $handler->display->display_options['defaults']['style_plugin'] = FALSE;
  $handler->display->display_options['style_plugin'] = 'list';
  $handler->display->display_options['defaults']['style_options'] = FALSE;
  $handler->display->display_options['defaults']['row_plugin'] = FALSE;
  $handler->display->display_options['row_plugin'] = 'fields';
  $handler->display->display_options['defaults']['row_options'] = FALSE;
  $handler->display->display_options['block_description'] = 'Analysis';
  $export['analysis'] = $view;

  $view = new view;
  $view->name = 'comptrollers_message';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'node';
  $view->human_name = 'Comptroller\'s Message';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Comptroller\'s Message';
  $handler->display->display_options['access']['type'] = 'perm';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['query']['options']['query_comment'] = FALSE;
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'some';
  $handler->display->display_options['pager']['options']['items_per_page'] = '2';
  $handler->display->display_options['style_plugin'] = 'default';
  $handler->display->display_options['row_plugin'] = 'fields';
  $handler->display->display_options['row_options']['hide_empty'] = 0;
  $handler->display->display_options['row_options']['default_field_elements'] = 1;
  /* Field: Content: Embed Code */
  $handler->display->display_options['fields']['field_video_embed_code']['id'] = 'field_video_embed_code';
  $handler->display->display_options['fields']['field_video_embed_code']['table'] = 'field_data_field_video_embed_code';
  $handler->display->display_options['fields']['field_video_embed_code']['field'] = 'field_video_embed_code';
  $handler->display->display_options['fields']['field_video_embed_code']['label'] = '';
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_video_embed_code']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_video_embed_code']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_video_embed_code']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_video_embed_code']['field_api_classes'] = 0;
  /* Field: Content: Description */
  $handler->display->display_options['fields']['field_video_description']['id'] = 'field_video_description';
  $handler->display->display_options['fields']['field_video_description']['table'] = 'field_data_field_video_description';
  $handler->display->display_options['fields']['field_video_description']['field'] = 'field_video_description';
  $handler->display->display_options['fields']['field_video_description']['label'] = '';
  $handler->display->display_options['fields']['field_video_description']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_video_description']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_video_description']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_video_description']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_video_description']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_video_description']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_video_description']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_video_description']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_video_description']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_video_description']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_video_description']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_video_description']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_video_description']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_video_description']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_video_description']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_video_description']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_video_description']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_video_description']['field_api_classes'] = 0;
  /* Sort criterion: Content: Post date */
  $handler->display->display_options['sorts']['created']['id'] = 'created';
  $handler->display->display_options['sorts']['created']['table'] = 'node';
  $handler->display->display_options['sorts']['created']['field'] = 'created';
  $handler->display->display_options['sorts']['created']['order'] = 'DESC';
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
    'video' => 'video',
  );

  /* Display: Block */
  $handler = $view->new_display('block', 'Block', 'comptroller_message');
  $handler->display->display_options['defaults']['style_plugin'] = FALSE;
  $handler->display->display_options['style_plugin'] = 'default';
  $handler->display->display_options['defaults']['style_options'] = FALSE;
  $handler->display->display_options['defaults']['row_plugin'] = FALSE;
  $handler->display->display_options['row_plugin'] = 'fields';
  $handler->display->display_options['defaults']['row_options'] = FALSE;
  $handler->display->display_options['block_description'] = 'Comtroller\'s Message';
  $export['comptrollers_message'] = $view;

  $view = new view;
  $view->name = 'spotlight';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'node';
  $view->human_name = 'Spotlight';
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
  $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = 'Spotlight';
  $handler->display->display_options['access']['type'] = 'perm';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['query']['options']['query_comment'] = FALSE;
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'some';
  $handler->display->display_options['pager']['options']['items_per_page'] = '4';
  $handler->display->display_options['pager']['options']['offset'] = '0';
  $handler->display->display_options['style_plugin'] = 'default';
  $handler->display->display_options['row_plugin'] = 'fields';
  $handler->display->display_options['row_options']['hide_empty'] = 0;
  $handler->display->display_options['row_options']['default_field_elements'] = 1;
  /* Field: Content: Link */
  $handler->display->display_options['fields']['field_spotlight_link']['id'] = 'field_spotlight_link';
  $handler->display->display_options['fields']['field_spotlight_link']['table'] = 'field_data_field_spotlight_link';
  $handler->display->display_options['fields']['field_spotlight_link']['field'] = 'field_spotlight_link';
  $handler->display->display_options['fields']['field_spotlight_link']['label'] = '';
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['external'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['replace_spaces'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['trim_whitespace'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['nl2br'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['alter']['html'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['element_label_colon'] = FALSE;
  $handler->display->display_options['fields']['field_spotlight_link']['element_default_classes'] = 1;
  $handler->display->display_options['fields']['field_spotlight_link']['hide_empty'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['empty_zero'] = 0;
  $handler->display->display_options['fields']['field_spotlight_link']['hide_alter_empty'] = 1;
  $handler->display->display_options['fields']['field_spotlight_link']['click_sort_column'] = 'url';
  $handler->display->display_options['fields']['field_spotlight_link']['field_api_classes'] = 0;
  /* Sort criterion: Content: Post date */
  $handler->display->display_options['sorts']['created']['id'] = 'created';
  $handler->display->display_options['sorts']['created']['table'] = 'node';
  $handler->display->display_options['sorts']['created']['field'] = 'created';
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
    'spotlight' => 'spotlight',
  );

  /* Display: Block */
  $handler = $view->new_display('block', 'Block', 'spotlight');
  $handler->display->display_options['defaults']['style_plugin'] = FALSE;
  $handler->display->display_options['style_plugin'] = 'list';
  $handler->display->display_options['defaults']['style_options'] = FALSE;
  $handler->display->display_options['defaults']['row_plugin'] = FALSE;
  $handler->display->display_options['row_plugin'] = 'fields';
  $handler->display->display_options['defaults']['row_options'] = FALSE;
  $handler->display->display_options['block_description'] = 'Spotlight';
  $export['spotlight'] = $view;

  return $export;
}
