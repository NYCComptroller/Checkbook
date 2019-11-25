<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2019 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * First step of Payroll section.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of first step of Payroll
 */
function checkbook_datafeeds_payroll($form, &$form_state)
{
  $datatype = $form_state['step_information']['type']['stored_values']['format'];
  $agency_options = _agency_options(Datasource::CITYWIDE);
  $oge_options = _agency_options(Datasource::NYCHA);
  $data_source = $form_state['values']['datafeeds-payroll-domain-filter'] ?? Datasource::CITYWIDE;

  //<editor-fold desc="Heading">

  $form['loaded'] = array(
    '#markup' => '<div class="updated-date">Last updated: ' . _checkbook_datafeeds_get_updated_date('payroll') . '</div>',
  );
  $form['requiredmessage'] = array(
    '#markup' => '<p class="required-message">Fields marked with an asterisk (<span class="form-required">*</span>) are required.</p><br/>',
  );
  $form['description'] = array(
    '#markup' => '<h2 class="domain-title">Payroll</h2>'
  );

  //</editor-fold>

  $form['datafeeds-payroll-domain-filter'] = DatafeedsConfigUtil::dataSourceRadio($data_source, 'payroll');

  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('3. Filter the Data:'),
    '#attributes' => array('class' => array('payroll-fieldset')),
  );
  $form['filter']['other_government_entity'] = array(
    '#type' => 'select',
    '#title' => t('Other Government<br/>Entities:'),
    '#options' => $oge_options['options'],
    '#option_attributes' => $oge_options['options_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['other_government_entity']) ? $form_state['values']['other_government_entity'] : 'Select One',
    '#prefix' => '<div class="column column-left"><div class="datafield other_government_entity">',
    '#suffix' => '</div>',
  );
  $form['filter']['title'] = array(
    '#type' => 'textfield',
    '#title' => t('Title:'),
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['title']) ? $form_state['values']['title'] : '',
    '#prefix' => '<div class="datafield title">',
    '#suffix' => '<input name="title_exact" value="' . $_POST['title_exact'] . '" type="hidden"></div>',
  );
  $form['filter']['agency'] = array(
    '#type' => 'select',
    '#title' => t('Agency:'),
    '#options' => $agency_options['options'],
    '#option_attributes' => $agency_options['options_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['agency']) ? $form_state['values']['agency'] : 'Citywide (All Agencies)',
    '#prefix' => '<div class="datafield agency">',
    '#suffix' => '</div>',
  );
  $form['filter']['otherfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield otherpay datarange"><label>Other Payments:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['otherfrom']) ? $form_state['values']['otherfrom'] : '',
  );
  $form['filter']['otherto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['otherto']) ? $form_state['values']['otherto'] : '',
  );
  $form['filter']['grossfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield grosspay datarange"><label>Gross Pay:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['grossfrom']) ? $form_state['values']['grossfrom'] : '',
  );
  $form['filter']['grossto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['grossto']) ? $form_state['values']['grossto'] : '',
  );
  $form['filter']['totalgrossfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield totalgross datarange"><label>Gross Pay YTD:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['totalgrossfrom']) ? $form_state['values']['totalgrossfrom'] : '',
  );
  $form['filter']['totalgrossto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['totalgrossto']) ? $form_state['values']['totalgrossto'] : '',
  );
  $form['filter']['salaryfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield salary datarange"><label>Amount:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['salaryfrom']) ? $form_state['values']['salaryfrom'] : '',
  );
  $form['filter']['salaryto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['salaryto']) ? $form_state['values']['salaryto'] : '',
  );
  $form['filter']['salary_type'] = array(
    '#type' => 'radios',
    '#options' => array(
      'ALL' => 'All',
      'ANNUAL' => 'Annual',
      'RATE' => 'Rate'
    ),
    '#default_value' => !empty($form_state['values']['salary_type']) ? $form_state['values']['salary_type'] : 'ALL',
    '#prefix' => '<div class="datafield salarytype">',
    '#suffix' => '</div></div>',
  );
  $form['filter']['basepayfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="column column-right"><div class="datafield basepay datarange"><label>Base Pay:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['basepayfrom']) ? $form_state['values']['basepayfrom'] : '',
  );
  $form['filter']['basepayto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['basepayto']) ? $form_state['values']['basepayto'] : '',
  );
  $form['filter']['overtimefrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield overtime datarange"><label>Overtime Payments:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['overtimefrom']) ? $form_state['values']['overtimefrom'] : '',
  );
  $form['filter']['overtimeto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['overtimeto']) ? $form_state['values']['overtimeto'] : '',
  );
  $form['filter']['payfrequency'] = array(
    '#type' => 'select',
    '#title' => t('Pay Frequency:'),
    '#options' => _checkbook_advanced_search_get_payroll_frequency(),
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['payfrequency']) ? $form_state['values']['payfrequency'] : '',
    '#prefix' => '<div class="datafield payfrequency">',
    '#suffix' => '</div>',
  );
  $form['filter']['year'] = array(
    '#type' => 'select',
    '#title' => t('Year:'),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield fiscalyear">',
    '#suffix' => '</div>',
  );
  $form['filter']['paydatefrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    '#prefix' => '<div class="datafield paydate datarange"><label>Pay Date:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['paydatefrom']) ? $form_state['values']['paydatefrom'] : '',
    '#attributes' => array('class' => array('datepicker')),
  );
  $form['filter']['paydateto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    '#suffix' => '</div></div></div>',
    '#default_value' => !empty($form_state['values']['paydateto']) ? $form_state['values']['paydateto'] : '',
    '#attributes' => array('class' => array('datepicker')),
  );
  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('4. Select Columns:'),
  );
  $form['columns']['column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns'),
    '#options' => _checkbook_datafeeds_payroll_column_options($datatype),
    '#multiple' => TRUE,
    '#default_value' => !empty($form_state['values']['column_select']) ? $form_state['values']['column_select'] : '',
    '#id' => 'edit-column-select',
  );

  $form['columns']['oge_column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required.">*</span>'),
    '#options' => _checkbook_datafeeds_payroll_column_options($datatype, 'checkbook_nycha'),
    '#multiple' => TRUE,
    '#default_value' => !empty($form_state['values']['oge_column_select']) ? $form_state['values']['oge_column_select'] : '',
    '#id' => 'edit-oge-column-select',
  );
  //Hidden Field for multi-select
  if ($data_source == 'checkbook_nycha') {
    $multi_select_hidden = isset($form_state['input']['oge_column_select']) ? '|' . implode('||', $form_state['input']['oge_column_select']) . '|' : '';
  } else {
    $multi_select_hidden = isset($form_state['input']['column_select']) ? '|' . implode('||', $form_state['input']['column_select']) . '|' : '';
  }

  $form['columns']['hidden_multiple_value'] = array(
    '#value' => $multi_select_hidden,
    '#type' => 'hidden',
  );

  $form['feeds_payroll_next'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
    '#submit' => array('checkbook_datafeeds_payroll_next_submit'),
  );
  $form['prev'] = array(
    '#type' => 'submit',
    '#value' => t('Previous'),
    '#name' => 'prev',
    '#submit' => array('previous_submit'),
    '#limit_validation_errors' => array(),
  );
  $form['#attached'] = array(
    'library' => array(
      array('system', 'ui.autocomplete', FALSE),
      array('system', 'ui.datepicker', FALSE),
      array('checkbook_datafeeds', 'jquery_multiselect', FALSE),
    ),
    'js' => array(
      'data' => drupal_get_path('module', 'checkbook_datafeeds') . '/js/payroll.js',
    )
  );
  $form['#validate'] = array('checkbook_datafeeds_payroll_validate');
  $form['#attributes'] = array(
    'class' => array(
      'payroll',
      'data-feeds-wizard',
    ),
  );
  return $form;
}

/**
 * Column options for Payroll domain.
 *
 * @param string $datatype
 *   Type of data for Data Feeds to return (xml or csv)
 *
 * @param string $data_source
 * @return array
 *   FAPI structured array for select box #options
 */
function _checkbook_datafeeds_payroll_column_options($datatype, $data_source = "checkbook")
{
  if ($datatype == 'csv') {
    $options = array("checkbook" => drupal_map_assoc(array("Agency", "Annual Salary", "Base Pay", "Calendar Year",
      "Fiscal year", "Gross Pay", "Gross Pay YTD", "Hourly Rate",
      "Other Payments", "Overtime Payments", "Pay Date",
      "Pay Frequency", "Payroll Type", "Title",)),
      "checkbook_nycha" => drupal_map_assoc(array("Annual Salary", "Base Pay", "Calendar Year", "Daily Wage",
        "Gross Pay", "Gross Pay YTD", "Hourly Rate",
        "Other Government Entities", "Other Payments", "Overtime Payments", "Pay Date",
        "Pay Frequency", "Payroll Type", "Title",))
    );
  } else if ($datatype == 'xml') {
    $options = array("checkbook" => drupal_map_assoc(array("agency", "annual_salary", "base_pay", "calendar_year", "fiscal_year",
      "gross_pay", "gross_pay_ytd", "hourly_rate", "other_payments", "overtime_payments",
      "pay_date", "pay_frequency", "payroll_type", "title",)),
      "checkbook_nycha" => drupal_map_assoc(array("annual_salary", "base_pay", "calendar_year", "daily_wage",
        "gross_pay", "gross_pay_ytd", "hourly_rate", "other_government_entities",
        "overtime_payments", "pay_date", "pay_frequency", "payroll_type", "title",)),
    );
  }

  return $options[$data_source];
}

/**
 * Stores form values and moves to next step of Data Feeds wizard in Payroll.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_payroll_next_submit($form, &$form_state)
{
  $current_step = &$form_state['step'];
  if (startsWith($form_state['values']['year'], 'F')) {
    if ($form_state['step_information']['type']['stored_values']['format'] == 'csv') {
      if ($form_state['values']['column_select']['Year']) {
        unset($form_state['values']['column_select']['Year']);
        $form_state['values']['column_select']['Fiscal year'] = 'Fiscal year';
      }
    } elseif ($form_state['step_information']['type']['stored_values']['format'] == 'xml') {
      if ($form_state['values']['column_select']['year']) {
        unset($form_state['values']['column_select']['year']);
        $form_state['values']['column_select']['fiscal_year'] = 'fiscal_year';
      }
    }
  } elseif (startsWith($form_state['values']['year'], 'C')) {
    if ($form_state['step_information']['type']['stored_values']['format'] == 'csv') {
      if ($form_state['values']['column_select']['Year']) {
        unset($form_state['values']['column_select']['Year']);
        $form_state['values']['column_select']['Calendar Year'] = 'Calendar Year';
      }
    } elseif ($form_state['step_information']['type']['stored_values']['format'] == 'xml') {
      if ($form_state['values']['column_select']['year']) {
        unset($form_state['values']['column_select']['year']);
        $form_state['values']['column_select']['calendar_year'] = 'calendar_year';
      }
    }
  }
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];
  $current_step = 'payroll_confirmation';
  if (!empty($form_state['step_information'][$current_step]['stored_values'])) {
    $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
  } else {
    $form_state['values'] = array();
  }
  $form_state['rebuild'] = TRUE; // Force rebuild with next step.
}

/**
 * Filters selected columns to only contain columns configured for this domain snd data_source
 *
 * @param array $selected_columns
 *   selected column array
 * @param string $data_source
 *   current selected data source (i.e. checkbook, checkbook_nycha)
 * @param string $response_type
 *   response type (i.e. csv, xml)
 * @param string $year_type
 * @return array
 */
function checkbook_datafeeds_payroll_filter_selected_columns($selected_columns, $data_source, $response_type, $year_type = 'calendar_year')
{
  //Get column configurations
  $domain = 'payroll';
  $domain = $data_source == 'checkbook_nycha' ? $domain . '_nycha' : $domain;
  $domain_config = ConfigUtil::getDomainConfiguration($domain);
  $domain = $domain . '_' . $year_type;
  //Get columns which should be included for the current data source
  $columns_included = $domain_config->$domain->dataset->displayConfiguration->$response_type->elementsColumn;
  //Filter out selected columns which are not configured to show for this data source
  $filtered_columns = array_intersect_key($selected_columns, (array)$columns_included);
  return $filtered_columns;
}

/**
 * Outputs selected search criteria and number of records found for Payroll.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of Contracts confirmation step
 */
function checkbook_datafeeds_payroll_confirmation($form, &$form_state)
{
  $values = $form_state['step_information']['payroll']['stored_values'];
  $responsetype = $form_state['step_information']['type']['stored_values']['format'];
  $user_criteria = array();
  $user_criteria['Type of Data'] = 'Payroll';
  $user_criteria['Type of File'] = $responsetype;
  //Get current data source
  $data_source = $form_state['input']['datafeeds-payroll-domain-filter'];
  //Set type/source of data by data_source
  $user_criteria['Type of Data'] = $data_source == 'checkbook_nycha' ? 'Payroll_NYCHA' : 'Payroll';

  $form['download_feeds'] = array(
    '#markup' => '<h2 id="edit-description">Download Data</h2>',
  );
  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('Selected Columns'),
  );
  $form['#attributes'] = array(
    'class' => array(
      'confirmation-page',
      'data-feeds-wizard',
    )
  );

  //Used to maintain the order of the columns
  $selected_columns = checkbook_datafeeds_format_columns();
  //Filter columns for current data source
  $filtered_columns = checkbook_datafeeds_payroll_filter_selected_columns($selected_columns, $data_source, $responsetype);
  if ($data_source == 'checkbook_nycha') {
    $form_state['step_information']['payroll']['stored_values']['oge_column_select'] = $filtered_columns;
  } else {
    $form_state['step_information']['payroll']['stored_values']['column_select'] = $filtered_columns;
  }

  foreach ($filtered_columns as $column) {
    $form['columns'][$column] = array('#markup' => '<div>' . $column . '</div>');
    $user_criteria['Columns'][] = $column;
  }

  $form['#attributes'] = array(
    'class' => array(
      'confirmation-page',
      'data-feeds-wizard',
    )
  );
  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('Search Criteria'),
  );
  $form['filter']['data_type'] = array(
    '#markup' => '<div><strong>Type of Data:</strong> Payroll</div>',
  );
  $formatted_search_criteria['Type of Data'] = 'Payroll';
  $form['filter']['file_type'] = array(
    '#markup' => '<div><strong>Type of File:</strong> ' . $form_state['step_information']['type']['stored_values']['format'] . '</div>',
  );
  $formatted_search_criteria['Type of File'] = $form_state['step_information']['type']['stored_values']['format'];
  //Add title_exact to hidden to persist back
  $form['title_exact'] = array(
    '#title' => 'title_exact',
    '#value' => $_POST['title_exact'],
    '#type' => 'hidden'
  );
  if ($values['title']) {
    $form['filter']['title'] = array(
      '#markup' => '<div><strong>Title:</strong> ' . $values['title'] . '</div>',
    );
    $user_criteria['Title'] = $values['title'];
    $formatted_search_criteria['Title'] = $values['title'];
  }

  if ($data_source == 'checkbook_nycha') {
    if ($values['other_government_entity']) {
      $other_government_entity = ($values['other_government_entity'] == 'Select One') ? 'All' : $values['other_government_entity'];
      $form['filter']['other_government_entity'] = array(
        '#markup' => '<div><strong>Other Government Entities:</strong> ' . $other_government_entity . '</div>',
      );
      $user_criteria['Other Government Entity'] = $values['other_government_entity'];
      $formatted_search_criteria['Other Government Entities'] = $other_government_entity;
    }
  } else {
    if ($values['agency']) {
      $form['filter']['agency'] = array(
        '#markup' => '<div><strong>Agency:</strong> ' . $values['agency'] . '</div>',
      );
      $user_criteria['Agency'] = $values['agency'];
      $formatted_search_criteria['Agency'] = $values['agency'];
    }
  }
  if ($values['otherfrom'] && $values['otherto']) {
    $form['filter']['other_pay'] = array('#markup' => '<div><strong>Other Payments:</strong> Greater Than Equal to: $' . $values['otherfrom'] . ' Less Than Equal to: $' . $values['otherto'] . '</div>');
    $user_criteria['Other Pay Greater Than'] = $values['otherfrom'];
    $user_criteria['Other Pay Less Than'] = $values['otherto'];
    $formatted_search_criteria['Other Payments'] = 'Greater Than Equal to: $' . $values['otherfrom'] . ' Less Than Equal to: $' . $values['otherto'];
  } elseif (!$values['otherfrom'] && $values['otherto']) {
    $form['filter']['other_pay'] = array('#markup' => '<div><strong>Other Payments:</strong> Less Than Equal to: $' . $values['otherto'] . '</div>');
    $user_criteria['Other Pay Less Than'] = $values['otherto'];
    $formatted_search_criteria['Other Payments'] = 'Less Than Equal to: $' . $values['otherto'];
  } elseif ($values['otherfrom'] && !$values['otherto']) {
    $form['filter']['other_pay'] = array('#markup' => '<div><strong>Other Payments:</strong> Greater Than Equal to: $' . $values['otherfrom'] . '</div>');
    $user_criteria['Other Pay Greater Than'] = $values['otherfrom'];
    $formatted_search_criteria['Other Payments'] = 'Greater Than Equal to: $' . $values['otherfrom'];
  }
  if ($values['grossfrom'] && $values['grossto']) {
    $form['filter']['gross_pay'] = array('#markup' => '<div><strong>Gross Pay:</strong> Greater Than Equal to: $' . $values['grossfrom'] . ' Less Than Equal to: $' . $values['grossto'] . '</div>');
    $user_criteria['Gross Pay Greater Than'] = $values['grossfrom'];
    $user_criteria['Gross Pay Less Than'] = $values['grossto'];
    $formatted_search_criteria['Gross Pay'] = 'Greater Than Equal to: $' . $values['grossfrom'] . ' Less Than Equal to: $' . $values['grossto'];
  } elseif (!$values['grossfrom'] && $values['grossto']) {
    $form['filter']['gross_pay'] = array('#markup' => '<div><strong>Gross Pay:</strong> Less Than Equal to: $' . $values['grossto'] . '</div>');
    $user_criteria['Gross Pay Less Than'] = $values['grossto'];
    $formatted_search_criteria['Gross Pay'] = 'Less Than Equal to: $' . $values['grossto'];
  } elseif ($values['grossfrom'] && !$values['grossto']) {
    $form['filter']['gross_pay'] = array('#markup' => '<div><strong>Gross Pay:</strong> Greater Than Equal to: $' . $values['grossfrom'] . '</div>');
    $user_criteria['Gross Pay Greater Than'] = $values['grossfrom'];
    $formatted_search_criteria['Gross Pay'] = 'Greater Than Equal to: $' . $values['grossfrom'];
  }
  if ($values['totalgrossfrom'] && $values['totalgrossto']) {
    $form['filter']['total_gross'] = array('#markup' => '<div><strong>Gross Pay YTD:</strong> Greater Than Equal to: $' . $values['totalgrossfrom'] . ' Less Than Equal to: $' . $values['totalgrossto'] . '</div>');
    $user_criteria['Gross Pay YTD Greater Than'] = $values['totalgrossfrom'];
    $user_criteria['Gross Pay YTD Less Than'] = $values['totalgrossto'];
    $formatted_search_criteria['Gross Pay YTD'] = 'Greater Than Equal to: $' . $values['totalgrossfrom'] . ' Less Than Equal to: $' . $values['totalgrossto'];
  } elseif (!$values['totalgrossfrom'] && $values['totalgrossto']) {
    $form['filter']['total_gross'] = array('#markup' => '<div><strong>Gross Pay YTD:</strong> Less Than Equal to: $' . $values['totalgrossto'] . '</div>');
    $user_criteria['Gross Pay YTD Less Than'] = $values['totalgrossto'];
    $formatted_search_criteria['Gross Pay YTD'] = 'Less Than Equal to: $' . $values['totalgrossto'];
  } elseif ($values['totalgrossfrom'] && !$values['totalgrossto']) {
    $form['filter']['total_gross'] = array('#markup' => '<div><strong>Gross Pay YTD:</strong> Greater Than Equal to: $' . $values['totalgrossfrom'] . '</div>');
    $user_criteria['Gross Pay YTD Greater Than'] = $values['totalgrossfrom'];
    $formatted_search_criteria['Gross Pay YTD'] = 'Greater Than Equal to: $' . $values['totalgrossfrom'];
  }
  $form['filter']['salary_type'] = array(
    '#markup' => '<div><strong>Salary Type:</strong> ' . $values['salary_type'] . '</div>'
  );
  $user_criteria['Salary Type'] = $values['salary_type'];
  $formatted_search_criteria['Salary Type'] = $values['salary_type'];
  if ($values['salaryfrom'] && $values['salaryto']) {
    $form['filter']['annual_salary'] = array('#markup' => '<div><strong>Amount:</strong> Greater Than Equal to: $' . $values['salaryfrom'] . ' Less Than Equal to: $' . $values['salaryto'] . '</div>');
    $user_criteria['Amount Greater Than'] = $values['salaryfrom'];
    $user_criteria['Amount Less Than'] = $values['salaryto'];
    $formatted_search_criteria['Amount'] = 'Greater Than Equal to: $' . $values['salaryfrom'] . ' Less Than Equal to: $' . $values['salaryto'];
  } elseif (!$values['salaryfrom'] && $values['salaryto']) {
    $form['filter']['annual_salary'] = array('#markup' => '<div><strong>Amount:</strong> Less Than Equal to: $' . $values['salaryto'] . '</div>');
    $user_criteria['Amount Less Than'] = $values['salaryto'];
    $formatted_search_criteria['Amount'] = 'Less Than Equal to: $' . $values['salaryto'];
  } elseif ($values['salaryfrom'] && !$values['salaryto']) {
    $form['filter']['annual_salary'] = array('#markup' => '<div><strong>Amount:</strong> Greater Than Equal to: $' . $values['salaryfrom'] . '</div>');
    $user_criteria['Amount Greater Than'] = $values['salaryfrom'];
    $formatted_search_criteria['Amount'] = 'Greater Than Equal to: $' . $values['salaryfrom'];
  }
  if ($values['basepayfrom'] && $values['basepayto']) {
    $form['filter']['base_pay'] = array('#markup' => '<div><strong>Base Pay:</strong> Greater Than Equal to: $' . $values['basepayfrom'] . ' Less Than Equal to: $' . $values['basepayto'] . '</div>');
    $user_criteria['Base Pay Greater Than'] = $values['basepayfrom'];
    $user_criteria['Base Pay Less Than'] = $values['basepayto'];
    $formatted_search_criteria['Base Pay'] = 'Greater Than Equal to: $' . $values['basepayfrom'] . ' Less Than Equal to: $' . $values['basepayto'];
  } elseif (!$values['basepayfrom'] && $values['basepayto']) {
    $form['filter']['base_pay'] = array('#markup' => '<div><strong>Base Pay:</strong> Less Than Equal to: $' . $values['basepayto'] . '</div>');
    $user_criteria['Base Pay Less Than'] = $values['basepayto'];
    $formatted_search_criteria['Base Pay'] = 'Less Than Equal to: $' . $values['basepayto'];
  } elseif ($values['basepayfrom'] && !$values['basepayto']) {
    $form['filter']['base_pay'] = array('#markup' => '<div><strong>Base Pay:</strong> Greater Than Equal to: $' . $values['basepayfrom'] . '</div>');
    $user_criteria['Base Pay Greater Than'] = $values['basepayfrom'];
    $formatted_search_criteria['Base Pay'] = 'Greater Than Equal to: $' . $values['basepayfrom'];
  }
  if ($values['overtimefrom'] && $values['overtimeto']) {
    $form['filter']['overtime_pay'] = array('#markup' => '<div><strong>Overtime Payments:</strong> Greater Than Equal to: $' . $values['overtimefrom'] . ' Less Than Equal to: $' . $values['overtimeto'] . '</div>');
    $user_criteria['Overtime Pay Greater Than'] = $values['overtimefrom'];
    $user_criteria['Overtime Pay Less Than'] = $values['overtimeto'];
    $formatted_search_criteria['Overtime Payments'] = 'Greater Than Equal to: $' . $values['overtimefrom'] . ' Less Than Equal to: $' . $values['overtimeto'];
  } elseif (!$values['overtimefrom'] && $values['overtimeto']) {
    $form['filter']['overtime_pay'] = array('#markup' => '<div><strong>Overtime Payments:</strong> Less Than Equal to: $' . $values['overtimeto'] . '</div>');
    $user_criteria['Overtime Pay Less Than'] = $values['overtimeto'];
    $formatted_search_criteria['Overtime Payments'] = 'Less Than Equal to: $' . $values['overtimeto'];
  } elseif ($values['overtimefrom'] && !$values['overtimeto']) {
    $form['filter']['overtime_pay'] = array('#markup' => '<div><strong>Overtime Payments:</strong> Greater Than Equal to: $' . $values['overtimefrom'] . '</div>');
    $user_criteria['Overtime Pay Greater Than'] = $values['overtimefrom'];
    $formatted_search_criteria['Overtime Payments'] = 'Greater Than Equal to: $' . $values['overtimefrom'];
  }
  if ($values['payfrequency']) {
    $form['filter']['payfrequency'] = array(
      '#markup' => '<div><strong>Pay Frequency:</strong> ' . $values['payfrequency'] . '</div>',
    );
    $user_criteria['Pay Frequency'] = $values['payfrequency'];
    $formatted_search_criteria['Pay Frequency'] = $values['payfrequency'];
  }
  if (startsWith($values['year'], 'F')) {
    $form['filter']['year'] = array(
      '#markup' => '<div><strong>Year:</strong> ' . $values['year'] . '</div>',
    );
    $user_criteria['Fiscal Year'] = $values['year'];
    $formatted_search_criteria['Year'] = $values['year'];
    $year_type = 'fiscal_year';
  } elseif (startsWith($values['year'], 'C')) {
    $form['filter']['year'] = array(
      '#markup' => '<div><strong>Year:</strong> ' . $values['year'] . '</div>',
    );
    $user_criteria['Calendar Year'] = $values['year'];
    $formatted_search_criteria['Year'] = $values['year'];
    $year_type = 'calendar_year';
  }
  if ($values['paydatefrom'] && $values['paydateto']) {
    $form['filter']['pay_date'] = array('#markup' => '<div><strong>Pay Date:</strong> From: ' . $values['paydatefrom'] . ' To: ' . $values['paydateto'] . '</div>');
    $user_criteria['Pay Date After'] = $values['paydatefrom'];
    $user_criteria['Pay Date Before'] = $values['paydateto'];
    $formatted_search_criteria['Pay Date'] = 'From: ' . $values['paydatefrom'] . ' To: ' . $values['paydateto'];
  } elseif (!$values['paydatefrom'] && $values['paydateto']) {
    $form['filter']['pay_date'] = array('#markup' => '<div><strong>Pay Date:</strong> To: ' . $values['paydateto'] . '</div>');
    $user_criteria['Pay Date Before'] = $values['paydateto'];
    $formatted_search_criteria['Pay Date'] = 'To: ' . $values['paydateto'];
  } elseif ($values['paydatefrom'] && !$values['paydateto']) {
    $form['filter']['pay_date'] = array('#markup' => '<div><strong>Pay Date:</strong> From: ' . $values['paydatefrom'] . '</div>');
    $user_criteria['Pay Date After'] = $values['paydatefrom'];
    $formatted_search_criteria['Pay Date'] = 'Pay Date:</strong> From: ' . $values['paydatefrom'];
  }
  if ($values['payratefrom'] && $values['payrateto']) {
    $form['filter']['pay_rate'] = array('#markup' => '<div><strong>Pay Rate:</strong> Greater Than Equal to: $' . $values['payratefrom'] . ' Less Than Equal to: $' . $values['payrateto'] . '</div>');
    $user_criteria['Pay Rate Greater Than'] = $values['payratefrom'];
    $user_criteria['Pay Rate Less Than'] = $values['payrateto'];
    $formatted_search_criteria['Pay Rate'] = 'Greater Than Equal to: $' . $values['payratefrom'] . ' Less Than Equal to: $' . $values['payrateto'];
  } elseif (!$values['payratefrom'] && $values['payrateto']) {
    $form['filter']['pay_rate'] = array('#markup' => '<div><strong>Pay Rate:</strong> Less Than Equal to: $' . $values['payrateto'] . '</div>');
    $user_criteria['Pay Rate Less Than'] = $values['payrateto'];
    $formatted_search_criteria['Pay Rate'] = 'Less Than Equal to: $' . $values['payrateto'];
  } elseif ($values['payratefrom'] && !$values['payrateto']) {
    $form['filter']['pay_rate'] = array('#markup' => '<div><strong>Pay Rate:</strong> Greater Than Equal to: $' . $values['payratefrom'] . '</div>');
    $user_criteria['Pay Rate Greater Than'] = $values['payratefrom'];
    $formatted_search_criteria['Pay Rate'] = 'Greater Than Equal to: $' . $values['payratefrom'];
  }

  //Used to maintain the order of the columns
  $selected_columns = checkbook_datafeeds_format_columns();
  //Filter columns for current data source
  $filtered_columns = checkbook_datafeeds_payroll_filter_selected_columns($selected_columns, $data_source, $responsetype, $year_type);

  if ($data_source == 'checkbook_nycha') {
    $form_state['step_information']['payroll']['stored_values']['oge_column_select'] = $filtered_columns;
  } else {
    $form_state['step_information']['payroll']['stored_values']['column_select'] = $filtered_columns;
  }
  foreach ($filtered_columns as $column) {
    $form['columns'][$column] = array(
      '#markup' => '<div>' . $column . '</div>',
    );
    $user_criteria['Columns'][] = $column;
  }

  $user_criteria['Formatted'] = $formatted_search_criteria;
  $criteria = checkbook_datafeeds_process_payroll_values($form, $form_state, $data_source);
  $form_state['step_information']['confirmation']['stored_values']['criteria'] = $criteria;
  $form_state['step_information']['confirmation']['stored_values']['user_criteria'] = $user_criteria;
  $modified_form = checkbook_datafeeds_end_of_confirmation_form($form, $form_state, $criteria, $responsetype, 'payroll');
  return $modified_form;
}

/**
 * Retreives form values from previous step and moves back one step in Payroll.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_payroll_confirmation_previous_submit($form, &$form_state)
{
  $current_step = &$form_state['step'];
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];
  $current_step = 'payroll';
  $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
  $form_state['rebuild'] = TRUE;
}

/**
 * Convert values from Payroll section of form to an array format expected by API SearchCriteria.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @param $data_source
 * @return array
 *   Structured array expected by API
 */
function checkbook_datafeeds_process_payroll_values($form, &$form_state, $data_source)
{
  $values = $form_state['step_information']['payroll']['stored_values'];
  $pattern = "/.*?(\\[.*?\\])/is";
  global $conf;
  $criteria = array(
    'global' => array(
      //Set data source for query
      'type_of_data' => $data_source == 'checkbook_nycha' ? 'Payroll_NYCHA' : 'Payroll',
      'records_from' => 1,
      'max_records' => isset($conf['check_book']['data_feeds']['max_record_limit']) ? $conf['check_book']['data_feeds']['max_record_limit'] : 200000,
    ),
    'responseColumns' => ($data_source == 'checkbook_nycha') ? $values['oge_column_select'] : $values['column_select'],
  );
  if (startsWith($values['year'], 'F')) {
    $criteria['value']['fiscal_year'] = ltrim($values['year'], 'FY ');
  } elseif (startsWith($values['year'], 'C')) {
    $criteria['value']['calendar_year'] = ltrim($values['year'], 'CY ');
  }

  if ($data_source == 'checkbook_nycha') {
    if ($values['other_government_entity'] != 'Select One') {
      preg_match($pattern, $values['other_government_entity'], $amatches);
      $criteria['value']['other_government_entities_code'] = trim($amatches[1], '[ ]');
    }
  } else {
    if ($values['agency'] != 'Citywide (All Agencies)') {
      preg_match($pattern, $values['agency'], $amatches);
      $criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
    }
  }
  if ($values['title']) {
    $title_exact = $_POST['title_exact'];
    $title = $values['title'];
    if ($title_exact && $title_exact == $title)
      $criteria['value']['title_exact'] = $title_exact;
    else
      $criteria['value']['title'] = $title;
  }
  if ($values['payfrequency'] != '') {
    $criteria['value']['pay_frequency'] = $values['payfrequency'];
  }
  if ($values['basepayfrom'] !== '' || $values['basepayto'] !== '') {
    $criteria['range']['base_pay'] = array(
      checknull($values['basepayfrom']),
      checknull($values['basepayto']),
    );
  }
  if ($values['overtimefrom'] !== '' || $values['overtimeto'] !== '') {
    $criteria['range']['overtime_payments'] = array(
      checknull($values['overtimefrom']),
      checknull($values['overtimeto']),
    );
  }
  if ($values['otherfrom'] !== '' || $values['otherto'] !== '') {
    $criteria['range']['other_payments'] = array(
      checknull($values['otherfrom']),
      checknull($values['otherto']),
    );
  }
  if ($values['grossfrom'] !== '' || $values['grossto'] !== '') {
    $criteria['range']['gross_pay'] = array(
      checknull($values['grossfrom']),
      checknull($values['grossto']),
    );
  }
  if ($values['paydatefrom'] !== '' || $values['paydateto'] !== '') {
    $criteria['range']['pay_date'] = array(
      checknull($values['paydatefrom']),
      checknull($values['paydateto']),
    );
  }
  if ($values['totalgrossfrom'] !== '' || $values['totalgrossto'] !== '') {
    $criteria['range']['gross_pay_ytd'] = array(
      checknull($values['totalgrossfrom']),
      checknull($values['totalgrossto']),
    );
  }
  if ($values['salary_type']) {
    $criteria['value']['amount_type'] = $values['salary_type'];
  }
  if ($values['salaryfrom'] !== '' || $values['salaryto'] !== '') {
    $criteria['range']['amount'] = array(
      checknull($values['salaryfrom']),
      checknull($values['salaryto']),
    );
  }
  return $criteria;
}

/**
 * Validate handler for Payroll section of form
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_payroll_validate($form, &$form_state)
{
  $basepayfrom = $form_state['values']['basepayfrom'];
  $basepayto = $form_state['values']['basepayto'];
  $overtimefrom = $form_state['values']['overtimefrom'];
  $overtimeto = $form_state['values']['overtimeto'];
  $otherfrom = $form_state['values']['otherfrom'];
  $otherto = $form_state['values']['otherto'];
  $grossfrom = $form_state['values']['grossfrom'];
  $grossto = $form_state['values']['grossto'];
  $paydatefrom = $form_state['values']['paydatefrom'];
  $paydateto = $form_state['values']['paydateto'];
  $totalgrossfrom = $form_state['values']['totalgrossfrom'];
  $totalgrossto = $form_state['values']['totalgrossto'];
  $salaryfrom = $form_state['values']['salaryfrom'];
  $salaryto = $form_state['values']['salaryto'];
  $data_source = $form_state['values']['datafeeds-payroll-domain-filter'];
  $oge_response_columns = $form_state['values']['oge_column_select'];
  $response_columns = $form_state['values']['column_select'];

  // Base Pay:
  if ($basepayfrom && !is_numeric($basepayfrom)) {
    form_set_error('basepayfrom', t('Base Pay From value must be a number.'));
  }
  if ($basepayto && !is_numeric($basepayto)) {
    form_set_error('basepayto', t('Base Pay To value must be a number.'));
  }
  if ($basepayfrom && $basepayto && $basepayto < $basepayfrom) {
    form_set_error('basepayto', t('Invalid range for Base Pay.'));
  }
  // Overtime Pay:
  if ($overtimefrom && !is_numeric($overtimefrom)) {
    form_set_error('overtimefrom', t('Overtime Payments From value must be a number.'));
  }
  if ($overtimeto && !is_numeric($overtimeto)) {
    form_set_error('overtimeto', t('Overtime Payments To value must be a number.'));
  }
  if ($overtimefrom && $overtimeto && $overtimeto < $overtimefrom) {
    form_set_error('overtimeto', t('Invalid range for Overtime Payments.'));
  }
  // Other Pay:
  if ($otherfrom && !is_numeric($otherfrom)) {
    form_set_error('otherfrom', t('Other Payments From value must be a number.'));
  }
  if ($otherto && !is_numeric($otherto)) {
    form_set_error('otherto', t('Other Payments To value must be a number.'));
  }
  if ($otherfrom && $otherto && $otherto < $otherfrom) {
    form_set_error('otherto', t('Invalid range for Other Payments.'));
  }
  // Gross Pay:
  if ($grossfrom && !is_numeric($grossfrom)) {
    form_set_error('grossfrom', t('Gross Pay From value must be a number.'));
  }
  if ($grossto && !is_numeric($grossto)) {
    form_set_error('grossto', t('Gross Pay To value must be a number.'));
  }
  if ($grossfrom && $grossto && $grossto < $grossfrom) {
    form_set_error('grossto', t('Invalid range for Gross Pay'));
  }
  // Pay Date:
  if ($paydatefrom && !checkDateFormat($paydatefrom)) {
    form_set_error('paydatefrom', t('Pay Date From must be a valid date (YYYY-MM-DD).'));
  }
  if ($paydateto && !checkDateFormat($paydateto)) {
    form_set_error('paydateto', t('Pay Date To must be a valid date (YYYY-MM-DD).'));
  }
  if ($paydatefrom && $paydateto && strtotime($paydateto) < strtotime($paydatefrom)) {
    form_set_error('paydateto', t('Invalid range for Pay Date'));
  }
  // Total Gross YTD:
  if ($totalgrossfrom && !is_numeric($totalgrossfrom)) {
    form_set_error('totalgrossfrom', t('Gross Pay YTD From value must be a number.'));
  }
  if ($totalgrossto && !is_numeric($totalgrossto)) {
    form_set_error('totalgrossto', t('Gross Pay YTD To value must be a number.'));
  }
  if ($totalgrossfrom && $totalgrossto && $totalgrossto < $totalgrossfrom) {
    form_set_error('totalgrossto', t('Invalid range for Gross Pay YTD.'));
  }
  // Annual Salary:
  if ($salaryfrom && !is_numeric($salaryfrom)) {
    form_set_error('salaryfrom', t('Amount From value must be a number.'));
  }
  if ($salaryto && !is_numeric($salaryto)) {
    form_set_error('salaryto', t('Amount To value must be a number.'));
  }
  if ($salaryfrom && $salaryto && $salaryto < $salaryfrom) {
    form_set_error('salaryto', t('Invalid range for Amount.'));
  }
  //Validate response columns
  if ('checkbook_nycha' == $data_source && !$oge_response_columns) {
    form_set_error('oge_column_select', t('You must select at least one column.'));
  } else if ('checkbook' == $data_source && !$response_columns) {
    form_set_error('column_select', t('You must select at least one column.'));
  }

  //Set the hidden filed values on Spending form
  $form_state['complete form']['data_source']['#value'] = $data_source;
  //Hidden Field for multi-select
  if ($data_source == 'checkbook_nycha') {
    $multi_select_hidden = isset($form_state['input']['oge_column_select']) ? '|' . implode('||', $form_state['input']['oge_column_select']) . '|' : '';
  } else {
    $multi_select_hidden = isset($form_state['input']['column_select']) ? '|' . implode('||', $form_state['input']['column_select']) . '|' : '';
  }
  $form_state['complete form']['data_source']['#value'] = $data_source;
  $form_state['complete form']['hidden_multiple_value']['#value'] = $multi_select_hidden;
}
