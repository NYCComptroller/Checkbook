<?php

function dblog_filters_recent_aged_settings() {
  $form['time_thresholds'] = array(
    '#type' => 'fieldset',
    '#title' => t('Time thresholds'),
    '#description' => t('Comma separated thresholds times (in minutes) for log entries time filters.')
      . '<br/>' . t('Leave empty to disable respective time filter.'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  
  $form['time_thresholds']['dblog_time_filters:recent'] = array(
    '#type' => 'textfield',
    '#title' => t('Recent'),
    '#default_value' => implode(', ', variable_get('dblog_time_filters:recent', unserialize(DBLOB_FILTERS__DEFAULT_RECENT))),
  );
  
  $form['time_thresholds']['dblog_time_filters:aged'] = array(
    '#type' => 'textfield',
    '#title' => t('Aged'),
    '#default_value' => implode(', ', variable_get('dblog_time_filters:aged', unserialize(DBLOB_FILTERS__DEFAULT_AGED))),
  );
  
  return system_settings_form($form);
}

function dblog_filters_recent_aged_settings_validate($form, &$form_state) {
  foreach(array('recent', 'aged') as $key) {
    $strings = explode(',', $form_state['values']["dblog_time_filters:$key"]);
    $error[$key] = FALSE;
    $values[$key] = array();
    foreach($strings as $str) {
      if (!is_numeric(trim($str)) || trim($str) != (int)$str) {
        $error[$key] = $str;
        break;
      } 
      $values[$key][] = (int)$str; // casting trims the string
    }
    if ($error[$key]) form_set_error("dblog_time_filters:$key", t('Thresholds must be comma separated positive integers: %invalid', array('%invalid' => $error[$key])));
    else $form_state['values']["dblog_time_filters:$key"] = $values[$key];
  }
}
