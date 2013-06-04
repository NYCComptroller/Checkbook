<?php

/**
 * The plugin that handles entity-attached views.
 */
class eva_plugin_display_entity extends views_plugin_display {
  function option_definition() {
    $options = parent::option_definition();

    $options['entity_type'] = array('default' => '');
    $options['bundles'] = array('default' => array());
    $options['argument_mode'] = array('default' => 'id');
    $options['default_argument'] = array('default' => '');
    $options['show_title'] = 0;

    return $options;
  }

  /**
   * Provide the summary for page options in the views UI.
   *
   * This output is returned as an array.
   */
  function options_summary(&$categories, &$options) {
    // It is very important to call the parent function here:
    parent::options_summary($categories, $options);

    $categories['entity_view'] = array(
      'title' => t('Entity content settings'),
      'column' => 'second',
      'build' => array(
        '#weight' => -10,
      ),
    );

    if ($entity_type = $this->get_option('entity_type')) {
      $entity_info = entity_get_info($entity_type);
      $type_name = $entity_info['label'];
      $bundle_names = array();
      foreach ($this->get_option('bundles') as $bundle) {
        $bundle_names[] = $entity_info['bundles'][$bundle]['label'];
      }
    }

    $options['entity_type'] = array(
      'category' => 'entity_view',
      'title' => t('Entity type'),
      'value' => empty($type_name) ? t('None') : $type_name,
    );

    $options['bundles'] = array(
      'category' => 'entity_view',
      'title' => t('Bundles'),
      'value' => empty($bundle_names) ? t('None') : implode(', ', $bundle_names),
    );

    $argument_mode = $this->get_option('argument_mode');
    $options['arguments'] = array(
      'category' => 'entity_view',
      'title' => t('Arguments'),
      'value' => empty($argument_mode) ? t('None') : check_plain($argument_mode),
    );

    $options['show_title'] = array(
      'category' => 'entity_view',
      'title' => t('Show title'),
      'value' => $this->get_option('show_title') ? t('Yes') : t('No'),
    );
  }

  /**
   * Provide the default form for setting options.
   */
  function options_form(&$form, &$form_state) {
    // It is very important to call the parent function here:
    parent::options_form($form, $form_state);
    
    $entity_info = entity_get_info();
    $entity_type = $this->get_option('entity_type');

    switch ($form_state['section']) {
      case 'entity_type':
        foreach ($entity_info as $type => $info) {
          if (!empty($info['view modes'])) {
            $entity_names[$type] = $info['label'];
          }
        }

        $form['#title'] .= t('Entity type');
        $form['entity_type'] = array(
          '#type' => 'radios',
          '#required' => TRUE,
          '#title' => t("Attach this display to the following entity type"),
          '#options' => $entity_names,
          '#default_value' => $this->get_option('entity_type'),
        );
        break;

      case 'bundles':
        foreach ($entity_info[$entity_type]['bundles'] as $bundle => $info) {
          $options[$bundle] = $info['label'];
        }
        $form['#title'] .= t('Bundles');
        $form['bundles'] = array(
          '#type' => 'checkboxes',
          '#title' => t("Attach this display to the following bundles"),
          '#options' => $options,
          '#default_value' => $this->get_option('bundles'),
        );
        break;

      case 'arguments':
        $form['#title'] .= t('Arguments');
        $default = $this->get_option('argument_mode');
        $options = array(
          'none' => t("No special handling"),
          'id' => t("Use the ID of the entity the view is attached to"),
          'token' => t("Use tokens from the entity the view is attached to"),
        );

        $form['argument_mode'] = array(
          '#type' => 'radios',
          '#title' => t("How should this display populate the view's arguments?"),
          '#options' => $options,
          '#default_value' => $default,
        );

        $form['default_argument'] = array(
          '#type' => 'textfield',
          '#default_value' => $this->get_option('default_argument'),
          '#description' => t('You may use token replacement to provide arguments based on the current entity. Separate arguments with "/".'),
        );
        break;

      case 'show_title':
        $form['#title'] .= t('Show title');
        $form['show_title'] = array(
          '#type' => 'checkbox',
          '#title' => t('Show the title of the view above the attached view.'),
          '#default_value' => $this->get_option('show_title'),
        );
        break;
    }

  }

  function options_submit(&$form, &$form_state) {
    // It is very important to call the parent function here:
    parent::options_submit($form, $form_state);

    switch ($form_state['section']) {
      case 'entity_type':
        $new_entity = $form_state['values']['entity_type'];
        $old_entity = $this->get_option('entity_type');

        $this->set_option('entity_type', $new_entity);
        if ($new_entity != $old_entity) {
          // Each entity has its own list of bundles and view modes. If there's
          // only one on the new type, we can select it automatically. Otherwise
          // we need to wipe the options and start over.
          $new_entity_info = entity_get_info($new_entity);
          
          $new_bundle_keys = array_keys($new_entity_info['bundles']);
          $new_bundles = array();
          if (count($new_bundle_keys) == 1) {
            $new_bundles[] = $new_bundle_keys[0];
          }
          $this->set_option('bundles', $new_bundles);
        }
        break;
      case 'bundles':
        $this->set_option('bundles', array_values(array_filter($form_state['values']['bundles'])));
        break;
      case 'arguments':
        $this->set_option('argument_mode', $form_state['values']['argument_mode']);
        if ($form_state['values']['argument_mode'] == 'token') {
          $this->set_option('default_argument', $form_state['values']['default_argument']);
        }
        else {
          $this->set_option('default_argument', NULL);
        }
        break;
      case 'show_title':
        $this->set_option('show_title', $form_state['values']['show_title']);
        break;
    }
  }

  function validate() {
    $errors = parent::validate();

    $entity_type = $this->get_option('entity_type');
    if (empty($entity_type)) {
      $errors[] = t('Display @display must have an entity type selected.', array('@display' => $this->display->display_title));
    }

    $bundles = $this->get_option('bundles');
    if (empty($bundles)) {
      $errors[] = t('Display @display must have at least one bundle selected.', array('@display' => $this->display->display_title));
    }

    return $errors;
  }

  /**
   * Node content views use exposed widgets only if AJAX is set.
   */
  function uses_exposed() {
    if ($this->use_ajax()) {
      return parent::uses_exposed();
    }
    return FALSE;
  }

  /**
   * We have to run token replacement before the arguments are used.
   */
  function pre_execute() {
    // Call the parent setup function so we do not lose data.
    parent::pre_execute();

    if (isset($this->view->current_entity)) {
      $entity = $this->view->current_entity;
      $entity_type = $this->view->display_handler->get_option('entity_type');
      $entity_info = entity_get_info($entity_type);
  
      $arg_mode = $this->view->display_handler->get_option('argument_mode');
      if ($arg_mode == 'token') {
        if ($token_string = $this->view->display_handler->get_option('default_argument')) {
          // Now do the token replacement.
          $token_values = eva_get_arguments_from_token_string($token_string, $entity_type, $entity);
          $new_args = array();
          // We have to be careful to only replace arguments that have tokens.
          foreach ($token_values as $key => $value) {
            $new_args[$key] = $value;
          }
  
          $this->view->args = $new_args;
        }
      }
      elseif ($arg_mode == 'id') {
        $this->view->args = array($entity->{$entity_info['entity keys']['id']});
      }
    }
  }

  /**
   * The display block handler returns the structure necessary for a block.
   */
  function execute() {
    // Prior to this being called, the $view should already be set to this
    // display, and arguments should be set on the view.
    if (!isset($this->view->override_path)) {
      $this->view->override_path = $_GET['q'];
    }

    $data = $this->view->render();
    if (!empty($this->view->result) || $this->get_option('empty') || !empty($this->view->style_plugin->definition['even empty'])) {
      return $data;
    }
  }
}
