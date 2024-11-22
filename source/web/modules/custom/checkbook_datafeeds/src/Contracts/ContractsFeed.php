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

namespace Drupal\checkbook_datafeeds\Contracts;

use Drupal\checkbook_datafeeds\Utilities\FeedConstants;
use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_log\LogHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\data_controller\Controller\DataQueryControllerProxy;
use Exception;

/**
 * Class ContractsFeed
 * @package checkbook_datafeeds
 */
abstract class ContractsFeed
{
  /**
   * @var string
   */
  protected $data_source = '';
  /**
   * @var string
   */
  protected $response_type = 'csv';
  /**
   * @var
   */
  protected $form;
  /**
   * @var array
   */
  protected $user_criteria = [];
  /**
   * @var
   */
  protected $form_state;
  /**
   * @var array
   */
  protected $formatted_search_criteria;

  /**
   * @var string
   */
  protected $agency_label = 'Agency';
  /**
   * @var
   */
  protected $values;
  /**
   * @var array
   */
  protected $selected_columns;
  /**
   * @var array
   */
  protected $filtered_columns;

  protected $type_of_data = '';
  /**
   * @var
   */
  protected $filtered_columns_container;
  /**
   * @var array
   */
  protected $criteria;

  protected $bracket_value_pattern = "/.*?(\\[.*?\\])/is";

  protected $select_at_least_one_column_message = 'You must select at least one column.';

  protected $contract_id_32_char_message = 'Contract ID must be less than or equal to 32 characters.';

  protected $type_of_data_key = 'Type of Data';

  /**
   * ContractsFeed constructor.
   */
  public function __construct()
  {
    $this->user_criteria = [$this->type_of_data_key => $this->type_of_data];
  }

  /**
   * @param $form
   * @param $form_state
   * @return array
   */
  public function process_confirmation($form, FormStateInterface &$form_state)
  {
    $this->form = $form;
    $this->form_state = $form_state;

    $this->_process_user_criteria_confirmation();
    $this->user_criteria['Formatted'] = $this->formatted_search_criteria;
    $this->_process_criteria();

    $this->form_state->set(['step_information', 'confirmation', 'stored_values', 'criteria'], $this->criteria);
    $this->form_state->set(['step_information', 'confirmation', 'stored_values', 'user_criteria'], $this->user_criteria);
    if ($this->data_source == Datasource::NYCHA) {
      $form_state->set(['step_information', 'contracts', 'stored_values', 'nycha_column_select'], $this->filtered_columns);
    } elseif ($this->data_source == Datasource::OGE) {
      $form_state->set(['step_information', 'contracts', 'stored_values', 'oge_column_select'], $this->filtered_columns);
    } else {
      $form_state->set(['step_information', 'contracts', 'stored_values', 'column_select'], $this->filtered_columns);
    }
    $modified_form = checkbook_datafeeds_end_of_confirmation_form($this->form, $this->form_state, $this->criteria, $this->response_type, CheckbookDomain:: $CONTRACTS);
    $form_state = $this->form_state;
    return $modified_form;
  }

  protected function _process_user_criteria_confirmation()
  {
    $this->values = $this->form_state->get(['step_information', 'contracts', 'stored_values']);
    $this->response_type = $this->form_state->get(['step_information', 'type', 'stored_values', 'format']) ?? 'CSV';
    $this->user_criteria = [];

    switch ($this->data_source) {
      case Datasource::NYCHA:
        $this->user_criteria[$this->type_of_data_key] = 'Contracts_NYCHA';
        break;
      case Datasource::OGE:
        $this->user_criteria[$this->type_of_data_key] = 'Contracts_OGE';
        break;
      default:
        $this->user_criteria[$this->type_of_data_key] = 'Contracts';
        break;
    }

    $this->user_criteria['Type of File'] = $this->response_type;

    $this->values = $this->form_state->get(['step_information', 'contracts', 'stored_values']);

    $this->form['download_feeds'] = [
      '#markup' => '<h2 id="edit-description">Download Data</h2>',
    ];
    $this->form['columns'] = [
      '#type' => 'fieldset',
      '#title' => t('Selected Columns'),
    ];
    $this->form['#attributes'] = [
      'class' => [
        'confirmation-page',
        'data-feeds-wizard',
      ]
    ];

    //Used to maintain the order of the columns
    $this->selected_columns = checkbook_datafeeds_format_columns(CheckbookDomain::$CONTRACTS, $this->data_source);
    //Filter columns for current data source
    if ($this->data_source == Datasource::NYCHA) {
      $year = $this->values['nycha_year'];
    } else {
      $year = $this->values['year'];
    }
    $this->filtered_columns = checkbook_datafeeds_contracts_filter_selected_columns($this->selected_columns, $this->data_source, $this->response_type, $this->values['df_contract_status'], $this->values['category'],$year);
    foreach ($this->selected_columns as $column) {
      $this->form['columns'][$column] = array('#markup' => '<div>' . $column . '</div>');
      $this->user_criteria['Columns'][] = $column;
    }

    $this->filtered_columns = checkbook_datafeeds_contracts_filter_selected_columns($this->selected_columns, $this->data_source, $this->response_type, $this->values['df_contract_status'], $this->values['category'],$year);

    $this->form['filter'] = array(
      '#type' => 'fieldset',
      '#title' => t('Search Criteria'),
    );

    $this->formatted_search_criteria = array();

    $this->form['filter']['data_type'] = array(
      '#markup' => '<div><strong>'.$this->type_of_data_key.':</strong> Contracts</div>',
    );
    $this->formatted_search_criteria[$this->type_of_data_key] = 'Contracts';

    $this->form['filter']['file_type'] = array(
      '#markup' => '<div><strong>Type of File:</strong> ' . $this->form_state->get(['step_information', 'type', 'stored_values', 'format']). '</div>',
    );
    $this->formatted_search_criteria['Type of File'] = $this->form_state->get(['step_information', 'type', 'stored_values', 'format']);

    $this->_process_user_criteria_by_datasource();

    //Issued Date
    if ($this->form_state->getValue('date_filter') == 1) {
      if ($this->form_state->getValue('issuedfrom') && $this->form_state->getValue('issuedto')) {
        $this->form['filter']['issued_date'] = array(
          '#markup' => '<div><strong>'.FeedConstants::ISSUE_DATE.':</strong> From: ' . $this->form_state->getValue('issuedfrom') . ' To: ' . $this->form_state->getValue('issuedto') . '</div>'
        );
        $this->user_criteria['Issued Date After'] = $this->form_state->getValue('issuedfrom');
        $this->user_criteria['Issued Date Before'] = $this->form_state->getValue('issuedto');
        $this->formatted_search_criteria[FeedConstants::ISSUE_DATE] = 'From: ' . $this->form_state->getValue('issuedfrom') . ' To: ' . $this->form_state->getValue('issuedto');
      } elseif (!$this->form_state->getValue('issuedfrom') && $this->form_state->getValue('issuedto')) {
        $this->form['filter']['issued_date'] = array(
          '#markup' => '<div><strong>'.FeedConstants::ISSUE_DATE.':</strong> From: ' . $this->form_state->getValue('issuedto') . '</div>',
        );
        $this->user_criteria['Issued Date Before'] = $this->form_state->getValue('issuedto');
        $this->formatted_search_criteria[FeedConstants::ISSUE_DATE] = 'From: ' . $this->form_state->getValue('issuedto');
      } elseif ($this->form_state->getValue('issuedfrom') && !$this->form_state->getValue('issuedto')) {
        $this->form['filter']['issued_date'] = array(
          '#markup' => '<div><strong>'.FeedConstants::ISSUE_DATE.':</strong> To: ' . $this->form_state->getValue('issuedfrom') . '</div>',
        );
        $this->user_criteria['Issued Date After'] = $this->form_state->getValue('issuedfrom');
        $this->formatted_search_criteria[FeedConstants::ISSUE_DATE] = 'To: ' . $this->form_state->getValue('issuedfrom');
      }
    }
  }

  abstract protected function _process_user_criteria_by_datasource();

  /**
   * Convert values from Contracts section of form to an array format expected by API SearchCriteria.
   */
  private function _process_criteria()
  {
    $this->criteria = [
      'global' => [
        //Set data source for query
        'type_of_data' => $this->type_of_data,
        'records_from' => 1,
        'max_records' => \Drupal::config('check_book')->get('data_feeds')['max_record_limit'] ?? 200000,
      ],
      'responseColumns' => $this->filtered_columns
    ];

    if (strtolower($this->form_state->getValue('df_contract_status')) != 'pending' && strtolower($this->form_state->getValue('category')) != 'revenue' && $this->data_source != Datasource::OGE) {
      $temp_response_type = $this->form_state->get(['step_information', 'type', 'stored_values', 'format']);
      $category = $this->form_state->getValue('category');
      $status = $this->form_state->getValue('status');
      $year = $this->form_state->getValue('fiscal_year');
      $intended_order = _checkbook_datafeeds_contracts_override_column_options($temp_response_type, $this->data_source, $status, $category, $year);
      $this->criteria['responseColumns'] = checkbook_datafeeds_override_column_order($this->criteria['responseColumns'], $intended_order);
    }

    if ($this->form_state->getValue('dept') && !in_array($this->form_state->getValue('dept'), ['Select Department', '0']) && $this->values['agency'] != FeedConstants::CITYWIDE_ALL_AGENCIES) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('dept'), $department_matches);
      $this->criteria['value']['department_code'] = trim($department_matches[1], '[ ]');
    }

    if ($this->form_state->getValue('expense_category') && !in_array($this->form_state->getValue('expense_category'), ['Select Expense Category', '0']) && $this->values['agency'] != FeedConstants::CITYWIDE_ALL_AGENCIES) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('expense_category'), $expense_category_matches);
      $this->criteria['value']['expense_category'] = trim($expense_category_matches[1], '[ ]');
    }

    if ($this->form_state->getValue('payee_name')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('payee_name'), $payee_name_matches);
      if ($payee_name_matches) {
        $this->criteria['value']['payee_code'] = trim($payee_name_matches[1], '[ ]');
      } else {
        $this->criteria['value']['payee_name'] = $this->form_state->getValue('payee_name');
      }
    }

    if (!empty($this->form_state->getValue('check_amt_from')) || !empty($this->form_state->getValue('check_amt_to'))) {
      $this->criteria['range']['check_amount'] = array(
        checknull($this->form_state->getValue('check_amt_from')),
        checknull($this->form_state->getValue('check_amt_to')),
      );
    }

    if ($this->form_state->getValue('contractno')) {
      $this->criteria['value']['contract_id'] = strtoupper($this->form_state->getValue('contractno'));
    }

    if ($this->form_state->getValue('document_id')) {
      $this->criteria['value']['document_id'] = $this->form_state->getValue('document_id');
    }

    if ($this->form_state->getValue('entity_contract_number')) {
      $this->criteria['value']['entity_contract_number'] = $this->form_state->getValue('entity_contract_number');
    }

    if ($this->form_state->getValue('date_filter') == '1') {
      $this->_process_ranged_datasource_values('issuedfrom', 'issuedto', 'issue_date');
    }

    $this->_process_datasource_values();
  }

  // @ToDo: maybe seoparate the logic per datasource
  protected function _process_datasource_values()
  {
    if ($this->data_source != Datasource::NYCHA) {
      $this->_process_datasource_values_non_nycha();
    }
    //By data source
    switch ($this->data_source) {
      case Datasource::OGE:
        $this->_process_datasource_values_oge();
        break;
      case Datasource::NYCHA:
        $this->_process_datasource_values_nycha();
        break;
      default:
        $this->_process_datasource_values_citywide();
        break;
    }
  }

  protected function _process_datasource_values_non_nycha() {
    $values_new = $this->form_state->get(['step_information','contracts', 'stored_values']);
    if ($values_new['year'] && $values_new['year'] != '0' && $values_new['df_contract_status'] != 'pending') {
      if (startsWith($values_new['year'], 'F')) {
        $this->criteria['value']['fiscal_year'] = ltrim($values_new['year'], 'FY');
      } elseif (startsWith($values_new['year'], 'C')) {
        $this->criteria['value']['calendar_year'] = ltrim($values_new['year'], 'CY');
      }
    }

    $this->_process_ranged_datasource_values('currentamtfrom', 'currentamtto', 'current_amount', $values_new);

    if ($values_new['award_method'] && $values_new['award_method'] != 'No Award Method Selected') {
      preg_match($this->bracket_value_pattern, $values_new['award_method'], $awmatches);
      $this->criteria['value']['award_method'] = trim($awmatches[1], '[ ]');
    }

    $this->_process_datasource_values_check_and_set('contractno','contract_id', $values_new);

    if ($values_new['contract_type'] != 'No Contract Type Selected') {
      preg_match($this->bracket_value_pattern, $values_new['contract_type'], $ctypematches);
      $this->criteria['value']['contract_type'] = trim($ctypematches[1], '[ ]');
    }

    $this->_process_ranged_datasource_values('startdatefrom', 'startdateto', 'start_date', $values_new);

    $this->_process_ranged_datasource_values('enddatefrom', 'enddateto', 'end_date', $values_new);

    $this->_process_datasource_values_check_and_set('df_contract_status','status', $values_new);

    $this->_process_datasource_values_check_and_set('category','category', $values_new);

    if ($this->data_source != Datasource::OGE) {
      $this->_process_ranged_datasource_values('regdatefrom', 'regdateto', 'registration_date', $values_new);

      $this->_process_ranged_datasource_values('recdatefrom', 'recdateto', 'received_date', $values_new);
    }

    $this->_process_datasource_values_check_and_set('pin','pin', $values_new);

    $this->_process_datasource_values_check_and_set('apt_pin','apt_pin', $values_new);

    $this->_process_datasource_values_check_and_set('purpose','purpose', $values_new);
  }

  protected function _process_datasource_values_oge() {
    $values_new = $this->form_state->get(['step_information','contracts', 'stored_values']);
    if ($values_new['entity_contract_number']) {
      $this->criteria['value']['entity_contract_number'] = $values_new['entity_contract_number'];
    }
    if ($values_new['commodity_line']) {
      $this->criteria['value']['commodity_line'] = $values_new['commodity_line'];
    }
    if ($values_new['budget_name']) {
      $this->criteria['value']['budget_name'] = $values_new['budget_name'];
    }
    if ($values_new['vendor']) {
      $this->criteria['value']['prime_vendor'] = $values_new['vendor'];
    }
  }

  protected function _process_datasource_values_nycha() {
    $values_new = $this->form_state->get(['step_information','contracts', 'stored_values']);
    if ($values_new['purchase_order_type'] && $values_new['purchase_order_type'] != 'All') {
      preg_match($this->bracket_value_pattern, $values_new['purchase_order_type'], $pmatches);
      if ($pmatches) {
        $this->criteria['value']['purchase_order_type'] = trim($pmatches[1], '[ ]');
      } else {
        $this->criteria['value']['purchase_order_type'] = $values_new['purchase_order_type'];
      }
    }

    $this->_process_datasource_values_check_and_set('nycha_contract_id','contract_id', $values_new);

    if ($values_new['nycha_vendor']) {
      preg_match($this->bracket_value_pattern, $values_new['nycha_vendor'], $vmatches);
      if ($vmatches) {
        $this->criteria['value']['vendor_code'] = trim($vmatches[1], '[ ]');
      } else {
        $this->criteria['value']['vendor_name'] = $values_new['nycha_vendor'];
      }
    }

    $this->_process_datasource_values_nycha_match_pattrens();

    $this->_process_ranged_datasource_values('nycha_currentamtfrom', 'nycha_currentamtto', 'current_amount', $values_new);

    $this->_process_datasource_values_check_and_set('nycha_purpose','purpose', $values_new);

    $this->_process_datasource_values_check_and_set('nycha_apt_pin','pin', $values_new);

    $this->_process_ranged_datasource_values('nycha_startdatefrom', 'nycha_startdateto', 'start_date', $values_new);

    $this->_process_ranged_datasource_values('nycha_enddatefrom', 'nycha_enddateto', 'end_date', $values_new);

    $this->_process_ranged_datasource_values('nycha_appr_datefrom', 'nycha_appr_dateto', 'approved_date', $values_new);

    if ($values_new['nycha_year'] && $values_new['nycha_year'] != '0' && startsWith($values_new['nycha_year'], 'F')) {
      $this->criteria['value']['fiscal_year'] = ltrim($values_new['nycha_year'], 'FY');
    }
  }

  protected function _process_datasource_values_nycha_match_pattrens() {
    $values_new = $this->form_state->get(['step_information','contracts', 'stored_values']);
    if ($values_new['resp_center']) {
      preg_match($this->bracket_value_pattern, $values_new['resp_center'], $amatches);
      $this->criteria['value']['responsibility_center'] = trim($amatches[1], '[ ]');
    }
    if ($values_new['nycha_contract_type']) {
      preg_match($this->bracket_value_pattern, $values_new['nycha_contract_type'], $amatches);
      $this->criteria['value']['contract_type'] = trim($amatches[1], '[ ]');
    }
    if ($values_new['nycha_awd_method']) {
      preg_match($this->bracket_value_pattern, $values_new['nycha_awd_method'], $amatches);
      $this->criteria['value']['award_method'] = trim($amatches[1], '[ ]');
    }
    if ($values_new['nycha_industry']) {
      preg_match($this->bracket_value_pattern, $values_new['nycha_industry'], $amatches);
      $this->criteria['value']['industry'] = trim($amatches[1], '[ ]');
    }
  }

  protected function _process_datasource_values_citywide() {
    $values_new = $this->form_state->get(['step_information','contracts', 'stored_values']);
    if ($values_new['agency'] != FeedConstants::CITYWIDE_ALL_AGENCIES) {
      preg_match($this->bracket_value_pattern, $values_new['agency'], $amatches);
      $this->criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
    }
    if ($values_new['vendor']) {
      preg_match($this->bracket_value_pattern, $values_new['vendor'], $vmatches);
      if ($vmatches) {
        $this->criteria['value']['vendor_code'] = trim($vmatches[1], '[ ]');
      } else {
        $this->criteria['value']['vendor_code'] = $values_new['vendor'];
      }
    }
    if ($values_new['mwbe_category'] && $values_new['mwbe_category'] != 'Select Category') {
      $this->criteria['value']['mwbe_category'] = $values_new['mwbe_category'];
    }
    if ($values_new['industry'] && $values_new['industry'] != 'Select Industry') {
      preg_match($this->bracket_value_pattern, $values_new['industry'], $imatches);
      $this->criteria['value']['industry'] = trim($imatches[1], '[ ]');
    }

    if ($values_new['category'] != 'revenue' && ($values_new['conditional_category'] && ((substr($values_new['year'], -4) >= 2020) || $values_new['year'] == '0'))) {
      preg_match($this->bracket_value_pattern, $values_new['conditional_category'], $ematches);
      $this->criteria['value']['conditional_category'] = trim($ematches[1], '[ ]');
    }
    $this->_process_datasource_values_citywide_subvendors();
  }

  protected function _process_datasource_values_citywide_subvendors() {
    $values_new = $this->form_state->get(['step_information','contracts', 'stored_values']);
    if ($values_new['contract_includes_sub_vendors_id'] != '' && $values_new['contract_includes_sub_vendors_id'] != 0) {
      $this->criteria['value']['contract_includes_sub_vendors'] = $values_new['contract_includes_sub_vendors_id'];
    }
    if ($values_new['sub_contract_status_id'] != 'Select Status' && $values_new['sub_contract_status_id'] != 0 && !empty($values_new['sub_contract_status_id'])) {
      $this->criteria['value']['sub_contract_status'] = $values_new['sub_contract_status_id'];
    }
  }

  protected function _process_datasource_values_check_and_set($field_name, $criteria_key, $pvalues) {
    if ($pvalues[$field_name]) {
      $this->criteria['value'][$criteria_key] = $pvalues[$field_name];
    }
  }

  /**
   * This function will process ranged values for datasource and place inside criteria
   *
   * @param $start_field_name
   * @param $end_field_name
   * @param $criteria_key
   * @param $pvalues
   *
   * @return void
   */
  protected function _process_ranged_datasource_values($start_field_name, $end_field_name, $criteria_key, $pvalues=null) {
    if (is_null($pvalues)) {
      $start = $this->form_state->getValue($start_field_name);
      $end = $this->form_state->getValue($end_field_name);
    } else {
      $start = $pvalues[$start_field_name];
      $end = $pvalues[$end_field_name];
    }
    if ($start !== '' || $end !== '') {
      $this->criteria['range'][$criteria_key] = array(
        checknull($start),
        checknull($end),
      );
    }
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
    $data_source_temp = $form_state->getValue('datafeeds-contracts-domain-filter');
    if ($data_source_temp == Datasource::NYCHA) {
      $this->checkbook_datafeeds_contracts_validate_nycha($form, $form_state);
    } else {
      $this->checkbook_datafeeds_contracts_validate_oge_and_citywide($form, $form_state);
    }
    //Multi-select Columns
    $this->checkbook_datafeeds_contracts_validate_multiselect($form, $form_state);

    // Vendor:
    $this->checkbook_datafeeds_contracts_validate_vendor($form, $form_state);
  }

  protected function checkbook_datafeeds_contracts_validate_oge_and_citywide($form, &$form_state) {
    $data_source_temp = $form_state->getValue('datafeeds-contracts-domain-filter');
    $contract_id = $form_state->getValue('contractno');

    //Contract Id
    if ($contract_id && strlen($contract_id) > 32) {
      $form_state->setErrorByName('contractno', t($this->contract_id_32_char_message));
    }

    //Commodity Line
    if (Datasource::OGE == $data_source_temp) {
      $this->checkbook_datafeeds_contracts_validate_oge($form, $form_state);
    }

    // Start Date:
    checkbook_datafeeds_check_ranged_date($form_state, 'startdatefrom', 'startdateto', 'Start Date');

    // End Date:
    checkbook_datafeeds_check_ranged_date($form_state, 'enddatefrom', 'enddateto', 'End Date');

    // Registered Date:
    checkbook_datafeeds_check_ranged_date($form_state, 'regdatefrom', 'regdateto', 'Registered Date');

    // Received Date:
    checkbook_datafeeds_check_ranged_date($form_state, 'recdatefrom', 'recdateto', 'Received Date');

    // Current Amount:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'currentamtfrom', 'currentamtto', 'Current Amount');

    if ($form_state->getValue('df_contract_status') != 'pending' && $form_state->getValue('recdatefrom')) {
      $form_state->setErrorByName('recdatefrom', t('Received Date filter is not available on active or registered contracts.'));
    }
    else if($form_state->getValue('df_contract_status') != 'pending' && $form_state->getValue('recdateto')) {
      $form_state->setErrorByName('recdateto', t('Received Date filter is not available on active or registered contracts.'));
    }
    else if ($form_state->getValue('df_contract_status') == 'pending' && !empty($form_state->getValue('regdatefrom'))) {
      $form_state->setErrorByName('spentto', t('Registered1 Date filter is not available on pending contracts.'));
    }
    else if ($form_state->getValue('df_contract_status') == 'pending' && !empty($form_state->getValue('regdateto'))) {
      $form_state->setErrorByName('spentto', t('Registered2 Date filter is not available on pending contracts.'));
    }

    if (in_array(intval($form_state->getValue('contract_includes_sub_vendors_id')), array(1, 3, 4))) {
      $form_state->setValue('sub_contract_status_id', 0);
      $form_state->set(['complete form', 'sub_contract_status_id', '#value'], 0);
    }
  }

  protected function checkbook_datafeeds_contracts_validate_oge($form, &$form_state) {
    $entity_contractno = $form_state->getValue('entity_contract_number');
    $commodity_line = $form_state->getValue('commodity_line');
    if ($commodity_line && !is_numeric($commodity_line)) {
      $form_state->setErrorByName('contractno', t($this->contract_id_32_char_message));
    }
    if ($entity_contractno && !is_numeric($entity_contractno)) {
      $form_state->setErrorByName('contractno', t($this->contract_id_32_char_message));
    }
  }

  protected function checkbook_datafeeds_contracts_validate_multiselect($form, &$form_state) {
    $data_source_temp = $form_state->getValue('datafeeds-contracts-domain-filter');
    $activeexpense = $form_state->getValue('column_select_expense');
    $activerevenue = $form_state->getValue('column_select_revenue');
    $all = $form_state->getValue('column_select_all');
    $pending = $form_state->getValue('column_select_pending');
    $pending_all = $form_state->getValue('column_select_pending_all');
    $ogeexpense = $form_state->getValue('column_select_oge_expense');

    switch($data_source_temp) {
      case Datasource::NYCHA:
        $multi_select_hidden = !empty($form_state->getValue('column_select_nycha')) ? '|' . implode('||', $form_state->getValue('column_select_nycha')) . '|' : '';
        break;
      case Datasource::OGE:
        $multi_select_hidden = !empty($form_state->getValue('column_select_oge_expense')) ? '|' . implode('||', $form_state->getValue('column_select_oge_expense')) . '|' : '';
        if (!$ogeexpense) {
          $form_state->setErrorByName('column_select_oge_expense', t($this->select_at_least_one_column_message));
        }
        break;
      default:
        if ($form_state->getValue('df_contract_status') == 'pending' && $form_state->getValue('category') != 'all') {
          $multi_select_hidden = !empty($form_state->getValue('column_select_pending')) ? '|' . implode('||', $form_state->getValue('column_select_pending')) . '|' : '';
          if (!$pending) {
            $form_state->setErrorByName('column_select_pending', t($this->select_at_least_one_column_message));
          }
        }else if ($form_state->getValue('df_contract_status') == 'pending' && $form_state->getValue('category') == 'all') {
          $multi_select_hidden = !empty($form_state->getValue('column_select_pending_all')) ? '|' . implode('||', $form_state->getValue('column_select_pending_all')) . '|' : '';
          if (!$pending_all) {
            $form_state->setErrorByName('column_select_pending_all', t($this->select_at_least_one_column_message));
          }
        } else if ($form_state->getValue('category') == 'all') {
          $multi_select_hidden = !empty($form_state->getValue('column_select_all')) ? '|' . implode('||', $form_state->getValue('column_select_all')) . '|' : '';
          if (!$all) {
            $form_state->setErrorByName('column_select_all', t($this->select_at_least_one_column_message));
          }
        } else if ($form_state->getValue('category') == 'expense') {
          $multi_select_hidden = !empty($form_state->getValue('column_select_expense')) ? '|' . implode('||', $form_state->getValue('column_select_expense')) . '|' : '';
          if (!$activeexpense) {
            $form_state->setErrorByName('column_select_expense', t($this->select_at_least_one_column_message));
          }
        } else if (!$activerevenue && $form_state->getValue('category') == 'revenue') {
          $multi_select_hidden = !empty($form_state->getValue('column_select_revenue')) ? '|' . implode('||', $form_state->getValue('column_select_revenue')) . '|' : '';
          if (!$activerevenue) {
            $form_state->setErrorByName('column_select_revenue', t($this->select_at_least_one_column_message));
          }
        }
    }

    //Hidden Field for multi-select
    $form_state->set(['complete form', 'hidden_multiple_value', '#value'], $multi_select_hidden);
  }

  protected function checkbook_datafeeds_contracts_validate_nycha($form, &$form_state) {
    $nycha_multi_select = $form_state->getValue('column_select_nycha');

    // Current Amount:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'nycha_currentamtfrom', 'nycha_currentamtto', 'Current Amount');

    // Start Date:
    checkbook_datafeeds_check_ranged_date($form_state, 'nycha_startdatefrom', 'nycha_startdateto', 'Start Date');

    // End Date:
    checkbook_datafeeds_check_ranged_date($form_state, 'nycha_enddatefrom', 'nycha_enddateto', 'End Date');

    // Registered Date:
    checkbook_datafeeds_check_ranged_date($form_state, 'nycha_appr_datefrom', 'nycha_appr_dateto', 'Approved Date');

    //multi-select
    if (!$nycha_multi_select) {
      $form_state->setErrorByName('column_select_nycha', t($this->select_at_least_one_column_message));
    }
  }

  protected function checkbook_datafeeds_contracts_validate_vendor($form, &$form_state) {
    $data_source_temp = $form_state->getValue('datafeeds-contracts-domain-filter');
    $vendor = ($data_source_temp == Datasource::NYCHA) ? $form_state->getValue('nycha_vendor') : $form_state->getValue('vendor');
    if ($vendor) {
      preg_match($this->bracket_value_pattern, $vendor, $vmatches);
      if (!$vmatches) {
        try {
          $dataController = data_controller_get_instance();
          switch ($data_source_temp) {
            case Datasource::NYCHA:
              $query = "SELECT vendor_name FROM all_agreement_transactions
                                        WHERE latest_flag = 'Y'
                                        AND vendor_name ILIKE '" . $vendor . "'";
              $results = _checkbook_project_execute_sql($query, "main", $data_source_temp);
              $this->checkbook_datafeeds_contracts_validate_vendor_check_results($form_state, 'nycha_vendor', $results, 'Please enter a valid vendor name.');
              break;
            case Datasource::OGE:
              $query = "SELECT DISTINCT display_vendor_name
                                    FROM contracts_detailed_transactions
                                    WHERE status_flag = 'A'
                                    AND display_vendor_name ILIKE '" . $vendor . "'";
              $results = _checkbook_project_execute_sql($query, "main", $data_source_temp);
              $this->checkbook_datafeeds_contracts_validate_vendor_check_results($form_state, 'payee_name', $results, 'Please enter a valid vendor name.');
              break;
            default:
              $results = $dataController->queryDataset('checkbook:vendor', array('vendor_customer_code'), array('vendor_customer_code' => $vendor));
              $this->checkbook_datafeeds_contracts_validate_vendor_check_results($form_state, 'payee_name', $results, 'Please enter a valid vendor code.');
              break;
          }
        } catch (Exception $e) {
          LogHelper::log_error($e->getMessage());
        }
      }
    }
  }

  protected function checkbook_datafeeds_contracts_validate_vendor_check_results(&$form_state, $field, $results, $error_message) {
    if (!$results[0]) {
      $form_state->setErrorByName($field, t($error_message));
    }
  }

  protected function _process_user_criteria_by_datasource_single_field($field_name, $form_filter_key, $visual_field_name, $user_criteria_name = null) {
    $this->form['filter'][$form_filter_key] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> ' . $this->form_state->getValue($field_name) . '</div>');
    if (is_null($user_criteria_name)) {
      $this->user_criteria[$visual_field_name] = $this->form_state->getValue($field_name);
    } else {
      $this->user_criteria[$user_criteria_name] = $this->form_state->getValue($field_name);
    }
    $this->formatted_search_criteria[$visual_field_name] = $this->form_state->getValue($field_name);
  }

  protected function _process_user_criteria_by_datasource_single_field_and_check($field_name, $form_filter_key, $visual_field_name, $user_criteria_name = null) {
    if ($this->form_state->getValue($field_name)) {
      $this->_process_user_criteria_by_datasource_single_field($field_name, $form_filter_key, $visual_field_name, $user_criteria_name);
    }
  }

  protected function _process_user_criteria_by_datasource_ranged_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name, $field_type = 'amount') {
    $user_criteria_greater_than_id = $visual_field_name . ' Greater Than';
    $user_criteria_less_than_id = $visual_field_name . ' Less Than';
    $formatted_search_criteria_id = $visual_field_name;

    $greater_than_equal_text = 'Greater Than Equal to: ';
    $less_than_equal_text = 'Less Than Equal to: ';
    if ($field_type == 'amount') {
      $greater_than_equal_text = $greater_than_equal_text . '$';
      $less_than_equal_text = $less_than_equal_text . '$';
    }

    if (($this->form_state->getValue($start_field_name) || ($field_type == 'amount' && $this->form_state->getValue($start_field_name) === "0")) && ($this->form_state->getValue($end_field_name) || ($field_type == 'amount' && $this->form_state->getValue($end_field_name) === "0"))) {
      $this->form['filter'][$form_filter_id] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> '.$greater_than_equal_text . $this->form_state->getValue($start_field_name) . ' and ' . $less_than_equal_text . $this->form_state->getValue($end_field_name) . '</div>');
      $this->user_criteria[$user_criteria_greater_than_id] = $this->form_state->getValue($start_field_name);
      $this->user_criteria[$user_criteria_less_than_id] = $this->form_state->getValue($end_field_name);
      $this->formatted_search_criteria[$formatted_search_criteria_id] = 'Greater Than Equal to: $' . $this->form_state->getValue($start_field_name) . ' and Less Than Equal to: $' . $this->form_state->getValue($end_field_name);
    } elseif (!$this->form_state->getValue($start_field_name) && ($this->form_state->getValue($end_field_name) || ($field_type == 'amount' && $this->form_state->getValue($end_field_name) === "0"))) {
      $this->form['filter'][$form_filter_id] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> ' . $less_than_equal_text . $this->form_state->getValue($end_field_name) . '</div>');
      $this->user_criteria[$user_criteria_less_than_id] = $this->form_state->getValue($end_field_name);
      $this->formatted_search_criteria[$formatted_search_criteria_id] = 'Less Than Equal to: $' . $this->form_state->getValue($end_field_name);
    } elseif (($this->form_state->getValue($start_field_name) || ($field_type == 'amount' && $this->form_state->getValue($start_field_name) === "0")) && !$this->form_state->getValue($end_field_name)) {
      $this->form['filter'][$form_filter_id] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> ' . $greater_than_equal_text . $this->form_state->getValue($start_field_name) . '</div>');
      $this->user_criteria[$user_criteria_greater_than_id] = $this->form_state->getValue($start_field_name);
      $this->formatted_search_criteria[$formatted_search_criteria_id] = 'Greater Than Equal to: $' . $this->form_state->getValue($start_field_name);
    }
  }

  protected function _process_user_criteria_by_datasource_ranged_date_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name) {
    $this->_process_user_criteria_by_datasource_ranged_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name, 'date');
  }

  protected function _process_user_criteria_by_datasource_ranged_amount_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name) {
    $this->_process_user_criteria_by_datasource_ranged_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name);
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
  }

  // -------------------- Migrated from Data Controller to here ------------------------------------------------------------

  /**
   * @return DataQueryController
   */
  function data_controller_get_instance()
  {
    return DataQueryControllerProxy::getInstance();
  }
}
