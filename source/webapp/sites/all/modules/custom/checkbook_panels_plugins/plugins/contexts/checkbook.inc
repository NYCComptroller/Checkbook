<?php

/**
 * @file
 *
 * Plugin to provide a checkbook context
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  'title' => t('checkbook'),
  'description' => t('A context that is just a checkbook.'),
  'context' => 'ctools_context_create_checkbook',
  'keyword' => 'checkbook',
  'no ui' => TRUE,
  'context name' => 'checkbook',
  'convert list' => array(
    'raw' => t('Raw checkbook'),
    'html_safe' => t('HTML-safe checkbook'),
	'checkbook_value_for_id' => t('A value for the ID as string'),
  ),
  'convert' => 'ctools_context_checkbook_convert',
  'placeholder form' => array(
    '#type' => 'textfield',
    '#description' => t('Enter the checkbook for this context.'),
  ),
);

/**
 * implementation of checkbook context plugin
 * @param $empty
 * @param null $data
 * @param bool $conf
 * @return ctools_context
 */
function ctools_context_create_checkbook($empty, $data = NULL, $conf = FALSE) {
  // The input is expected to be an object as created by ctools_break_phrase
  // which contains a group of checkbook.

  $context = new ctools_context('checkbook');
  $context->plugin = 'checkbook';

  if ($empty) {
    return $context;
  }

  if ($data !== FALSE ) {
    $context->data = $data;
    $context->title = check_plain($data);   
    return $context;
  }

}

/**
 * Convert a context specific checkbook.
 * @param $context
 * @param $type
 * @return string
 */
function ctools_context_checkbook_convert($context, $type) {
 switch ($type) {
    case 'raw':
      return $context->data;
    case 'html_safe':
      return check_plain($context->data);
    case 'checkbook_value_for_id':
    	return _checkbook_project_get_name_for_argument($context->keyword,$context->data) ;    	  
  }
      
}

