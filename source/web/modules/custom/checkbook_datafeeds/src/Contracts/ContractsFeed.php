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

  /**
   * ContractsFeed constructor.
   */
  public function __construct()
  {
    $this->user_criteria = ['Type of Data' => $this->type_of_data];
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

    $current_step = 'contracts';
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
    $pvalues = $this->form_state->get('page_values');
    $this->response_type = $this->form_state->get(['step_information', 'type', 'stored_values', 'format']) ?? 'CSV';
    $this->user_criteria = [];

    if ($this->data_source == Datasource::NYCHA) {
      $this->user_criteria['Type of Data'] = 'Contracts_NYCHA';
    } elseif ($this->data_source == Datasource::OGE) {
      $this->user_criteria['Type of Data'] = 'Contracts_OGE';
    } else {
      $this->user_criteria['Type of Data'] = 'Contracts';
    }

    $this->user_criteria['Type of File'] = $this->response_type;

    $this->values = $this->form_state->get(['step_information', 'contracts', 'stored_values']);
    $pvalues = $this->form_state->get('page_values');

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
      '#markup' => '<div><strong>Type of Data:</strong> Contracts</div>',
    );
    $this->formatted_search_criteria['Type of Data'] = 'Contracts';

    $this->form['filter']['file_type'] = array(
      '#markup' => '<div><strong>Type of File:</strong> ' . $this->form_state->get(['step_information', 'type', 'stored_values', 'format']). '</div>',
    );
    $this->formatted_search_criteria['Type of File'] = $this->form_state->get(['step_information', 'type', 'stored_values', 'format']);

    $this->_process_user_criteria_by_datasource();

    //Issued Date
    if ($this->form_state->getValue('date_filter') == 1) {
      if ($this->form_state->getValue('issuedfrom') && $this->form_state->getValue('issuedto')) {
        $this->form['filter']['issued_date'] = array(
          '#markup' => '<div><strong>Issue Date:</strong> From: ' . $this->form_state->getValue('issuedfrom') . ' To: ' . $this->form_state->getValue('issuedto') . '</div>'
        );
        $this->user_criteria['Issued Date After'] = $this->form_state->getValue('issuedfrom');
        $this->user_criteria['Issued Date Before'] = $this->form_state->getValue('issuedto');
        $this->formatted_search_criteria['Issue Date'] = 'From: ' . $this->form_state->getValue('issuedfrom') . ' To: ' . $this->form_state->getValue('issuedto');
      } elseif (!$this->form_state->getValue('issuedfrom') && $this->form_state->getValue('issuedto')) {
        $this->form['filter']['issued_date'] = array(
          '#markup' => '<div><strong>Issue Date:</strong> From: ' . $this->form_state->getValue('issuedto') . '</div>',
        );
        $this->user_criteria['Issued Date Before'] = $this->form_state->getValue('issuedto');
        $this->formatted_search_criteria['Issue Date'] = 'From: ' . $this->form_state->getValue('issuedto');
      } elseif ($this->form_state->getValue('issuedfrom') && !$this->form_state->getValue('issuedto')) {
        $this->form['filter']['issued_date'] = array(
          '#markup' => '<div><strong>Issue Date:</strong> To: ' . $this->form_state->getValue('issuedfrom') . '</div>',
        );
        $this->user_criteria['Issued Date After'] = $this->form_state->getValue('issuedfrom');
        $this->formatted_search_criteria['Issue Date'] = 'To: ' . $this->form_state->getValue('issuedfrom');
      }
    }

    return;
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
      $response_type = $this->form_state->get(['step_information', 'type', 'stored_values', 'format']);
      //$data_source = Datasource::CITYWIDE;
      $data_source = $this->data_source;
      $category = $this->form_state->getValue('category');
      $status = $this->form_state->getValue('status');
      $year = $this->form_state->getValue('fiscal_year');
      $intended_order = _checkbook_datafeeds_contracts_override_column_options($response_type, $data_source, $status, $category, $year);
      $this->criteria['responseColumns'] = checkbook_datafeeds_override_column_order($this->criteria['responseColumns'], $intended_order);
    }

    if ($this->form_state->getValue('dept') && $this->form_state->getValue('dept') != 'Select Department' && $this->form_state->getValue('dept') != '0' && $this->values['agency'] != 'Citywide (All Agencies)') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('dept'), $department_matches);
      $this->criteria['value']['department_code'] = trim($department_matches[1], '[ ]');
    }

    if ($this->form_state->getValue('expense_category') && $this->form_state->getValue('expense_category') != 'Select Expense Category' && $this->form_state->getValue('expense_category') != '0' && $this->values['agency'] != 'Citywide (All Agencies)') {
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
      if ($this->form_state->getValue('issuedfrom') !== '' || $this->form_state->getValue('issuedto') !== '') {
        $this->criteria['range']['issue_date'] = array(
          checknull($this->form_state->getValue('issuedfrom')),
          checknull($this->form_state->getValue('issuedto'))
        );
      }
    }

    $this->_process_datasource_values();

    return;
  }

  // @ToDo: maybe seoparate the logic per datasource
  protected function _process_datasource_values()
  {
    $values = $this->form_state->get(['step_information','contracts', 'stored_values']);
    $pattern = "/.*?(\\[.*?\\])/is";

    if ($this->data_source != Datasource::NYCHA) {

      if ($values['year'] && $values['year'] != '0' && $values['df_contract_status'] != 'pending') {
        if (startsWith($values['year'], 'F')) {
          $this->criteria['value']['fiscal_year'] = ltrim($values['year'], 'FY');
        } elseif (startsWith($values['year'], 'C')) {
          $this->criteria['value']['calendar_year'] = ltrim($values['year'], 'CY');
        }
      }

      if ($values['currentamtfrom'] !== '' || $values['currentamtto'] !== '') {
        $this->criteria['range']['current_amount'] = array(
          checknull($values['currentamtfrom']),
          checknull($values['currentamtto']),
        );
      }
      if ($values['award_method'] && $values['award_method'] != 'No Award Method Selected') {
        preg_match($pattern, $values['award_method'], $awmatches);
        $this->criteria['value']['award_method'] = trim($awmatches[1], '[ ]');
      }
      if ($values['contractno']) {
        $this->criteria['value']['contract_id'] = $values['contractno'];
      }
      if ($values['contract_type'] != 'No Contract Type Selected') {
        preg_match($pattern, $values['contract_type'], $ctypematches);
        $this->criteria['value']['contract_type'] = trim($ctypematches[1], '[ ]');
      }
      if ($values['startdatefrom'] !== '' || $values['startdateto'] !== '') {
        $this->criteria['range']['start_date'] = array(
          checknull($values['startdatefrom']),
          checknull($values['startdateto']),
        );
      }
      if ($values['enddatefrom'] !== '' || $values['enddateto'] !== '') {
        $this->criteria['range']['end_date'] = array(
          checknull($values['enddatefrom']),
          checknull($values['enddateto']),
        );
      }
      if ($values['df_contract_status']) {
        $this->criteria['value']['status'] = $values['df_contract_status'];
      }
      if ($values['category']) {
        $this->criteria['value']['category'] = $values['category'];
      }
      if ($this->data_source != Datasource::OGE) {
        if ($values['regdatefrom'] !== '' || $values['regdateto'] !== '') {
          $this->criteria['range']['registration_date'] = array(
            checknull($values['regdatefrom']),
            checknull($values['regdateto']),
          );
        }
        if ($values['recdatefrom'] !== '' || $values['recdateto'] !== '') {
          $this->criteria['range']['received_date'] = array(
            checknull($values['recdatefrom']),
            checknull($values['recdateto']),
          );
        }
      }
      if ($values['pin']) {
        $this->criteria['value']['pin'] = strtoupper($values['pin']);
      }
      if ($values['apt_pin']) {
        $this->criteria['value']['apt_pin'] = strtoupper($values['apt_pin']);
      }
      if ($values['purpose']) {
        $this->criteria['value']['purpose'] = $values['purpose'];
      }
    }
    //By data source
    switch ($this->data_source) {
      case Datasource::OGE:
        if ($values['entity_contract_number']) {
          $this->criteria['value']['entity_contract_number'] = $values['entity_contract_number'];
        }
        if ($values['commodity_line']) {
          $this->criteria['value']['commodity_line'] = $values['commodity_line'];
        }
        if ($values['budget_name']) {
          $this->criteria['value']['budget_name'] = $values['budget_name'];
        }
        if ($values['vendor']) {
          $this->criteria['value']['prime_vendor'] = $values['vendor'];
        }
        break;
      case Datasource::NYCHA:
        if ($values['purchase_order_type'] && $values['purchase_order_type'] != 'All') {
          preg_match($pattern, $values['purchase_order_type'], $pmatches);
          if ($pmatches) {
            $this->criteria['value']['purchase_order_type'] = trim($pmatches[1], '[ ]');
          } else {
            $this->criteria['value']['purchase_order_type'] = $values['purchase_order_type'];
          }
        }
        if ($values['nycha_contract_id']) {
          $this->criteria['value']['contract_id'] = $values['nycha_contract_id'];
        }
        if ($values['nycha_vendor']) {
          preg_match($pattern, $values['nycha_vendor'], $vmatches);
          if ($vmatches) {
            $this->criteria['value']['vendor_code'] = trim($vmatches[1], '[ ]');
          } else {
            $this->criteria['value']['vendor_name'] = $values['nycha_vendor'];
          }
        }
        if ($values['resp_center']) {
          preg_match($pattern, $values['resp_center'], $amatches);
          $this->criteria['value']['responsibility_center'] = trim($amatches[1], '[ ]');
        }
        if ($values['nycha_contract_type']) {
          preg_match($pattern, $values['nycha_contract_type'], $amatches);
          $this->criteria['value']['contract_type'] = trim($amatches[1], '[ ]');
        }
        if ($values['nycha_awd_method']) {
          preg_match($pattern, $values['nycha_awd_method'], $amatches);
          $this->criteria['value']['award_method'] = trim($amatches[1], '[ ]');
        }
        if ($values['nycha_industry']) {
          preg_match($pattern, $values['nycha_industry'], $amatches);
          $this->criteria['value']['industry'] = trim($amatches[1], '[ ]');
        }
        if ($values['nycha_currentamtfrom'] !== '' || $values['nycha_currentamtto'] !== '') {
          $this->criteria['range']['current_amount'] = array(checknull($values['nycha_currentamtfrom']), checknull($values['nycha_currentamtto']),);
        }
        if ($values['nycha_purpose']) {
          $this->criteria['value']['purpose'] = $values['nycha_purpose'];
        }
        if ($values['nycha_apt_pin']) {
          $this->criteria['value']['pin'] = strtoupper($values['nycha_apt_pin']);
        }
        if ($values['nycha_startdatefrom'] !== '' || $values['nycha_startdateto'] !== '') {
          $this->criteria['range']['start_date'] = array(checknull($values['nycha_startdatefrom']), checknull($values['nycha_startdateto']),);
        }
        if ($values['nycha_enddatefrom'] !== '' || $values['nycha_enddateto'] !== '') {
          $this->criteria['range']['end_date'] = array(checknull($values['nycha_enddatefrom']), checknull($values['nycha_enddateto']),);
        }
        if ($values['nycha_appr_datefrom'] !== '' || $values['nycha_appr_dateto'] !== '') {
          $this->criteria['range']['approved_date'] = array(checknull($values['nycha_appr_datefrom']), checknull($values['nycha_appr_dateto']),);
        }
        if ($values['nycha_year'] && $values['nycha_year'] != '0') {
          if (startsWith($values['nycha_year'], 'F')) {
            $this->criteria['value']['fiscal_year'] = ltrim($values['nycha_year'], 'FY');
          }
        }
        break;
      default:
        if ($values['agency'] != 'Citywide (All Agencies)') {
          preg_match($pattern, $values['agency'], $amatches);
          $this->criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
        }
        if ($values['vendor']) {
          preg_match($pattern, $values['vendor'], $vmatches);
          if ($vmatches) {
            $this->criteria['value']['vendor_code'] = trim($vmatches[1], '[ ]');
          } else {
            $this->criteria['value']['vendor_code'] = $values['vendor'];
          }
        }
        if ($values['mwbe_category'] && $values['mwbe_category'] != 'Select Category') {
          $this->criteria['value']['mwbe_category'] = $values['mwbe_category'];
        }
        if ($values['industry'] && $values['industry'] != 'Select Industry') {
          preg_match($pattern, $values['industry'], $imatches);
          $this->criteria['value']['industry'] = trim($imatches[1], '[ ]');
        }

        if ($values['category'] != 'revenue') {
          if ($values['catastrophic_event'] && ((substr($values['year'], -4) >= 2020) || $values['year'] == '0')) {
            preg_match($pattern, $values['catastrophic_event'], $ematches);
            $this->criteria['value']['catastrophic_event'] = trim($ematches[1], '[ ]');
          }
        }
        if ($values['contract_includes_sub_vendors_id'] != '' && $values['contract_includes_sub_vendors_id'] != 0) {
          $this->criteria['value']['contract_includes_sub_vendors'] = $values['contract_includes_sub_vendors_id'];
        }
        if ($values['sub_vendor_status_in_pip_id'] != 'Select Status' && $values['sub_vendor_status_in_pip_id'] != 0 && !empty($values['sub_vendor_status_in_pip_id'])) {
          $this->criteria['value']['sub_vendor_status_in_pip'] = $values['sub_vendor_status_in_pip_id'];
        }

        break;
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
    //$data_source = $form_state['input']['datafeeds-contracts-domain-filter'];
    $data_source = $form_state->getValue('datafeeds-contracts-domain-filter');
    if ($data_source == Datasource::NYCHA) {
//  $startfrom = $form_state['values']['nycha_startdatefrom'];
      $startfrom = $form_state->getValue('nycha_startdatefrom');
//  $startto = $form_state['values']['nycha_startdateto'];
      $startto = $form_state->getValue('nycha_startdateto');
//  $enddatefrom = $form_state['values']['nycha_enddatefrom'];
      $enddatefrom = $form_state->getValue('nycha_enddatefrom');
//  $enddateto = $form_state['values']['nycha_enddateto'];
      $enddateto = $form_state->getValue('nycha_enddateto');
//  $appr_date_from = $form_state['values']['nycha_appr_datefrom'];
      $appr_date_from = $form_state->getValue('nycha_appr_datefrom');
//  $appr_date_to = $form_state['values']['nycha_appr_dateto'];
      $appr_date_to = $form_state->getValue('nycha_appr_dateto');
//  $currentfrom = $form_state['values']['nycha_currentamtfrom'];
      $currentfrom = $form_state->getValue('nycha_currentamtfrom');
//  $currentto = $form_state['values']['nycha_currentamtfrom'];
      $currentto = $form_state->getValue('nycha_currentamtto');
//  $nycha_multi_select = $form_state['values']['column_select_nycha'];
      $nycha_multi_select = $form_state->getValue('column_select_nycha');
//  $vendor = $form_state['values']['nycha_vendor'];
      $vendor = $form_state->getValue('nycha_vendor');

      // $multi_select_hidden = isset($form_state['input']['column_select_nycha']) ? '|' . implode('||', $form_state['input']['column_select_nycha']) . '|' : '';
      //$multi_select_hidden = $form_state->hasValue(['input', 'column_select_nycha']) ? '|' . implode('||', $form_state->getValue(['input', 'column_select_nycha'])) . '|' : '';
      $multi_select_hidden = !empty($form_state->getValue('column_select_nycha')) ? '|' . implode('||', $form_state->getValue('column_select_nycha')) . '|' : '';

      // Current Amount:
      if ($currentfrom && !is_numeric($currentfrom)) {
        //form_set_error('nycha_currentamtfrom', t('Current Amount must be a number.'));
        $form_state->setErrorByName('nycha_currentamtfrom', t('Current Amount must be a number.'));
      }
      if ($currentto && !is_numeric($currentto)) {
        //form_set_error('nycha_currentamtto', t('Current Amount must be a number.'));
        $form_state->setErrorByName('nycha_currentamtto', t('Current Amount must be a number.'));
      }
      if (is_numeric($currentfrom) && is_numeric($currentto) && $currentto < $currentfrom) {
//      form_set_error('nycha_currentamtto', t('Invalid range for Current Amount.'));
        $form_state->setErrorByName('nycha_currentamtto', t('Invalid range for Current Amount'));
      }
      // Start Date:
      if ($startfrom && !checkDateFormat($startfrom)) {
//      form_set_error('nycha_startdatefrom', t('Start Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('nycha_startdatefrom', t('Start Date must be a valid date (YYYY-MM-DD).'));
      }
      if ($startto && !checkDateFormat($startto)) {
//      form_set_error('nycha_startdateto', t('Start Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('nycha_startdateto', t('Start Date must be a valid date (YYYY-MM-DD).'));
      }
      if ($startfrom && $startto && strtotime($startto) < strtotime($startfrom)) {
//      form_set_error('nycha_startdateto', t('Invalid date range for Start Date.'));
        $form_state->setErrorByName('nycha_startdateto', t('Invalid date range for Start Date.'));
      }
      // End Date:
      if (strlen($enddatefrom) > 0 && !checkDateFormat($enddatefrom)) {
//      form_set_error('nycha_enddatefrom', t('End Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('nycha_enddatefrom', t('End Date must be a valid date (YYYY-MM-DD).'));
      }
      if (strlen($enddateto) > 0 && !checkDateFormat($enddateto)) {
//        form_set_error('nycha_enddateto', t('End Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('nycha_enddateto', t('End Date must be a valid date (YYYY-MM-DD).'));
      }
      if (strlen($enddatefrom) > 0 && strlen($enddateto) > 0 && strtotime($enddateto) < strtotime($enddatefrom)) {
//      form_set_error('nycha_enddateto', t('Invalid date range for End Date.'));
        $form_state->setErrorByName('nycha_enddateto', t('Invalid date range for End Date.'));
      }
      // Registered Date:
      if (strlen($appr_date_from) > 0 && !checkDateFormat($appr_date_from)) {
//      form_set_error('nycha_appr_datefrom', t('Approved Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('nycha_appr_datefrom', t('Approved Date must be a valid date (YYYY-MM-DD).'));
      }
      if (strlen($appr_date_to) > 0 && !checkDateFormat($appr_date_to)) {
//      form_set_error('nycha_appr_dateto', t('Approved Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('nycha_appr_dateto', t('Approved Date must be a valid date (YYYY-MM-DD).'));
      }
      if (strlen($appr_date_from) > 0 && strlen($appr_date_to) > 0 && strtotime($appr_date_to) < strtotime($appr_date_from)) {
//      form_set_error('nycha_appr_dateto', t('Invalid Date range for Approved Date.'));
        $form_state->setErrorByName('nycha_appr_dateto', t('Invalid Date range for Approved Date.'));
      }
      //multi-select
      if (!$nycha_multi_select) {
//      form_set_error('column_select_nycha', t('You must select at least one column.'));
        $form_state->setErrorByName('column_select_nycha', t('You must select at least one column.'));
      }
    } else {
      $startfrom = $form_state->getValue('startdatefrom');
      $startto = $form_state->getValue('startdateto');
      $enddatefrom = $form_state->getValue('enddatefrom');
      $enddateto = $form_state->getValue('enddateto');
      $regdatefrom = $form_state->getValue('regdatefrom');
      $regdateto = $form_state->getValue('regdateto');
      $recdatefrom = $form_state->getValue('recdatefrom');
      $recdateto = $form_state->getValue('recdateto');
      $currentfrom = $form_state->getValue('currentamtfrom');
      $currentto = $form_state->getValue('currentamtto');
      $activeexpense = $form_state->getValue('column_select_expense');
      $activerevenue = $form_state->getValue('column_select_revenue');
      $all = $form_state->getValue('column_select_all');
      $pending = $form_state->getValue('column_select_pending');
      $pending_all = $form_state->getValue('column_select_pending_all');
      $vendor = $form_state->getValue('vendor');
      $contract_id = $form_state->getValue('contractno');
      $ogeexpense = $form_state->getValue('column_select_oge_expense');
      $entity_contractno = $form_state->getValue('entity_contract_number');
      //Contract Id
      if ($contract_id && strlen($contract_id) > 32) {
//      form_set_error('contractno', t('Contract ID must be less than or equal to 32 characters'));
        $form_state->setErrorByName('contractno', t('Contract ID must be less than or equal to 32 characters.'));
      }

      //Commodity Line
      if (Datasource::OGE == $data_source) {
        $commodity_line = $form_state->getValue('commodity_line');
        if ($commodity_line && !is_numeric($commodity_line)) {
//        form_set_error('commodity_line', t('Commodity Line must be a number'));
          //      form_set_error('contractno', t('Contract ID must be less than or equal to 32 characters'));
          $form_state->setErrorByName('contractno', t('Contract ID must be less than or equal to 32 characters.'));
        }
        if ($entity_contractno && !is_numeric($entity_contractno)) {
//        form_set_error('entity_contract_number', t('Entity Contract # must be a number'));
          //      form_set_error('contractno', t('Contract ID must be less than or equal to 32 characters'));
          $form_state->setErrorByName('contractno', t('Contract ID must be less than or equal to 32 characters.'));
        }
      }

      // Start Date:
      if (strlen($startfrom) > 0 && !checkDateFormat($startfrom)) {
//      form_set_error('startdatefrom', t('Start Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('startdatefrom', t('Start Date must be a valid date (YYYY-MM-DD).'));
      }
      if (strlen($startto) > 0 && !checkDateFormat($startto)) {
//      form_set_error('startdateto', t('Start Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('startdateto', t('Start Date must be a valid date (YYYY-MM-DD).'));

      }
      if (strlen($startfrom) > 0 && $startto && strtotime($startto) < strtotime($startfrom)) {
//      form_set_error('startdateto', t('Invalid date range for Start Date.'));
        $form_state->setErrorByName('startdateto', t('Invalid date range for Start Date.'));
      }
      // End Date:
      if (strlen($enddatefrom) > 0 && !checkDateFormat($enddatefrom)) {
//      form_set_error('enddatefrom', t('End Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('enddatefrom', t('End Date must be a valid date (YYYY-MM-DD).'));
      }
      if (strlen($enddateto) > 0 && !checkDateFormat($enddateto)) {
//      form_set_error('enddateto', t('End Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('enddateto', t('End Date must be a valid date (YYYY-MM-DD).'));
      }
      if (strlen($enddatefrom) > 0 && strlen($enddateto) > 0 && strtotime($enddateto) < strtotime($enddatefrom)) {
//      form_set_error('enddateto', t('Invalid date range for End Date.'));
        $form_state->setErrorByName('enddateto', t('Invalid date range for End Date.'));
      }
      // Registered Date:
      if (strlen($regdatefrom) > 0 && !checkDateFormat($regdatefrom)) {
//      form_set_error('regdatefrom', t('Registered Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('regdatefrom', t('Registered Date must be a valid date (YYYY-MM-DD).'));
      }
      if (strlen($regdateto) > 0 && !checkDateFormat($regdateto)) {
//      form_set_error('regdateto', t('Registered Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('regdateto', t('Registered Date must be a valid date (YYYY-MM-DD).'));
      }
      if (strlen($regdatefrom) > 0 && strlen($regdateto) > 0 && strtotime($regdateto) < strtotime($regdatefrom)) {
//      form_set_error('regdateto', t('Invalid Date range for Registered Date.'));
        $form_state->setErrorByName('regdateto', t('Invalid Date range for Registered Date.'));
      }
      // Received Date:
      if (strlen($recdatefrom) > 0 && !checkDateFormat($recdatefrom)) {
//      form_set_error('recdatefrom', t('Received Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('recdatefrom', t('Received Date must be a valid date (YYYY-MM-DD).'));
      }
      if (strlen($recdateto) > 0 && !checkDateFormat($recdateto)) {
//      form_set_error('recdateto', t('Received Date must be a valid date (YYYY-MM-DD).'));
        $form_state->setErrorByName('recdateto', t('Received Date must be a valid date (YYYY-MM-DD).'));
      }
      if (strlen($recdatefrom) > 0 && strlen($recdateto) > 0 && strtotime($recdateto) < strtotime($recdatefrom)) {
//      form_set_error('recdateto', t('Invalid Date range for Received Date.'));
        $form_state->setErrorByName('recdateto', t('Invalid Date range for Received Date.'));
      }
      // Current Amount:
      if ($currentfrom && !is_numeric($currentfrom)) {
//      form_set_error('currentamtfrom', t('Current Amount must be a number.'));
        $form_state->setErrorByName('currentamtfrom', t('Current Amount must be a number.'));
      }
      if ($currentto && !is_numeric($currentto)) {
//      form_set_error('currentamtto', t('Current Amount must be a number.'));
        $form_state->setErrorByName('currentamtto', t('Current Amount must be a number.'));
      }
      if (is_numeric($currentfrom) && is_numeric($currentto) && $currentto < $currentfrom) {
//      form_set_error('currentamtto', t('Invalid range for Current Amount.'));
        $form_state->setErrorByName('currentamtto', t('Invalid range for Current Amount.'));
      }

      if ($form_state->getValue('df_contract_status') != 'pending') {
        //dump($form_state->getValue('recdatefrom'));
        if ($form_state->getValue('recdatefrom')) {
//        form_set_error('recdatefrom', t('Received Date filter is not available on active or registered contracts.'));
          $form_state->setErrorByName('recdatefrom', t('Received Date filter is not available on active or registered contracts.'));
        }
        if ($form_state->getValue('recdateto')) {
//        form_set_error('recdateto', t('Received Date filter is not available on active or registered contracts.'));
          $form_state->setErrorByName('recdateto', t('Received Date filter is not available on active or registered contracts.'));
        }
      } elseif ($form_state->getValue('df_contract_status') == 'pending') {
        if (!empty($form_state->getValue('regdatefrom'))) {
//        form_set_error('spentto', t('Registered Date1 filter is not available on pending contracts'));
          $form_state->setErrorByName('spentto', t('Registered1 Date filter is not available on pending contracts.'));
        }
//      if ($form_state->hasValue('regdateto')) {
        if (!empty($form_state->getValue('regdateto'))) {
//        form_set_error('spentto', t('Registered Date filter is not available on pending contracts'));
          $form_state->setErrorByName('spentto', t('Registered2 Date filter is not available on pending contracts.'));
        }
      }

      //Multi-select Columns
      if ($data_source == Datasource::OGE) {
        $multi_select_hidden = !empty($form_state->getValue('column_select_oge_expense')) ? '|' . implode('||', $form_state->getValue('column_select_oge_expense')) . '|' : '';
        if (!$ogeexpense) {
//        form_set_error('column_select_oge_expense', t('You must select at least one column.'));
          $form_state->setErrorByName('column_select_oge_expense', t('You must select at least one column.'));
        }
      } else {
        if ($form_state->getValue('df_contract_status') == 'pending') {
          if ($form_state->getValue('category') != 'all') {
            $multi_select_hidden = !empty($form_state->getValue('column_select_pending')) ? '|' . implode('||', $form_state->getValue('column_select_pending')) . '|' : '';
            if (!$pending) {
//            form_set_error('column_select_pending', t('You must select at least one column.'));
              $form_state->setErrorByName('column_select_pending', t('You must select at least one column.'));
            }
          }
          if ($form_state->getValue('category') == 'all') {
            $multi_select_hidden = !empty($form_state->getValue('column_select_pending_all')) ? '|' . implode('||', $form_state->getValue('column_select_pending_all')) . '|' : '';
            if (!$pending_all) {
//            form_set_error('column_select_pending_all', t('You must select at least one column.'));
              $form_state->setErrorByName('column_select_pending_all', t('You must select at least one column.'));
            }
          }
        } else {
          if ($form_state->getValue('category') == 'all') {
            $multi_select_hidden = !empty($form_state->getValue('column_select_all')) ? '|' . implode('||', $form_state->getValue('column_select_all')) . '|' : '';
            if (!$all) {
//            form_set_error('column_select_all', t('You must select at least one column.'));
              $form_state->setErrorByName('column_select_all', t('You must select at least one column.'));
            }
          }
          if ($form_state->getValue('category') == 'expense') {
            $multi_select_hidden = !empty($form_state->getValue('column_select_expense')) ? '|' . implode('||', $form_state->getValue('column_select_expense')) . '|' : '';
            if (!$activeexpense) {
//            form_set_error('column_select_expense', t('You must select at least one column.'));
              $form_state->setErrorByName('column_select_expense', t('You must select at least one column.'));
            }
          }
          if (!$activerevenue && $form_state->getValue('category') == 'revenue') {
            $multi_select_hidden = !empty($form_state->getValue('column_select_revenue')) ? '|' . implode('||', $form_state->getValue('column_select_revenue')) . '|' : '';
            if (!$activerevenue) {
//            form_set_error('column_select_revenue', t('You must select at least one column.'));
              $form_state->setErrorByName('column_select_revenue', t('You must select at least one column.'));
            }
          }
        }
      }

      if (in_array(intval($form_state->getValue('contract_includes_sub_vendors_id')), array(1, 3, 4))) {
        $form_state->setValue('sub_vendor_status_in_pip_id', 0);
        $form_state->set(['complete form', 'sub_vendor_status_in_pip_id', '#value'], 0);
      }
    }
    // Vendor:
    if ($vendor) {
      $pattern = "/.*?(\\[.*?\\])/is";
      preg_match($pattern, $vendor, $vmatches);
      if (!$vmatches) {
        try {
          $dataController = data_controller_get_instance();
          switch ($data_source) {
            case Datasource::NYCHA:
              $query = "SELECT vendor_name FROM all_agreement_transactions
                                        WHERE latest_flag = 'Y'
                                        AND vendor_name ILIKE '" . $vendor . "'";
              $results = _checkbook_project_execute_sql($query, "main", $data_source);
              if (!$results[0]) {
//              form_set_error('nycha_vendorf', t('Please enter a valid vendor name.'));
                $form_state->setErrorByName('nycha_vendorf', t('Please enter a valid vendor name.'));
              }
              break;
            case Datasource::OGE:
              $query = "SELECT DISTINCT display_vendor_name
                                    FROM contracts_detailed_transactions
                                    WHERE status_flag = 'A'
                                    AND display_vendor_name ILIKE '" . $vendor . "'";
              $results = _checkbook_project_execute_sql($query, "main", $data_source);
              if (!$results[0]) {
//              form_set_error('payee_name', t('Please enter a valid vendor name.'));
                $form_state->setErrorByName('payee_name', t('Please enter a valid vendor name.'));
              }
              break;
            default:
              $results = $dataController->queryDataset('checkbook:vendor', array('vendor_customer_code'), array('vendor_customer_code' => $vendor));
              if (!$results[0]) {
//              form_set_error('payee_name', t('Please enter a valid vendor code.'));
                $form_state->setErrorByName('payee_name', t('Please enter a valid vendor code.'));
              }
              break;
          }
        } catch (Exception $e) {
          LogHelper::log_error($e->getMessage());
        }
      }
    }

    //Hidden Field for multi-select
    $form_state->set(['complete form', 'hidden_multiple_value', '#value'], $multi_select_hidden);
    //$this->_validate_by_datasource($form, $form_state);
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
