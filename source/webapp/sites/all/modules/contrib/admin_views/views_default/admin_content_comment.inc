<?php

$view = new view;
$view->name = 'admin_content_comment';
$view->description = 'List and edit site comments and the comment moderation queue.';
$view->tag = 'admin';
$view->base_table = 'comment';
$view->human_name = '';
$view->core = 0;
$view->api_version = '3.0';
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

/* Display: Defaults */
$handler = $view->new_display('default', 'Defaults', 'default');
$handler->display->display_options['title'] = 'Comments';
$handler->display->display_options['css_class'] = 'admin-views-view';
$handler->display->display_options['use_ajax'] = TRUE;
$handler->display->display_options['access']['type'] = 'perm';
$handler->display->display_options['access']['perm'] = 'administer comments';
$handler->display->display_options['cache']['type'] = 'none';
$handler->display->display_options['query']['type'] = 'views_query';
$handler->display->display_options['query']['options']['query_comment'] = FALSE;
$handler->display->display_options['exposed_form']['type'] = 'basic';
$handler->display->display_options['pager']['type'] = 'full';
$handler->display->display_options['pager']['options']['items_per_page'] = 50;
$handler->display->display_options['style_plugin'] = 'table';
$handler->display->display_options['style_options']['columns'] = array(
  'subject' => 'subject',
  'name' => 'name',
  'title' => 'title',
  'timestamp' => 'timestamp',
  'edit_comment' => 'edit_comment',
  'delete_comment' => 'delete_comment',
);
$handler->display->display_options['style_options']['default'] = 'timestamp';
$handler->display->display_options['style_options']['info'] = array(
  'subject' => array(
    'sortable' => 1,
    'default_sort_order' => 'asc',
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
  'name' => array(
    'sortable' => 1,
    'default_sort_order' => 'asc',
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
  'title' => array(
    'sortable' => 1,
    'default_sort_order' => 'asc',
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
  'timestamp' => array(
    'sortable' => 1,
    'default_sort_order' => 'desc',
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
  'edit_comment' => array(
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
  'delete_comment' => array(
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
);
$handler->display->display_options['style_options']['override'] = 1;
$handler->display->display_options['style_options']['sticky'] = 1;
$handler->display->display_options['style_options']['empty_table'] = 0;
/* Relationship: Comment: Content */
$handler->display->display_options['relationships']['nid']['id'] = 'nid';
$handler->display->display_options['relationships']['nid']['table'] = 'comment';
$handler->display->display_options['relationships']['nid']['field'] = 'nid';
$handler->display->display_options['relationships']['nid']['label'] = 'Node';
$handler->display->display_options['relationships']['nid']['required'] = 0;
/* Field: Comment: Bulk operations */
$handler->display->display_options['fields']['views_bulk_operations']['id'] = 'views_bulk_operations';
$handler->display->display_options['fields']['views_bulk_operations']['table'] = 'comment';
$handler->display->display_options['fields']['views_bulk_operations']['field'] = 'views_bulk_operations';
$handler->display->display_options['fields']['views_bulk_operations']['label'] = '';
$handler->display->display_options['fields']['views_bulk_operations']['alter']['alter_text'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['alter']['make_link'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['alter']['absolute'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['alter']['external'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['alter']['replace_spaces'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['alter']['trim_whitespace'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['alter']['nl2br'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['alter']['word_boundary'] = 1;
$handler->display->display_options['fields']['views_bulk_operations']['alter']['ellipsis'] = 1;
$handler->display->display_options['fields']['views_bulk_operations']['alter']['strip_tags'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['alter']['trim'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['alter']['html'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['element_label_colon'] = FALSE;
$handler->display->display_options['fields']['views_bulk_operations']['element_default_classes'] = 1;
$handler->display->display_options['fields']['views_bulk_operations']['hide_empty'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['empty_zero'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['hide_alter_empty'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['vbo']['entity_load_capacity'] = '10';
$handler->display->display_options['fields']['views_bulk_operations']['vbo']['operations'] = array(
  'views_bulk_operations_delete_item' => array(
    'selected' => 1,
    'use_queue' => 1,
    'skip_confirmation' => 1,
    'override_label' => 1,
    'label' => 'Delete',
  ),
  'system_message_action' => array(
    'selected' => 0,
    'use_queue' => 0,
    'skip_confirmation' => 0,
    'override_label' => 0,
    'label' => '',
  ),
  'views_bulk_operations_script_action' => array(
    'selected' => 0,
    'use_queue' => 0,
    'skip_confirmation' => 0,
    'override_label' => 0,
    'label' => '',
  ),
  'views_bulk_operations_argument_selector_action' => array(
    'selected' => 0,
    'skip_confirmation' => 0,
    'override_label' => 0,
    'label' => '',
    'settings' => array(
      'url' => '',
    ),
  ),
  'comment_publish_action' => array(
    'selected' => 1,
    'use_queue' => 1,
    'skip_confirmation' => 1,
    'override_label' => 1,
    'label' => 'Publish',
  ),
  'system_goto_action' => array(
    'selected' => 0,
    'use_queue' => 0,
    'skip_confirmation' => 0,
    'override_label' => 0,
    'label' => '',
  ),
  'comment_save_action' => array(
    'selected' => 0,
    'use_queue' => 0,
    'skip_confirmation' => 0,
    'override_label' => 0,
    'label' => '',
  ),
  'system_send_email_action' => array(
    'selected' => 0,
    'use_queue' => 0,
    'skip_confirmation' => 0,
    'override_label' => 0,
    'label' => '',
  ),
  'comment_unpublish_action' => array(
    'selected' => 1,
    'use_queue' => 1,
    'skip_confirmation' => 1,
    'override_label' => 1,
    'label' => 'Unpublish',
  ),
  'comment_unpublish_by_keyword_action' => array(
    'selected' => 0,
    'use_queue' => 0,
    'skip_confirmation' => 0,
    'override_label' => 0,
    'label' => '',
  ),
);
$handler->display->display_options['fields']['views_bulk_operations']['vbo']['enable_select_all_pages'] = 1;
$handler->display->display_options['fields']['views_bulk_operations']['vbo']['display_type'] = '0';
$handler->display->display_options['fields']['views_bulk_operations']['vbo']['display_result'] = 1;
$handler->display->display_options['fields']['views_bulk_operations']['vbo']['merge_single_action'] = 0;
$handler->display->display_options['fields']['views_bulk_operations']['vbo']['force_single'] = 0;
/* Field: Comment: Title */
$handler->display->display_options['fields']['subject']['id'] = 'subject';
$handler->display->display_options['fields']['subject']['table'] = 'comment';
$handler->display->display_options['fields']['subject']['field'] = 'subject';
$handler->display->display_options['fields']['subject']['alter']['alter_text'] = 0;
$handler->display->display_options['fields']['subject']['alter']['make_link'] = 0;
$handler->display->display_options['fields']['subject']['alter']['word_boundary'] = 1;
$handler->display->display_options['fields']['subject']['alter']['ellipsis'] = 1;
$handler->display->display_options['fields']['subject']['alter']['strip_tags'] = 0;
$handler->display->display_options['fields']['subject']['alter']['trim'] = 0;
$handler->display->display_options['fields']['subject']['alter']['html'] = 0;
$handler->display->display_options['fields']['subject']['link_to_comment'] = 1;
/* Field: Comment: Author */
$handler->display->display_options['fields']['name']['id'] = 'name';
$handler->display->display_options['fields']['name']['table'] = 'comment';
$handler->display->display_options['fields']['name']['field'] = 'name';
$handler->display->display_options['fields']['name']['alter']['alter_text'] = 0;
$handler->display->display_options['fields']['name']['alter']['make_link'] = 0;
$handler->display->display_options['fields']['name']['alter']['word_boundary'] = 1;
$handler->display->display_options['fields']['name']['alter']['ellipsis'] = 1;
$handler->display->display_options['fields']['name']['alter']['strip_tags'] = 0;
$handler->display->display_options['fields']['name']['alter']['trim'] = 0;
$handler->display->display_options['fields']['name']['alter']['html'] = 0;
$handler->display->display_options['fields']['name']['link_to_user'] = 1;
/* Field: Content: Title */
$handler->display->display_options['fields']['title']['id'] = 'title';
$handler->display->display_options['fields']['title']['table'] = 'node';
$handler->display->display_options['fields']['title']['field'] = 'title';
$handler->display->display_options['fields']['title']['relationship'] = 'nid';
$handler->display->display_options['fields']['title']['label'] = 'Posted in';
$handler->display->display_options['fields']['title']['alter']['alter_text'] = 0;
$handler->display->display_options['fields']['title']['alter']['make_link'] = 0;
$handler->display->display_options['fields']['title']['alter']['max_length'] = '16';
$handler->display->display_options['fields']['title']['alter']['word_boundary'] = 1;
$handler->display->display_options['fields']['title']['alter']['ellipsis'] = 1;
$handler->display->display_options['fields']['title']['alter']['strip_tags'] = 0;
$handler->display->display_options['fields']['title']['alter']['trim'] = 1;
$handler->display->display_options['fields']['title']['alter']['html'] = 0;
$handler->display->display_options['fields']['title']['hide_empty'] = 0;
$handler->display->display_options['fields']['title']['empty_zero'] = 0;
$handler->display->display_options['fields']['title']['link_to_node'] = 1;
/* Field: Comment: Updated date */
$handler->display->display_options['fields']['timestamp']['id'] = 'timestamp';
$handler->display->display_options['fields']['timestamp']['table'] = 'comment';
$handler->display->display_options['fields']['timestamp']['field'] = 'changed';
$handler->display->display_options['fields']['timestamp']['label'] = 'Updated';
$handler->display->display_options['fields']['timestamp']['alter']['alter_text'] = 0;
$handler->display->display_options['fields']['timestamp']['alter']['make_link'] = 0;
$handler->display->display_options['fields']['timestamp']['alter']['absolute'] = 0;
$handler->display->display_options['fields']['timestamp']['alter']['external'] = 0;
$handler->display->display_options['fields']['timestamp']['alter']['replace_spaces'] = 0;
$handler->display->display_options['fields']['timestamp']['alter']['trim_whitespace'] = 0;
$handler->display->display_options['fields']['timestamp']['alter']['nl2br'] = 0;
$handler->display->display_options['fields']['timestamp']['alter']['word_boundary'] = 1;
$handler->display->display_options['fields']['timestamp']['alter']['ellipsis'] = 1;
$handler->display->display_options['fields']['timestamp']['alter']['strip_tags'] = 0;
$handler->display->display_options['fields']['timestamp']['alter']['trim'] = 0;
$handler->display->display_options['fields']['timestamp']['alter']['html'] = 0;
$handler->display->display_options['fields']['timestamp']['element_label_colon'] = 1;
$handler->display->display_options['fields']['timestamp']['element_default_classes'] = 1;
$handler->display->display_options['fields']['timestamp']['hide_empty'] = 0;
$handler->display->display_options['fields']['timestamp']['empty_zero'] = 0;
$handler->display->display_options['fields']['timestamp']['hide_alter_empty'] = 0;
$handler->display->display_options['fields']['timestamp']['date_format'] = 'time ago';
/* Field: Comment: Edit link */
$handler->display->display_options['fields']['edit_comment']['id'] = 'edit_comment';
$handler->display->display_options['fields']['edit_comment']['table'] = 'comment';
$handler->display->display_options['fields']['edit_comment']['field'] = 'edit_comment';
$handler->display->display_options['fields']['edit_comment']['label'] = '';
$handler->display->display_options['fields']['edit_comment']['alter']['alter_text'] = 0;
$handler->display->display_options['fields']['edit_comment']['alter']['make_link'] = 0;
$handler->display->display_options['fields']['edit_comment']['alter']['word_boundary'] = 1;
$handler->display->display_options['fields']['edit_comment']['alter']['ellipsis'] = 1;
$handler->display->display_options['fields']['edit_comment']['alter']['strip_tags'] = 0;
$handler->display->display_options['fields']['edit_comment']['alter']['trim'] = 0;
$handler->display->display_options['fields']['edit_comment']['alter']['html'] = 0;
$handler->display->display_options['fields']['edit_comment']['hide_empty'] = 0;
$handler->display->display_options['fields']['edit_comment']['empty_zero'] = 0;
/* Field: Comment: Delete link */
$handler->display->display_options['fields']['delete_comment']['id'] = 'delete_comment';
$handler->display->display_options['fields']['delete_comment']['table'] = 'comment';
$handler->display->display_options['fields']['delete_comment']['field'] = 'delete_comment';
$handler->display->display_options['fields']['delete_comment']['label'] = '';
$handler->display->display_options['fields']['delete_comment']['alter']['alter_text'] = 0;
$handler->display->display_options['fields']['delete_comment']['alter']['make_link'] = 0;
$handler->display->display_options['fields']['delete_comment']['alter']['word_boundary'] = 1;
$handler->display->display_options['fields']['delete_comment']['alter']['ellipsis'] = 1;
$handler->display->display_options['fields']['delete_comment']['alter']['strip_tags'] = 0;
$handler->display->display_options['fields']['delete_comment']['alter']['trim'] = 0;
$handler->display->display_options['fields']['delete_comment']['alter']['html'] = 0;
$handler->display->display_options['fields']['delete_comment']['hide_empty'] = 0;
$handler->display->display_options['fields']['delete_comment']['empty_zero'] = 0;
/* Sort criterion: Comment: Updated date */
$handler->display->display_options['sorts']['timestamp']['id'] = 'timestamp';
$handler->display->display_options['sorts']['timestamp']['table'] = 'comment';
$handler->display->display_options['sorts']['timestamp']['field'] = 'changed';
$handler->display->display_options['sorts']['timestamp']['order'] = 'DESC';
/* Filter criterion: Comment: Title */
$handler->display->display_options['filters']['subject']['id'] = 'subject';
$handler->display->display_options['filters']['subject']['table'] = 'comment';
$handler->display->display_options['filters']['subject']['field'] = 'subject';
$handler->display->display_options['filters']['subject']['operator'] = 'contains';
$handler->display->display_options['filters']['subject']['exposed'] = TRUE;
$handler->display->display_options['filters']['subject']['expose']['operator_id'] = 'subject_op';
$handler->display->display_options['filters']['subject']['expose']['label'] = 'Title';
$handler->display->display_options['filters']['subject']['expose']['use_operator'] = FALSE;
$handler->display->display_options['filters']['subject']['expose']['operator'] = 'subject_op';
$handler->display->display_options['filters']['subject']['expose']['identifier'] = 'subject';
/* Filter criterion: Comment: Author */
$handler->display->display_options['filters']['name']['id'] = 'name';
$handler->display->display_options['filters']['name']['table'] = 'comment';
$handler->display->display_options['filters']['name']['field'] = 'name';
$handler->display->display_options['filters']['name']['operator'] = 'contains';
$handler->display->display_options['filters']['name']['exposed'] = TRUE;
$handler->display->display_options['filters']['name']['expose']['operator_id'] = 'name_op';
$handler->display->display_options['filters']['name']['expose']['label'] = 'Author';
$handler->display->display_options['filters']['name']['expose']['use_operator'] = FALSE;
$handler->display->display_options['filters']['name']['expose']['operator'] = 'name_op';
$handler->display->display_options['filters']['name']['expose']['identifier'] = 'name';
/* Filter criterion: Content: Title */
$handler->display->display_options['filters']['title']['id'] = 'title';
$handler->display->display_options['filters']['title']['table'] = 'node';
$handler->display->display_options['filters']['title']['field'] = 'title';
$handler->display->display_options['filters']['title']['relationship'] = 'nid';
$handler->display->display_options['filters']['title']['operator'] = 'contains';
$handler->display->display_options['filters']['title']['exposed'] = TRUE;
$handler->display->display_options['filters']['title']['expose']['operator_id'] = 'title_op';
$handler->display->display_options['filters']['title']['expose']['label'] = 'Posted in';
$handler->display->display_options['filters']['title']['expose']['use_operator'] = FALSE;
$handler->display->display_options['filters']['title']['expose']['operator'] = 'title_op';
$handler->display->display_options['filters']['title']['expose']['identifier'] = 'title';
/* Filter criterion: Comment: Approved */
$handler->display->display_options['filters']['status']['id'] = 'status';
$handler->display->display_options['filters']['status']['table'] = 'comment';
$handler->display->display_options['filters']['status']['field'] = 'status';
$handler->display->display_options['filters']['status']['value'] = 'All';
$handler->display->display_options['filters']['status']['exposed'] = TRUE;
$handler->display->display_options['filters']['status']['expose']['label'] = 'Published';
$handler->display->display_options['filters']['status']['expose']['use_operator'] = FALSE;
$handler->display->display_options['filters']['status']['expose']['identifier'] = 'status';
$handler->display->display_options['filters']['status']['expose']['required'] = FALSE;
$handler->display->display_options['filters']['status']['expose']['remember'] = 1;

/* Display: System */
$handler = $view->new_display('system', 'System', 'system_1');
$handler->display->display_options['path'] = 'admin/content/comment';
$translatables['admin_content_comment'] = array(
  t('Defaults'),
  t('Comments'),
  t('more'),
  t('Apply'),
  t('Reset'),
  t('Sort by'),
  t('Asc'),
  t('Desc'),
  t('Items per page'),
  t('- All -'),
  t('Offset'),
  t('Node'),
  t('Title'),
  t('Author'),
  t('Posted in'),
  t('Updated'),
  t('Published'),
  t('System'),
);
