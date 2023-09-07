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

class ContractsFeedNycha extends ContractsFeed
{
  protected $data_source = 'checkbook_nycha';
  protected $type_of_data = 'Contracts_NYCHA';
  protected $filtered_columns_container = 'nycha_column_select';
  protected $oge_label = 'Other Government Entity';
  protected $oge_name_code = "NEW YORK CITY HOUSING AUTHORITY[996]";
  protected $oge_name = "NEW YORK CITY HOUSING AUTHORITY";

  protected function _process_user_criteria_by_datasource(){

    if ($this->form_state->getValue('purchase_order_type')) {
      $this->form['filter']['purchase_order_type'] = array('#markup' => '<div><strong>Purchase Order Type:</strong> ' . $this->form_state->getValue('purchase_order_type') . '</div>');
      $this->user_criteria['Purchase Order Type'] = $this->form_state->getValue('purchase_order_type');
      $this->formatted_search_criteria['Purchase Order Type'] = $this->form_state->getValue('purchase_order_type');
    }
    if ($this->form_state->getValue('nycha_contract_id')) {
      $this->form['filter']['nycha_contract_id'] = array('#markup' => '<div><strong>Contract ID:</strong> ' . $this->form_state->getValue('nycha_contract_id') . '</div>');
      $this->user_criteria['Contract ID'] = $this->form_state->getValue('nycha_contract_id');
      $this->formatted_search_criteria['Contract ID'] = $this->form_state->getValue('nycha_contract_id');
    }
    if ($this->form_state->getValue('nycha_vendor')) {
      $this->form['filter']['nycha_vendor'] = array('#markup' => '<div><strong>Vendor:</strong> ' . $this->form_state->getValue('nycha_vendor') . '</div>');
      $this->user_criteria['Vendor'] = $this->form_state->getValue('nycha_vendor');
      $this->formatted_search_criteria['Vendor'] = $this->form_state->getValue('nycha_vendor');
    }
    if ($this->form_state->getValue('resp_center')) {
      $this->form['filter']['resp_center'] = array('#markup' => '<div><strong>Responsibility Center:</strong> ' . $this->form_state->getValue('resp_center') . '</div>');
      $this->user_criteria['Responsibility Center'] = $this->form_state->getValue('resp_center');
      $this->formatted_search_criteria['Responsibility Center'] = $this->form_state->getValue('resp_center');
    }
    if ($this->form_state->getValue('nycha_contract_type')) {
      if ($this->form_state->getValue('nycha_contract_type') != 'No Contract Type Selected') {
        $this->form['filter']['nycha_contract_type'] = array('#markup' => '<div><strong>Contract Type:</strong> ' . $this->form_state->getValue('nycha_contract_type') . '</div>');
        $this->user_criteria['Contract Type'] = $this->form_state->getValue('nycha_contract_type');
        $this->formatted_search_criteria['Contract Type'] = $this->form_state->getValue('nycha_contract_type');
      }
    }
    if ($this->form_state->getValue('nycha_awd_method')) {
      if ($this->form_state->getValue('nycha_awd_method') != 'No Award Method Selected') {
        $this->form['filter']['nycha_awd_method'] = array('#markup' => '<div><strong>Award Method:</strong> ' . $this->form_state->getValue('nycha_awd_method') . '</div>');
        $this->user_criteria['Award Method'] = $this->form_state->getValue('nycha_awd_method');
        $this->formatted_search_criteria['Award Method'] = $this->form_state->getValue('nycha_awd_method');
      }
    }
    if ($this->form_state->getValue('nycha_industry')) {
      if ($this->form_state->getValue('nycha_industry') != 'No Industry Selected') {
        $this->form['filter']['nycha_industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $this->form_state->getValue('nycha_industry') . '</div>');
        $this->user_criteria['Industry'] = $this->form_state->getValue('nycha_industry');
        $this->formatted_search_criteria['Industry'] = $this->form_state->getValue('nycha_industry');
      }
    }
    if (($this->form_state->getValue('nycha_currentamtfrom') || $this->form_state->getValue('nycha_currentamtfrom') === "0") && ($this->form_state->getValue('nycha_currentamtto') || $this->form_state->getValue('nycha_currentamtto') === "0")) {
      $this->form['filter']['nycha_current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Greater Than Equal to: $' . $this->form_state->getValue('nycha_currentamtfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_currentamtto') . '</div>');
      $this->user_criteria['Current Amount Greater Than'] = $this->form_state->getValue('nycha_currentamtfrom');
      $this->user_criteria['Current Amount Less Than'] = $this->form_state->getValue('nycha_currentamtto');
      $this->formatted_search_criteria['Current Amount'] = 'Greater Than Equal to: $' . $this->form_state->getValue('nycha_currentamtfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_currentamtto');
    } elseif (!$this->form_state->getValue('nycha_currentamtfrom') && ($this->form_state->getValue('nycha_currentamtto') || $this->form_state->getValue('nycha_currentamtto') === "0")) {
      $this->form['filter']['nycha_current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Less Than Equal to: $' . $this->form_state->getValue('nycha_currentamtto') . '</div>');
      $this->user_criteria['Current Amount Less Than'] = $this->form_state->getValue('currentamtto');
      $this->formatted_search_criteria['Current Amount'] = 'Less Than Equal to: $' . $this->form_state->getValue('nycha_currentamtto');
    } elseif (($this->form_state->getValue('nycha_currentamtfrom') || $this->form_state->getValue('nycha_currentamtfrom') === "0") && !$this->form_state->getValue('nycha_currentamtto')) {
      $this->form['filter']['nycha_current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Greater Than Equal to: $' . $this->form_state->getValue('nycha_currentamtfrom') . '</div>');
      $this->user_criteria['Current Amount Greater Than'] = $this->form_state->getValue('nycha_currentamtfrom');
      $this->formatted_search_criteria['Current Amount'] = 'Greater Than Equal to: $' . $this->form_state->getValue('nycha_currentamtfrom');
    }

    if ($this->form_state->getValue('agency')) {
      $this->form['filter']['agency'] = array(
        '#markup' => '<div><strong>Other Government Entities:</strong> ' . $this->form_state->getValue('agency') . '</div>',
      );
      $this->user_criteria['Other Government Entities'] = $this->form_state->getValue('agency');
      $this->formatted_search_criteria['Other Government Entities'] = $this->form_state->getValue('agency');
    }

    if ($this->form_state->getValue('nycha_purpose')) {
      $this->form['filter']['nycha_purpose'] = array(
        '#markup' => '<div><strong>Purpose:</strong> ' . $this->form_state->getValue('nycha_purpose') . '</div>',
      );
      $this->user_criteria['Purpose'] = $this->form_state->getValue('nycha_purpose');
      $this->formatted_search_criteria['Purpose'] = $this->form_state->getValue('nycha_purpose');
    }
    if ($this->form_state->getValue('nycha_apt_pin')) {
      $this->form['filter']['nycha_apt_pin'] = array(
        '#markup' => '<div><strong>PIN:</strong> ' . $this->form_state->getValue('nycha_apt_pin') . '</div>',
      );
      $this->user_criteria['PIN'] = $this->form_state->getValue('nycha_apt_pin');
      $this->formatted_search_criteria['PIN'] = $this->form_state->getValue('nycha_apt_pin');
    }

    if ($this->form_state->getValue('nycha_startdatefrom') && $this->form_state->getValue('nycha_startdateto')) {
      $this->form['filter']['nycha_start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('nycha_startdatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('nycha_startdateto') . '</div>');
      $this->user_criteria['Start Date Greater Than'] = $this->form_state->getValue('nycha_startdatefrom');
      $this->user_criteria['Start Date Less Than'] = $this->form_state->getValue('nycha_startdateto');
      $this->formatted_search_criteria['Start Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('startdatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('startdateto');
    } elseif (!$this->form_state->getValue('nycha_startdatefrom') && $this->form_state->getValue('nycha_startdateto')) {
      $this->form['filter']['nycha_start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Less Than Equal to: ' . $this->form_state->getValue('nycha_startdateto') . '</div>');
      $this->user_criteria['Start Date Less Than'] = $this->form_state->getValue('nycha_startdateto');
      $this->formatted_search_criteria['Start Date'] = 'Less Than Equal to: ' . $this->form_state->getValue('startdateto');
    } elseif ($this->form_state->getValue('nycha_startdatefrom') && !$this->form_state->getValue('nycha_startdateto')) {
      $this->form['filter']['nycha_start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('nycha_startdatefrom') . '</div>');
      $this->user_criteria['Start Date Greater Than'] = $this->form_state->getValue('nycha_startdatefrom');
      $this->formatted_search_criteria['Start Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('nycha_startdatefrom');
    }
    if ($this->form_state->getValue('nycha_enddatefrom') && $this->form_state->getValue('nycha_enddateto')) {
      $this->form['filter']['nycha_end_date'] = array('#markup' => '<div><strong>End Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('nycha_enddatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('nycha_enddateto') . '</div>');
      $this->user_criteria['End Date Greater Than'] = $this->form_state->getValue('nycha_enddatefrom');
      $this->user_criteria['End Date Less Than'] = $this->form_state->getValue('nycha_enddateto');
      $this->formatted_search_criteria['End Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('nycha_enddatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('nycha_enddateto');
    } elseif (!$this->form_state->getValue('nycha_enddatefrom') && $this->form_state->getValue('nycha_enddateto')) {
      $this->form['filter']['nycha_end_date'] = array('#markup' => '<div><strong>End Date:</strong> Less Than Equal to: ' . $this->form_state->getValue('nycha_enddateto') . '</div>');
      $this->user_criteria['End Date Less Than'] = $this->form_state->getValue('nycha_enddateto');
      $this->formatted_search_criteria['End Date'] = 'Less Than Equal to: ' . $this->form_state->getValue('nycha_enddateto');
    } elseif ($this->form_state->getValue('nycha_enddatefrom') && !$this->form_state->getValue('nycha_enddateto')) {
      $this->form['filter']['nycha_end_date'] = array('#markup' => '<div><strong>End Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('nycha_enddatefrom') . '</div>');
      $this->user_criteria['End Date Greater Than'] = $this->form_state->getValue('nycha_enddatefrom');
      $this->formatted_search_criteria['End Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('nycha_enddatefrom');
    }

    if ($this->form_state->getValue('nycha_appr_datefrom') && $this->form_state->getValue('nycha_appr_dateto')) {
      $this->form['filter']['nycha_approved_date'] = array('#markup' => '<div><strong>Approved Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('nycha_appr_datefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('nycha_appr_dateto') . '</div>');
      $this->user_criteria['Approved Date Greater Than'] = $this->form_state->getValue('nycha_appr_datefrom');
      $this->user_criteria['Approved Date Less Than'] = $this->form_state->getValue('nycha_appr_dateto');
      $this->formatted_search_criteria['Approved Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('nycha_appr_datefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('nycha_appr_dateto');
    } elseif (!$this->form_state->getValue('nycha_appr_datefrom') && $this->form_state->getValue('nycha_appr_dateto')) {
      $this->form['filter']['approved'] = array('#markup' => '<div><strong>Approved Date:</strong> Less Than Equal to: ' . $this->form_state->getValue('nycha_appr_dateto') . '</div>');
      $this->user_criteria['Approved Date Less Than'] = $this->form_state->getValue('nycha_appr_dateto');
      $this->formatted_search_criteria['Approved Date'] = 'Less Than Equal to: ' . $this->form_state->getValue('nycha_appr_dateto');
    } elseif ($this->form_state->getValue('nycha_appr_datefrom') && !$this->form_state->getValue('nycha_appr_dateto')) {
      $this->form['filter']['approved'] = array('#markup' => '<div><strong>Approved Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('nycha_appr_datefrom') . '</div>');
      $this->user_criteria['Approved Date Greater Than'] = $this->form_state->getValue('nycha_appr_datefrom');
      $this->formatted_search_criteria['Approved Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('nycha_appr_datefrom');
    }

    if ($this->form_state->hasValue('nycha_year')) {
      if ($this->form_state->getValue('nycha_year') == '0') {
        $this->form['filter']['nycha_year'] = array('#markup' => '<div><strong>Year:</strong> All Years</div>',);
        $this->formatted_search_criteria['Year'] = 'All Years';
      } else {
        $this->form['filter']['nycha_year'] = array('#markup' => '<div><strong>Year:</strong> ' . substr($this->form_state->getValue('nycha_year'), 0, -4) . ' ' . substr($this->form_state->getValue('nycha_year'), -4) . '</div>',);
        $this->formatted_search_criteria['Year'] = $this->form_state->getValue('nycha_year');
      }
    }
  }

  /*protected function _process_datasource_values()
  {
    if ($this->form_state->getValue('nycha_expense_type') && $this->form_state->getValue('expense type') != 'ts') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('nycha_expense_type'), $etmatches);
      if($etmatches) {
        $this->criteria['value']['spending_category'] = trim($etmatches[1], '[ ]');
      }
    }

    if ($this->form_state->getValue('nycha_industry') && $this->form_state->getValue('nycha_industry') != 'Select Industry') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('nycha_industry'), $imatches);
      if($imatches) {
        $this->criteria['value']['industry'] = trim($imatches[1], '[ ]');
      }
    }

    if ($this->form_state->getValue('funding_source') && $this->form_state->getValue('funding_source') != 'Select Funding Source') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('funding_source'), $imatches);
      if($imatches) {
        $this->criteria['value']['funding_source'] = trim($imatches[1], '[ ]');
      }
    }

    if ($this->form_state->getValue('resp_center') && $this->form_state->getValue('resp_center') != 'Select Responsibility Center') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('resp_center'), $imatches);
      if($imatches) {
        $this->criteria['value']['responsibility_center'] = trim($imatches[1], '[ ]');
      }
    }

    if ($this->form_state->getValue('purchase_order_type') && $this->form_state->getValue('purchase_order_type') != 'All') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('purchase_order_type'), $imatches);
      if($imatches) {
        $this->criteria['value']['purchase_order_type'] = trim($imatches[1], '[ ]');
      }
    }

    if ($this->form_state->getValue('spent_amt_from') !== '' || $this->form_state->getValue('spent_amt_to') !== '') {
      $this->criteria['range']['amount_spent'] = array(
        checknull($this->form_state->getValue('spent_amt_from')),
        checknull($this->form_state->getValue('spent_amt_to')),
      );
    }

    if ($this->form_state->getValue('nycha_year') && $this->form_state->getValue('nycha_year') != '0') {
      $this->criteria['value']['fiscal_year'] = ltrim($this->form_state->getValue('nycha_year'), 'FY') ;
    }
  }*/

  protected function _validate_by_datasource(&$form, &$form_state)
  {
//  $spent_amount_from = $form_state['values']['spent_amt_from'];
    $spent_amount_from = $form_state->getValue('spent_amt_from');
//  $spent_amount_to = $form_state['values']['spent_amt_to'];
    $spent_amount_to = $form_state->getValue('spent_amt_to');

    if ($spent_amount_from && !is_numeric($spent_amount_from)) {
//      form_set_error('spent_amt_from', t('Spent Amount must be a number.'));
      $form_state->setErrorByName('spent_amt_from', t('Spent Amount must be a number.'));
    }

    if ($spent_amount_to && !is_numeric($spent_amount_to)) {
//      form_set_error('spent_amt_to', t('Spent Amount must be a number.'));
      $form_state->setErrorByName('spent_amt_to', t('Spent Amount must be a number.'));
    }
    if (is_numeric($spent_amount_from) && is_numeric($spent_amount_to) && $spent_amount_to < $spent_amount_from) {
//      form_set_error('spent_amt_to', t('Invalid range for Spent Amount.'));
      $form_state->setErrorByName('spent_amt_to', t('Invalid range for Spent Amount.'));
    }
    // Check Columns
    $responseColumns = $form_state->getValue('nycha_column_select');
    if (empty(array_filter($responseColumns))) {
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }

//    $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
    $multi_select_hidden = $form_state->hasValue('nycha_column_select') ? '|' . implode('||', $form_state->getValue('nycha_column_select')) . '|' : '';
    if (!$multi_select_hidden) {
//      form_set_error('nycha_column_select', t('You must select at least one column.'));
      $form_state->setErrorByName('nycha_column_select', t('You must select at least one column.'));
    }
  }
}
