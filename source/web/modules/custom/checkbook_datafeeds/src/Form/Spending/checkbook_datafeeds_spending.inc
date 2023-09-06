<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

use Drupal\checkbook_api\config\ConfigUtil;
use Drupal\checkbook_datafeeds\Common\FeedFactory;
use Drupal\checkbook_datafeeds\DatafeedsConfigUtil;
use Drupal\checkbook_datafeeds\Utilities\FeedUtil;
use Drupal\checkbook_datafeeds\Utilities\FormUtil;
use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;

/**
 * First step of Spending section.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *Datafeed selected
 * @return array
 *   array of first step of Spending
 */
function checkbook_datafeeds_spending($form, &$form_state)
{
  $datatype = $form_state->get(['step_information', 'type', 'stored_values', 'format']);
  $form['loaded'] = array(
    '#markup' => '<div class="updated-date">Last updated: ' . FeedUtil::getDataFeedsUpdatedDate(CheckbookDomain::$SPENDING) . '</div>',
  );
  $form['requiredmessage'] = array(
    '#markup' => '<p class="required-message">Fields marked with an asterisk (<span class="form-required"></span>) are required.</p><br/>',
  );
  $form['description'] = array(
    '#markup' => '<h2 class="domain-title">Spending</h2>',
  );

  $data_source = $form_state->hasValue('datafeeds-spending-domain-filter') ? $form_state->getValue('datafeeds-spending-domain-filter') : Datasource::CITYWIDE;

  $form['datafeeds-spending-domain-filter'] = DatafeedsConfigUtil::dataSourceRadio($data_source, CheckbookDomain::$SPENDING);

  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('3. Filter the Data:'),
    '#attributes' => array('class' => array('spending-fieldset')),
    '#prefix' => '<div id="dynamic-filter-data-wrapper">',
    '#suffix' => '</div>',
    '#id' => 'edit-filter',
  );
//ERROR: Class 'NumberFormatter' not found in data_controller_dc_data_type()
  $form = checkbook_datafeeds_spending_filter_data($form, $form_state, $data_source);

  //GET COLUMNS

  $coldata_id = $form_state->getValue('column_select');
  //THE FOLLOWING WORKS INSERTING SELECTED
  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('4. Select Columns:'),
    '#id' => 'edit-columns',
  );

  $form['columns']['column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required."></span>'),
    '#options' => _checkbook_datafeeds_spending_column_options(Datasource::CITYWIDE, $datatype),
    '#multiple' => TRUE,
    '#validated' => TRUE,
    '#default_value' => checkbook_datafeeds_get_column_default($form, $form_state, 'column_select'),
    '#name' => 'column_select',
  );

  $form['columns']['oge_column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required.">*</span>'),
    '#options' => _checkbook_datafeeds_spending_column_options(Datasource::OGE, $datatype),
    '#multiple' => TRUE,
    '#validated' => TRUE,
    '#default_value' => checkbook_datafeeds_get_column_default($form, $form_state, 'oge_column_select'),
    '#name' => 'oge_column_select',
  );

  $form['columns']['nycha_column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required."></span>'),
    '#options' => _checkbook_datafeeds_spending_column_options(Datasource::NYCHA, $datatype),
    '#multiple' => TRUE,
    '#validated' => TRUE,
    '#default_value' => checkbook_datafeeds_get_column_default($form, $form_state, 'nycha_column_select'),
    '#name' => 'nycha_column_select',
  );

  //Hidden Field for multi-select
  switch ($data_source) {
    case Datasource::NYCHA:
      $multi_select_hidden = $form_state->hasValue('nycha_column_select') ? '|' . implode('||', $form_state->getValue('nycha_column_select')) . '|' : '';
      break;
    case Datasource::OGE:
      $multi_select_hidden = $form_state->hasValue('oge_column_select') ? '|' . implode('||', $form_state->getValue('oge_column_select')) . '|' : '';
      break;
    default:
      $multi_select_hidden = $form_state->hasValue('nycha_column_select') ? '|' . implode('||', $form_state->getValue('nycha_column_select')) . '|' : '';
  }

  $form['columns']['hidden_multiple_value'] = array(
    '#value' => $multi_select_hidden,
    '#type' => 'hidden',
  );
  $form['prev'] = array(
    '#type' => 'submit',
    '#value' => t('Previous'),
    '#name' => 'prev',
    '#submit' => array('initial_previous_submit'),
    '#limit_validation_errors' => array(),
    '#attributes' => array("onclick" => "
           jQuery('#checkbook-datafeeds-form').addClass('disable_me');
             jQuery('#datafeeds-rotator').css('display', 'block').addClass('loading_bigger_gif');
        ")
  );

  $form['feeds_spending_next'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
    '#submit' => array('checkbook_datafeeds_spending_next_submit'),
    '#attributes' => array("onclick" => "
           jQuery('#checkbook-datafeeds-form').addClass('disable_me');
             jQuery('#datafeeds-rotator').css('display', 'block').addClass('loading_bigger_gif');
        ")
  );

  $form['#attached']['library'][] = 'checkbook_datafeeds/datafeeds.spending';
  $form['#validate'] = array('checkbook_datafeeds_spending_validate');
  $form['#attributes'] = array('class' => array('spending', 'data-feeds-wizard',));
  return $form;
}

/**
 * @param $form
 * @param $form_state
 * @param $colname
 * @return mixed
 */
function checkbook_datafeeds_get_column_default($form, $form_state, $column_id)
{
  $col_default = $form_state->getValue($column_id) ?? '';
  return $col_default;
}

/**
 * @param $form
 * @param $form_state
 * @param $data_source
 * @return mixed
 */
function checkbook_datafeeds_spending_filter_data($form, $form_state, $data_source)
{
  $data_type = $form_state->get(['step_information', 'type', 'stored_values', 'format']);

  $first_time_load = checkbook_datafeeds_is_first_load($form_state, $data_source);
  $citywide_sc = checkbook_datafeeds_filter_get_default_value($form_state, 'expense_type', $first_time_load, 'Select Expense Category');
  $oge_sc = checkbook_datafeeds_filter_get_default_value($form_state, 'nycedc_expense_type', $first_time_load, 'Select Expense Category');
  $nycha_sc = checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_expense_type', $first_time_load, 'Select Expense Category');

  $citywide_expense_type_options = FormUtil::getSpendingCategories();
  $oge_expense_type_options = FormUtil::getSpendingCategories(Datasource::OGE);
  $nycha_expense_type_options = FormUtil::getSpendingCategories(Datasource::NYCHA);

  $nycha_expense_type_default_option = ['' => 'Total Spending'];
  if (isset($nycha_expense_type_options['options'])) {
    $nycha_expense_type_options['options'] = $nycha_expense_type_default_option + $nycha_expense_type_options['options'];
  }

  //Agency drop-down contains CityWide options
  $agency_options = FormUtil::getAgencies(Datasource::CITYWIDE);
  $form['filter']['agency'] = array(
    '#type' => 'select',
    '#title' => t('Agency:'),
    '#options' => $agency_options['options'],
  '#options_attributes' => $agency_options['options_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="column column-left"><div class="datafield agency">',
    '#suffix' => '</div>',
    '#id' => 'edit-agency',
    '#name' => 'agency',
    '#default_value' => $form_state->hasValue('agency') ? $form_state->getValue('agency') : 'Citywide (All Agencies)',
    '#validated' => true,
  );

  $form['filter']['dept'] = array(
    '#type' => 'select',
    '#title' => t('Department:'),
    '#prefix' => '<div id="dynamic-fields"><div class="datafield department citywide nycedc">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => $form_state->getValue('dept', 'Select Department'),
    '#id' => 'edit-dept',
    '#name' => 'dept',
    '#validated' => true,
  );
  $form['dept_hidden'] = array(
    '#title' => 'dept_hidden',
    '#value' => $form_state->getValue('dept'),
    '#type' => 'hidden'
  );
  $form['filter']['expense_category'] = array(
    '#type' => 'select',
    '#title' => t('Expense Category:'),
    '#prefix' => '<div id="expense-category"><div class="datafield expensecategory">',
    '#suffix' => '</div></div></div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-expense-category',
    '#name' => 'expense_category',
    '#default_value' => $form_state->hasValue('expense_category') ? $form_state->getValue('expense_category') : 'Select Expense Category',
    '#validated' => true
  );
  $form['expense_category_hidden'] = array(
    '#title' => 'expense_category_hidden',
    '#value' => $form_state->getValue('expense_category'),
    '#type' => 'hidden'
  );
  $form['filter']['expense_type'] = array(
    '#type' => 'select',
    '#title' => t('Spending Category:'),
    '#options' => $citywide_expense_type_options['options'],
    '#options_attributes' => $citywide_expense_type_options['options_attributes'],
    '#prefix' => '<div class="datafield expensetype citywide">',
    '#suffix' => '</div>',
    '#attributes' => [
      'class' => [
        'watch',
        'edit-expense-type'
      ]
    ],
      '#default_value' => $form_state->hasValue('expense_type') ? $form_state->getValue('expense_type') : 'Total Spending [ts]',
    '#name' => 'expense_type',
  );
//  NYC EDC
  $form['filter']['nycedc_expense_type'] = array(
    '#type' => 'select',
    '#title' => t('Spending Category:'),
    '#options' => $oge_expense_type_options['options'],
    '#options_attributes' => $oge_expense_type_options['options_attributes'],
    '#prefix' => '<div class="datafield expensetype nycedc">',
    '#suffix' => '</div>',
    '#attributes' => [
      'class' => [
        'watch',
        'edit-expense-type'
      ]
    ],
    '#default_value' => $form_state->hasValue('nycedc_expense_type') ? $form_state->getValue('nycedc_expense_type') : 'Total Spending [ts]',
    '#name' => 'nycedc_expense_type',
  );
//  NYCHA
  $form['filter']['nycha_expense_type'] = array(
    '#type' => 'select',
    '#title' => t('Spending Category:'),
    '#options' => $nycha_expense_type_options['options'],
    '#options_attributes' => $nycha_expense_type_options['options_attributes'],
    '#prefix' => '<div class="datafield expensetype nycha">',
    '#suffix' => '</div>',
    '#attributes' => [
      'class' => [
        'watch',
        'edit-expense-type'
      ]
    ],
    '#default_value' => $form_state->hasValue('nycha_expense_type') ? $form_state->getValue('nycha_expense_type') : 'Total Spending [ts]',

    '#name' => 'nycha_expense_type',
  );

  $cwIndustry = Formutil::getIndustry(Datasource::CITYWIDE, true);
  $form['filter']['industry'] = array(
    '#type' => 'select',
    '#title' => t('Industry:'),
    '#options' => $cwIndustry['options'],
    '#options_attributes' => $cwIndustry['option_attributes'],
    '#prefix' => '<div class="datafield industry citywide">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#multiple' => FALSE,
    '#id' => 'edit-industry',
    '#name' => 'industry',
    '#default_value' => $form_state->getValue('industry', ''),
  );

  $nychaIndustry = FormUtil::getIndustry(Datasource::NYCHA, true);
  $form['filter']['nycha_industry'] = array(
    '#type' => 'select',
    '#title' => t('Industry:'),
    '#options' => $nychaIndustry['options'],
    '#options_attributes' => $nychaIndustry['option_attributes'],
    '#prefix' => '<div class="datafield nycha_industry nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#multiple' => FALSE,
    '#id' => 'edit-nycha-industry',
    '#name' => 'nycha_industry',
    '#default_value' => $form_state->getValue('nycha_industry', ''),
  );

  $funding_source_options = FormUtil::getFundingSourceOptions(Datasource::NYCHA);
  $form['filter']['funding_source'] = array(
    '#type' => 'select',
    '#title' => t('Funding Source:'),
    '#options' => $funding_source_options['options'],
    '#options_attributes' => $funding_source_options['option_attributes'],
    '#prefix' => '<div class="datafield fundingsource nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#default_value' => $form_state->getValue('funding_source', ''),
  );

  $resp_center_options = FormUtil::getResponsibilityCenters(Datasource::NYCHA, true);

  $form['filter']['resp_center'] = array(
    '#type' => 'select',
    '#title' => t('Responsibility<br/>Center:'),
    '#options' => $resp_center_options['options'],
    '#options_attributes' => $resp_center_options['option_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield resp-center nycha">',
    '#suffix' => '</div>',
    '#id' => 'edit-resp-center',
    '#name' => 'resp_center',
    '#default_value' => $form_state->getValue('resp_center', ''),
  );

  $form['filter']['mwbe_category'] = array(
    '#type' => 'select',
    '#title' => t('M/WBE Category:'),
    '#options' => FormUtil::getMWBECategory(),
    '#prefix' => '<div class="datafield mwbecategory citywide">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#multiple' => FALSE,
    '#id' => 'edit-mwbe-category',
    '#name' => 'mwbe_category',
    '#default_value' => $form_state->getValue('mwbe_category', ''),
  );

  $form['filter']['catastrophic_event'] = array(
    '#type' => 'select',
    '#title' => t('Catastrophic Event:'),
    '#options' => FormUtil::getEventNameAndId(),
    '#prefix' => '<div class="datafield catastrophicevent citywide">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#multiple' => FALSE,
    '#id' => 'edit-catastrophic-event',
    '#name' => 'catastrophic_event',
    '#default_value' => $form_state->getValue('catastrophic_event', ''),
  );

  $form['filter']['hidden_data_format'] = array(
    '#value' => $data_type,
    '#type' => 'hidden',
  );

  $form['filter']['payee_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Payee Name:'),
    '#prefix' => '<div class="datafield payeename" id="df-payeename">',
    '#suffix' => '</div></div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-payee-name',
    '#name' => 'payee_name',
    '#size' => 100,
    '#maxlength' => 100,
    '#default_value' => $form_state->getValue('payee_name', ''),
  );
  $form['filter']['check_amt_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="column column-right ' . str_replace('_', '-', $data_source) . '"><div class="datafield datarange check_amount" id="df-check_amount"><label>Check Amount:</label><div class="ranges">',
    '#id' => 'edit-check_amt_from',
    '#name' => 'check_amt_from',
    '#default_value' => $form_state->getValue('check_amt_from', ''),
  );
  $form['filter']['check_amt_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#id' => 'edit-check_amt_to',
    '#name' => 'check_amt_to',
    '#default_value' => $form_state->getValue('check_amt_to', ''),
  );
  $form['filter']['spent_amt_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield datarange spent_amount nycha"><label>Amount Spent:</label><div class="ranges">',
    '#id' => 'edit-spent_amt_from',
    '#name' => 'spent_amt_from',
    '#default_value' => $form_state->getValue('spent_amt_from', ''),
  );
  $form['filter']['spent_amt_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#id' => 'edit-spent_amt_to',
    '#name' => 'spent_amt_to',
    '#default_value' => $form_state->getValue('spent_amt_to', ''),
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
    '#default_value' => $form_state->getValue('purchase_order_type', ''),
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
    '#default_value' => $form_state->getValue('contractno', ''),
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
    '#default_value' => $form_state->getValue('commodity_line', ''),
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
    '#default_value' => $form_state->getValue('entity_contract_number', ''),
  );
  $form['filter']['document_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Document ID:'),
    '#maxlength' => 36,
    '#prefix' => '<div class="datafield expenseid citywide nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-document-id',
    '#name' => 'document_id',
    '#default_value' => $form_state->getValue('document_id', ''),
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
    '#default_value' => $form_state->getValue('capital_project', ''),
  );
  $form['filter']['budget_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Budget Name:'),
    '#prefix' => '<div class="datafield budgetname nycedc">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-budget-name',
    '#name' => 'budget_name',
    '#default_value' => $form_state->getValue('budget_name', ''),
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
  $form['date_filter_hidden'] = array(
    '#title' => 'date_filter_hidden',
    '#value' => $form_state->getValue('date_filter'),
    '#type' => 'hidden'
  );
  $form['filter']['year_filter_start'] = array(
    '#markup' => '<div class="datafield year-filters last-item">',
  );

  $form['filter']['year'] = array(
    '#type' => 'select',
    '#options' => FormUtil::getYearOptions('all-years'),
    '#attributes' => array('class' => array('watch')),
    '#default_value' => $form_state->getValue('year', ''),
    '#prefix' => '<div id="year"><div class="datafield year citywide nycedc">',
    '#suffix' => '</div></div>',
    '#id' => 'edit-year',
    '#name' => 'year',
    '#validated' => true,

  );

  $form['filter']['nycha_year'] = array(
    '#type' => 'select',
    '#options' => FormUtil::getYearOptions('all-years', Datasource::NYCHA), // Remove domain argument after year-filter separation
    '#attributes' => array('class' => array('watch')),
    '#default_value' => $form_state->getValue('nycha_year', ''),
    '#prefix' => '<div id="year"><div class="datafield nycha_year nycha">',
    '#suffix' => '</div></div>',
    '#id' => 'edit-nycha-year',
    '#name' => 'nycha_year',
    '#validated' => true,

  );

  $form['filter']['issuedfrom'] = array(
    '#type' => 'date',
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => t('E.g., ' . date('Y-m-d')),
    '#prefix' => '<div class="datafield datarange issueddate"><div class="ranges">',
    '#default_value' => $form_state->getValue('issuedfrom', ''),
    '#attributes' => array('class' => array('datepicker')),
  );
  $form['filter']['issuedto'] = array(
    '#type' => 'date',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => t('E.g., ' . date('Y-m-d')),
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('issuedto', ''),
    '#attributes' => array('class' => array('datepicker')),
  );
  $form['filter']['year_filter_end'] = array(
    '#markup' => '</div>',
  );

  return $form;
}

/**
 * Implements hook_element_info_alter().
 */
function checkbook_datafeeds_element_info_alter(array &$types)
{
  $types['datetime']['#process'][] = 'datetime_tweaks_datetime_set_format';
}

/**
 * Element process callback for datetime fields.
 */
function datetime_tweaks_datetime_set_format($element)
{
  // Use d/m/Y format.
  $element['date']['#date_date_format'] = 'Y-m-d';
  return $element;
}

/**
 * Column options for Spending CheckbookDomain.
 *
 * @param $data_source
 * @param $data_type
 * @return array
 *   FAPI structured array for select box #options
 */
function _checkbook_datafeeds_spending_column_options($data_source, $data_type)
{
  $options = DatafeedsConfigUtil::getConfig(strtolower(CheckbookDomain::$SPENDING));
  $options = $options['data_source'][$data_source] ?? $options['data_source'][Datasource::CITYWIDE];
  $options = $options['data_type'][$data_type] ?? $options['data_type']['default'];
  ksort($options);
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
  $current_step = &$form_state->get('step');
  $form_state->set(['step_information', $current_step, 'stored_values'], $form_state->getValues());
  $current_step = 'spending_confirmation';

  if (!empty($form_state->get(['step_information', $current_step, 'stored_values']))) {
    $form_state->set('values', $form_state->get(['step_information', $current_step, 'stored_values']));
  } else {
    $form_state->setValue('values', array());
  }
  $form_state
    ->set('user_values', $form_state->getValues())
    ->set('page_num', 3)
    ->setRebuild();
}

/**
 * Filters selected columns to only contain columns configured for this CheckbookDomain snd data_source
 *
 * @param array $selected_columns
 *   selected column array
 * @param string $data_source
 *   current selected data source (i.e. checkbook, checkbook_oge)
 * @param string $response_type
 *   response type (i.e. csv, xml)
 * @param $export_order
 * @return array
 */
function checkbook_datafeeds_spending_filter_selected_columns($selected_columns, $data_source, $response_type, $export_order = null)
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

  //Only for NYCHA, export order is not same as multi-select options
  if ($data_source == Datasource::NYCHA && isset($export_order)) {
    $filtered_columns = [];
    foreach ($columns_included as $key => $value) {
      if (in_array($key, $selected_columns)) {
        $filtered_columns[$key] = $key;
      }
    }
  }
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
  $data_source = $form_state->getValue('datafeeds-spending-domain-filter');
  $feed = FeedFactory::getFeed($data_source, CheckbookDomain::$SPENDING);
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
  $current_step = &$form_state->get('step');
  $form_state->set(['step_information', $current_step, 'stored_values'], $form_state->getValues());

  $current_step = 'spending';
  $stored = $form_state->get(['step_information', 'spending', 'stored_values']);

  $form_state
    ->setValues($form_state->get('user_values'))
    ->set('page_num', 2)
    ->setRebuild();
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
  $data_source = $form_state->getValue('datafeeds-spending-domain-filter') ?? Datasource::CITYWIDE;
  $feed = FeedFactory::getFeed($data_source, CheckbookDomain::$SPENDING);
  $feed->checkbook_datafeeds_spending_validate($form, $form_state);
}
