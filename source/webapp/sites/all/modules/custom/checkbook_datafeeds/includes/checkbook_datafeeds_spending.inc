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
 * First step of Spending section.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of first step of Spending
 */
function checkbook_datafeeds_spending($form, &$form_state)
{
  $datatype = $form_state['step_information']['type']['stored_values']['format'];
  $data_source = $form_state['values']['datafeeds-spending-domain-filter'];
  $data_source = !isset($data_source) ? Datasource::CITYWIDE : $data_source;

  $form['loaded'] = array(
    '#markup' => '<div class="updated-date">Last updated: ' . _checkbook_datafeeds_get_updated_date('spending') . '</div>',
  );
  $form['requiredmessage'] = array(
    '#markup' => '<p class="required-message">Fields marked with an asterisk (<span class="form-required">*</span>) are required.</p><br/>',
  );
  $form['description'] = array(
    '#markup' => '<h2 class="domain-title">Spending</h2>',
  );

  $form['datafeeds-spending-domain-filter'] = DatafeedsConfigUtil::dataSourceRadio($data_source, 'spending');

  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('3. Filter the Data:'),
    '#attributes' => array('class' => array('spending-fieldset')),
    '#prefix' => '<div id="dynamic-filter-data-wrapper">',
    '#suffix' => '</div>',
    '#id' => 'edit-filter',
  );

  $form = checkbook_datafeeds_spending_filter_data($form, $form_state, $data_source);

  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('4. Select Columns:'),
    '#id' => 'edit-columns',
  );

  $form['columns']['column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required.">*</span>'),
    '#options' => _checkbook_datafeeds_spending_column_options(Datasource::CITYWIDE, $datatype),
    '#multiple' => TRUE,
    '#default_value' => $form_state['values']['column_select'] ?? '',
    '#id' => 'edit-column-select',
    '#name' => 'column_select',
  );

  $form['columns']['oge_column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required.">*</span>'),
    '#options' => _checkbook_datafeeds_spending_column_options(Datasource::OGE, $datatype),
    '#multiple' => TRUE,
    '#default_value' => $form_state['values']['oge_column_select'] ?? '',
    '#id' => 'edit-oge-column-select',
  );

  $form['columns']['nycha_column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required.">*</span>'),
    '#options' => _checkbook_datafeeds_spending_column_options(Datasource::NYCHA, $datatype),
    '#multiple' => TRUE,
    '#default_value' => $form_state['values']['nycha_column_select'] ?? '',
    '#id' => 'edit-nycha-column-select',
  );

  //Hidden Field for multi-select
  switch ($data_source) {
    case Datasource::NYCHA:
      $multi_select_hidden = isset($form_state['values']['nycha_column_select']) ? '|' . implode('||', $form_state['values']['nycha_column_select']) . '|' : '';
      break;
    case Datasource::OGE:
      $multi_select_hidden = isset($form_state['values']['oge_column_select']) ? '|' . implode('||', $form_state['values']['oge_column_select']) . '|' : '';
      break;
    default:
      $multi_select_hidden = isset($form_state['values']['column_select']) ? '|' . implode('||', $form_state['values']['column_select']) . '|' : '';
  }

  $form['columns']['hidden_multiple_value'] = array(
    '#value' => $multi_select_hidden,
    '#type' => 'hidden',
  );

  $form['feeds_spending_next'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
    '#submit' => array('checkbook_datafeeds_spending_next_submit'),
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
      'data' => drupal_get_path('module', 'checkbook_datafeeds') . '/js/spending.js',
    )
  );
  $form['#validate'] = array('checkbook_datafeeds_spending_validate');
  $form['#attributes'] = array(
    'class' => array(
      'spending',
      'data-feeds-wizard',
    )
  );
  return $form;
}

function ckbk_datafeeds_spending_update_options_by_datasource(&$form, $form_state, $datasource = Datasource::CITYWIDE)
{
  $agency_options = _agency_options($datasource);

  $form['filter']['agency'] = array(
    '#type' => 'select',
    '#title' => t('Agency:'),
    '#options' => $agency_options['options'],
    '#option_attributes' => $agency_options['options_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="column column-left"><div class="datafield agency">',
    '#suffix' => '</div>',
    '#id' => 'edit-agency',
    '#name' => 'agency',
    '#default_value' => $form_state['values']['agency'] ?? 'Citywide (All Agencies)',
    '#validated' => true,
  );

  $departments = ['0' => 'Select Department'];
  $agency = $form_state['values']['agency'] ?? 0;
  $spending_cat = $form_state['values']['expense_category'] ?? 0;
  $year = $form_state['values']['year'] ?? 'FY' . CheckbookDateUtil::getCurrentFiscalYear();
  $agency_code = _emptyToZero($agency);
  if ($agency_code) {
    $solr_departments = _checkbook_autocomplete_spending_dept(/**$year**/ 0, $agency_code, $spending_cat, $datasource, false);
    if ($solr_departments && !is_array($solr_departments[0])) {
      $solr_departments = array_map('strtoupper', $solr_departments);
      if (sizeof($solr_departments)) {
        $kv_depts = [];
        foreach ($solr_departments as $v) {
          $val = strlen($v) > 20 ? substr($v, 0, 20) . '...' : $v;
          $kv_depts[$v] = $val;
        }
        $departments += $kv_depts;
      }
    }
  }

  $form['filter']['dept'] = array(
    '#type' => 'select',
    '#title' => t('Department:'),
    '#prefix' => '<div id="dynamic-fields"><div class="datafield department">',
    '#suffix' => '</div>',
    '#options' => $departments,
    '#attributes' => array('class' => array('watch')),
    '#default_value' => $form_state['values']['dept'] ?? 'Select Department',
    '#id' => 'edit-dept',
    '#name' => 'dept',
    '#validated' => true,
  );

  $expcats = ['0' => 'Select Expense Category'];
  if ($agency_code) {
    $dept = emptyToZero($form_state['values']['dept'] ?? 0);
    $solr_expcats = _checkbook_autocomplete_spending_expcategory($agency_code, $dept, 0, $year, $datasource, false);
    if ($solr_expcats) {
      $expcats = $expcats + $solr_expcats;
    }
  }
  $form['filter']['expense_category'] = array(
    '#type' => 'select',
    '#title' => t('Expense Category:'),
    '#prefix' => '<div id="expense-category"><div class="datafield expensecategory">',
    '#suffix' => '</div></div></div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-expense-category',
    '#name' => 'expense_category',
    '#options' => $expcats,
    '#default_value' => $form_state['values']['expense_category'] ?? 'Select Expense Category',
    '#validated' => true
  );
}

/**
 * @param $form
 * @param $form_state
 * @param $data_source
 * @return mixed
 */
function checkbook_datafeeds_spending_filter_data($form, $form_state, $data_source)
{
  $first_time_load = checkbook_datafeeds_is_first_load($form_state, $data_source);
  $citywide_sc = checkbook_datafeeds_filter_get_default_value($form_state, 'expense_type', $first_time_load, 'Select Expense Category');
  $oge_sc = checkbook_datafeeds_filter_get_default_value($form_state, 'oge_expense_type', $first_time_load, 'Select Expense Category');
  $nycha_sc = checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_expense_type', $first_time_load, 'Select Expense Category');

  $citywide_expense_type_options = _expense_type_options(Datasource::CITYWIDE);
  $oge_expense_type_options = _expense_type_options(Datasource::OGE);
  $nycha_expense_type_default_option = ['' => 'Select Spending Category'];
  $nycha_expense_type_options = _expense_type_options(Datasource::NYCHA);
  $nycha_expense_type_options['options'] = $nycha_expense_type_default_option + $nycha_expense_type_options['options'];

  ckbk_datafeeds_spending_update_options_by_datasource($form, $form_state, $data_source);

  $form['filter']['expense_type'] = array(
    '#type' => 'select',
    '#title' => t('Spending Category:'),
    '#options' => $citywide_expense_type_options['options'],
    '#option_attributes' => $citywide_expense_type_options['options_attributes'],
    '#prefix' => '<div class="datafield expensetype citywide">',
    '#suffix' => '</div>',
    '#attributes' => [
      'class' => [
        'watch',
        'edit-expense-type'
      ]
    ],
    '#default_value' => $citywide_sc,
    '#name' => 'expense_type',
  );
  $form['filter']['nycedc_expense_type'] = array(
    '#type' => 'select',
    '#title' => t('Spending Category:'),
    '#options' => $oge_expense_type_options['options'],
    '#option_attributes' => $oge_expense_type_options['options_attributes'],
    '#prefix' => '<div class="datafield expensetype nycedc">',
    '#suffix' => '</div>',
    '#attributes' => [
      'class' => [
        'watch',
        'edit-expense-type'
      ]
    ],
    '#default_value' => $oge_sc,
    '#name' => 'nycedc_expense_type',
  );
  $form['filter']['nycha_expense_type'] = array(
    '#type' => 'select',
    '#title' => t('Spending Category:'),
    '#options' => $nycha_expense_type_options['options'],
    '#option_attributes' => $nycha_expense_type_options['options_attributes'],
    '#prefix' => '<div class="datafield expensetype nycha">',
    '#suffix' => '</div>',
    '#attributes' => [
      'class' => [
        'watch',
        'edit-expense-type'
      ]
    ],
    '#default_value' => $nycha_sc,
    '#name' => 'nycha_expense_type',
  );

  $form['filter']['industry'] = array(
    '#type' => 'select',
    '#title' => t('Industry:'),
    '#options' => _industry_options(),
    '#prefix' => '<div class="datafield industry citywide">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#multiple' => FALSE,
    '#id' => 'edit-industry',
    '#name' => 'industry',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'industry', $first_time_load, ''),
  );

  $form['filter']['nycha_industry'] = array(
    '#type' => 'select',
    '#title' => t('Industry:'),
    '#options' => _industry_options(Datasource::NYCHA),
    '#prefix' => '<div class="datafield nycha_industry nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#multiple' => FALSE,
    '#id' => 'edit-nycha-industry',
    '#name' => 'nycha_industry',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_industry', $first_time_load, ''),
  );

  $form['filter']['funding_source'] = array(
    '#type' => 'select',
    '#title' => t('Funding Source:'),
    '#options' => _funding_source_options(Datasource::NYCHA),
    '#prefix' => '<div class="datafield fundingsource nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => !empty($form_state['values']['funding_source']) ? $form_state['values']['funding_source'] : 'All Funding Sources',
  );

  $resp_center_options = _checkbook_advanced_search_get_responsibility_centers(Datasource::NYCHA, true);

  $form['filter']['resp_center'] = array(
    '#type' => 'select',
    '#title' => t('Responsibility<br/>Center:'),
    '#options' => $resp_center_options['options'],
    '#option_attributes' => $resp_center_options['option_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield resp-center nycha">',
    '#suffix' => '</div>',
    '#id' => 'edit-resp-center',
    '#name' => 'resp_center',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'resp_center', $first_time_load, 'Select Responsibility Center'),
  );

  $form['filter']['mwbe_category'] = array(
    '#type' => 'select',
    '#title' => t('M/WBE Category:'),
    '#options' => _mwbe_category_options(),
    '#prefix' => '<div class="datafield mwbecategory citywide">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#multiple' => FALSE,
    '#id' => 'edit-mwbe-category',
    '#name' => 'mwbe_category',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'mwbe_category', $first_time_load, ''),
  );
  $form['filter']['payee_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Payee Name:'),
    '#prefix' => '<div class="datafield payeename" id="df-payeename">',
    '#suffix' => '</div></div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-payee-name',
    '#name' => 'payee_name',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'payee_name', $first_time_load, ''),
  );
  $form['filter']['check_amt_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="column column-right ' . str_replace('_', '-', $data_source) . '"><div class="datafield datarange check_amount" id="df-check_amount"><label>Check Amount:</label><div class="ranges">',
    '#id' => 'edit-check_amt_from',
    '#name' => 'check_amt_from',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'check_amt_from', $first_time_load, ''),
  );
  $form['filter']['check_amt_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#id' => 'edit-check_amt_to',
    '#name' => 'check_amt_to',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'check_amt_to', $first_time_load, ''),
  );
  $form['filter']['spent_amt_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield datarange spent_amount nycha"><label>Amount Spent:</label><div class="ranges">',
    '#id' => 'edit-spent_amt_from',
    '#name' => 'spent_amt_from',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'spent_amt_from', $first_time_load, ''),
  );
  $form['filter']['spent_amt_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#id' => 'edit-spent_amt_to',
    '#name' => 'spent_amt_to',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'spent_amt_to', $first_time_load, ''),
  );

  $form['filter']['purchase_order_type'] = array(
    '#type' => 'select',
    '#title' => t('Purchase Order<br/>Type:'),
    '#options' => array("All" => "All", "Blanket Agreement [BA]" => "Blanket Agreement [BA]", "Planned Agreement [PA]" => "Planned Agreement [PA]", "Purchase Order [PO]" => "Purchase Order [PO]"),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield purchase_order_type nycha">',
    '#suffix' => '</div>',
    '#id' => 'edit-purchase-order-type',
    '#name' => 'purchase_order_type',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'purchase_order_type', $first_time_load, 'All'),
  );

  $form['filter']['contractno'] = array(
    '#type' => 'textfield',
    '#title' => t('Contract ID:'),
    '#prefix' => '<div class="datafield contractno">',
    '#suffix' => '</div>',
    '#maxlength' => 32,
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-contractno',
    '#name' => 'contractno',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'contractno', $first_time_load, ''),
  );
  $form['filter']['commodity_line'] = array(
    '#type' => 'textfield',
    '#title' => t('Commodity Line:'),
    '#maxlength' => 3,
    '#prefix' => '<div class="datafield commodityline nycedc">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-commodity-line',
    '#name' => 'commodity_line',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'commodity_line', $first_time_load, ''),
  );
  $form['filter']['entity_contract_number'] = array(
    '#type' => 'textfield',
    '#title' => t('Entity Contract #:'),
    '#prefix' => '<div class="datafield entity_contract_number nycedc">',
    '#suffix' => '</div>',
    '#maxlength' => 15,
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-entity-contract-number',
    '#name' => 'entity_contract_number',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'entity_contract_number', $first_time_load, ''),
  );
  $form['filter']['document_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Document ID:'),
    '#maxlength' => 30,
    '#prefix' => '<div class="datafield expenseid citywide nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-document-id',
    '#name' => 'document_id',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'document_id', $first_time_load, ''),
  );
  $form['filter']['capital_project'] = array(
    '#type' => 'textfield',
    '#title' => t('Capital Project:'),
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield capitalproject citywide nycedc">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-capital-project',
    '#name' => 'capital_project',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'capital_project', $first_time_load, ''),
  );
  $form['filter']['budget_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Budget Name:'),
    '#prefix' => '<div class="datafield budgetname nycedc">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-budget-name',
    '#name' => 'budget_name',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'budget_name', $first_time_load, ''),
  );
  $form['filter']['date_filter'] = array(
    '#type' => 'radios',
    '#title' => t('Date Filter:'),
    '#options' => array(
      'Year',
      'Issue Date',
    ),
    '#prefix' => '<div id="datefilter"><div class="datafield datefilter clearfix">',
    '#suffix' => '</div></div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => 0,
    '#id' => 'edit-date-filter',
    '#name' => 'date_filter',
  );
  $form['filter']['year_filter_start'] = array(
    '#markup' => '<div class="datafield year-filters last-item">',
  );
  $form['filter']['year'] = array(
    '#type' => 'select',
    '#options' => _year_options('all-years'),
    '#attributes' => [
      'class' => ['watch'],
      'default_selected_value' => $form_state['values']['year'] ?? 'FY' . CheckbookDateUtil::getCurrentFiscalYear()
    ],
    '#default_value' => $form_state['values']['year'] ?? 'FY' . CheckbookDateUtil::getCurrentFiscalYear(),
    '#prefix' => '<div id="year"><div class="datafield year">',
    '#suffix' => '</div></div>',
    '#id' => 'edit-year',
    '#name' => 'year',
  );
  $form['filter']['issuedfrom'] = array(
    '#type' => 'textfield',
    '#size' => 10,
    '#maxlength' => 10,
    '#description' => t('E.g., ' . date('Y-m-d')),
    '#attributes' => array('class' => array('datepicker')),
    '#prefix' => '<div class="datafield datarange issueddate"><div class="ranges">',
    '#id' => 'edit-issuedfrom',
    '#name' => 'issuedfrom',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'issuedfrom', $first_time_load, ''),
  );
  $form['filter']['issuedto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 10,
    '#maxlength' => 10,
    '#description' => t('E.g., ' . date('Y-m-d')),
    '#attributes' => array('class' => array('datepicker')),
    '#suffix' => '</div></div>',
    '#id' => 'edit-issuedto',
    '#name' => 'issuedto',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'issuedto', $first_time_load, ''),
  );
  $form['filter']['year_filter_end'] = array(
    '#markup' => '</div>',
  );

  return $form;
}


/**
 * Column options for Spending domain.
 *
 * @param $data_source
 * @param $data_type
 * @return array
 *   FAPI structured array for select box #options
 */
function _checkbook_datafeeds_spending_column_options($data_source, $data_type)
{
  $options = DatafeedsConfigUtil::getConfig(strtolower('spending'));

  $options = !isset($options['data_source'][$data_source]) ? $options['data_source'][Datasource::CITYWIDE] : $options['data_source'][$data_source];
  $options = !isset($options['data_type'][$data_type]) ? $options['data_type']['default'] : $options['data_type'][$data_type];
  return $options;
}

/**
 * Stores form values and moves to next step of Data Feeds wizard in Spending.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_spending_next_submit($form, &$form_state)
{
  $current_step = &$form_state['step'];
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];
  $current_step = 'spending_confirmation';
  if (!empty($form_state['step_information'][$current_step]['stored_values'])) {
    $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
  } else {
    $form_state['values'] = array();
  }
  // Force rebuild with next step.
  $form_state['rebuild'] = TRUE;
}

/**
 * Filters selected columns to only contain columns configured for this domain snd data_source
 *
 * @param array $selected_columns
 *   selected column array
 * @param string $data_source
 *   current selected data source (i.e. checkbook, checkbook_oge)
 * @param string $response_type
 *   response type (i.e. csv, xml)
 * @return array
 */
function checkbook_datafeeds_spending_filter_selected_columns($selected_columns, $data_source, $response_type)
{
  //Get column configurations
  switch ($data_source) {
    case Datasource::NYCHA:
      $domain = 'spending_nycha';
      break;
    case Datasource::OGE:
      $domain = 'spending_oge';
      break;
    default:
      $domain = 'spending';
  }
  $domain_config = ConfigUtil::getDomainConfiguration($domain);
  //Get columns which should be included for the current data source
  $columns_included = $domain_config->$domain->dataset->displayConfiguration->$response_type->elementsColumn;
  //Filter out selected columns which are not configured to show for this data source
  $filtered_columns = array_intersect_key($selected_columns, (array)$columns_included);
  return $filtered_columns;
}

/**
 * Outputs selected search criteria and number of records found for Spending.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of Payroll confirmation step
 */
function checkbook_datafeeds_spending_confirmation($form, &$form_state)
{
  //Get current data source
  $data_source = $form_state['input']['datafeeds-spending-domain-filter'];
  $feed = checkbook_datafeeds\FeedFactory::getFeed($data_source,Domain::$SPENDING);
  return $feed->process_confirmation($form, $form_state);
}

/**
 * Retrieves form values from previous step and moves back one step in Spending.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_spending_confirmation_previous_submit($form, &$form_state)
{
  $current_step = &$form_state['step'];
  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];
  $current_step = 'spending';
  $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
  $form_state['rebuild'] = TRUE;
}

/**
 * Validate handler for Spending section of form.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_spending_validate(&$form, &$form_state)
{
  $data_source = $form_state['input']['datafeeds-spending-domain-filter'];
  $agency = $form_state['values']['agency'];
  $agency_code = emptyToZero($agency);
  $check_from = $form_state['values']['check_amt_from'];
  $check_to = $form_state['values']['check_amt_to'];
  $date_from = $form_state['values']['issuedfrom'];
  $date_to = $form_state['values']['issuedto'];
  $vendor = $form_state['values']['payee_name'];
  $contract_id = $form_state['values']['contractno'];

  ckbk_datafeeds_spending_update_options_by_datasource($form, $form_state, $data_source);

  if ($check_from && !is_numeric($check_from)) {
    form_set_error('check_amt_from', t('Check Amount must be a number.'));
  }
  if ($check_to && !is_numeric($check_to)) {
    form_set_error('check_amt_to', t('Check Amount must be a number.'));
  }
  if ($check_from && $check_to && $check_to < $check_from) {
    form_set_error('check_amt_to', t('Invalid range for Check Amount.'));
  }
  if ($date_from && !checkDateFormat($date_from)) {
    form_set_error('issuedfrom', t('Issue Date must be a valid date (YYYY-MM-DD).'));
  }
  if ($date_to && !checkDateFormat($date_to)) {
    form_set_error('issuedto', t('Issue Date must be a valid date (YYYY-MM-DD).'));
  }
  if ($date_from && $date_to && strtotime($date_to) < strtotime($date_from)) {
    form_set_error('issuedto', t('Invalid range for Issue Date.'));
  }
  //Contract ID
  if ($contract_id && preg_match("/(^MMA1|^MA1)/", strtoupper($contract_id))) {
    form_set_error('contractno', t('Spending information for MMA1 and MA1 Contracts can be viewed using the Contract Data feeds feature.'));
  }
  //Vendor
  if ($vendor) {
    $pattern = "/.*?(\\[.*?\\])/is";
    preg_match($pattern, $vendor, $vmatches);
    if (!$vmatches) {
      try {
        $dataController = data_controller_get_instance();
        switch ($data_source) {
          case Datasource::OGE:

            $query = "SELECT DISTINCT vendor_name ";
            $query .= "FROM disbursement_line_item_details dld ";
            $query .= "JOIN ref_spending_category rsc on rsc.spending_category_id = dld.spending_category_id ";
            $query .= "JOIN ref_agency ra on ra.agency_code = dld.agency_code ";
            $query .= "WHERE ra.is_display = 'Y' ";
            $query .= "AND vendor_name ilike '" . $vendor . "'";
            if ($agency_code) $query .= "AND dld.agency_code = '" . $agency_code . "'";

            $results = _checkbook_project_execute_sql($query, "main", $data_source);

            break;
          default:
            $results = $dataController->queryDataset('checkbook:vendor', array('vendor_customer_code'), array('vendor_customer_code' => $vendor));
            break;
        }
      } catch (Exception $e) {
        log_error($e->getMessage());
      }
      if (!($results[0] ?? true)) {
        switch ($data_source) {
          case Datasource::OGE:
            form_set_error('payee_name', t('Please enter a valid vendor name.'));
            break;
          default:
            form_set_error('payee_name', t('Please enter a valid vendor code.'));
            break;
        }
      }
    }
  }

  //Commodity Line
  if (Datasource::OGE == $data_source) {
    //Validate Commodity Line
    $entity_contractno = $form_state['values']['entity_contract_number'];
    $commodity_line = $form_state['values']['commodity_line'];
    if ($commodity_line && !is_numeric($commodity_line)) {
      form_set_error('commodity_line', t('Commodity Line must be a number.'));
    }
    if ($entity_contractno && !is_numeric($entity_contractno)) {
      form_set_error('entity_contract_number', t('Entity Contract # must be a number'));
    }
  }

  //Set the hidden filed values on Spending form
  $form_state['complete form']['agency_hidden']['#value'] = $form_state['values']['agency'];
  $form_state['complete form']['date_filter_hidden']['#value'] = $form_state['values']['date_filter'];
  //Hidden Field for multi-select
  switch ($data_source) {
    case Datasource::OGE:
      $multi_select_hidden = isset($form_state['input']['oge_column_select']) ? '|' . implode('||', $form_state['input']['oge_column_select']) . '|' : '';
      if (!$multi_select_hidden) {
        form_set_error('oge_column_select', t('You must select at least one column.'));
      }
      break;
    case Datasource::NYCHA:
      $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
      if (!$multi_select_hidden) {
        form_set_error('nycha_column_select', t('You must select at least one column.'));
      }
      break;
    default:
      $multi_select_hidden = isset($form_state['input']['column_select']) ? '|' . implode('||', $form_state['input']['column_select']) . '|' : '';
      if (!$multi_select_hidden) {
        form_set_error('column_select', t('You must select at least one column.'));
      }
  }
}
