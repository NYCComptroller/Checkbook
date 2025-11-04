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
 * First step of Contracts section.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form state array
 *
 * @return array
 *   Renderable array of first step of Contracts
 */
function checkbook_datafeeds_contracts($form, &$form_state)
{
  $data_type = $form_state->get(['step_information', 'type', 'stored_values', 'format']);

  $form['loaded'] = array(
    '#markup' => '<div class="updated-date">Last updated: ' . FeedUtil::getDataFeedsUpdatedDate(CheckbookDomain::$CONTRACTS) . '</div>',
  );
  $form['requiredmessage'] = array(
    '#markup' => '<p class="required-message">Fields marked with an asterisk (<span class="form-required"></span>) are required.</p><br/>',
  );
  $form['description'] = array(
    '#markup' => '<h2 class="domain-title">Contracts</h2>'
  );

  $data_source = $form_state->hasValue('datafeeds-contracts-domain-filter') ? $form_state->getValue('datafeeds-contracts-domain-filter') : Datasource::CITYWIDE;

  $form['datafeeds-contracts-domain-filter'] = DatafeedsConfigUtil::dataSourceRadio($data_source, CheckbookDomain::$CONTRACTS);

  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('3. Filter the Data:'),
    '#attributes' => array('class' => array('contracts-fieldset')),
    '#prefix' => '<div id="dynamic-filter-data-wrapper">',
    '#suffix' => '</div>',
    '#id' => 'edit-filter',
  );

  $form = checkbook_datafeeds_contracts_filter_data($form, $form_state, $data_source);

  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('4. Select Columns:'),
    // These provide the wrapper referred to in #ajax['wrapper'] above.
    '#prefix' => '<div id="dynamic-columns-wrapper">',
    '#suffix' => '</div>',
    '#id' => 'edit-columns',
  );
  $form['columns']['column_select_expense'] = array(
    '#type' => 'select',
    '#title' => t('Expense<span class="form-required" title="This field is required."></span>'),
    '#multiple' => TRUE,
    '#options' => _checkbook_datafeeds_contracts_column_options(Datasource::CITYWIDE, 'default', 'expense', $data_type),
    '#id' => 'edit-column-select-expense',
    '#default_value' => $form_state->getValue('column_select_expense', ''),
  );
  $form['columns']['column_select_revenue'] = array(
    '#type' => 'select',
    '#title' => t('Revenue<span class="form-required" title="This field is required."></span>'),
    '#multiple' => TRUE,
    '#options' => _checkbook_datafeeds_contracts_column_options(Datasource::CITYWIDE, 'default', 'revenue', $data_type),
    '#id' => 'edit-column-select-revenue',
    '#default_value' => $form_state->getValue('column_select_revenue', ''),
  );
  $form['columns']['column_select_all'] = array(
    '#type' => 'select',
    '#title' => t('All<span class="form-required" title="This field is required."></span>'),
    '#multiple' => TRUE,
    '#options' => _checkbook_datafeeds_contracts_column_options(Datasource::CITYWIDE, 'default', 'all', $data_type),
    '#id' => 'edit-column-select-all',
   '#default_value' => $form_state->getValue('column_select_all', ''),
  );
  $form['columns']['column_select_pending'] = array(
    '#type' => 'select',
    '#title' => t('Pending<span class="form-required" title="This field is required."></span>'),
    '#multiple' => TRUE,
    '#options' => _checkbook_datafeeds_contracts_column_options(Datasource::CITYWIDE, 'pending', 'pending', $data_type),
    '#id' => 'edit-column-select-pending',
    '#default_value' => $form_state->getValue('column_select_pending', ''),
  );
  $form['columns']['column_select_pending_all'] = array(
    '#type' => 'select',
    '#title' => t('All<span class="form-required" title="This field is required."></span>'),
    '#multiple' => TRUE,
    '#options' => _checkbook_datafeeds_contracts_column_options(Datasource::CITYWIDE, 'pending', 'pending', $data_type),
    '#id' => 'edit-column-select-pending-all',
    '#default_value' => $form_state->getValue('column_select_pending_all', ''),
  );
  $form['columns']['column_select_oge_expense'] = array(
    '#type' => 'select',
    '#title' => t('Expense<span class="form-required" title="This field is required."></span>'),
    '#multiple' => TRUE,
    '#options' => _checkbook_datafeeds_contracts_column_options(Datasource::OGE, 'default', 'expense', $data_type),
    '#id' => 'edit-column-select-oge-expense',
    '#default_value' => $form_state->getValue('column_select_oge_expense', ''),
  );

  $form['columns']['column_select_nycha'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required."></span>'),
    '#multiple' => TRUE,
    '#options' => _checkbook_datafeeds_contracts_column_options(Datasource::NYCHA, 'default', '', $data_type),
    '#id' => 'edit-column-select-nycha',
    '#default_value' => $form_state->getValue('column_select_nycha', ''),
  );
  //$multi_select_hidden = $form_state->hasValue(['input','column_select_nycha']) ? '|' . implode('||', $form_state->getValue(['input','column_select_nycha'])) . '|' : '';
  $multi_select_hidden = '';
  $form['columns']['hidden_data_format'] = array(
    '#value' => $data_type,
    '#type' => 'hidden',
  );
  if ($data_source == Datasource::NYCHA) {
    $multi_select_hidden = !empty($form_state->getValue('column_select_nycha')) ? '|' . implode('||', $form_state->getValue('column_select_nycha')) . '|' : '';
  } elseif ($data_source == Datasource::OGE) {
    $multi_select_hidden = !empty($form_state->getValue('column_select_oge_expense')) ? '|' . implode('||', $form_state->getValue('column_select_oge_expense')) . '|' : '';
  } else {
    if ($form_state->getValue('df_contract_status') == 'pending') {
      if ($form_state->getValue('category') != 'all') {
        $multi_select_hidden = !empty($form_state->getValue('column_select_pending')) ? '|' . implode('||', $form_state->getValue('column_select_pending')) . '|' : '';
      }
      if ($form_state->getValue('category') == 'all') {
        $multi_select_hidden = !empty($form_state->getValue('column_select_pending_all')) ? '|' . implode('||', $form_state->getValue('column_select_pending_all')) . '|' : '';
      }
    } else {
      if ($form_state->getValue('category') == 'all') {
        $multi_select_hidden = !empty($form_state->getValue('column_select_all')) ? '|' . implode('||', $form_state->getValue('column_select_all')) . '|' : '';
      }
      if ($form_state->getValue('category') == 'expense') {
        $multi_select_hidden = !empty($form_state->getValue('column_select_expense')) ? '|' . implode('||', $form_state->getValue('column_select_expense')) . '|' : '';
      }
      if ($form_state->getValue(['input', 'category']) == 'revenue') {
        $multi_select_hidden = !empty($form_state->getValue('column_select_revenue')) ? '|' . implode('||', $form_state->getValue('column_select_revenue')) . '|' : '';
      }
    }
  }
  //Hidden Field for multi-select
  $form['columns']['hidden_multiple_value'] = array(
    '#value' => $multi_select_hidden,
    '#type' => 'hidden',
  );

  //</editor-fold>

  //<editor-fold desc="Submit Buttons">
  $form['prev'] = array(
    '#type' => 'submit',
    '#value' => t('Previous'),
    '#name' => 'prev',
    '#submit' => array('initial_previous_submit'),
    '#limit_validation_errors' => array(),
    '#attributes' => array("onclick" => "
             jQuery('#checkbook-datafeeds-form').addClass('disable_me');
             jQuery('#datafeeds-rotator').show();
        ")
  );
  $form['feeds_contract_next'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
    '#submit' => array('checkbook_datafeeds_contracts_next_submit'),
    '#attributes' => array("onclick" => "
           jQuery('#checkbook-datafeeds-form').addClass('disable_me');
             jQuery('#datafeeds-rotator').show();
        ")
  );
  //</editor-fold>
  $form['#attached']['library'][] = 'checkbook_datafeeds/datafeeds.contracts';
  // $form['#attached']['library'][] = 'checkbook_datafeeds/datafeeds.multi-select';
  /*
    $form['#attached'] = array(
      'library' => array(
        array('system', 'ui.autocomplete', FALSE),
        array('system', 'ui.datepicker', FALSE),
        array('checkbook_datafeeds', 'jquery_multiselect', FALSE),
      ),
      'js' => array(
        'data' => drupal_get_path('module', 'checkbook_datafeeds') . '/js/contracts.js',
      ),
    );
    */
  $form['#validate'] = array('checkbook_datafeeds_contracts_validate');
  $form['#attributes'] = array('class' => array('contracts', 'data-feeds-wizard', 'datafeeds-domain-form'));
  return $form;
}

/**
 * @param $form
 * @param $form_state
 * @param $data_source
 * @return mixed
 */
function checkbook_datafeeds_contracts_filter_data($form, $form_state, $data_source)
{
  $first_time_load = checkbook_datafeeds_is_first_load($form_state, $data_source);
  $agency_options = FormUtil::getAgencies($data_source);
  $contract_type_options = FormUtil::getContractTypes(true);
  $award_method_options = FormUtil::getAwardMethod(Datasource::CITYWIDE, true);
  $sub_contract_status_options = FormUtil::getSubvendorStatusInPIP();
  $contract_includes_sub_vendors_options = FormUtil::getContractIncludesSubvendors();
  $form['filter']['fields_start'] = array(
    '#prefix' => '<div class="default-fields">',
  );
  $form['filter']['df_contract_status'] = array(
    '#type' => 'select',
    '#title' => t('Status:'),
    '#options' => array(
      'active' => 'Active',
      'registered' => 'Registered',
      'pending' => 'Pending'
    ),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="column column-left"><div class="datafield contractstatus">',
    '#suffix' => '</div>',
    '#id' => 'edit-df-contract-status',
    '#name' => 'df_contract_status',
    '#default_value' => $form_state->getValue('df_contract_status', 'Active'),
  );
  $form['filter']['vendor'] = array(
    '#type' => 'textfield',
    '#title' => t('Vendor:'),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield vendor">',
    '#suffix' => '</div>',
    '#id' => 'edit-vendor',
    '#name' => 'vendor',
    '#default_value' => $form_state->getValue('vendor', ''),
  );
  $form['filter']['mwbe_category'] = array(
    '#type' => 'select',
    '#title' => t('M/WBE Category:'),
    '#options' => FormUtil::getMWBECategory(),
    '#prefix' => '<div class="datafield mwbecategory">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#multiple' => FALSE,
    '#id' => 'edit-mwbe-category',
    '#name' => 'mwbe_category',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'mwbe_category', $first_time_load, ''),
    '#default_value' => $form_state->getValue('mwbe_category', ''),
  );
  $form['filter']['contract_type'] = array(
    '#type' => 'select',
    '#title' => t('Contract Type:'),
    '#options' => $contract_type_options['options'],
    '#options_attributes' => $contract_type_options['options_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield contracttype">',
    '#suffix' => '</div>',
    '#id' => 'edit-contract-type',
    '#name' => 'contract_type',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'contract_type', $first_time_load, 'Any Contract Type'),
    '#default_value' => $form_state->getValue('contract_type', ''),
  );
  $form['filter']['contractno'] = array(
    '#type' => 'textfield',
    '#title' => t('Contract ID:'),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield contractno">',
    '#suffix' => '</div>',
    '#id' => 'edit-contractno',
    '#name' => 'contractno',
    '#maxlength' => 32,
//  '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'contractno', $first_time_load, ''),
    '#default_value' => $form_state->getValue('contractno', ''),
  );
  $form['filter']['commodity_line'] = array(
    '#type' => 'textfield',
    '#title' => t('Commodity Line:'),
    '#prefix' => '<div class="datafield commodityline">',
    '#suffix' => '</div>',
    '#maxlength' => 3,
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-commodity-line',
    '#name' => 'commodity_line',
//  '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'commodity_line', $first_time_load, ''),
    '#default_value' => $form_state->getValue('commodity_line', ''),
  );
  $form['filter']['entity_contract_number'] = array(
    '#type' => 'textfield',
    '#title' => t('Entity Contract #:'),
    '#prefix' => '<div class="datafield entity_contract_number">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-entity-contract-number',
    '#maxlength' => 15,
    '#name' => 'entity_contract_number',
    '#default_value' => $form_state->getValue('entity_contract_number', ''),
  );
  $form['filter']['budget_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Budget Name:'),
    '#prefix' => '<div class="datafield budgetname">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-budget-name',
    '#name' => 'budget_name',
    '#default_value' => $form_state->getValue('budget_name', ''),
  );
  $form['filter']['contract_includes_sub_vendors_id'] = array(
    '#type' => 'select',
    '#title' => t('Contract Includes <br />Sub Vendors:'),
    '#options' => $contract_includes_sub_vendors_options['options'],
    '#options_attributes' => $contract_includes_sub_vendors_options['option_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield scntrc_status">',
    '#suffix' => '</div>',
    '#id' => 'edit-contract_includes_sub_vendors_id',
    '#name' => 'contract_includes_sub_vendors_id',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'contract_includes_sub_vendors_id', $first_time_load, 0),
    '#default_value' => $form_state->getValue('contract_includes_sub_vendors_id', ''),
    '#states' => array(
      'disabled' => array(
        array(
          array('select[name="df_contract_status"]' => array('value' => 'pending')),
          'or',
          array('select[name="category"]' => array('value' => 'revenue')),
        ),
      ),
    )
  );
  $form['filter']['sub_contract_status_id'] = array(
    '#type' => 'select',
    '#title' => t('Subcontract Status:'),
    '#options' => $sub_contract_status_options['options'],
    '#options_attributes' => $sub_contract_status_options['options_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield sub_contract_status_id">',
    '#suffix' => '</div>',
    '#id' => 'edit-sub_contract_status_id',
    '#name' => 'sub_contract_status_id',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'sub_contract_status_id', $first_time_load, 0),
    '#default_value' => $form_state->getValue('sub_contract_status_id', ''),
    '#states' => array(
      'disabled' => array(
        array(
          array('select[name="df_contract_status"]' => array('value' => 'pending')),
          'or',
          array('select[name="category"]' => array('value' => 'revenue')),
          'or',
          array('select[name="contract_includes_sub_vendors_id"]' => array('value' => '1')),
          'or',
          array('select[name="contract_includes_sub_vendors_id"]' => array('value' => '3')),
          'or',
          array('select[name="contract_includes_sub_vendors_id"]' => array('value' => '4'))
        ),
      ),
    )
  );
  $form['filter']['pin'] = array(
    '#type' => 'textfield',
    '#title' => t('PIN:'),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield pin">',
    '#suffix' => '</div>',
    '#id' => 'edit-pin',
    '#name' => 'pin',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'pin', $first_time_load, ''),
    '#default_value' => $form_state->getValue('pin', ''),
  );
  $form['filter']['currentamtfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield currentamt datarange"><span class="span-label">Current Amount:</span><div class="ranges">',
    '#id' => 'edit-currentamtfrom',
    '#name' => 'currentamtfrom',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'currentamtfrom', $first_time_load, ''),
    '#default_value' => $form_state->getValue('currentamtfrom', ''),
  );
  $form['filter']['currentamtto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#id' => 'edit-currentamtto',
    '#name' => 'currentamtto',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'currentamtto', $first_time_load, ''),
    '#default_value' => $form_state->getValue('currentamtto', ''),
  );
  $form['filter']['enddatefrom'] = array(
    '#type' => 'date',
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    //'#attributes' => array('class' => array('datepicker')),
    //'#attributes' => array('placeholder' => 'yyyy-mm-dd'),
    '#prefix' => '<div class="datafield enddate datarange"><label>End Date:</label><div class="ranges">',
    '#id' => 'edit-enddatefrom',
    '#name' => 'enddatefrom',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'enddatefrom', $first_time_load, ''),
    '#default_value' => $form_state->getValue('enddatefrom', ''),
  );
  $form['filter']['enddateto'] = array(
    '#type' => 'date',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    //'#attributes' => array('class' => array('datepicker')),
    //'#attributes' => array('placeholder' => 'yyyy-mm-dd'),
    '#suffix' => '</div></div>',
    '#id' => 'edit-enddateto',
    '#name' => 'enddateto',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'enddateto', $first_time_load, ''),
    '#default_value' => $form_state->getValue('enddateto', ''),
    );
  $form['filter']['regdatefrom'] = array(
    '#type' => 'date',
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    //'#attributes' => array('class' => array('datepicker')),
    //'#attributes' => ['placeholder' => 'yyyy-mm-dd'],
    '#prefix' => '<div class="datafield regdate datarange"><label>Registration Date:</label><div class="ranges">',
    '#id' => 'edit-regdatefrom',
    '#name' => 'regdatefrom',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'regdatefrom', $first_time_load, ''),
    '#default_value' => $form_state->getValue('regdatefrom', ''),
    '#states' => array(
      'disabled' => array(
        'select[name="df_contract_status"]' => array('value' => 'pending')
      )
    )
  );
  $form['filter']['regdateto'] = array(
    '#type' => 'date',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    //'#attributes' => array('class' => array('datepicker')),
    '#suffix' => '</div></div></div>',
    '#id' => 'edit-regdateto',
    '#name' => 'regdateto',
//  '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'regdateto', $first_time_load, ''),
    '#default_value' => $form_state->getValue('regdateto', ''),
    '#states' => array(
      'disabled' => array(
        'select[name="df_contract_status"]' => array('value' => 'pending')
      )
    )
  );
  $form['filter']['category'] = array(
    '#type' => 'select',
    '#title' => t('Category:'),
    '#options' => array(
      'all' => 'All',
      'expense' => 'Expense',
      'revenue' => 'Revenue',
    ),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="column column-right"><div class="datafield category">',
    '#suffix' => '</div>',
    '#id' => 'edit-category',
    '#name' => 'category',
//   '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'category', $first_time_load, 'all'),
    '#default_value' => $form_state->getValue('category', ''),
  );
  $form['filter']['conditional_category'] = array(
    '#type' => 'select',
    '#title' => t('Conditional Category:'),
    '#options' => FormUtil::getEventNameAndId('attributes'),
    '#prefix' => '<div class="datafield conditional_category">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#validated' => true,
    '#id' => 'edit-conditional_category',
    '#name' => 'conditional_category',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'conditional_category', $first_time_load, 0),
    '#default_value' => $form_state->getValue('conditional_category', ''),
  );

  $form['filter']['purpose'] = array(
    '#type' => 'textfield',
    '#title' => t('Purpose:'),
    '#maxlength' => 100,
    '#prefix' => '<div class="datafield purpose">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-purpose',
    '#name' => 'purpose',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'purpose', $first_time_load, ''),
    '#default_value' => $form_state->getValue('purpose', ''),
  );
  $form['filter']['agency'] = array(
    '#type' => 'select',
    '#title' => t('Agency:'),
    '#options' => $agency_options['options'],
    '#options_attributes' => $agency_options['options_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield agency">',
    '#suffix' => '</div>',
    '#id' => 'edit-agency',
    '#name' => 'agency',
    '#validated' => true,
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'agency', $first_time_load, 'Citywide (All Agencies)'),
    '#default_value' => $form_state->getValue('agency', ''),
  );
  $industries = FormUtil::getIndustry(Datasource::CITYWIDE, true);
  $form['filter']['industry'] = array(
    '#type' => 'select',
    '#title' => t('Industry:'),
    '#options' => $industries['options'],
    '#options_attributes' => $industries['option_attributes'],
    '#prefix' => '<div class="datafield industry">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#multiple' => FALSE,
    '#id' => 'edit-industry',
    '#name' => 'industry',
//  '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'industry', $first_time_load, ''),
    '#default_value' => $form_state->getValue('industry', ''),
  );
  $form['filter']['apt_pin'] = array(
    '#type' => 'textfield',
    '#title' => t('APT PIN:'),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield apt-pin">',
    '#suffix' => '</div>',
    '#id' => 'edit-apt-pin',
    '#name' => 'apt_pin',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'apt_pin', $first_time_load, ''),
    '#default_value' => $form_state->getValue('apt_pin', ''),
  );
  $form['filter']['award_method'] = array(
    '#type' => 'select',
    '#title' => t('Award Method:'),
    '#options' => $award_method_options['options'],
    '#options_attributes' => $award_method_options['option_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield awardmethod">',
    '#suffix' => '</div>',
    '#id' => 'edit-award-method',
    '#name' => 'award_method',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'award_method', $first_time_load, ''),
    '#default_value' => $form_state->getValue('award_method', ''),
  );
  $form['filter']['startdatefrom'] = array(
    '#type' => 'date',
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    //'#attributes' => array('class' => array('datepicker')),
    '#prefix' => '<div class="datafield startdate datarange"><label>Start Date:</label><div class="ranges">',
    //'#attributes' => ['placeholder' => 'yyyy-mm-dd'],
    '#id' => 'edit-startdatefrom',
    '#name' => 'startdatefrom',
//   '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'startdatefrom', $first_time_load, ''),
    '#default_value' => $form_state->getValue('startdatefrom', ''),
  );
  $form['filter']['startdateto'] = array(
    '#type' => 'date',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    '#suffix' => '</div></div>',
    //'#attributes' => ['placeholder' => 'yyyy-mm-dd'],
    '#id' => 'edit-startdateto',
    '#name' => 'startdateto',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'startdateto', $first_time_load, ''),
    '#default_value' => $form_state->getValue('startdateto', ''),
  );
  $form['filter']['recdatefrom'] = array(
    '#type' => 'date',
    // '#type' => 'date',
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    //'#attributes' => array('class' => array('datepicker')),
    //'#attributes' => ['placeholder' => 'yyyy-mm-dd'],
    '#prefix' => '<div class="datafield receiveddate datarange"><label>Received Date:</label><div class="ranges">',
    '#id' => 'edit-recdatefrom',
    '#name' => 'recdatefrom',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'recdatefrom', $first_time_load, ''),
    '#default_value' => $form_state->getValue('recdatefrom', ''),
    '#states' => array(
      'enabled' => array(
        'select[name="df_contract_status"]' => array('value' => 'pending')
      )
    )
  );
  $form['filter']['recdateto'] = array(
    '#type' => 'date',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    //'#attributes' => array('class' => array('datepicker')),
    //'#attributes' => ['placeholder' => 'yyyy-mm-dd'],
    '#suffix' => '</div></div>',
    '#id' => 'edit-recdateto',
    '#name' => 'recdateto',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'recdateto', $first_time_load, ''),
    '#default_value' => $form_state->getValue('recdateto', ''),
    '#states' => array(
      'enabled' => array(
        'select[name="df_contract_status"]' => array('value' => 'pending')
      )
    )
  );
  $form['filter']['year'] = array(
    '#type' => 'select',
    '#title' => t('Year:'),
    '#options' => FormUtil::getYearOptions('all-years'),
    '#attributes' => array('class' => array('watch')),
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'year', $first_time_load, '0'),
    '#default_value' => $form_state->getValue('year', ''),
    '#prefix' => '<div class="datafield year last-item">',
    '#suffix' => '</div></div></div>',
    '#id' => 'edit-year',
    '#name' => 'year',
    '#states' => array(
      'disabled' => array(
        'select[name="df_contract_status"]' => array('value' => 'pending')
      )
    )
  );


  //NYCHA Fields
  $form['filter']['nycha_fields_start'] = array(
    '#prefix' => '<div class="nycha-fields">',
  );
  $form = checkbook_datafeeds_nycha_contracts_filter_data($form, $form_state, $first_time_load);
  return $form;
}

/**
 * @param $form
 * @param $form_state
 * @param $first_time_load
 * @return mixed
 */
function checkbook_datafeeds_nycha_contracts_filter_data($form, $form_state, $first_time_load)
{
  $resp_center_options = FormUtil::getResponsibilityCenters(Datasource::NYCHA, true);
  $contract_types = FormUtil::getNychaContractTypes(Datasource::NYCHA, true);
  $award_methods = FormUtil::getAwardMethod(Datasource::NYCHA, true);
  $industries = FormUtil::getIndustry(Datasource::NYCHA, true);

  $form['filter']['purchase_order_type'] = array(
    '#type' => 'select',
    '#title' => t('Purchase Order<br/>Type:'),
    '#options' => array("All" => "All", "Blanket Agreement [BA]" => "Blanket Agreement [BA]", "Planned Agreement [PA]" => "Planned Agreement [PA]", "Purchase Order [PO]" => "Purchase Order [PO]"),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="column column-left"><div class="datafield purchase_order_type">',
    '#suffix' => '</div>',
    '#id' => 'edit-purchase-order-type',
    '#name' => 'purchase_order_type',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'purchase_order_type', $first_time_load, 'All'),
    '#default_value' => $form_state->getValue('purchase_order_type', ''),
  );
  $form['filter']['nycha_contract_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Contract ID:'),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield nycha-contract-id">',
    '#suffix' => '</div>',
    '#id' => 'edit-nycha-contract-id',
    '#name' => 'nycha_contract_id',
    '#maxlength' => 32,
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_contract_id', $first_time_load, ''),
    '#default_value' => $form_state->getValue('nycha_contract_id', ''),

  );
  $form['filter']['nycha_vendor'] = array(
    '#type' => 'textfield',
    '#title' => t('Vendor:'),
    '#maxlength' => 100,
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield nycha-vendor">',
    '#suffix' => '</div>',
    '#id' => 'edit-nycha-vendor',
    '#name' => 'nycha_vendor',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_vendor', $first_time_load, ''),
    '#default_value' => $form_state->getValue('nycha_vendor', ''),
  );
  $form['filter']['resp_center'] = array(
    '#type' => 'select',
    '#title' => t('Responsibility<br/>Center:'),
    '#options' => $resp_center_options['options'],
    '#options_attributes' => $resp_center_options['option_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield resp-center">',
    '#suffix' => '</div>',
    '#id' => 'edit-resp-center',
    '#name' => 'resp_center',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'resp_center', $first_time_load, 'Select Responsibility Center'),
    '#default_value' => $form_state->getValue('resp_center', ''),
  );
  $form['filter']['nycha_contract_type'] = array(
    '#type' => 'select',
    '#title' => t('Contract Type:'),
    '#options' => $contract_types['options'],
    '#options_attributes' => $contract_types['option_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield nycha-contract-type">',
    '#suffix' => '</div>',
    '#id' => 'edit-nycha-contract-type',
    '#name' => 'nycha_contract_type',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_contract_type', $first_time_load, 'Select Contract Type'),
    '#default_value' => $form_state->getValue('nycha_contract_type', ''),
  );
  $form['filter']['nycha_awd_method'] = array(
    '#type' => 'select',
    '#title' => t('Award Method:'),
    '#options' => $award_methods['options'],
    '#options_attributes' => $award_methods['option_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield nycha-awd-method">',
    '#suffix' => '</div>',
    '#id' => 'edit-nycha-awd-method',
    '#name' => 'nycha_awd_method',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_awd_method', $first_time_load, 'Select Award Method'),
    '#default_value' => $form_state->getValue('nycha_awd_method', ''),
  );
  $form['filter']['nycha_industry'] = array(
    '#type' => 'select',
    '#title' => t('Industry:'),
    '#options' => $industries['options'],
    '#options_attributes' => $industries['option_attributes'],
    '#prefix' => '<div class="datafield nycha-industry">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#multiple' => FALSE,
    '#id' => 'edit-nycha-industry',
    '#name' => 'nycha_industry',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_industry', $first_time_load, ''),
    '#default_value' => $form_state->getValue('nycha_industry', ''),
  );
  $form['filter']['nycha_currentamtfrom'] = array(
    '#type' => 'textfield',
    '#size' => 18,
    '#maxlength' => 18,
    '#prefix' => '<div class="datafield currentamt datarange"><span class="span-label">Current Amount:</span><div class="ranges">',
    '#id' => 'edit-nycha-currentamtfrom',
    '#name' => 'nycha_currentamtfrom',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_currentamtfrom', $first_time_load, ''),
    '#default_value' => $form_state->getValue('nycha_currentamtfrom', ''),
  );
  $form['filter']['nycha_currentamtto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 18,
    '#maxlength' => 18,
    '#suffix' => '</div></div>',
    '#id' => 'edit-nycha-currentamtto',
    '#name' => 'nycha_currentamtto',
//    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_currentamtto', $first_time_load, ''),
    '#default_value' => $form_state->getValue('nycha_currentamtto', ''),
  );
  $form['filter']['nycha_purpose'] = array(
    '#type' => 'textfield',
    '#title' => t('Purpose:'),
    '#maxlength' => 100,
    '#prefix' => '</div><div class="column column-right"><div class="datafield nycha-purpose">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#id' => 'edit-nycha-purpose',
    '#name' => 'nycha_purpose',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_purpose', $first_time_load, ''),
  );
  $form['filter']['nycha_apt_pin'] = array(
    '#type' => 'textfield',
    '#title' => t('PIN:'),
    '#maxlength' => 100,
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield nycha-apt-pin">',
    '#suffix' => '</div>',
    '#id' => 'edit-nycha-apt-pin',
    '#name' => 'nycha_apt_pin',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_apt_pin', $first_time_load, ''),
  );
  $form['filter']['nycha_startdatefrom'] = array(
    '#type' => 'date',
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    '#attributes' => array('class' => array('datepicker')),
    '#prefix' => '<div class="datafield startdate datarange"><label>Start Date:</label><div class="ranges">',
    '#id' => 'edit-nycha-startdatefrom',
    '#name' => 'nycha_startdatefrom',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_startdatefrom', $first_time_load, ''),
    '#states' => array(
      'disabled' => array(
        array(
          array('select[name="purchase_order_type"]' => array('value' => 'All')),
          'or',
          array('select[name="purchase_order_type"]' => array('value' => 'Purchase Order [PO]'))
        ),
      ),
    )
  );
  $form['filter']['nycha_startdateto'] = array(
    '#type' => 'date',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    '#suffix' => '</div></div>',
    '#attributes' => array('class' => array('datepicker')),
    '#id' => 'edit-nycha-startdateto',
    '#name' => 'nycha_startdateto',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_startdateto', $first_time_load, ''),
    '#states' => array(
      'disabled' => array(
        array(
          array('select[name="purchase_order_type"]' => array('value' => 'All')),
          'or',
          array('select[name="purchase_order_type"]' => array('value' => 'Purchase Order [PO]'))
        ),
      ),
    )
  );
  $form['filter']['nycha_enddatefrom'] = array(
    '#type' => 'date',
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    '#attributes' => array('class' => array('datepicker')),
    '#prefix' => '<div class="datafield enddate datarange"><label>End Date:</label><div class="ranges">',
    '#id' => 'edit-nycha-enddatefrom',
    '#name' => 'nycha_enddatefrom',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_enddatefrom', $first_time_load, ''),
    '#states' => array(
      'disabled' => array(
        array(
          array('select[name="purchase_order_type"]' => array('value' => 'All')),
          'or',
          array('select[name="purchase_order_type"]' => array('value' => 'Purchase Order [PO]'))
        ),
      ),
    )
  );
  $form['filter']['nycha_enddateto'] = array(
    '#type' => 'date',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    '#suffix' => '</div></div>',
    '#attributes' => array('class' => array('datepicker')),
    '#id' => 'edit-nycha-enddateto',
    '#name' => 'nycha_enddateto',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_enddateto', $first_time_load, ''),
    '#states' => array(
      'disabled' => array(
        array(
          array('select[name="purchase_order_type"]' => array('value' => 'All')),
          'or',
          array('select[name="purchase_order_type"]' => array('value' => 'Purchase Order [PO]'))
        ),
      ),
    )
  );
  $form['filter']['nycha_appr_datefrom'] = array(
    '#type' => 'date',
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    '#attributes' => array('class' => array('datepicker')),
    '#prefix' => '<div class="datafield appr-date datarange"><label>Approved Date:</label><div class="ranges">',
    '#id' => 'edit-nycha-appr-datefrom',
    '#name' => 'nycha_appr_datefrom',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_appr_datefrom', $first_time_load, ''),
    '#states' => array(
      'disabled' => array(
        array(
          array('select[name="purchase_order_type"]' => array('value' => 'All')),
          'or',
          array('select[name="purchase_order_type"]' => array('value' => 'Blanket Agreement [BA]')),
          'or',
          array('select[name="purchase_order_type"]' => array('value' => 'Planned Agreement [PA]'))
        ),
      ),
    )
  );
  $form['filter']['nycha_appr_dateto'] = array(
    '#type' => 'date',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    '#suffix' => '</div></div>',
    '#attributes' => array('class' => array('datepicker')),
    '#id' => 'edit-nycha-appr-dateto',
    '#name' => 'nycha_appr_dateto',
    '#default_value' => checkbook_datafeeds_filter_get_default_value($form_state, 'nycha_appr_dateto', $first_time_load, ''),
    '#states' => array(
      'disabled' => array(
        array(
          array('select[name="purchase_order_type"]' => array('value' => 'All')),
          'or',
          array('select[name="purchase_order_type"]' => array('value' => 'Blanket Agreement [BA]')),
          'or',
          array('select[name="purchase_order_type"]' => array('value' => 'Planned Agreement [PA]'))
        ),
      ),
    )
  );
  $form['filter']['nycha_year'] = array(
    '#type' => 'select',
    '#title' => t('Year:'),
    '#options' => FormUtil::getYearOptions('all-years', Datasource::NYCHA),
    '#attributes' => array('class' => array('watch')),
    '#default_value' => $form_state->getValue('nycha_year', '0'),
    '#prefix' => '<div class="datafield nycha-year last-item">',
    '#suffix' => '</div></div></div>',
    '#id' => 'edit-nycha-year',
    '#name' => 'nycha_year',
  );
  return $form;
}


/**
 * Stores form values and moves to next step of Data Feeds wizard in Contracts.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_contracts_next_submit($form, &$form_state)
{
  $input = $form_state->getUserInput();
  foreach (['contract_includes_sub_vendors_id', 'sub_contract_status_id'] as $key) {
    if (!isset($input[$key])) {
      $form_state->unsetValue($key);
    }
  }

  $current_step = &$form_state->get('step');
  $form_state->set(['step_information', $current_step, 'stored_values'], $form_state->getValues());
  $current_step = 'contracts_confirmation';

  if (!empty($form_state->get(['step_information', $current_step, 'stored_values']))) {
    $form_state->set('values', $form_state->get(['step_information', $current_step, 'stored_values']));
  } else {
    $form_state->setValue('values', array());
  }

  $form_state
    ->set('user_values', $form_state->getValues() )
    ->set('page_num', 3)
    // Since we have logic in our buildForm() method, we have to tell the form
    // builder to rebuild the form. Otherwise, even though we set 'page_num'
    // to 2, the AJAX-rendered form will still show page 1.
    ->setRebuild();
}

/**
 * Outputs selected search criteria and number of records found for Contracts.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of Contracts confirmation step
 */
function checkbook_datafeeds_contracts_confirmation($form, &$form_state)
{
  //Get current data source
  $data_source = $form_state->getValue('datafeeds-contracts-domain-filter');
  $feed = FeedFactory::getFeed($data_source, CheckbookDomain::$CONTRACTS);
  return $feed->process_confirmation($form, $form_state);
}

/**
 * Column options for contracts domain.
 *
 * @param $data_source
 * @param $status
 * @param $category
 * @param string $data_type
 *   Type of data for Data Feeds to return (xml or csv)
 * @return array
 *   FAPI structured array for select box #options
 */
function _checkbook_datafeeds_contracts_column_options($data_source, $status, $category, $data_type)
{
  $options = DatafeedsConfigUtil::getConfig(strtolower(CheckbookDomain::$CONTRACTS));
//  dpm($options);
  $options = !isset($options['data_source'][$data_source]) ? $options['data_source']['checkbook'] : $options['data_source'][$data_source];
  //dpm($options);
  $options = !isset($options['status'][$status]) ? $options['status']['default'] : $options['status'][$status];
  $options = !isset($options['category'][$category]) ? $options['category']['default'] : $options['category'][$category];
  $options = !isset($options['data_type'][$data_type]) ? $options['data_type']['default'] : $options['data_type'][$data_type];

  return $options;
}

/**
 * Reads the checkbook api configuration file to get the column order to override on export
 * @param $response_type
 * @param $data_source
 * @param $status
 * @param $expense_category
 * @param $year
 * @return array
 */
function _checkbook_datafeeds_contracts_override_column_options($response_type, $data_source, $status, $expense_category, $year)
{
  //Get column configurations
  $domain = CheckbookDomain::$CONTRACTS;
  if ($data_source == Datasource::OGE) {
    $domain = $domain . '_oge';
  } else if ($data_source == Datasource::NYCHA) {
    $domain = $domain . '_nycha';
  }
  $domain_config = ConfigUtil::getDomainConfiguration($domain);
  $domain_filter = checkbook_datafeeds_get_configuration_key($domain, $data_source, $status, $expense_category, $year);

  //Get columns which should be required to get but not display
  $override_columns = $domain_config->$domain_filter->dataset->displayConfiguration->$response_type->overrideColumns;
  return (array)$override_columns;
}

/**
 * Retreives form values from previous step and moves back one step in Contracts.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_contracts_confirmation_previous_submit($form, &$form_state)
{
//  $current_step = &$form_state['step'];
  $current_step = &$form_state->get('step');
//  $form_state['step_information'][$current_step]['stored_values'] = $form_state['values'];
  $form_state->set(['step_information', $current_step, 'stored_values'], $form_state->getValues());
  $current_step = 'contracts';
//  $form_state['values'] = $form_state['step_information'][$current_step]['stored_values'];
  $stored = $form_state->get(['step_information', 'contracts', 'stored_values']);
//  $form_state['rebuild'] = TRUE;
  $form_state
    ->setValues($form_state->get('user_values'))
    ->set('page_num', 2)
    ->setRebuild();

}

/**
 * Validate handler for Contracts section of form.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_contracts_validate($form, &$form_state)
{
  $data_source = $form_state->getValue('datafeeds-contracts-domain-filter') ?? Datasource::CITYWIDE;
  $feed = FeedFactory::getFeed($data_source, CheckbookDomain::$CONTRACTS);
  $feed->checkbook_datafeeds_contracts_validate($form, $form_state);
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
 * @param string $status
 *   status
 * @param string $expense_category
 *   expense category
 * @param string $year
 *   year
 * @return array
 */
function checkbook_datafeeds_contracts_filter_selected_columns($selected_columns, $data_source, $response_type, $status, $expense_category, $year)
{
  //Get column configurations
//  $domain = 'contracts';
  $domain = CheckbookDomain::$CONTRACTS;
  if ($data_source == Datasource::OGE) {
    $domain = $domain . '_oge';
  } else if ($data_source == Datasource::NYCHA) {
    $domain = $domain . '_nycha';
  }
  $domain_config = ConfigUtil::getDomainConfiguration($domain);
  $domain_filter = checkbook_datafeeds_get_configuration_key($domain, $data_source, $status, $expense_category, $year);

  //Get columns which should be included for the current data source
  $columns_included = $domain_config->$domain_filter->dataset->displayConfiguration->$response_type->elementsColumn;
  //Filter out selected columns which are not configured to show for this data source
  $filtered_columns = array_intersect_key($selected_columns, (array)$columns_included);

  return $filtered_columns;
}

/**
 * This function is to filter out columns which are needed in the result set to derive others but will not be displayed
 *
 * @param $response_type
 * @param $data_source
 * @param $status
 * @param $expense_category
 * @param $year
 * @return mixed
 */
function checkbook_datafeeds_contracts_add_required_columns($response_type, $data_source, $status, $expense_category, $year)
{
  //Get column configurations
  $domain = 'contracts';
  if ($data_source == Datasource::OGE) {
    $domain = $domain . '_oge';
  } else if ($data_source == Datasource::NYCHA) {
    $domain = $domain . '_nycha';
  }
  $domain_config = ConfigUtil::getDomainConfiguration($domain);
  $domain_filter = checkbook_datafeeds_get_configuration_key($domain, $data_source, $status, $expense_category, $year);

  //Get columns which should be required to get but not display
  $required_columns = $domain_config->$domain->dataset->displayConfiguration->$response_type->required_non_display_columns;

  return $required_columns;
}

/**
 * Returns the configuration key
 * @param $domain
 * @param $data_source
 * @param $status
 * @param $expense_category
 * @param $year
 * @return mixed
 */
function checkbook_datafeeds_get_configuration_key($domain, $data_source, $status, $expense_category, $year)
{
  $domain_filter = $domain;
  if ($data_source == Datasource::NYCHA) {
    $domain_filter .= ($year == '0') ? "_all_years" : "";
  } else {
    $domain_filter .= $status == 'pending' ? '_pending' : '_active';
    $domain_filter .= $status == 'pending' ? '' : '_' . $expense_category;
    $domain_filter .= (($year == 'All Years' || $year == '0') && $status != 'pending') ? "_all_years" : "";
  }
  return $domain_filter;
}
