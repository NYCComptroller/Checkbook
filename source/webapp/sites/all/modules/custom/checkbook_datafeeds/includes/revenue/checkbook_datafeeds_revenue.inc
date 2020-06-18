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
function checkbook_datafeeds_revenue($form, &$form_state){
  $format = $form_state['step_information']['type']['stored_values']['format'];

  //<editor-fold desc="Heading">
  $form['loaded'] = array(
    '#markup' => '<div class="updated-date">Last updated: ' . _checkbook_datafeeds_get_updated_date(Domain::$REVENUE) . '</div>',
  );
  $form['requiredmessage'] = array(
    '#markup' => '<p class="required-message">Fields marked with an asterisk (<span class="form-required">*</span>) are required.</p><br/>',
  );
  $form['description'] = array(
    '#markup' => '<h2 class="domain-title">Revenue</h2>'
  );

  $data_source = $form_state['values']['datafeeds-revenue-domain-filter'] ?? Datasource::CITYWIDE;
  $form['datafeeds-revenue-domain-filter'] = DatafeedsConfigUtil::dataSourceRadio($data_source, Domain::$REVENUE);
  //</editor-fold>

  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('3. Filter the Data:'),
    '#attributes' => array('class' => array('revenue-fieldset')),
  );
  //<editor-fold desc="CityWide Fields">
  $form['filter']['checkbook-fields-begin'] = array(
    '#markup' => '<div class="checkbook_fields">',
  );
  $form = checkbook_datafeeds_revenue_filter_data($form, $form_state, Datasource::CITYWIDE);
  $form['filter']['checkbook-fields-end'] = array(
    '#markup' => '</div>',
  );
  //</editor-fold>

  //<editor-fold desc="NYCHA Fields">
  $form['filter']['checkbook-nycha-fields-begin'] = array(
    '#markup' => '<div class="checkbook_nycha_fields">',
  );
  $form = checkbook_datafeeds_nycha_revenue_filter_data($form, $form_state, Datasource::NYCHA);
  $form['filter']['checkbook-nycha-fields-end'] = array(
    '#markup' => '</div>',
  );
  //</editor-fold>


  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('4. Select Columns:'),
  );

  //<editor-fold desc="CityWide and NYCHA Multi-select Options">
  $form['columns']['column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns'),
    '#options' => _checkbook_datafeeds_revenue_column_options(DataSource::CITYWIDE, $format),
    '#default_value' => !empty($form_state['values']['column_select']) ? $form_state['values']['column_select'] : '',
    '#required' => TRUE,
    '#multiple' => TRUE,
  );

  $form['columns']['nycha_column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required.">*</span>'),
    '#options' => _checkbook_datafeeds_revenue_column_options(Datasource::NYCHA, $format),
    '#multiple' => TRUE,
    '#default_value' => !empty($form_state['values']['nycha_column_select']) ? $form_state['values']['nycha_column_select'] : '',
  );

  //Hidden Field for multi-select
  if ($data_source == Datasource::NYCHA) {
    $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
  } else {
    $multi_select_hidden = isset($form_state['input']['column_select']) ? '|' . implode('||', $form_state['input']['column_select']) . '|' : '';
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
 * @param $form
 * @param $form_state
 * @param $data_source
 * @return mixed
 */
function checkbook_datafeeds_revenue_filter_data($form, $form_state, $data_source = Datasource::CITYWIDE){
  $agency_options = _agency_options($data_source);
  $revenue_fys = array('' => "All Fiscal Years");
  $revenue_fys = $revenue_fys + _checkbook_advanced_search_get_year(Domain::$REVENUE, 'datafeeds');

  $form['filter']['leftbegin'] = array(
    '#markup' => '<div class="column column-left">',
  );
  $form['filter']['budget_fiscal_year'] = array(
    '#type' => 'select',
    '#title' => t('Budget Fiscal Year:'),
    '#options' => CheckbookDateUtil::getLast10fiscalYears(),
    '#prefix' => '<div class="datafield budgetyear">',
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
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['recognizedto']) ? $form_state['values']['recognizedto'] : '',
  );
  $form['filter']['leftend'] = array(
    '#markup' => '</div><div class="column column-right">',
  );
  $form['filter']['fiscal_year'] = array(
    '#type' => 'select',
    '#title' => t('Fiscal Year:'),
    '#options' => $revenue_fys,
    '#prefix' => '<div class="datafield year">',
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
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['modifiedto']) ? $form_state['values']['modifiedto'] : '',
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
function checkbook_datafeeds_nycha_revenue_filter_data($form, $form_state, $data_source = Datasource::NYCHA){
  $first_time_load = checkbook_datafeeds_is_first_load($form_state, $data_source);
  $form['filter']['nycha-leftbegin'] = array(
    '#markup' => '<div class="column column-left">',
  );
  $form['filter']['nycha_budget_fy'] = array(
    '#type' => 'select',
    '#title' => t('Budget FY:'),
    '#options' => CheckbookDateUtil::getNychaBudgetFiscalYears(true),
    '#default_value' => !empty($form_state['values']['nycha_budget_fy']) ? $form_state['values']['nycha_budget_fy'] : CheckbookDateUtil::getCurrentFiscalYear(),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield nycha_budget_fy">',
    '#suffix' => '</div>',
  );

  $expenseCategories = _budget_expcat_options(Domain::$REVENUE, NUll, NUll, NULL, Datasource::NYCHA);
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

  $budget_type_options = _budget_type_options(Domain::$REVENUE, Datasource::NYCHA);
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

  $budget_name_options = _budget_name_options(Domain::$REVENUE,Datasource::NYCHA);
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

  //Revenue Category
  $form['filter']['nycha_rev_cat'] = array(
    '#type' => 'textfield',
    '#title' => t('Revenue Category:'),
    '#prefix' => '<div class="datafield nycha_rev_cat">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['nycha_rev_cat']) ? $form_state['values']['nycha_rev_cat'] : '',
  );

  //Revenue Class
  $form['filter']['nycha_rev_class'] = array(
    '#type' => 'textfield',
    '#title' => t('Revenue Class:'),
    '#prefix' => '<div class="datafield nycha_rev_class">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['nycha_rev_class']) ? $form_state['values']['nycha_rev_class'] : '',
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
  //Recognized Amount
  $form['filter']['nycha_recognized_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_recognized_from datarange"><label>Recognized:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['nycha_recognized_from']) ? $form_state['values']['nycha_recognized_from'] : '',
  );
  $form['filter']['nycha_recognized_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['nycha_recognized_to']) ? $form_state['values']['nycha_recognized_to'] : '',
  );
  //Remaining  Amount
  $form['filter']['nycha_remaining_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_remaining_from datarange"><label>Remaining:</label><div class="ranges">',
    '#default_value' => !empty($form_state['values']['nycha_remaining_from']) ? $form_state['values']['nycha_remaining_from'] : '',
  );
  $form['filter']['nycha_remaining_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => !empty($form_state['values']['nycha_remaining_to']) ? $form_state['values']['nycha_remaining_to'] : '',
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
 *   FAPI structured array for select box #options
 */
function _checkbook_datafeeds_revenue_column_options($dataSource, $dataType){
  $options = DatafeedsConfigUtil::getConfig(strtolower(Domain::$REVENUE));

  $options = $options['data_source'][$dataSource] ?? $options['data_source'][Datasource::CITYWIDE];
  $options = $options['data_type'][$dataType] ?? $options['data_type']['default'];

  ksort($options);
  return $options;

}

/**
 * Stores form values and moves to next step of Data Feeds wizard in Revenue.
 *
 * @param array $form
 *   Data Feed wizard form array
 * @param array $form_state
 *   Data Feed wizard form_state array
 */
function checkbook_datafeeds_revenue_next_submit($form, &$form_state){
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
function checkbook_datafeeds_revenue_confirmation($form, &$form_state){
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
  $modified_form = checkbook_datafeeds_end_of_confirmation_form($form, $form_state, $criteria, $responsetype, Domain::$REVENUE);
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
function checkbook_datafeeds_revenue_validate($form, &$form_state){
  $adoptedfrom = $form_state['values']['adoptedfrom'];
  $adoptedto = $form_state['values']['adoptedto'];
  if ($adoptedfrom && !is_numeric($adoptedfrom)) {
    form_set_error('adoptedfrom', t('Adopted From value must be a number.'));
  }
  if ($adoptedto && !is_numeric($adoptedto)) {
    form_set_error('adoptedto', t('Adopted To value must be a number.'));
  }
  if (is_numeric($adoptedfrom) && is_numeric($adoptedto) && $adoptedto < $adoptedfrom) {
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
  if (is_numeric($modifiedfrom) && is_numeric($modifiedto) && $modifiedto < $modifiedfrom) {
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
  if (is_numeric($recognizedfrom) && is_numeric($recognizedto) && $recognizedto < $recognizedfrom) {
    form_set_error('recognizedto', t('Invalid range for Recognized.'));
  }
}
