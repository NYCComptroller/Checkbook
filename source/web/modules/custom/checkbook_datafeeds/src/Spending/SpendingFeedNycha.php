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

namespace Drupal\checkbook_datafeeds\Spending;

class SpendingFeedNycha extends SpendingFeed
{
  protected $data_source = 'checkbook_nycha';
  protected $type_of_data = 'Spending_NYCHA';
  protected $filtered_columns_container = 'nycha_column_select';
  protected $oge_label = 'Other Government Entity';
  protected $oge_name_code = "NEW YORK CITY HOUSING AUTHORITY[996]";
  protected $oge_name = "NEW YORK CITY HOUSING AUTHORITY";

  protected function _process_user_criteria_by_datasource(){
    //OGE Display
    $this->form['filter']['agency'] = array('#markup' => '<div><strong>' . $this->oge_label .':</strong> ' . $this->oge_name_code . '</div>',);
    $this->formatted_search_criteria[$this->oge_label] = $this->oge_name_code;

    //Expense Category
    $this->_process_user_criteria_by_datasource_expense_category();

    //Spending Category
    if ($this->form_state->getValue('nycha_expense_type')) {
      $this->_process_user_criteria_by_datasource_single_field('nycha_expense_type', 'nycha_expense_type', 'Spending Category', 'Expense Type');
    }else{
      $this->form['filter']['nycha_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> Total Spending</div>');
      $this->formatted_search_criteria['Spending Category'] = 'Total Spending';
    }

    //Industry
    if ($this->form_state->getValue('nycha_industry') && $this->form_state->getValue('nycha_industry') != 'Select Industry') {
      $this->_process_user_criteria_by_datasource_single_field('nycha_industry', 'nycha_industry', 'Industry');
    }

    //Funding Source
    if ($this->form_state->getValue('funding_source') && $this->form_state->getValue('funding_source') != 'Select Funding Source') {
      $this->_process_user_criteria_by_datasource_single_field('funding_source', 'funding_source', 'Funding Source');
    }

    //Responsibility Center
    if ($this->form_state->getValue('resp_center') && $this->form_state->getValue('resp_center') != 'Select Responsibility Center') {
      $this->_process_user_criteria_by_datasource_single_field('resp_center', 'resp_center', 'Responsibility Center');
    }

    //Vendor
    $this->_process_user_criteria_by_datasource_single_field_and_check('payee_name', 'payee_name', 'Vendor');

    //Check Amount
    $this->_process_user_criteria_by_datasource_ranged_amount_field('check_amt_from', 'check_amt_to', 'chkamount', 'Check Amount');

    //Amount Spent
    $this->_process_user_criteria_by_datasource_ranged_amount_field('spent_amt_from', 'spent_amt_to', 'amount_spent', 'Amount Spent');

    //Purchase Order Type
    if ($this->form_state->getValue('purchase_order_type') && $this->form_state->getValue('nycha_expense_type') != 'Payroll [PAYROLL]' && $this->form_state->getValue('nycha_expense_type') != 'Section 8 [SECTION8]') {
      $this->_process_user_criteria_by_datasource_single_field('purchase_order_type', 'purchase_order_type', 'Purchase Order Type');
    }

    //Contract ID
    $this->_process_user_criteria_by_datasource_single_field_and_check('contractno', 'contractno', 'Contract ID');

    //Document ID
    $this->_process_user_criteria_by_datasource_single_field_and_check('document_id', 'document_id', 'Document ID');

    //Year
    if ($this->form_state->getValue('nycha_year') && $this->form_state->getValue('nycha_year') != '0') {
      $this->form['filter']['nycha_year'] = array('#markup' => '<div><strong>Year:</strong> ' . substr($this->form_state->getValue('nycha_year'), 0, -4) .' '. substr($this->form_state->getValue('nycha_year'),-4) . '</div>');
      $this->formatted_search_criteria['Year'] = $this->form_state->getValue('nycha_year');
      $this->user_criteria['Year'] = $this->form_state->getValue('nycha_year');
    }else{
      $this->form['filter']['nycha_year'] = array('#markup' => '<div><strong>Year:</strong> All Years</div>');
      $this->formatted_search_criteria['Year'] = 'All Years';
      $this->user_criteria['Year'] = 'All Years';
    }
  }

  protected function _process_user_criteria_by_datasource_expense_category() {
    if ($this->form_state->hasValue('expense_category') && $this->form_state->getValue('expense_category') != 'Select Expense Category' && $this->form_state->getValue('expense_category') != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('expense_category'))) {
        $this->form_state->setValue('expense_category', htmlspecialchars($this->form_state->getValue('expense_category')));
      }
      $this->_process_user_criteria_by_datasource_single_field('expense_category', 'expense_category', 'Expense Category');
    }
  }

  protected function _process_datasource_values()
  {
    if ($this->form_state->getValue('nycha_expense_type') && $this->form_state->getValue('nycha_expense_type') != 'ts') {
      $this->_process_datasource_values_pregmatch('nycha_expense_type', 'spending_category');
    }

    if ($this->form_state->getValue('nycha_industry') && $this->form_state->getValue('nycha_industry') != 'Select Industry') {
      $this->_process_datasource_values_pregmatch('nycha_industry', 'industry');
    }

    if ($this->form_state->getValue('funding_source') && $this->form_state->getValue('funding_source') != 'Select Funding Source') {
      $this->_process_datasource_values_pregmatch('funding_source', 'funding_source');
    }

    if ($this->form_state->getValue('resp_center') && $this->form_state->getValue('resp_center') != 'Select Responsibility Center') {
      $this->_process_datasource_values_pregmatch('resp_center', 'responsibility_center');
    }

    if ($this->form_state->getValue('purchase_order_type') && $this->form_state->getValue('purchase_order_type') != 'All') {
      $this->_process_datasource_values_pregmatch('purchase_order_type', 'purchase_order_type');
    }

    $this->_process_ranged_datasource_values('spent_amt_from', 'spent_amt_to', 'amount_spent');

    if ($this->form_state->getValue('nycha_year') && $this->form_state->getValue('nycha_year') != '0') {
      $this->criteria['value']['fiscal_year'] = ltrim($this->form_state->getValue('nycha_year'), 'FY') ;
    }
  }

  protected function _process_datasource_values_pregmatch($field_name, $criteria_key_name) {
    preg_match($this->bracket_value_pattern, $this->form_state->getValue($field_name), $imatches);
    if($imatches) {
      $this->criteria['value'][$criteria_key_name] = trim($imatches[1], '[ ]');
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    $spent_amount_from = $form_state->getValue('spent_amt_from');
    $spent_amount_to = $form_state->getValue('spent_amt_to');

    if ($spent_amount_from && !is_numeric($spent_amount_from)) {
      $form_state->setErrorByName('spent_amt_from', t('Spent Amount must be a number.'));
    }

    if ($spent_amount_to && !is_numeric($spent_amount_to)) {
      $form_state->setErrorByName('spent_amt_to', t('Spent Amount must be a number.'));
    }
    if (is_numeric($spent_amount_from) && is_numeric($spent_amount_to) && $spent_amount_to < $spent_amount_from) {
      $form_state->setErrorByName('spent_amt_to', t('Invalid range for Spent Amount.'));
    }
    // Check Columns
    $responseColumns = $form_state->getValue('nycha_column_select');
    if (empty(array_filter($responseColumns))) {
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }

    $multi_select_hidden = $form_state->hasValue('nycha_column_select') ? '|' . implode('||', $form_state->getValue('nycha_column_select')) . '|' : '';
    if (!$multi_select_hidden) {
      $form_state->setErrorByName('nycha_column_select', t('You must select at least one column.'));
    }
  }
}
