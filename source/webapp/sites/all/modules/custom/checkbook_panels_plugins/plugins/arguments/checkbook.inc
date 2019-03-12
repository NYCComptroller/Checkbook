<?php

/**
 *
 * Plugin to provide an argument handler for a raw checkbook
 */
/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  'title' => t("checkbook"),
  // keyword to use for %substitution
  'keyword' => 'checkbook',
  'description' => t('A checkbook is a minimal context that simply holds a checkbook that can be used for some other purpose.'),
  'settings form' => 'ctools_checkbook_settings_form',
  'context' => 'ctools_checkbook_context',
  'placeholder form' => array(
    '#type' => 'textfield',
    '#description' => t('Enter a value for this argument'),
  ),
  'path placeholder' => 'ctools_checkbook_path_placeholder', // This is in pagemanager.
);

/**
 * Discover if this argument gives us the term we crave.
 * @param null $arg
 * @param null $conf
 * @param bool $empty
 * @return ctools_context
 */
function ctools_checkbook_context($arg = NULL, $conf = NULL, $empty = FALSE) {
  // If unset it wants a generic, unfilled context.
  if ($empty) {
    return ctools_context_create_empty('checkbook');
  }

  $context = ctools_context_create('checkbook', $arg);
  $context->original_argument = $arg;

  return $context;
}

/**
 * Settings form for the argument
 * @param $form
 * @param $form_state
 * @param $conf
 */
function ctools_checkbook_settings_form(&$form, &$form_state, $conf) {
  $form['settings']['use_tail'] = array(
    '#title' => t('Get all arguments after this one'),
    '#type' => 'checkbox',
    '#default_value' => !empty($conf['use_tail']),
    '#description' => t('If checked, this checkbook will include all arguments. For example, if the path is "path/%" and the user visits "path/foo/bar", if this is not checked the checkbook will be "foo". If it is checked the checkbook will be "foo/bar".'),
  );
//  return $form;
}

/**
 * Switch the placeholder based upon user settings.
 * @param $argument
 * @return string
 */
function ctools_checkbook_path_placeholder($argument) {
  if (empty($argument['settings']['use_tail'])) {
    return '%pm_arg';
  }
  else {
    return '%pm_arg_tail';
  }
}
