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
 * First step of Revenue section.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of first step of Payroll
 */
function checkbook_datafeeds_revenue($form, &$form_state)
{
  $datatype = $form_state['step_information']['type']['stored_values']['format'];
  $revenue_fys = array('' => "All Fiscal Years");
  $revenue_fys = $revenue_fys + _checkbook_advanced_search_get_year('revenue', 'datafeeds');
  $agency_options = _agency_options();

  //<editor-fold desc="Heading">

  $form['loaded'] = array(
    '#markup' => '<div class="updated-date">Last updated: ' . _checkbook_datafeeds_get_updated_date('revenue') . '</div>',
  );
  $form['requiredmessage'] = array(
    '#markup' => '<p class="required-message">Fields marked with an asterisk (<span class="form-required">*</span>) are required.</p><br/>',
  );
  $form['description'] = array(
    '#markup' => '<h2 class="domain-title">Revenue</h2>'
  );

  //</editor-fold>

  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('3. Filter the Data:'),
    '#attributes' => array('class' => array('revenue-fieldset')),
  );
  $form['filter']['budget_fiscal_year'] = array(
    '#type' => 'select',
    '#title' => t('Budget Fiscal Year:'),
    '#options' => CheckbookDateUtil::getLast10fiscalYears(),
    '#prefix' => '<div class="column column-left"><div class="datafield budgetyear">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['budget_fiscal_year']) ? $form_state['values']['budget_fiscal_year'] : CheckbookDateUtil::getCurrentFiscalYear(),
  );
  $form['filter']['agency'] = array(
    '#type' => 'select',
    '#title' => t('Agency:'),
    '#options' => $agency_options['options'],
    '#option_attributes' => $agency_options['options_attributes'],
    '#prefix' => '<div class="datafield agency">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['agency']) ? $form_state['values']['agency'] : 'Citywide (All Agencies)',
  );
  $form['filter']['revenue_category'] = array(
    '#type' => 'select',
    '#title' => t('Revenue Category:'),
    '#options' => _revenue_category_options(),
    '#prefix' => '<div class="datafield revenuecategory">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['revenue_category']) ? $form_state['values']['revenue_category'] : 'All Revenue Categories',
  );
  $form['filter']['revenue_source'] = array(
    '#type' => 'textfield',
    '#title' => t('Revenue Source:'),
    '#prefix' => '<div class="datafield revenuesource">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['revenue_source']) ? $form_state['values']['revenue_source'] : '',
  );
  $form['filter']['adoptedfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield adopted datarange"><label>Adopted:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['adoptedfrom']) ? $form_state['values']['adoptedfrom'] : '',
  );
  $form['filter']['adoptedto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['adoptedto']) ? $form_state['values']['adoptedto'] : '',
  );
  $form['filter']['recognizedfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield recognized datarange"><label>Recognized:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['recognizedfrom']) ? $form_state['values']['recognizedfrom'] : '',
  );
  $form['filter']['recognizedto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div></div>',
    '#default_value' => !empty($form_state['values']['recognizedto']) ? $form_state['values']['recognizedto'] : '',
  );
  $form['filter']['fiscal_year'] = array(
    '#type' => 'select',
    '#title' => t('Fiscal Year:'),
    '#options' => $revenue_fys,
    '#prefix' => '<div class="column column-right"><div class="datafield year">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['fiscal_year']) ? $form_state['values']['fiscal_year'] : '',
  );
  $form['filter']['funding_class'] = array(
    '#type' => 'select',
    '#title' => t('Funding Class:'),
    '#options' => _funding_class_options(),
    '#prefix' => '<div class="datafield fundingclass">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['funding_class']) ? $form_state['values']['funding_class'] : 'All Funding Classes',
  );
  $form['filter']['revenue_class'] = array(
    '#type' => 'textfield',
    '#title' => t('Revenue Class:'),
    '#prefix' => '<div class="datafield revenueclass">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['revenue_class']) ? $form_state['values']['revenue_class'] : '',
  );
  $form['filter']['fund_class'] = array(
    '#type' => 'select',
    '#title' => t('Fund Class:'),
    '#options' => _fund_class_options(),
    '#prefix' => '<div class="datafield fundclass">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['fund_class']) ? $form_state['values']['fund_class'] : 'All Fund Classes',
  );
  $form['filter']['modifiedfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield modified datarange"><label>Modified:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['modifiedfrom']) ? $form_state['values']['modifiedfrom'] : '',
  );
  $form['filter']['modifiedto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div></div>',
    '#default_value' => !empty($form_state['values']['modifiedto']) ? $form_state['values']['modifiedto'] : '',
  );

  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('4. Select Columns:'),
  );
  $form['columns']['column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns'),
    '#options' => _checkbook_datafeeds_revenue_column_options($datatype),
    '#default_value' => !empty($form_state['values']['column_select']) ? $form_state['values']['column_select'] : '',
    '#required' => TRUE,
    '#multiple' => TRUE,
  );
  //Hidden Field for multi-select
  $form['columns']['hidden_multiple_value'] = array(
    '#value' => isset($form_state['values']['column_select']) ? '|' . implode('||', $form_state['values']['column_select']) . '|' : '',
    '#type' => 'hidden',
  );
  $form['#attached'] = array(
    'library' => array(
      array('system', 'ui.autocomplete', FALSE),
      array('checkbook_datafeeds', 'jquery_multiselect', FALSE),
    ),
    'js' => array(
      'data' => drupal_get_path('module', 'checkbook_datafeeds') . '/js/revenue.js',
    )
  );
  $form['#validate'] = array('checkbook_datafeeds_revenue_validate');
  $form['prev'] = array(
    '#type' => 'submit',
    '#value' => t('Previous'),
    '#name' => 'prev',
    '#submit' => array('previous_submit'),
    '#limit_validation_errors' => array(),
  );
  $form['feeds_revenue_next'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
    '#submit' => array('checkbook_datafeeds_revenue_next_submit'),
  );
  $form['#attributes'] = array(
    'class' => array(
      'revenue',
      'data-feeds-wizard',
    )
  );
  return $form;
}

/**
 * Column options for Revenue section.
 *
 * @param string $datatype
 *   Type of data for Data Feeds to return (xml or csv)
 *
 * @return array
 *   FAPI structured array for select box #options
 */
function _checkbook_datafeeds_revenue_column_options($datatype)
{
  if ($datatype == 'csv') {
    return drupal_map_assoc(array(
      "Adopted",
      "Agency",
      "Budget Fiscal Year",
      "Closing Classification Name",
      "Fiscal Year",
      "Fund Class",
      "Funding Class",
      "Modified",
      "Recognized",
      "Revenue Category",
      "Revenue Class",
      "Revenue Source",
    ));
  } else {
    if ($datatype == 'xml') {
      return drupal_map_assoc(array(
        "adopted",
        "agency",
        "budget_fiscal_year",
        "closing_classification_name",
        "fiscal_year",
        "fund_class",
        "funding_class",
        "modified",
        "recognized",
        "revenue_category",
        "revenue_class",
        "revenue_source",
      ));
    }
  }
}

/**
 * Stores form values and moves to next step of Data Feeds wizard in Revenue.
 *
 * @param array $form
 *   Data Feed wizard form array
 * @param array $form_state
 *   Data Feed wizard form_state array
 */
function checkbook_datafeeds_revenue_next_submit($form, &$form_state)
{
  $current_step = &$form_state['step'];
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];
  $current_step = 'revenue_confirmation';
  if (!empty($form_state['step_information'][$current_step]['stored_values'])) {
    $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
  } else {
    $form_state['values'] = array();
  }
  // Force rebuild with next step.
  $form_state['rebuild'] = TRUE;
}

/**
 * Outputs selected search criteria and number of records found for Revenue.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of Payroll confirmation step
 */
function checkbook_datafeeds_revenue_confirmation($form, &$form_state)
{
  $values = $form_state['step_information']['revenue']['stored_values'];
  $responsetype = $form_state['step_information']['type']['stored_values']['format'];
  $user_criteria = array();
  $user_criteria['Type of Data'] = 'Revenue';
  $user_criteria['Type of File'] = $responsetype;
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

  $formatted_search_criteria = array();
  //Used to maintain the order of the columns
  $selected_columns = checkbook_datafeeds_format_columns();
  $form_state['step_information']['revenue']['stored_values']['column_select'] = $selected_columns;
  foreach ($selected_columns as $column) {
    $form['columns'][$column] = array('#markup' => '<div>' . $column . '</div>');
    $user_criteria['Columns'][] = $column;
  }
  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('Search Criteria'),
  );
  $form['filter']['data_type'] = array(
    '#markup' => '<div><strong>Type of Data:</strong> Revenue</div>',
  );
  $formatted_search_criteria['Type of Data'] = 'Revenue';
  $form['filter']['file_type'] = array(
    '#markup' => '<div><strong>Type of File:</strong> ' . $form_state['step_information']['type']['stored_values']['format'] . '</div>',
  );
  $formatted_search_criteria['Type of File'] = $form_state['step_information']['type']['stored_values']['format'];
  if ($values['budget_fiscal_year']) {
    $form['filter']['budget_fiscal_year'] = array(
      '#markup' => '<div><strong>Budget Fiscal Year:</strong> ' . $values['budget_fiscal_year'] . '</div>',
    );
    $user_criteria['Budget Fiscal Year'] = $values['budget_fiscal_year'];
    $formatted_search_criteria['Budget Fiscal Year'] = $values['budget_fiscal_year'];
  }
  if ($values['agency']) {
    $form['filter']['agency'] = array(
      '#markup' => '<div><strong>Agency:</strong> ' . $values['agency'] . '</div>',
    );
    $user_criteria['Agency'] = $values['agency'];
    $formatted_search_criteria['Agency'] = $values['agency'];
  }
  if ($values['revenue_category']) {
    $form['filter']['revenue_category'] = array(
      '#markup' => '<div><strong>Revenue Category:</strong> ' . $values['revenue_category'] . '</div>',
    );
    $user_criteria['Revenue Category'] = $values['revenue_category'];
    $formatted_search_criteria['Revenue Category'] = $values['revenue_category'];
  }
  if ($values['revenue_source']) {
    $form['filter']['revenue_source'] = array(
      '#markup' => '<div><strong>Revenue Source:</strong> ' . $values['revenue_source'] . '</div>',
    );
    $user_criteria['Revenue Source'] = $values['revenue_source'];
    $formatted_search_criteria['Revenue Source'] = $values['revenue_source'];
  }
  if ($values['adoptedfrom'] && $values['adoptedto']) {
    $form['filter']['adopted'] = array(
      '#markup' => '<div><strong>Adopted:</strong> Greater Than Equal to: $' . $values['adoptedfrom'] . ' Less Than Equal to: $' . $values['adoptedto'] . '</div>',
    );
    $user_criteria['Adopted Greater Than'] = $values['adoptedfrom'];
    $user_criteria['Adopted Less Than'] = $values['adoptedto'];
    $formatted_search_criteria['Adopted'] = 'Greater Than Equal to: $' . $values['adoptedfrom'] . ' Less Than Equal to: $' . $values['adoptedto'];
  } elseif ($values['adoptedfrom'] && !$values['adoptedto']) {
    $form['filter']['adopted'] = array(
      '#markup' => '<div><strong>Adopted:</strong> Greater Than Equal to: $' . $values['adoptedfrom'] . '</div>',
    );
    $user_criteria['Adopted Greater Than'] = $values['adoptedfrom'];
    $formatted_search_criteria['Adopted'] = 'Greater Than Equal to: $' . $values['adoptedfrom'];
  } elseif (!$values['adoptedfrom'] && $values['adoptedto']) {
    $form['filter']['adopted'] = array(
      '#markup' => '<div><strong>Adopted:</strong> Less Than Equal to: $' . $values['adoptedto'] . '</div>',
    );
    $user_criteria['Adopted Less Than'] = $values['adoptedto'];
    $formatted_search_criteria['Adopted'] = 'Less Than Equal to: $' . $values['adoptedto'];
  }
  if ($values['recognizedfrom'] && $values['recognizedto']) {
    $form['filter']['recognized'] = array(
      '#markup' => '<div><strong>Recognized:</strong> Greater Than Equal to: $' . $values['recognizedfrom'] . ' Less Than Equal to: $' . $values['recognizedto'] . '</div>',
    );
    $user_criteria['Recognized Greater Than'] = $values['recognizedfrom'];
    $user_criteria['Recognized Less Than'] = $values['recognizedto'];
    $formatted_search_criteria['Recognized'] = 'Greater Than Equal to: $' . $values['recognizedfrom'] . ' Less Than Equal to: $' . $values['recognizedto'];
  } elseif ($values['recognizedfrom'] && !$values['recognizedto']) {
    $form['filter']['recognized'] = array(
      '#markup' => '<div><strong>Recognized:</strong> Greater Than Equal to: $' . $values['recognizedfrom'] . '</div>',
    );
    $user_criteria['Recognized Greater Than'] = $values['recognizedfrom'];
    $formatted_search_criteria['Recognized'] = 'Greater Than Equal to: $' . $values['recognizedfrom'];
  } elseif (!$values['recognizedfrom'] && $values['recognizedto']) {
    $form['filter']['recognized'] = array(
      '#markup' => '<div><strong>Recognized:</strong> Less Than Equal to: $' . $values['recognizedto'] . '</div>',
    );
    $user_criteria['Recognized Less Than'] = $values['recognizedto'];
    $formatted_search_criteria['Recognized'] = 'Less Than Equal to: $' . $values['recognizedto'];
  }
  if ($values['fiscal_year'] && $values['fiscal_year'] != '') {
    $form['filter']['fiscal_year'] = array(
      '#markup' => '<div><strong>Fiscal Year:</strong> ' . $values['fiscal_year'] . '</div>',
    );
    $user_criteria['Fiscal Year'] = $values['fiscal_year'];
    $formatted_search_criteria['Fiscal Year'] = $values['fiscal_year'];
  } else {
    $form['filter']['fiscal_year'] = array(
      '#markup' => '<div><strong>Fiscal Year:</strong> All Fiscal Years</div>',
    );
    $formatted_search_criteria['Fiscal Year'] = 'All Fiscal Years';
  }
  if ($values['funding_class']) {
    $form['filter']['funding_class'] = array(
      '#markup' => '<div><strong>Funding Class:</strong> ' . $values['funding_class'] . '</div>',
    );
    $user_criteria['Funding Class'] = $values['funding_class'];
    $formatted_search_criteria['Funding Class'] = $values['funding_class'];
  }
  if ($values['revenue_class']) {
    $form['filter']['revenue_class'] = array(
      '#markup' => '<div><strong>Revenue Class:</strong> ' . $values['revenue_class'] . '</div>',
    );
    $user_criteria['Revenue Class'] = $values['revenue_class'];
    $formatted_search_criteria['Revenue Class'] = $values['revenue_class'];
  }
  if ($values['fund_class']) {
    $form['filter']['fund_class'] = array(
      '#markup' => '<div><strong>Fund Class:</strong> ' . $values['fund_class'] . '</div>',
    );
    $user_criteria['Fund Class'] = $values['fund_class'];
    $formatted_search_criteria['Fund Class'] = $values['fund_class'];
  }
  if ($values['modifiedfrom'] && $values['modifiedto']) {
    $form['filter']['modified'] = array(
      '#markup' => '<div><strong>Modified:</strong> Greater Than Equal to: $' . $values['modifiedfrom'] . ' Less Than Equal to: $' . $values['modifiedto'] . '</div>',
    );
    $user_criteria['Modified Greater Than'] = $values['modifiedfrom'];
    $user_criteria['Modified Less Than'] = $values['modifiedto'];
    $formatted_search_criteria['Modified'] = 'Greater Than Equal to: $' . $values['modifiedfrom'] . ' Less Than Equal to: $' . $values['modifiedto'];
  } elseif ($values['modifiedfrom'] && !$values['modifiedto']) {
    $form['filter']['modified'] = array(
      '#markup' => '<div><strong>Modified:</strong> Greater Than Equal to: $' . $values['modifiedfrom'] . '</div>',
    );
    $user_criteria['Modified Greater Than'] = $values['modifiedfrom'];
    $formatted_search_criteria['Modified'] = 'Greater Than Equal to: $' . $values['modifiedfrom'];
  } elseif (!$values['modifiedfrom'] && $values['modifiedto']) {
    $form['filter']['modified'] = array(
      '#markup' => '<div><strong>Modified:</strong> Less Than Equal to: $' . $values['modifiedto'] . '</div>',
    );
    $user_criteria['Modified Less Than'] = $values['modifiedto'];
    $formatted_search_criteria['Modified'] = 'Less Than Equal to: $' . $values['modifiedto'];
  }
  $user_criteria['Formatted'] = $formatted_search_criteria;
  $criteria = checkbook_datafeeds_process_revenue_values($form, $form_state);
  $form_state['step_information']['confirmation']['stored_values']['criteria'] = $criteria;
  $form_state['step_information']['confirmation']['stored_values']['user_criteria'] = $user_criteria;
  $modified_form = checkbook_datafeeds_end_of_confirmation_form($form, $form_state, $criteria, $responsetype, 'revenue');
  return $modified_form;
}

/**
 * Retreives form values from previous step and moves back one step in Revenue.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_revenue_confirmation_previous_submit($form, &$form_state)
{
  $current_step = &$form_state['step'];
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];
  $current_step = 'revenue';
  $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
  $form_state['rebuild'] = TRUE;
}

/**
 * Convert values from Revenue section of form to an array format expected by API SearchCriteria.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Structured array expected by API
 */
function checkbook_datafeeds_process_revenue_values($form, &$form_state)
{
  $values = $form_state['step_information']['revenue']['stored_values'];
  $pattern = "/.*?(\\[.*?\\])/is";
  global $conf;
  $criteria = array(
    'global' => array(
      'type_of_data' => 'Revenue',
      'records_from' => 1,
      'max_records' => isset($conf['check_book']['data_feeds']['max_record_limit']) ? $conf['check_book']['data_feeds']['max_record_limit'] : 200000,
    ),
    'responseColumns' => $values['column_select'],
  );
  if ($values['fund_class'] != 'All Fund Classes') {
    preg_match($pattern, $values['fund_class'], $fcmatches);
    $criteria['value']['fund_class'] = trim($fcmatches[1], '[ ]');
  }
  if ($values['agency'] != 'Citywide (All Agencies)') {
    preg_match($pattern, $values['agency'], $amatches);
    $criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
  }
  if ($values['budget_fiscal_year'] != 'All Years') {
    $criteria['value']['budget_fiscal_year'] = $values['budget_fiscal_year'];
  }
  if ($values['fiscal_year'] != 'All Fiscal Years' && $values['fiscal_year'] != '') {
    $criteria['value']['fiscal_year'] = $values['fiscal_year'];
  }
  if ($values['revenue_category'] != 'All Revenue Categories') {
    preg_match($pattern, $values['revenue_category'], $rcmatches);
    $criteria['value']['revenue_category'] = trim($rcmatches[1], '[ ]');
  }
  if ($values['revenue_class']) {
    preg_match($pattern, $values['revenue_class'], $rclmatches);
    $criteria['value']['revenue_class'] = trim($rclmatches[1], '[ ]');
  }
  if ($values['revenue_source']) {
    preg_match($pattern, $values['revenue_source'], $rsmatches);
    $criteria['value']['revenue_source'] = trim($rsmatches[1], '[ ]');
  }
  if ($values['funding_class'] != 'All Funding Classes') {
    preg_match($pattern, $values['funding_class'], $fsmatches);
    $criteria['value']['funding_class'] = trim($fsmatches[1], '[ ]');
  }
  if ($values['modifiedfrom'] !== '' || $values['modifiedto'] !== '') {
    $criteria['range']['modified'] = array(
      checknull($values['modifiedfrom']),
      checknull($values['modifiedto'])
    );
  }
  if ($values['adoptedfrom'] !== '' || $values['adoptedto'] !== '') {
    $criteria['range']['adopted'] = array(
      checknull($values['adoptedfrom']),
      checknull($values['adoptedto'])
    );
  }
  if ($values['recognizedfrom'] !== '' || $values['recognizedto'] !== '') {
    $criteria['range']['recognized'] = array(
      checknull($values['recognizedfrom']),
      checknull($values['recognizedto'])
    );
  }
  return $criteria;
}

/**
 * Validate handler for Revenue section of form.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_revenue_validate($form, &$form_state)
{
  $numbermessage = 'Value must be a number.';
  $gtmessage = 'Value of To value must be greater than From value.';

  $adoptedfrom = $form_state['values']['adoptedfrom'];
  $adoptedto = $form_state['values']['adoptedto'];
  if ($adoptedfrom && !is_numeric($adoptedfrom)) {
    form_set_error('adoptedfrom', t('Adopted From value must be a number.'));
  }
  if ($adoptedto && !is_numeric($adoptedto)) {
    form_set_error('adoptedto', t('Adopted To value must be a number.'));
  }
  if ($adoptedfrom && $adoptedto && $adoptedto < $adoptedfrom) {
    form_set_error('adoptedto', t('Invalid range for Adopted.'));
  }

  $modifiedfrom = $form_state['values']['modifiedfrom'];
  $modifiedto = $form_state['values']['modifiedto'];
  if ($modifiedfrom && !is_numeric($modifiedfrom)) {
    form_set_error('modifiedfrom', t('Modified From value must be a number.'));
  }
  if ($modifiedto && !is_numeric($modifiedto)) {
    form_set_error('modifiedto', t('Modified To value must be a number.'));
  }
  if ($modifiedfrom && $modifiedto && $modifiedto < $modifiedfrom) {
    form_set_error('modifiedto', t('Invalid range for Modified.'));
  }

  $recognizedfrom = $form_state['values']['recognizedfrom'];
  $recognizedto = $form_state['values']['recognizedto'];
  if ($recognizedfrom && !is_numeric($recognizedfrom)) {
    form_set_error('recognizedfrom', t('Recognized From value must be a number.'));
  }
  if ($recognizedto && !is_numeric($recognizedto)) {
    form_set_error('recognizedto', t('Recognized To value must be a number.'));
  }
  if ($recognizedfrom && $recognizedto && $recognizedto < $recognizedfrom) {
    form_set_error('recognizedto', t('Invalid range for Recognized.'));
  }
}
