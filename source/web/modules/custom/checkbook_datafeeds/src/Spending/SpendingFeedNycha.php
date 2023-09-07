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
    if ($this->form_state->hasValue('expense_category') && $this->form_state->getValue('expense_category') != 'Select Expense Category' && $this->form_state->getValue('expense_category') != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('expense_category'))) {
        $this->form_state->setValue('expense_category', htmlspecialchars($this->form_state->getValue('expense_category')));
      }
      $this->form['filter']['expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $this->form_state->getValue('expense_category') . '</div>');
      $this->user_criteria['Expense Category'] = $this->form_state->getValue('expense_category');
      $this->formatted_search_criteria['Expense Category'] = $this->form_state->getValue('expense_category');
    }

    //Spending Category
    if ($this->form_state->getValue('nycha_expense_type')) {
      $this->form['filter']['nycha_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> ' . $this->form_state->getValue('nycha_expense_type') . '</div>');
      $this->user_criteria['Expense Type'] = $this->form_state->getValue('nycha_expense_type');
      $this->formatted_search_criteria['Spending Category'] = $this->form_state->getValue('nycha_expense_type');
    }else{
      $this->form['filter']['nycha_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> Total Spending</div>');
      $this->formatted_search_criteria['Spending Category'] = 'Total Spending';
    }

    //Industry
    if ($this->form_state->getValue('nycha_industry') && $this->form_state->getValue('nycha_industry') != 'Select Industry') {
      $industry_type = $this->form_state->getValue('nycha_industry');
      $this->form['filter']['nycha_industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $industry_type . '</div>');
      $this->user_criteria['Industry'] = $industry_type;
      $this->formatted_search_criteria['Industry'] = $industry_type;
    }

    //Funding Source
    if ($this->form_state->getValue('funding_source') && $this->form_state->getValue('funding_source') != 'Select Funding Source') {
      $funding_source = $this->form_state->getValue('funding_source');
      $this->form['filter']['funding_source'] = array('#markup' => '<div><strong>Funding Source:</strong> ' . $funding_source . '</div>');
      $this->user_criteria['Funding Source'] = $funding_source;
      $this->formatted_search_criteria['Funding Source'] = $funding_source;
    }

    //Responsibility Center
    if ($this->form_state->getValue('resp_center') && $this->form_state->getValue('resp_center') != 'Select Responsibility Center') {
      $resp_center = $this->form_state->getValue('resp_center');
      $this->form['filter']['resp_center'] = array('#markup' => '<div><strong>Responsibility Center:</strong> ' . $resp_center . '</div>');
      $this->user_criteria['Responsibility Center'] = $resp_center;
      $this->formatted_search_criteria['Responsibility Center'] = $resp_center;
    }

    //Vendor
    if ($this->form_state->getValue('payee_name')) {
      $this->form['filter']['payee_name'] = array(
        '#markup' => '<div><strong>Vendor:</strong> ' . $this->form_state->getValue('payee_name') . '</div>',
      );
      $this->user_criteria['Vendor'] = $this->form_state->getValue('payee_name');
      $this->formatted_search_criteria['Vendor'] = $this->form_state->getValue('payee_name');
    }

    //Check Amount
    if (($this->form_state->getValue('check_amt_from') || $this->form_state->getValue('check_amt_from') === "0") && ($this->form_state->getValue('check_amt_to') || $this->form_state->getValue('check_amt_to') === "0")) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Greater Than Equal to: $' . $this->form_state->getValue('check_amt_from') . ' and Less Than Equal to: $' . $this->form_state->getValue('check_amt_to') . '</div>',
      );
      $this->user_criteria['Check Amount Greater Than'] = $this->form_state->getValue('check_amt_from');
      $this->user_criteria['Check Amount Less Than'] = $this->form_state->getValue('check_amt_to');
      $this->formatted_search_criteria['Check Amount'] = 'Greater Than Equal to: $' . $this->form_state->getValue('check_amt_from') . ' and Less Than Equal to: $' . $this->form_state->getValue('check_amt_to');
    } elseif (!$this->form_state->getValue('check_amt_from') && ($this->form_state->getValue('check_amt_to') || $this->form_state->getValue('check_amt_to') === "0")) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Less Than Equal to: $' . $this->form_state->getValue('check_amt_to') . '</div>',
      );
      $this->user_criteria['Check Amount Less Than'] = $this->form_state->getValue('check_amt_to');
      $this->formatted_search_criteria['Check Amount'] = 'Less Than Equal to: $' . $this->form_state->getValue('check_amt_to');
    } elseif (($this->form_state->getValue('check_amt_from') || $this->form_state->getValue('check_amt_from') === "0")  && !$this->form_state->getValue('check_amt_to')) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Greater Than Equal to: $' . $this->form_state->getValue('check_amt_from') . '</div>',
      );
      $this->user_criteria['Check Amount Greater Than'] = $this->form_state->getValue('check_amt_to');
      $this->formatted_search_criteria['Check Amount'] = 'Greater Than Equal to: $' . $this->form_state->getValue('check_amt_from');
    }

    //Amount Spent
    if (($this->form_state->getValue('spent_amt_from') || $this->form_state->getValue('spent_amt_from') === "0") && ($this->form_state->getValue('spent_amt_to') || $this->form_state->getValue('spent_amt_to') === "0")) {
      $this->form['filter']['amount_spent'] = array(
        '#markup' => '<div><strong>Amount Spent:</strong> Greater Than Equal to: $' . $this->form_state->getValue('spent_amt_from') . ' and Less Than Equal to: $' . $this->form_state->getValue('spent_amt_to') . '</div>',
      );
      $this->user_criteria['Amount Spent Greater Than'] = $this->form_state->getValue('spent_amt_from');
      $this->user_criteria['Amount Spent Less Than'] = $this->form_state->getValue('spent_amt_to');
      $this->formatted_search_criteria['Amount Spent'] = 'Greater Than Equal to: $' . $this->form_state->getValue('spent_amt_from') . ' and Less Than Equal to: $' . $this->form_state->getValue('spent_amt_to');
    } elseif (!$this->form_state->getValue('spent_amt_from') && ($this->form_state->getValue('spent_amt_to') || $this->form_state->getValue('spent_amt_to') === "0")) {
      $this->form['filter']['amount_spent'] = array(
        '#markup' => '<div><strong>Amount Spent:</strong> Less Than Equal to: $' . $this->form_state->getValue('spent_amt_to') . '</div>',
      );
      $this->user_criteria['Amount Spent Less Than'] = $this->form_state->getValue('spent_amt_to');
      $this->formatted_search_criteria['Amount Spent'] = 'Less Than Equal to: $' . $this->form_state->getValue('spent_amt_to');
    } elseif (($this->form_state->getValue('spent_amt_from') || $this->form_state->getValue('spent_amt_from') === "0") && !$this->form_state->getValue('spent_amt_to')) {
      $this->form['filter']['amount_spent'] = array(
        '#markup' => '<div><strong>Amount Spent:</strong> Greater Than Equal to: $' . $this->form_state->getValue('spent_amt_from') . '</div>',
      );
      $this->user_criteria['Amount Spent Greater Than'] = $this->form_state->getValue('spent_amt_to');
      $this->formatted_search_criteria['Amount Spent'] = 'Greater Than Equal to: $' . $this->form_state->getValue('spent_amt_from');
    }

    //Purchase Order Type
    if ($this->form_state->getValue('purchase_order_type') && $this->form_state->getValue('nycha_expense_type') != 'Payroll [PAYROLL]' && $this->form_state->getValue('nycha_expense_type') != 'Section 8 [SECTION8]') {
      $po_type = trim($this->form_state->getValue('purchase_order_type'));
      $this->form['filter']['purchase_order_type'] = array('#markup' => '<div><strong>Purchase Order Type:</strong> ' . $po_type . '</div>');
      $this->user_criteria['Purchase Order Type'] = $po_type;
      $this->formatted_search_criteria['Purchase Order Type'] = $po_type;
    }

    //Contract ID
    if ($this->form_state->getValue('contractno')) {
      $this->form['filter']['contractno'] = array(
        '#markup' => '<div><strong>Contract ID:</strong> ' . $this->form_state->getValue('contractno') . '</div>',
      );
      $this->user_criteria['Contract ID'] = $this->form_state->getValue('contractno');
      $this->formatted_search_criteria['Contract ID'] = $this->form_state->getValue('contractno');
    }

    //Document ID
    if ($this->form_state->getValue('document_id')) {
      $this->form['filter']['document_id'] = array(
        '#markup' => '<div><strong>Document ID:</strong> ' . $this->form_state->getValue('document_id') . '</div>',
      );
      $this->user_criteria['Document ID'] = $this->form_state->getValue('document_id');
      $this->formatted_search_criteria['Document ID'] = $this->form_state->getValue('document_id');
    }

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

  protected function _process_datasource_values()
  {
    if ($this->form_state->getValue('nycha_expense_type') && $this->form_state->getValue('nycha_expense_type') != 'ts') {
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
  }

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
