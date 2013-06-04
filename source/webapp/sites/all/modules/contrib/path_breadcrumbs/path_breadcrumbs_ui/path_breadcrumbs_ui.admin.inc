<?php

/**
 * @file
 * Administrative callbacks for PATH BREADCRUMBS UI module.
 */

/**
 * Page callback for module settings page.
 */
function path_breadcrumbs_ui_breadcrumbs_list($form, $form_state) {

  // Load path breadcrumbs.
  $result = path_breadcrumbs_load_all();

  // NOTE: Is it too heavy?
  $form_state['storage']['objects'] = $result;
  $form['#tree'] = TRUE;

  foreach ($result as $path) {
    $form[$path->machine_name]['title']['#markup'] = $path->name;
    // If object is overwritten.
    if ($path->is_overwritten) {
      $form[$path->machine_name]['title']['#markup'] .= '<sup>'. $path->type .'</sup>';
    }
    $form[$path->machine_name]['name']['#markup'] = $path->machine_name;
    $form[$path->machine_name]['path']['#markup'] = $path->path;
    // All paths are in $form_state['values']['paths'].
    $form[$path->machine_name]['#parents'] = array('paths', $path->machine_name);

    // Create operations for current breadcrumb.
    $operations = array();

    if ($path->disabled == TRUE) {
      $operations[] = array(
        'title' => t('Enable'),
        'href' => 'admin/structure/path-breadcrumbs/enable/' . $path->machine_name,
      );
    }

    $operations[] = array(
      'title' => t('Edit'),
      'href' => 'admin/structure/path-breadcrumbs/edit/' . $path->machine_name,
    );
    $operations[] = array(
      'title' => t('Clone'),
      'href' => 'admin/structure/path-breadcrumbs/clone/' . $path->machine_name,
    );
    $operations[] = array(
      'title' => t('Export'),
      'href' => 'admin/structure/path-breadcrumbs/export/' . $path->machine_name,
    );
    if (empty($path->in_code_only)) {
      $operations[] = array(
        'title' => $path->is_overwritten ? t('Revert') : t('Delete'),
        'href' => 'admin/structure/path-breadcrumbs/delete/' . $path->machine_name,
      );
    }

    if ($path->disabled == FALSE) {
      $operations[] = array(
        'title' => t('Disable'),
        'href' => 'admin/structure/path-breadcrumbs/disable/' . $path->machine_name,
      );
    }

    $form[$path->machine_name]['actions']['#markup'] = theme('links__ctools_dropbutton',
      array('links' => $operations, 'attributes' => array('class' => array('links', 'inline'))));

    $form[$path->machine_name]['weight'] = array(
      '#type' => 'weight',
      '#default_value' => $path->weight,
      '#delta' => 100,
      '#attributes' => array('class' => array('path-breadcrumbs-ui-table-weight')),
    );

    $form[$path->machine_name]['disabled'] = array(
      '#type' => 'value',
      '#value' => $path->disabled,
    );
  }

  $form['actions'] = array(
    '#type' => 'action',
  );

  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
  );

  $form['#attached']['css'][] = drupal_get_path('module', 'path_breadcrumbs_ui') . '/path_breadcrumbs_ui.css';

  return $form;
}

/**
 * Submit callback for path_breadcrumbs_ui_breadcrumbs_list form.
 */
function path_breadcrumbs_ui_breadcrumbs_list_submit($form, &$form_state) {
  if (!empty($form_state['values']['paths'])) {
    foreach ($form_state['values']['paths'] as $machine_name => $value) {
      if (isset($value['weight'])) {
        $path_breadcrumbs = $form_state['storage']['objects'][$machine_name];
        $path_breadcrumbs->weight = $value['weight'];
        path_breadcrumbs_save($path_breadcrumbs);
      }
    }
  }

  drupal_set_message(t('Path breadcrumbs was updated.'));
}

/**
 * Path breadcrumbs settings form.
 */
function path_breadcrumbs_ui_settings($form, $form_state) {

  $form['path_breadcrumbs_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Breadcrumbs settings'),
  );

  $form['path_breadcrumbs_settings']['path_breadcrumbs_delimiter'] = array(
    '#type' => 'textfield',
    '#title' => t('Delimiter'),
    '#default_value' => variable_get('path_breadcrumbs_delimiter', '»'),
    '#size' => 3,
    '#description' => t('Symbol that separates breadcrumbs.'),
  );

  $form['path_breadcrumbs_settings']['path_breadcrumbs_rich_snippets'] = array(
    '#type' => 'select',
    '#title' => t('Rich snippets support'),
    '#options' => array(
      PATH_BREADCRUMBS_RICH_SNIPPETS_DISABLED => t('Disabled'),
      PATH_BREADCRUMBS_RICH_SNIPPETS_RDFA => t('RDFa'),
      PATH_BREADCRUMBS_RICH_SNIPPETS_MICRODATA => t('Microdata'),
    ),
    '#default_value' => variable_get('path_breadcrumbs_rich_snippets', PATH_BREADCRUMBS_RICH_SNIPPETS_DISABLED),
    '#description' => t('Provides rich snippets support for breadcrumbs. It is important to build a structure of a site in the SERP.')
  );

  $form['path_breadcrumbs_settings']['path_breadcrumbs_hide_single_breadcrumb'] = array(
    '#type' => 'checkbox',
    '#title' => t('Hide breadcrumbs navigation for single breadcrumb'),
    '#default_value' => variable_get('path_breadcrumbs_hide_single_breadcrumb', 0),
    '#description' => t('If breacrumbs navigation contains only one breadcrumb then breadcrumb navigation will be hidden.'),
  );

  $form['path_breadcrumbs_settings']['path_breadcrumbs_decode_entities'] = array(
    '#type' => 'checkbox',
    '#title' => t('Decode HTML entities'),
    '#description' => t('Converts quotes in the breadcrumbs to their applicable characters.'),
    '#default_value' => variable_get('path_breadcrumbs_decode_entities', TRUE),
  );

  $internal_render = variable_get('path_breadcrumbs_internal_render', 1);
  if (!$internal_render) {
    $form['path_breadcrumbs_settings']['path_breadcrumbs_delimiter']['#disabled'] = TRUE;
    $form['path_breadcrumbs_settings']['path_breadcrumbs_rich_snippets']['#disabled'] = TRUE;
    $form['path_breadcrumbs_settings']['path_breadcrumbs_hide_single_breadcrumb']['#disabled'] = TRUE;
    $notice = '<div style="color:red">' . t('You are not able to use this feature until internal render is disabled.') . '</div>';
    $form['path_breadcrumbs_settings']['path_breadcrumbs_delimiter']['#description'] .= $notice;
    $form['path_breadcrumbs_settings']['path_breadcrumbs_rich_snippets']['#description'] .= $notice;
    $form['path_breadcrumbs_settings']['path_breadcrumbs_hide_single_breadcrumb']['#description'] .= $notice;
  }

  $form['path_breadcrumbs_settings']['path_breadcrumbs_internal_render'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use module breadcrumbs render function'),
    '#default_value' => variable_get('path_breadcrumbs_internal_render', 1),
    '#description' => t("If this value checked module will replace system or theme breadcrumb render function by module's one.<br/>Note that if this checkbox is unchecked module will not be able to implement settings above.")
  );

  $form['path_breadcrumbs_home_link'] = array(
    '#type' => 'fieldset',
    '#title' => t('Home link settings'),
  );

  $form['path_breadcrumbs_home_link']['path_breadcrumbs_home_link_enabled'] = array(
    '#type' => 'checkbox',
    '#title' => t('Prepend breadcrumbs with a home link by default'),
    '#default_value' => variable_get('path_breadcrumbs_home_link_enabled', 1),
    '#description' => t('This value does not changes current breadcrumbs navigation. It is just set default value for new path breadcrumbs.'),
  );

  $form['path_breadcrumbs_home_link']['path_breadcrumbs_home_link_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Home link title'),
    '#default_value' => variable_get('path_breadcrumbs_home_link_title', 'Home'),
    '#description' => t('Title of the link that points to the front page.'),
    '#size' => 30,
  );

  $form['#submit'][] = 'path_breadcrumbs_ui_settings_submit';

  return system_settings_form($form);
}

/**
 * Submit function for path_breadcrumbs_ui_settings form.
 */
function path_breadcrumbs_ui_settings_submit($form, &$form_state) {
  // Flush theme registry if internal render was changed to enable/disable system render in theme_breadcrumb() function.
  $internal_render_new_value = $form_state['values']['path_breadcrumbs_internal_render'];
  $internal_render_old_value = variable_get('path_breadcrumbs_internal_render', 1);
  if ($internal_render_new_value != $internal_render_old_value) {
    drupal_theme_rebuild();
  }
}

/**
 * Form for path breadcrumbs clone.
 */
function path_breadcrumbs_clone_breadcrumb($path_breadcrumb) {
  $path_breadcrumb->machine_name .= '_clone';
  $path_breadcrumb->export_type = NULL;
  unset($path_breadcrumb->path_id);
  path_breadcrumbs_object_cache_set($path_breadcrumb->machine_name, $path_breadcrumb);
  $form_state = array('storage' => array('machine_name' => $path_breadcrumb->machine_name));
  return drupal_build_form('path_breadcrumbs_ui_add_form', $form_state);
}

/**
 * Form for object export.
 */
function path_breadcrumbs_export_form($form, $form_state, $path_breadcrumbs) {

  drupal_set_title(t('Export path breadcrumb "!name"', array('!name' => $path_breadcrumbs->name)));

  ctools_include('export');

  $code = ctools_export_crud_export('path_breadcrumbs', $path_breadcrumbs);
  $lines = substr_count($code, "\n");

  $form['export'] = array(
    '#title' => t('Export data'),
    '#type' => 'textarea',
    '#value' => $code,
    '#rows' => $lines,
    '#description' => t('Copy the export text and paste it into import area.'),
  );

  return $form;
}

/**
 * Form for object import.
 */
function path_breadcrumbs_import_form($form, $form_state) {

  $form['import'] = array(
    '#type' => 'textarea',
    '#title' => t('Paste code here to import path breadcrumbs'),
  );

  $form['actions'] = array(
    '#type' => 'action',
  );

  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Import'),
  );

  return $form;
}

/**
 * Submit callback for path breadcrumbs import form.
 */
function path_breadcrumbs_import_form_submit($form, &$form_state) {
  $code = $form_state['values']['import'];
  ctools_include('export');

  $path_breadcrumbs_raw = ctools_export_crud_import('path_breadcrumbs', $code);
  if (is_string($path_breadcrumbs_raw)) {
    form_error($form['import'], t('Unable to get an import from the code. Errors reported: @errors', array('@errors' => $path_breadcrumbs_raw)));
    $form_state['rebuild'] = TRUE;
    return;
  }
  // Because we get raw object from Ctools we should prepare it before saving.
  $path_breadcrumbs = path_breadcrumbs_load_prepare($path_breadcrumbs_raw);
  $path_breadcrumbs_original = path_breadcrumbs_load_by_name($path_breadcrumbs->machine_name);
  // If there is path breadcrumbs object with the same machine name,
  // delete the original so that this one writes properly.
  if (!empty($path_breadcrumbs_original)) {
    path_breadcrumbs_delete($path_breadcrumbs_original->machine_name);
  }
  if (path_breadcrumbs_save($path_breadcrumbs)) {
    if (!empty($path_breadcrumbs_original)) {
      drupal_set_message(filter_xss(t('Path breadcrumb "!name" was successfully updated.', array('!name' => $path_breadcrumbs->name))));
    }
    else {
      drupal_set_message(filter_xss(t('Path breadcrumb "!name" was successfully imported.', array('!name' => $path_breadcrumbs->name))));
    }
  }
  else {
    form_error($form['import'], t('Could not import path breadcrumb.'));
  }

  $form_state['redirect'] = 'admin/structure/path-breadcrumbs';
}
