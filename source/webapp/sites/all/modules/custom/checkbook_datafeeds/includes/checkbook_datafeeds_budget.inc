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
 * First step of Budget section.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of first step of Budget
 */
function checkbook_datafeeds_budget($form, &$form_state)
{
  $format = $form_state['step_information']['type']['stored_values']['format'];
  $agency = isset($form_state['values']['agency']) ? $form_state['values']['agency'] : 'Citywide (All Agencies)';
  $dept = !empty($form_state['values']['dept']) ? $form_state['values']['dept'] : '';
  $expcat = !empty($form_state['values']['expense_category']) ? $form_state['values']['expense_category'] : '';
  $year = !empty($form_state['values']['fiscal_year']) ? $form_state['values']['fiscal_year'] : 'FY' . CheckbookDateUtil::getCurrentFiscalYear();
  $agency_options = _agency_options();

  //<editor-fold desc="Heading">

  $form['loaded'] = array(
    '#markup' => '<div class="updated-date">Last updated: ' . _checkbook_datafeeds_get_updated_date('budget') . '</div>',
  );
  $form['requiredmessage'] = array(
    '#markup' => '<p class="required-message">Fields marked with an asterisk (<span class="form-required">*</span>) are required.</p><br/>',
  );
  $form['description'] = array(
    '#markup' => '<h2 class="domain-title">Budget</h2>'
  );

  //</editor-fold>

  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('3. Filter the Data:'),
    '#attributes' => array('class' => array('budget-fieldset')),
  );
  $form['filter']['leftbegin'] = array(
    '#markup' => '<div class="column column-left">',
  );
  $form['filter']['agency'] = array(
    '#type' => 'select',
    '#title' => t('Agency:'),
    '#options' => $agency_options['options'],
    '#option_attributes' => $agency_options['options_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield agency">',
    '#suffix' => '</div>',
    '#default_value' => !empty($form_state['values']['agency']) ? $form_state['values']['agency'] : 'Citywide (All Agencies)',
  );
  $form['filter']['dept'] = array(
    '#type' => 'select',
    '#title' => t('Department:'),
    '#prefix' => '<div id="department" class="datafield department">',
    '#suffix' => '</div>',
    '#validated' => TRUE,
    '#default_value' => !empty($form_state['values']['dept']) ? $form_state['values']['dept'] : 'Select Department',
  );

  $form['dept_hidden'] = array(
    '#title' => 'dept_hidden',
    '#value' => $form_state['values']['dept'],
    '#type' => 'hidden'
  );

  $form['filter']['expense_category'] = array(
    '#type' => 'select',
    '#title' => t('Expense Category:'),
    '#prefix' => '<div id="expense-category" class="datafield expensecategory">',
    '#suffix' => '</div>',
    '#validated' => TRUE,
    '#default_value' => !empty($form_state['values']['expense_category']) ? $form_state['values']['expense_category'] : 'Select Expense Category',
  );

  $form['expense_category_hidden'] = array(
    '#title' => 'expense_category_hidden',
    '#value' => $form_state['values']['expense_category'],
    '#type' => 'hidden'
  );

  $form['filter']['budget_code'] = array(
    '#type' => 'textfield',
    '#title' => t('Budget Code:'),
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['budget_code']) ? $form_state['values']['budget_code'] : '',
    '#prefix' => '<div class="datafield budgetcode">',
    '#suffix' => '</div>',
  );
  $form['filter']['fiscal_year'] = array(
    '#type' => 'select',
    '#title' => t('Year:'),
    '#options' => CheckbookDateUtil::getLast10fiscalYears(),
    '#default_value' => !empty($form_state['values']['fiscal_year']) ? $form_state['values']['fiscal_year'] : CheckbookDateUtil::getCurrentFiscalYear(),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield fiscalyear">',
    '#suffix' => '</div>',
  );
  $form['filter']['currentfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield currentmodified datarange"><label>Modified Budget:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['currentfrom']) ? $form_state['values']['currentfrom'] : '',
  );
  $form['filter']['currentto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['currentto']) ? $form_state['values']['currentto'] : '',
  );
  $form['filter']['leftend'] = array(
    '#markup' => '</div><div class="column column-right">',
  );
  $form['filter']['adoptedfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield adoptedbudget datarange"><label>Adopted Budget:</label><div class="ranges">',
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
  $form['filter']['preencumberedfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield preencumbered datarange"><label>Pre-encumbered:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['preencumberedfrom']) ? $form_state['values']['preencumberedfrom'] : '',
  );
  $form['filter']['preencumberedto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['preencumberedto']) ? $form_state['values']['preencumberedto'] : '',
  );
  $form['filter']['encumberedfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#default_value' => !empty($form_state['values']['encumberedfrom']) ? $form_state['values']['encumberedfrom'] : '',
    '#prefix' => '<div class="datafield encumbered datarange"><label>Encumbered:</label><div class="ranges">',
  );
  $form['filter']['encumberedto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#default_value' => !empty($form_state['values']['encumberedto']) ? $form_state['values']['encumberedto'] : '',
    '#suffix' => '</div></div>',
  );
  $form['filter']['accruedexpensefrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield accruedexpense datarange"><label>Accrued Expense:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['accruedexpensefrom']) ? $form_state['values']['accruedexpensefrom'] : '',
  );
  $form['filter']['accruedexpenseto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['accruedexpenseto']) ? $form_state['values']['accruedexpenseto'] : '',
  );
  $form['filter']['cashfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#default_value' => !empty($form_state['values']['cashfrom']) ? $form_state['values']['cashfrom'] : '',
    '#prefix' => '<div class="datafield cashexpense datarange"><label>Cash Expense:</label><div class="ranges">',
  );
  $form['filter']['cashto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#default_value' => !empty($form_state['values']['cashto']) ? $form_state['values']['cashto'] : '',
    '#suffix' => '</div></div>',
  );
  $form['filter']['postadjustmentsfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield postadjustments datarange"><label>Post Adjustment:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['postadjustmentsfrom']) ? $form_state['values']['postadjustmentsfrom'] : '',
  );
  $form['filter']['postadjustmentsto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['postadjustmentsto']) ? $form_state['values']['postadjustmentsto'] : '',
  );
  $form['filter']['rightend'] = array(
    '#markup' => '</div>',
  );
  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('4. Select Columns:'),
  );
  $form['columns']['column_select_expense'] = array(
    '#type' => 'select',
    '#title' => t('Budget Columns<span class="form-required" title="This field is required.">*</span>'),
    '#options' => _checkbook_datafeeds_budget_column_options($format),
    '#multiple' => TRUE,
    '#default_value' => !empty($form_state['values']['column_select_expense']) ? $form_state['values']['column_select_expense'] : '',
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
      'data' => drupal_get_path('module', 'checkbook_datafeeds') . '/js/budget.js',
    ),
  );
  $form['#validate'] = array('checkbook_datafeeds_budget_validate');
  $form['#attributes'] = array('class' => array('budget', 'data-feeds-wizard'));
  $form['prev'] = array(
    '#type' => 'submit',
    '#value' => t('Previous'),
    '#name' => 'prev',
    '#submit' => array('previous_submit'),
    '#limit_validation_errors' => array(),
  );
  $form['feeds_budget_next'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
    '#submit' => array('checkbook_datafeeds_budget_next_submit'),
  );
  return $form;
}

/**
 * Budget column options.
 *
 * @param string $datatype
 *   Data type (xml or csv)
 *
 * @return array
 *   FAPI structured array for select box #options
 */
function _checkbook_datafeeds_budget_column_options($datatype)
{
  if ($datatype == 'csv') {
    $out = drupal_map_assoc(array(
      "Accrued Expense",
      "Adopted",
      "Agency",
      "Budget Code",
      "Budget Name",
      "Cash Expense",
      "Department",
      "Encumbered",
      "Expense Category",
      "Modified",
      "Post Adjustments",
      "Pre-Encumbered",
      "Year",
    ));
  } else {
    if ($datatype = 'xml') {
      $out = drupal_map_assoc(array(
        "accrued_expense",
        "adopted",
        "agency",
        "budget_code",
        "budget_name",
        "cash_expense",
        "department",
        "encumbered",
        "expense_category",
        "modified",
        "post_adjustment",
        "pre_encumbered",
        "year",
      ));
    }
  }
  return $out;
}

/**
 * Stores form values and moves to next step of Data Feeds wizard in Budget.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_budget_next_submit($form, &$form_state)
{
  $current_step = &$form_state['step'];
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];

  $current_step = 'budget_confirmation';
  if (!empty($form_state['step_information'][$current_step]['stored_values'])) {
    $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
  } else {
    $form_state['values'] = array();
  }
  // Force rebuild with next step.
  $form_state['rebuild'] = TRUE;
}

/**
 * Outputs selected search criteria and number of records found for Budget.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of Budget confirmation page step
 */
function checkbook_datafeeds_budget_confirmation($form, &$form_state)
{
  $values = $form_state['step_information']['budget']['stored_values'];
  $responsetype = $form_state['step_information']['type']['stored_values']['format'];
  $user_criteria = array();
  $user_criteria['Type of Data'] = 'Budget';
  $user_criteria['Type of File'] = $responsetype;
  $form['download_feeds'] = array(
    '#markup' => '<h2 id="edit-description">Download Data</h2>',
  );
  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('Selected Columns'),
  );
  $formatted_search_criteria = array();
  //Used to maintain the order of the columns
  $selected_columns = checkbook_datafeeds_format_columns();
  $form_state['step_information']['budget']['stored_values']['column_select'] = $selected_columns;
  foreach ($selected_columns as $column) {
    $form['columns'][$column] = array(
      '#markup' => '<div>' . $column . '</div>',
    );
    $user_criteria['Columns'][] = $column;
  }
  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('Search Criteria'),
  );
  $form['filter']['data_type'] = array(
    '#markup' => '<div><strong>Type of Data:</strong> Budget</div>',
  );
  $formatted_search_criteria['Type of Data'] = 'Budget';
  $form['filter']['file_type'] = array(
    '#markup' => '<div><strong>Type of File:</strong> ' . $form_state['step_information']['type']['stored_values']['format'] . '</div>',
  );
  $formatted_search_criteria['Type of File'] = $form_state['step_information']['type']['stored_values']['format'];
  if ($values['agency']) {
    $form['filter']['agency'] = array('#markup' => '<div><strong>Agency:</strong> ' . $values['agency'] . '</div>');
    $user_criteria['Agency'] = $values['agency'];
    $formatted_search_criteria['Agency'] = $values['agency'];
  }
  if ($values['dept'] && $values['dept'] != 'Select Department') {
    $form['filter']['department'] = array('#markup' => '<div><strong>Department:</strong> ' . $values['dept'] . '</div>');
    $user_criteria['Department'] = $values['dept'];
    $formatted_search_criteria['Department'] = $values['dept'];
  }
  if ($values['expense_category'] && $values['expense_category'] != 'Select Expense Category') {
    $form['filter']['expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $values['expense_category'] . '</div>');
    $user_criteria['Expense Category'] = $values['expense_category'];
    $formatted_search_criteria['Expense Category'] = $values['expense_category'];
  }
  if ($values['budget_code']) {
    $form['filter']['budget_code'] = array('#markup' => '<div><strong>Budget Code:</strong> ' . $values['budget_code'] . '</div>');
    $user_criteria['Budget Code'] = $values['budget_code'];
    $formatted_search_criteria['Budget Code'] = $values['budget_code'];
  }
  if ($values['fiscal_year']) {
    $form['filter']['fiscal_year'] = array('#markup' => '<div><strong>Year:</strong> ' . $values['fiscal_year'] . '</div>');
    $user_criteria['Year'] = $values['fiscal_year'];
    $formatted_search_criteria['Year'] = $values['fiscal_year'];
  }
  if ($values['currentfrom'] && $values['currentto']) {
    $form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Greater Than Equal to: $' . $values['currentfrom'] . ' Less Than Equal to: $' . $values['currentto'] . '</div>');
    $user_criteria['Modified Budget Greater Than'] = $values['currentfrom'];
    $user_criteria['Modified Budget Less Than'] = $values['currentto'];
    $formatted_search_criteria['Modified Budget'] = 'Greater Than Equal to: $' . $values['currentfrom'] . ' Less Than Equal to: $' . $values['currentto'];
  } elseif (!$values['currentfrom'] && $values['currentto']) {
    $form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Less Than Equal to: $' . $values['currentto'] . '</div>');
    $user_criteria['Modified Budget Less Than'] = $values['currentto'];
    $formatted_search_criteria['Modified Budget'] = 'Less Than Equal to: $' . $values['currentto'];
  } elseif ($values['currentfrom'] && !$values['currentto']) {
    $form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Greater Than Equal to: $' . $values['currentfrom'] . '</div>');
    $user_criteria['Modified Budget Greater Than'] = $values['currentfrom'];
    $formatted_search_criteria['Modified Budget'] = 'Greater Than Equal to: $' . $values['currentfrom'];
  }
  if ($values['adoptedfrom'] && $values['adoptedto']) {
    $form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Greater Than Equal to: $' . $values['adoptedfrom'] . ' Less Than Equal to: $' . $values['adoptedto'] . '</div>');
    $user_criteria['Adopted Budget Greater Than'] = $values['adoptedfrom'];
    $user_criteria['Adopted Budget Less Than'] = $values['adoptedto'];
    $formatted_search_criteria['Adopted Budget'] = 'Greater Than Equal to: $' . $values['adoptedfrom'] . ' Less Than Equal to: $' . $values['adoptedto'];
  } elseif (!$values['adoptedfrom'] && $values['adoptedto']) {
    $form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Less Than Equal to: $' . $values['adoptedto'] . '</div>');
    $user_criteria['Adopted Budget Less Than'] = $values['adoptedto'];
    $formatted_search_criteria['Adopted Budget'] = 'Less Than Equal to: $' . $values['adoptedto'];
  } elseif ($values['adoptedfrom'] && !$values['adoptedto']) {
    $form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Greater Than Equal to: $' . $values['adoptedfrom'] . '</div>');
    $user_criteria['Adopted Budget Greater Than'] = $values['adoptedfrom'];
    $formatted_search_criteria['Adopted Budget'] = 'Greater Than Equal to: $' . $values['adoptedfrom'];
  }
  if ($values['preencumberedfrom'] && $values['preencumberedto']) {
    $form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Greater Than Equal to: $' . $values['preencumberedfrom'] . ' Less Than Equal to: $' . $values['preencumberedto'] . '</div>');
    $user_criteria['Pre-encumbered Greater Than'] = $values['preencumberedfrom'];
    $user_criteria['Pre-encumbered Less Than'] = $values['preencumberedto'];
    $formatted_search_criteria['Pre-encumbered'] = 'Greater Than Equal to: $' . $values['preencumberedfrom'] . ' Less Than Equal to: $' . $values['preencumberedto'];
  } elseif (!$values['preencumberedfrom'] && $values['preencumberedto']) {
    $form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Less Than Equal to: $' . $values['preencumberedto'] . '</div>');
    $user_criteria['Pre-encumbered Less Than'] = $values['preencumberedto'];
    $formatted_search_criteria['Pre-encumbered'] = 'Less Than Equal to: $' . $values['preencumberedto'];
  } elseif ($values['preencumberedfrom'] && !$values['preencumberedto']) {
    $form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Greater Than Equal to: $' . $values['preencumberedfrom'] . '</div>');
    $user_criteria['Pre-encumbered Greater Than'] = $values['preencumberedfrom'];
    $formatted_search_criteria['Pre-encumbered'] = 'Greater Than Equal to: $' . $values['preencumberedfrom'];
  }
  if ($values['encumberedfrom'] && $values['encumberedto']) {
    $form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Greater Than Equal to: $' . $values['encumberedfrom'] . ' Less Than Equal to: $' . $values['encumberedto'] . '</div>');
    $user_criteria['Encumbered Greater Than'] = $values['encumberedfrom'];
    $user_criteria['Encumbered Less Than'] = $values['encumberedto'];
    $formatted_search_criteria['Encumbered'] = 'Greater Than Equal to: $' . $values['encumberedfrom'] . ' Less Than Equal to: $' . $values['encumberedto'];
  } elseif (!$values['encumberedfrom'] && $values['encumberedto']) {
    $form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Less Than Equal to: $' . $values['encumberedto'] . '</div>');
    $user_criteria['Encumbered Less Than'] = $values['encumberedto'];
    $formatted_search_criteria['Encumbered'] = 'Less Than Equal to: $' . $values['encumberedto'];
  } elseif ($values['encumberedfrom'] && !$values['encumberedto']) {
    $form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Greater Than Equal to: $' . $values['encumberedfrom'] . '</div>');
    $user_criteria['Encumbered Greater Than'] = $values['encumberedfrom'];
    $formatted_search_criteria['Encumbered'] = 'Greater Than Equal to: $' . $values['encumberedfrom'];
  }
  if ($values['accruedexpensefrom'] && $values['accruedexpenseto']) {
    $form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Greater Than Equal to: $' . $values['accruedexpensefrom'] . ' Less Than Equal to: $' . $values['accruedexpenseto'] . '</div>');
    $user_criteria['Accrued Expense Greater Than'] = $values['accruedexpensefrom'];
    $user_criteria['Accrued Expense Less Than'] = $values['accruedexpenseto'];
    $formatted_search_criteria['Accrued Expense'] = 'Greater Than Equal to: $' . $values['accruedexpensefrom'] . ' Less Than Equal to: $' . $values['accruedexpenseto'];
  } elseif (!$values['accruedexpensefrom'] && $values['accruedexpenseto']) {
    $form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Less Than Equal to: $' . $values['accruedexpenseto'] . '</div>');
    $user_criteria['Accrued Expense Less Than'] = $values['accruedexpenseto'];
    $formatted_search_criteria['Accrued Expense'] = 'Less Than Equal to: $' . $values['accruedexpenseto'];
  } elseif ($values['accruedexpensefrom'] && !$values['accruedexpenseto']) {
    $form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Greater Than Equal to: $' . $values['accruedexpensefrom'] . '</div>');
    $user_criteria['Accrued Expense Greater Than'] = $values['accruedexpensefrom'];
    $formatted_search_criteria['Accrued Expense'] = 'Greater Than Equal to: $' . $values['accruedexpensefrom'];
  }
  if ($values['cashfrom'] && $values['cashto']) {
    $form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Greater Than Equal to: $' . $values['cashfrom'] . ' Less Than Equal to: $' . $values['cashto'] . '</div>');
    $user_criteria['Cash Expense Greater Than'] = $values['cashfrom'];
    $user_criteria['Cash Expense Less Than'] = $values['cashto'];
    $formatted_search_criteria['Cash Expense'] = 'Greater Than Equal to: $' . $values['cashfrom'] . ' Less Than Equal to: $' . $values['cashto'];
  } elseif (!$values['cashfrom'] && $values['cashto']) {
    $form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Less Than Equal to: $' . $values['cashto'] . '</div>');
    $user_criteria['Cash Expense Less Than'] = $values['cashto'];
    $formatted_search_criteria['Cash Expense'] = 'Less Than Equal to: $' . $values['cashto'];
  } elseif ($values['cashfrom'] && !$values['cashto']) {
    $form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Greater Than Equal to: $' . $values['cashfrom'] . '</div>');
    $user_criteria['Cash Expense Greater Than'] = $values['cashfrom'];
    $formatted_search_criteria['Cash Expense'] = 'Greater Than Equal to: $' . $values['cashfrom'];
  }
  if ($values['postadjustmentsfrom'] && $values['postadjustmentsto']) {
    $form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Greater Than Equal to: $' . $values['postadjustmentsfrom'] . ' Less Than Equal to: $' . $values['postadjustmentsto'] . '</div>');
    $user_criteria['Post Adjustments Greater Than'] = $values['postadjustmentsfrom'];
    $user_criteria['Post Adjustments Less Than'] = $values['postadjustmentsto'];
    $formatted_search_criteria['Post Adjustments'] = 'Greater Than Equal to: $' . $values['postadjustmentsfrom'] . ' Less Than Equal to: $' . $values['postadjustmentsto'];
  } elseif (!$values['postadjustmentsfrom'] && $values['postadjustmentsto']) {
    $form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Less Than Equal to: $' . $values['postadjustmentsto'] . '</div>');
    $user_criteria['Post Adjustments Less Than'] = $values['postadjustmentsto'];
    $formatted_search_criteria['Post Adjustments'] = 'Less Than Equal to: $' . $values['postadjustmentsto'];
  } elseif ($values['postadjustmentsfrom'] && !$values['postadjustmentsto']) {
    $form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Greater Than Equal to: $' . $values['postadjustmentsfrom'] . '</div>');
    $user_criteria['Post Adjustments Greater Than'] = $values['postadjustmentsfrom'];
    $formatted_search_criteria['Post Adjustments'] = 'Greater Than Equal to: $' . $values['postadjustmentsfrom'];
  }
  $form['#attributes'] = array(
    'class' => array(
      'confirmation-page',
      'data-feeds-wizard',
    ),
  );

  $user_criteria['Formatted'] = $formatted_search_criteria;
  $criteria = checkbook_datafeeds_process_budget_values($form, $form_state);
  $form_state['step_information']['confirmation']['stored_values']['criteria'] = $criteria;
  $form_state['step_information']['confirmation']['stored_values']['user_criteria'] = $user_criteria;
  $modified_form = checkbook_datafeeds_end_of_confirmation_form($form, $form_state, $criteria, $responsetype, 'budget');
  return $modified_form;
}

/**
 * Retreives form values from previous step and moves back one step in Budget.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_budget_confirmation_previous_submit($form, &$form_state)
{
  $current_step = &$form_state['step'];
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];
  $current_step = 'budget';
  $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
  $form_state['rebuild'] = TRUE;
}

/**
 * Convert values from Budget section of form to an array format expected by API SearchCriteria.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Structured array expected by API
 */
function checkbook_datafeeds_process_budget_values($form, $form_state)
{
  $values = $form_state['step_information']['budget']['stored_values'];
  $pattern = "/.*?(\\[.*?\\])/is";
  global $conf;
  $criteria = array(
    'global' => array(
      'type_of_data' => 'Budget',
      'records_from' => 1,
      'max_records' => isset($conf['check_book']['data_feeds']['max_record_limit']) ? $conf['check_book']['data_feeds']['max_record_limit'] : 200000,
    ),
  );
  $criteria['responseColumns'] = $values['column_select'];
  if ($values['fiscal_year'] != 'All Years') {
    $criteria['value']['year'] = $values['fiscal_year'];
  }
  if ($values['agency'] != 'Citywide (All Agencies)') {
    preg_match($pattern, $values['agency'], $amatches);
    if ($amatches) {
      $criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
    }
  }
  if ($values['dept']) {
    preg_match($pattern, $values['dept'], $dcmatches);
    if ($dcmatches) {
      $criteria['value']['department_code'] = trim($dcmatches[1], '[ ]');
    }
  }
  if ($values['expense_category']) {
    preg_match($pattern, $values['expense_category'], $ecmatches);
    if ($ecmatches) {
      $criteria['value']['expense_category'] = trim($ecmatches[1], '[ ]');
    }
  }
  if ($values['budget_code']) {
    preg_match($pattern, $values['budget_code'], $bcmatches);
    if ($bcmatches) {
      $criteria['value']['budget_code'] = trim($bcmatches[1], '[ ]');
      $criteria['value']['budget_code_name'] = str_replace($bcmatches[1], "", $values['budget_code']);
    } else {
      $criteria['value']['budget_code_name'] = $values['budget_code'];
    }
  }
  if ($values['adoptedfrom'] !== '' || $values['adoptedto'] !== '') {
    $criteria['range']['adopted'] = array(
      checknull($values['adoptedfrom']),
      checknull($values['adoptedto']),
    );
  }
  if ($values['currentfrom'] !== '' || $values['currentto'] !== '') {
    $criteria['range']['modified'] = array(
      checknull($values['currentfrom']),
      checknull($values['currentto']),
    );
  }
  if ($values['preencumberedfrom'] !== '' || $values['preencumberedto'] !== '') {
    $criteria['range']['pre_encumbered'] = array(
      checknull($values['preencumberedfrom']),
      checknull($values['preencumberedto']),
    );
  }
  if ($values['encumberedfrom'] !== '' || $values['encumberedto'] !== '') {
    $criteria['range']['encumbered'] = array(
      checknull($values['encumberedfrom']),
      checknull($values['encumberedto']),
    );
  }
  if ($values['cashfrom'] !== '' || $values['cashto'] !== '') {
    $criteria['range']['cash_expense'] = array(
      checknull($values['cashfrom']),
      checknull($values['cashto']),
    );
  }
  if ($values['postadjustmentsfrom'] !== '' || $values['postadjustmentsto'] !== '') {
    $criteria['range']['post_adjustment'] = array(
      checknull($values['postadjustmentsfrom']),
      checknull($values['postadjustmentsto']),
    );
  }
  if ($values['accruedexpensefrom'] !== '' || $values['accruedexpenseto'] !== '') {
    $criteria['range']['accrued_expense'] = array(
      checknull($values['accruedexpensefrom']),
      checknull($values['accruedexpenseto']),
    );
  }
  return $criteria;
}

/**
 * Validate handler for Budget section of form.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_budget_validate($form, &$form_state)
{
  $numbermessage = ' must be a number.';
  $gtmessage = 'Invalid range for ';
  $adoptedfrom = $form_state['values']['adoptedfrom'];
  $adoptedto = $form_state['values']['adoptedto'];
  $currentfrom = $form_state['values']['currentfrom'];
  $currentto = $form_state['values']['currentto'];
  $preencfrom = $form_state['values']['preencumberedfrom'];
  $preencto = $form_state['values']['preencumberedto'];
  $encto = $form_state['values']['encumberedto'];
  $encfrom = $form_state['values']['encumberedfrom'];
  $cashfrom = $form_state['values']['cashfrom'];
  $cashto = $form_state['values']['cashto'];
  $postfrom = $form_state['values']['postadjustmentsfrom'];
  $postto = $form_state['values']['postadjustmentsto'];
  $accruedfrom = $form_state['values']['accruedexpensefrom'];
  $accruedto = $form_state['values']['accruedexpenseto'];
  $expensecolumns = $form_state['values']['column_select_expense'];
  // Adopted:
  if ($adoptedfrom && !is_numeric($adoptedfrom)) {
    form_set_error('adoptedfrom', t('Adopted Budget From value must be a number.'));
  }
  if ($adoptedto && !is_numeric($adoptedto)) {
    form_set_error('adoptedto', t('Adopted Budget To value must be a number.'));
  }
  if ($adoptedfrom && $adoptedto && $adoptedto < $adoptedfrom) {
    form_set_error('adoptedto', t('Invalid range for Adopted Budget.'));
  }
  // Current:
  if ($currentfrom && !is_numeric($currentfrom)) {
    form_set_error('currentfrom', t('Modified Budget From value must be a number.'));
  }
  if ($currentto && !is_numeric($currentto)) {
    form_set_error('currentto', t('Modified Budget To value must be a number.'));
  }
  if ($currentfrom && $currentto && $currentto < $currentfrom) {
    form_set_error('currentto', t('Invalid range for Modified Budget.'));
  }
  // Preencumbered:
  if ($preencfrom && !is_numeric($preencfrom)) {
    form_set_error('preencumberedfrom', t('Pre-encumbered From value must be a number.'));
  }
  if ($preencto && !is_numeric($preencto)) {
    form_set_error('preencumberedto', t('Pre-encumbered To value must be a number.'));
  }
  if ($preencfrom && $preencto && $preencto < $preencfrom) {
    form_set_error('preencumberedto', t('Invalid range for Pre-encumbered.'));
  }
  // Encumbered:
  if ($encfrom && !is_numeric($encfrom)) {
    form_set_error('encumberedfrom', t('Encumbered From value must be a number.'));
  }
  if ($encto && !is_numeric($encto)) {
    form_set_error('encumberedto', t('Encumbered To value must be a number.'));
  }
  if ($encfrom && $encto && $encto < $encfrom) {
    form_set_error('encumberedto', t('Invalid range for Encumbered.'));
  }
  // Cash:
  if ($cashfrom && !is_numeric($cashfrom)) {
    form_set_error('cashfrom', t('Cash Expense From value must be a number.'));
  }
  if ($cashto && !is_numeric($cashto)) {
    form_set_error('cashto', t('Cash Expense To value must be a number.'));
  }
  if ($cashfrom && $cashto && $cashto < $cashfrom) {
    form_set_error('cashto', t('Invalid range for Cash Expense.'));
  }
  // Post Adjustments:
  if ($postfrom && !is_numeric($postfrom)) {
    form_set_error('postadjustmentsfrom', t('Post Adjustment From value must be a number.'));
  }
  if ($postto && !is_numeric($postto)) {
    form_set_error('postadjustmentsto', t('Post Adjustment To value must be a number.'));
  }
  if ($postfrom && $postto && $postto < $postfrom) {
    form_set_error('postadjustmentsto', t('Invalid range for Post Adjustment.'));
  }
  // Accrued Expense:
  if ($accruedfrom && !is_numeric($accruedfrom)) {
    form_set_error('accruedexpensefrom', t('Accrued Expense From value must be a number.'));
  }
  if ($accruedto && !is_numeric($accruedto)) {
    form_set_error('accruedexpenseto', t('Accrued Expense To value must be a number.'));
  }
  if ($accruedfrom && $accruedto && $accruedto < $accruedfrom) {
    form_set_error('accruedexpenseto', t('Invalid range for Accrued Expense.'));
  }
  // Columns:
  if (!$expensecolumns) {
    form_set_error('column_select_expense', t('You must select at least one column.'));
  }
  //Set the hidden filed values on Budget form
  $form_state['complete form']['dept_hidden']['#value'] = $form_state['values']['dept'];
  $form_state['complete form']['expense_category_hidden']['#value'] = $form_state['values']['expense_category'];
}
