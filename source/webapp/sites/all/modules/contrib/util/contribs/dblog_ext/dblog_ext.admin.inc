<?php

/**
 * Menu callback; displays a listing of log messages.
 */
function dblog_ext_overview() {
  module_load_include('inc', 'dblog', 'dblog.admin');
  
  $filter = dblog_ext_build_filter_query();
  $rows = array();
  $icons = array(
    WATCHDOG_DEBUG    => '',
    WATCHDOG_INFO     => '',
    WATCHDOG_NOTICE   => '',
    WATCHDOG_WARNING  => theme('image', 'misc/watchdog-warning.png', t('warning'), t('warning')),
    WATCHDOG_ERROR    => theme('image', 'misc/watchdog-error.png', t('error'), t('error')),
    WATCHDOG_CRITICAL => theme('image', 'misc/watchdog-error.png', t('critical'), t('critical')),
    WATCHDOG_ALERT    => theme('image', 'misc/watchdog-error.png', t('alert'), t('alert')),
    WATCHDOG_EMERG    => theme('image', 'misc/watchdog-error.png', t('emergency'), t('emergency')),
  );
  $classes = array(
    WATCHDOG_DEBUG    => 'dblog-debug',
    WATCHDOG_INFO     => 'dblog-info',
    WATCHDOG_NOTICE   => 'dblog-notice',
    WATCHDOG_WARNING  => 'dblog-warning',
    WATCHDOG_ERROR    => 'dblog-error',
    WATCHDOG_CRITICAL => 'dblog-critical',
    WATCHDOG_ALERT    => 'dblog-alert',
    WATCHDOG_EMERG    => 'dblog-emerg',
  );

  $output = drupal_get_form('dblog_ext_filter_form');

  $header = array(
    ' ',
    array('data' => t('Type'), 'field' => 'w.type'),
    array('data' => t('Date'), 'field' => 'w.wid', 'sort' => 'desc'),
    t('Message'),
    array('data' => t('User'), 'field' => 'u.name'),
    array('data' => t('Operations')),
  );

  $sql = "SELECT w.wid, w.uid, w.severity, w.type, w.timestamp, w.message, w.variables, w.link, u.name FROM {watchdog} w INNER JOIN {users} u ON w.uid = u.uid";
  $tablesort = tablesort_sql($header);
  if (!empty($filter['where'])) {
    $result = pager_query($sql ." WHERE ". $filter['where'] . $tablesort, 50, 0, NULL, $filter['args']);
  }
  else {
    $result = pager_query($sql . $tablesort, 50);
  }

  while ($dblog = db_fetch_object($result)) {
    $rows[] = array('data' =>
      array(
        // Cells
        $icons[$dblog->severity],
        t($dblog->type),
        format_date($dblog->timestamp, 'small'),
        l(truncate_utf8(_dblog_format_message($dblog), 56, TRUE, TRUE), 'admin/reports/event/'. $dblog->wid, array('html' => TRUE)),
        theme('username', $dblog),
        $dblog->link,
      ),
      // Attributes for tr
      'class' => "dblog-". preg_replace('/[^a-z]/i', '-', $dblog->type) .' '. $classes[$dblog->severity]
    );
  }

  if (!$rows) {
    $rows[] = array(array('data' => t('No log messages available.'), 'colspan' => 6));
  }

  $output .= theme('table', $header, $rows, array('id' => 'admin-dblog'));
  $output .= theme('pager', NULL, 50, 0);

  return $output;
}

/**
 * Build query for dblog administration filters based on session.
 */
function dblog_ext_build_filter_query() {
  if (empty($_SESSION['dblog_overview_filter'])) {
    return;
  }

  $filters = dblog_ext_get_filters();

  // Build query
  $where = $args = array();
  foreach ($_SESSION['dblog_overview_filter'] as $key => $filter) {
    $filter_where = array();
    // modules might disable their filters (hook log_filters)
    if (!isset($filters[$key])) continue;
    
    if ($filters[$key]['conjunction']) {
      $filter_where = array();
      foreach ($filter as $value) {
        $filter_where[] = $filters[$key]['where'];
        $args[] = $value;
      }
      if (!empty($filter_where)) {
        $where[] = '(' . implode(' OR ', $filter_where) . ')';
      }
    }
    elseif(!empty($filter)) {
      $where[] = $filters[$key]['where'];
      $args[] = $filter;
    }
  }
  $where = !empty($where) ? implode(' AND ', $where) : '';

  return array(
    'where' => $where,
    'args' => $args,
  );
}

/**
 * Return form for dblog administration filters.
 *
 * @ingroup forms
 * @see dblog_ext_filter_form_submit()
 * @see dblog_ext_filter_form_validate()
 */
function dblog_ext_filter_form() {
  $session = &$_SESSION['dblog_overview_filter'];
  $session = is_array($session) ? $session : array();
  
  drupal_add_css(drupal_get_path('module', 'dblog_ext') .'/dblog_ext.css');
  $filters = dblog_ext_get_filters();

  $form = array();

  $form['filters'] = array(
    '#type' => 'fieldset',
    '#title' => t('Filter log messages'),
    '#theme' => 'dblog_filters',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  
  foreach ($filters as $key => $filter) {
    // initialize default widget
    $widget = array(
      '#title' => $filter['title'],
      '#type' => 'textfield',
    );
    if (isset($filter['options'])) {
      $widget = array_merge($widget, array(
        '#type' => 'select',
        '#multiple' => $filter['conjunction'],
        '#size' => 8,
        '#options' => $filter['options'],
      ));
    }
    
    // check if filter has its own widget's defaults
    if (empty($filter['widget'])) {
      $form['filters']['status'][$key] = $widget;
    }
    else {
      $form['filters']['status'][$key] = array_merge($widget, $filter['widget']);
    }
    
    if (!empty($session[$key])) {
      $form['filters']['status'][$key]['#default_value'] = $session[$key];
      $form['filters']['#collapsed'] = FALSE;
    }
  }

  $form['filters']['buttons']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Filter'),
  );
  if (!empty($session)) {
    $form['filters']['buttons']['reset'] = array(
      '#type' => 'submit',
      '#value' => t('Reset')
    );
  }

  return $form;
}

/**
 * Validate result from dblog administration filter form.
 */
function dblog_ext_filter_form_validate($form, &$form_state) {
  if ($form_state['values']['op'] == t('Filter')) {
    $filters = dblog_ext_get_filters();
    $valid = FALSE;
    foreach($filters as $key => $filter) {
      if (!empty($form_state['values'][$key])) {
        $valid = TRUE;
        break;
      }
    }
    if (!$valid) form_set_error('type', t('You must select something to filter by.'));
  }
  module_invoke_all('log_filters_validate', $form, $form_state);
}

/**
 * Process result from dblog administration filter form.
 */
function dblog_ext_filter_form_submit($form, &$form_state) {
  $op = $form_state['values']['op'];
  $filters = dblog_ext_get_filters();
  switch ($op) {
    case t('Filter'):
      foreach ($filters as $name => $filter) {
        if (isset($form_state['values'][$name])) {
          $_SESSION['dblog_overview_filter'][$name] = $form_state['values'][$name];
        }
        else {
          // modules might disable their filters (hook log_filters)
          // clear in case of future re-enabling
          unset($_SESSION['dblog_overview_filter'][$name]);
        }
      }
      break;
    case t('Reset'):
      $_SESSION['dblog_overview_filter'] = array();
      break;
  }
  return 'admin/reports/dblog';
}
