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
function checkbook_datafeeds_budget($form, &$form_state){
  $format = $form_state['step_information']['type']['stored_values']['format'];
  //<editor-fold desc="Heading">

  $form['loaded'] = array(
    '#markup' => '<div class="updated-date">Last updated: ' . _checkbook_datafeeds_get_updated_date(Domain::$BUDGET) . '</div>',
  );
  $form['requiredmessage'] = array(
    '#markup' => '<p class="required-message">Fields marked with an asterisk (<span class="form-required">*</span>) are required.</p><br/>',
  );
  $form['description'] = array(
    '#markup' => '<h2 class="domain-title">Budget</h2>'
  );

  $data_source = $form_state['values']['datafeeds-budget-domain-filter'] ?? Datasource::CITYWIDE;
  $form['datafeeds-budget-domain-filter'] = DatafeedsConfigUtil::dataSourceRadio($data_source, Domain::$BUDGET);

  //</editor-fold>

  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('3. Filter the Data:'),
    '#attributes' => array('class' => array('budget-fieldset')),
  );

  //<editor-fold desc="CityWide Fields">
  $form['filter']['checkbook-fields-begin'] = array(
    '#markup' => '<div class="checkbook_fields">',
  );
  $form = checkbook_datafeeds_budget_filter_data($form, $form_state, Datasource::CITYWIDE);
  $form['filter']['checkbook-fields-end'] = array(
    '#markup' => '</div>',
  );
  //</editor-fold>

  //<editor-fold desc="NYCHA Fields">
  $form['filter']['checkbook-nycha-fields-begin'] = array(
    '#markup' => '<div class="checkbook_nycha_fields">',
  );
  $form = checkbook_datafeeds_nycha_budget_filter_data($form, $form_state, Datasource::NYCHA);
  $form['filter']['checkbook-nycha-fields-end'] = array(
    '#markup' => '</div>',
  );
  //</editor-fold>

  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('4. Select Columns:'),
  );

  //<editor-fold desc="CityWide and NYCHA Multi-select Options">
  $form['columns']['column_select_expense'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required.">*</span>'),
    '#options' => _checkbook_datafeeds_budget_column_options(Datasource::CITYWIDE, $format),
    '#multiple' => TRUE,
    '#default_value' => !empty($form_state['values']['column_select_expense']) ? $form_state['values']['column_select_expense'] : '',
  );

  $form['columns']['nycha_column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required.">*</span>'),
    '#options' => _checkbook_datafeeds_budget_column_options(Datasource::NYCHA, $format),
    '#multiple' => TRUE,
    '#default_value' => !empty($form_state['values']['nycha_column_select']) ? $form_state['values']['nycha_column_select'] : '',
  );

  //Hidden Field for multi-select
  if ($data_source == Datasource::NYCHA) {
    $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
  } else {
    $multi_select_hidden = isset($form_state['input']['column_select_expense']) ? '|' . implode('||', $form_state['input']['column_select_expense']) . '|' : '';
  }

  //Hidden Field for multi-select
  $form['columns']['hidden_multiple_value'] = array(
    '#value' => $multi_select_hidden,
    '#type' => 'hidden',
  );
  //</editor-fold>

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
 * @param $form
 * @param $form_state
 * @param $data_source
 * @return mixed
 */
function checkbook_datafeeds_budget_filter_data($form, $form_state, $data_source = Datasource::CITYWIDE){
  $agency_options = _agency_options($data_source);
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
  return $form;
}

/**
 * @param $form
 * @param $form_state
 * @param $data_source
 * @return mixed
 */
function checkbook_datafeeds_nycha_budget_filter_data($form, $form_state, $data_source){
  $first_time_load = checkbook_datafeeds_is_first_load($form_state, $data_source);
  $form['filter']['nycha-leftbegin'] = array(
    '#markup' => '<div class="column column-left">',
  );
  $form['filter']['nycha_fiscal_year'] = array(
    '#type' => 'select',
    '#title' => t('Year:'),
    '#options' => CheckbookDateUtil::getNychaBudgetFiscalYears(),
    '#default_value' => !empty($form_state['values']['nycha_fiscal_year']) ? $form_state['values']['nycha_fiscal_year'] : CheckbookDateUtil::getCurrentFiscalYear(),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield fiscalyear">',
    '#suffix' => '</div>',
  );

  $expenseCategories = _budget_expcat_options(NUll, NUll, NULL, Datasource::NYCHA);
  $form['filter']['nycha_expense_category'] = array(
    '#type' => 'select',
    '#title' => t('Expense Category:'),
    '#options' => $expenseCategories['options'],
    '#option_attributes' => $expenseCategories['option_attributes'],
    '#prefix' => '<div id="expense-category" class="datafield expensecategory">',
    '#suffix' => '</div>',
    '#validated' => TRUE,
    '#default_value' => !empty($form_state['values']['nycha_expense_category']) ? $form_state['values']['nycha_expense_category'] : 'Select Expense Category',
  );

  $resp_center_options = _checkbook_advanced_search_get_responsibility_centers(Datasource::NYCHA, true);
  $form['filter']['nycha_resp_center'] = array(
    '#type' => 'select',
    '#title' => t('Responsibility<br/>Center:'),
    '#options' => $resp_center_options['options'],
    '#option_attributes' => $resp_center_options['option_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield nycha_resp_center">',
    '#suffix' => '</div>',
    '#validated' => TRUE,
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_resp_center', $first_time_load, 'Select Responsibility Center'),
  );
  $funding_source_options = _funding_source_options(Datasource::NYCHA);
  $form['filter']['nycha_funding_source'] = array(
    '#type' => 'select',
    '#title' => t('Funding Source:'),
    '#options' => $funding_source_options['options'],
    '#option_attributes' => $funding_source_options['option_attributes'],
    '#prefix' => '<div class="datafield nycha_funding_source nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#validated' => TRUE,
    '#default_value' => !empty($form_state['values']['nycha_funding_source']) ? $form_state['values']['nycha_funding_source'] : 'Select Funding Source',
  );

  $program_options = _budget_program_options(Datasource::NYCHA);
  $form['filter']['nycha_program'] = array(
    '#type' => 'select',
    '#title' => t('Program:'),
    '#options' => $program_options['options'],
    '#option_attributes' => $program_options['option_attributes'],
    '#prefix' => '<div class="datafield program nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#validated' => TRUE,
    '#default_value' => !empty($form_state['values']['nycha_program']) ? $form_state['values']['nycha_program'] : 'Select Program',
  );

  $project_options = _budget_project_options(Datasource::NYCHA);
  $form['filter']['nycha_project'] = array(
    '#type' => 'select',
    '#title' => t('Project:'),
    '#options' => $project_options['options'],
    '#option_attributes' => $project_options['option_attributes'],
    '#prefix' => '<div class="datafield project nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#validated' => TRUE,
    '#default_value' => !empty($form_state['values']['nycha_project']) ? $form_state['values']['nycha_project'] : 'Select Project',
  );

  $budget_type_options = _budget_type_options(Datasource::NYCHA);
  $form['filter']['nycha_budget_type'] = array(
    '#type' => 'select',
    '#title' => t('Budget Type:'),
    '#options' => $budget_type_options['options'],
    '#option_attributes' => $budget_type_options['option_attributes'],
    '#prefix' => '<div class="datafield budgettype nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#validated' => TRUE,
    '#default_value' => !empty($form_state['values']['nycha_budget_type']) ? $form_state['values']['nycha_budget_type'] : 'Select Budget Type',
  );

  $form['nycha_budget_type_hidden'] = array(
    '#title' => 'nycha_budget_type_hidden',
    '#value' => $form_state['values']['nycha_budget_type'],
    '#type' => 'hidden'
  );

  $budget_name_options = _budget_name_options(Datasource::NYCHA);
  $form['filter']['nycha_budget_name'] = array(
    '#type' => 'select',
    '#title' => t('Budget Name:'),
    '#options' => $budget_name_options['options'],
    '#option_attributes' => $budget_name_options['option_attributes'],
    '#prefix' => '<div class="datafield budgetname nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#validated' => TRUE,
    '#default_value' => !empty($form_state['values']['nycha_budget_name']) ? $form_state['values']['nycha_budget_name'] : 'Select Budget Name',
  );

  $form['nycha_budget_name_hidden'] = array(
    '#title' => 'nycha_budget_name_hidden',
    '#value' => $form_state['values']['nycha_budget_name'],
    '#type' => 'hidden'
  );

  $form['filter']['nycha-leftend'] = array(
    '#markup' => '</div>',
  );
  $form['filter']['nycha-rightbegin'] = array(
    '#markup' => '<div class="column column-right">',
  );
  //Adopted Amount
  $form['filter']['nycha_adopted_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_adopted_from datarange"><label>Adopted:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['nycha_adopted_from']) ? $form_state['values']['nycha_adopted_from'] : '',
  );
  $form['filter']['nycha_adopted_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['nycha_adopted_to']) ? $form_state['values']['nycha_adopted_to'] : '',
  );
  //Modified Amount
  $form['filter']['nycha_modified_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_modified_from datarange"><label>Modified:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['nycha_modified_from']) ? $form_state['values']['nycha_modified_from'] : '',
  );
  $form['filter']['nycha_modified_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['nycha_modified_to']) ? $form_state['values']['nycha_modified_to'] : '',
  );
  //Committed Amount
  $form['filter']['nycha_committed_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_committed_from datarange"><label>Committed:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['nycha_committed_from']) ? $form_state['values']['nycha_committed_from'] : '',
  );
  $form['filter']['nycha_committed_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['nycha_committed_to']) ? $form_state['values']['nycha_committed_to'] : '',
  );
  //Encumbered  Amount
  $form['filter']['nycha_encumbered_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_encumbered_from datarange"><label>Encumbered:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['nycha_encumbered_from']) ? $form_state['values']['nycha_encumbered_from'] : '',
  );
  $form['filter']['nycha_encumbered_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['nycha_encumbered_to']) ? $form_state['values']['nycha_encumbered_to'] : '',
  );
  //Actual Amount
  $form['filter']['nycha_actual_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_actual_from datarange"><label>Actual Amount:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['nycha_actual_from']) ? $form_state['values']['nycha_actual_from'] : '',
  );
  $form['filter']['nycha_actual_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['nycha_actual_to']) ? $form_state['values']['nycha_actual_to'] : '',
  );
  $form['filter']['nycha-rightend'] = array(
    '#markup' => '</div>',
  );
  return $form;
}
/**
 * Budget column options.
 * @param string $dataSource
 * @param string $dataType
 *   Data type (xml or csv)
 *
 * @return array
 *   API structured array for select box #options
 */
function _checkbook_datafeeds_budget_column_options($dataSource, $dataType){
  $options = DatafeedsConfigUtil::getConfig(strtolower(Domain::$BUDGET));

  $options = $options['data_source'][$dataSource] ?? $options['data_source'][Datasource::CITYWIDE];
  $options = $options['data_type'][$dataType] ?? $options['data_type']['default'];

  ksort($options);
  return $options;
}

/**
 * Stores form values and moves to next step of Data Feeds wizard in Budget.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_budget_next_submit($form, &$form_state){
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
function checkbook_datafeeds_budget_confirmation($form, &$form_state){
  //Get current data source
  $data_source = $form_state['input']['datafeeds-budget-domain-filter'];
  $feed = checkbook_datafeeds\FeedFactory::getFeed($data_source, Domain::$BUDGET);
  return $feed->process_confirmation($form, $form_state);
}

/**
 * Retreives form values from previous step and moves back one step in Budget.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_budget_confirmation_previous_submit($form, &$form_state){
  $current_step = &$form_state['step'];
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];
  $current_step = 'budget';
  $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
  $form_state['rebuild'] = TRUE;
}

/**
 * Validate handler for Budget section of form.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_budget_validate($form, &$form_state){
  $data_source = $form_state['input']['datafeeds-budget-domain-filter'];
  $feed = checkbook_datafeeds\FeedFactory::getFeed($data_source, Domain::$BUDGET);
  $feed->checkbook_datafeeds_budget_validate($form, $form_state);
}

/**
 * Filters selected columns to only contain columns configured for this domain and data_source
 *
 * @param array $selected_columns
 *   selected column array
 * @param string $data_source
 *   current selected data source (i.e. checkbook, checkbook_nycha)
 * @param string $response_type
 *   response type (i.e. csv, xml)
 * @param $export_order
 * @return array
 */
function checkbook_datafeeds_budget_filter_selected_columns($selected_columns, $data_source, $response_type, $export_order = null){
  //Get column configurations
  switch ($data_source) {
    case Datasource::NYCHA:
      $domain = 'budget_nycha';
      break;
    default:
      $domain = 'budget';
  }
  $domain_config = ConfigUtil::getDomainConfiguration($domain);
  //Get columns which should be included for the current data source
  $columns_included = $domain_config->$domain->dataset->displayConfiguration->$response_type->elementsColumn;

  //Filter out selected columns which are not configured to show for this data source
  $filtered_columns = array_intersect_key($selected_columns, (array)$columns_included);

  //Only for NYCHA, export order is not same as multi-select options
  if($data_source == Datasource::NYCHA && isset($export_order)) {
    $filtered_columns = [];
    foreach ($columns_included as $key => $value) {
      if (in_array($key, $selected_columns)) {
        $filtered_columns[$key] = $key;
      }
    }
  }
  return $filtered_columns;
}
