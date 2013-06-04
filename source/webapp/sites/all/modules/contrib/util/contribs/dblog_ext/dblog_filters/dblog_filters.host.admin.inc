<?php

function dblog_host_filter_settings() {
  $form['dblog_host_filter:comparison'] = array(
    '#type' => 'radios',
    '#title' => t('Comparison operation'),
    '#default_value' => variable_get('dblog_host_filter:comparison', DBLOG_HOST_FILTER__DEFAULT_COMPARISON),
    '#required' => TRUE,
    '#options' => array(
      DBLOG_HOST_FILTER__COMPARISON_EQUALS => t('Exact match'),
      DBLOG_HOST_FILTER__COMPARISON_CONTAINS => t('Contains'),
      DBLOG_HOST_FILTER__COMPARISON_STARTS => t('Starts with'),
      DBLOG_HOST_FILTER__COMPARISON_ENDS => t('Ends with'),
    ),
  );
  
  return system_settings_form($form);
}
