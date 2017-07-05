<?php

/**
 * @file
 * The Node export features ui pages file.
 */

/**
 * Admin settings form.
 *
 * @param array $form
 *   The current form array.
 * @param array $form_state
 *   The current form array
 * @return array
 *   A form array
 */
function node_export_features_ui_settings($form, &$form_state) {

  $types = node_type_get_names();
  $filter_types = variable_get('node_export_features_ui_content_types', array());
  $default_types = array();
  if ( ! empty($filter_types) ) {
    foreach ( $types as $key => $value ) {
      if ( in_array($key, $filter_types)) {
        $default_types[$key] = $key;
      }
      else {
        $default_type[$key] = 0;
      }
    }
  }

  $form = array();

  $form['info'] = array(
    '#pre' => '<div id="node-export-features-ui-info">',
    '#markup' => t('The settings below will effect what nodes will get listed in the Node Export section of the Features UI. Note that each of these is an AND condition and so the limiting effect is additive.  Also, when rebuilding a feature, the same filter settings will need to be applied.'),
    '#suffix' => '</div>',
  );

  $form['node_export_features_ui_range'] = array(
    '#type' => 'textfield',
    '#title' => t('Number of nodes to list'),
    '#default_value' => variable_get('node_export_features_ui_range', 250),
    '#size' => 6,
    '#element_validate' => array('element_validate_integer_positive'),
    '#description' => t("The Features' UI and system can become unusable if a a large number of nodes are listed to be exported. This value is the maximum number of nodes tht will be displayed." ),
  );

  // Content types
  $form['content types'] = array(
    '#type' => 'fieldset',
    '#title' => t('Filter by Content Type'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#attributes' => array(
        'class' => array('node-form-options'),
    ),
  );

  $form['content types']['node_export_features_ui_content_types'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Content types to display'),
    '#default_value' => $default_types,
    '#options' => $types,
    '#description' => t('Which content types should be listed?'),
  );

  // Node options
  $form['options'] = array(
    '#type' => 'fieldset',
    '#title' => t('Filter by Publishing options'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#attributes' => array(
      'class' => array('node-form-options'),
    ),
  );

  $form['options']['node_export_features_ui_status'] = array(
    '#type' => 'select',
    '#options' => array('Any', 'Unpublished', 'Published'),
    '#title' => t('Published'),
    '#default_value' => variable_get('node_export_features_ui_status', 0),
  );
  $form['options']['node_export_features_ui_promote'] = array(
    '#type' => 'select',
    '#options' => array('Any', 'Unpromoted', 'Promoted'),
    '#title' => t('Promoted to front page'),
    '#default_value' => variable_get('node_export_features_ui_promote', 0),
  );
  $form['options']['node_export_features_ui_sticky'] = array(
    '#type' => 'select',
    '#options' => array('Any', 'Not Sticky', 'Sticky'),
    '#title' => t('Sticky at top of lists'),
    '#default_value' => variable_get('node_export_features_ui_sticky', 0),
  );
  // Specific node filters
  $form['node info'] = array(
      '#type' => 'fieldset',
      '#title' => t('Filter by Specific Node Info'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#attributes' => array(
          'class' => array('node-form-options'),
      ),
  );

  $form['node info']['node_export_features_ui_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Title match'),
      '#default_value' => variable_get('node_export_features_ui_title', ''),
      '#size' => 60,
      '#description' => t("An SQL 'LIKE' match for titles. Use % to match any substring and _ to match a single character. For example, %Test_Node% to find any title like A Test Node for... or A Test-Node for..." ),
  );

  $form['node info']['node_export_features_ui_uuids'] = array(
    '#type' => 'textarea',
    '#title' => t('Specific node UUIDs to display'),
    '#default_value' => variable_get('node_export_features_ui_uuids', ''),
    '#description' => t('Enter node UUIDs to list.  One uuid per line.'),
    '#cols' => 20,
    '#rows' => 10,
  );

  return system_settings_form($form);
}

/**
 * Validate and normalize some of the inputs.
 *
 * @param array $form
 *   The form array
 * @param array $form_state
 *   The current form state.
 */
function node_export_features_ui_settings_validate($form, &$form_state ) {

  module_load_include('inc', 'uuid');

  $values = $form_state['values'];
  $field = 'node_export_features_ui_uuids';
  $uuids = node_export_features_ui_textarea_to_array($values[$field]);
  $invalid_uuids = array();
  foreach ( $uuids as $uuid ) {
    if ( ! uuid_is_valid($uuid) ) {
      $invalid_uuids[] = $uuid;
    }
  }
  if ( empty($invalid_uuids) ) {
    $form_state['values'][$field] = implode("\r\n", $uuids);
  }
  else {
    form_set_error($field, t("Invalid UUID(s) found: ") . check_plain(implode(',', $invalid_uuids)));
  }

  // Convert type options to wanted type array
  $field = 'node_export_features_ui_content_types';
  $types = array();
  foreach ( $values[$field] as $type ) {
    if ( $type ) {
      $types[] = $type;
    }
  }
  $form_state['values'][$field] = $types;
}
