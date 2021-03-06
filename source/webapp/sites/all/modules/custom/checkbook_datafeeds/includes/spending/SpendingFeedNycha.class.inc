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

namespace checkbook_datafeeds;


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
    if ($this->values['expense_category'] && $this->values['expense_category'] != 'Select Expense Category' && $this->values['expense_category'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['expense_category'])) {
        $this->values['expense_category'] = htmlspecialchars($this->values['expense_category']);
      }
      $this->form['filter']['expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $this->values['expense_category'] . '</div>');
      $this->user_criteria['Expense Category'] = $this->values['expense_category'];
      $this->formatted_search_criteria['Expense Category'] = $this->values['expense_category'];
    }

    //Spending Category
    if ($this->values['nycha_expense_type']) {
      $this->form['filter']['nycha_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> ' . $this->values['nycha_expense_type'] . '</div>');
      $this->user_criteria['Expense Type'] = $this->values['nycha_expense_type'];
      $this->formatted_search_criteria['Spending Category'] = $this->values['nycha_expense_type'];
    }else{
      $this->form['filter']['nycha_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> Total Spending</div>');
      $this->formatted_search_criteria['Spending Category'] = 'Total Spending';
    }

    //Industry
    if ($this->values['nycha_industry'] && $this->values['nycha_industry'] != 'Select Industry') {
      $industry_type = $this->values['nycha_industry'];
      $this->form['filter']['nycha_industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $industry_type . '</div>');
      $this->user_criteria['Industry'] = $industry_type;
      $this->formatted_search_criteria['Industry'] = $industry_type;
    }

    //Funding Source
    if ($this->values['funding_source'] && $this->values['funding_source'] != 'Select Funding Source') {
      $funding_source = $this->values['funding_source'];
      $this->form['filter']['funding_source'] = array('#markup' => '<div><strong>Funding Source:</strong> ' . $funding_source . '</div>');
      $this->user_criteria['Funding Source'] = $funding_source;
      $this->formatted_search_criteria['Funding Source'] = $funding_source;
    }

    //Responsibility Center
    if ($this->values['resp_center'] && $this->values['resp_center'] != 'Select Responsibility Center') {
      $resp_center = $this->values['resp_center'];
      $this->form['filter']['resp_center'] = array('#markup' => '<div><strong>Responsibility Center:</strong> ' . $resp_center . '</div>');
      $this->user_criteria['Responsibility Center'] = $resp_center;
      $this->formatted_search_criteria['Responsibility Center'] = $resp_center;
    }

    //Vendor
    if ($this->values['payee_name']) {
      $this->form['filter']['payee_name'] = array(
        '#markup' => '<div><strong>Vendor:</strong> ' . $this->values['payee_name'] . '</div>',
      );
      $this->user_criteria['Vendor'] = $this->values['payee_name'];
      $this->formatted_search_criteria['Vendor'] = $this->values['payee_name'];
    }

    //Check Amount
    if (($this->values['check_amt_from'] || $this->values['check_amt_from'] === "0") && ($this->values['check_amt_to'] || $this->values['check_amt_to'] === "0")) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Greater Than Equal to: $' . $this->values['check_amt_from'] . ' and Less Than Equal to: $' . $this->values['check_amt_to'] . '</div>',
      );
      $this->user_criteria['Check Amount Greater Than'] = $this->values['check_amt_from'];
      $this->user_criteria['Check Amount Less Than'] = $this->values['check_amt_to'];
      $this->formatted_search_criteria['Check Amount'] = 'Greater Than Equal to: $' . $this->values['check_amt_from'] . ' and Less Than Equal to: $' . $this->values['check_amt_to'];
    } elseif (!$this->values['check_amt_from'] && ($this->values['check_amt_to'] || $this->values['check_amt_to'] === "0")) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Less Than Equal to: $' . $this->values['check_amt_to'] . '</div>',
      );
      $this->user_criteria['Check Amount Less Than'] = $this->values['check_amt_to'];
      $this->formatted_search_criteria['Check Amount'] = 'Less Than Equal to: $' . $this->values['check_amt_to'];
    } elseif (($this->values['check_amt_from'] || $this->values['check_amt_from'] === "0")  && !$this->values['check_amt_to']) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Greater Than Equal to: $' . $this->values['check_amt_from'] . '</div>',
      );
      $this->user_criteria['Check Amount Greater Than'] = $this->values['check_amt_to'];
      $this->formatted_search_criteria['Check Amount'] = 'Greater Than Equal to: $' . $this->values['check_amt_from'];
    }

    //Amount Spent
    if (($this->values['spent_amt_from'] || $this->values['spent_amt_from'] === "0") && ($this->values['spent_amt_to'] || $this->values['spent_amt_to'] === "0")) {
      $this->form['filter']['amount_spent'] = array(
        '#markup' => '<div><strong>Amount Spent:</strong> Greater Than Equal to: $' . $this->values['spent_amt_from'] . ' and Less Than Equal to: $' . $this->values['spent_amt_to'] . '</div>',
      );
      $this->user_criteria['Amount Spent Greater Than'] = $this->values['spent_amt_from'];
      $this->user_criteria['Amount Spent Less Than'] = $this->values['spent_amt_to'];
      $this->formatted_search_criteria['Amount Spent'] = 'Greater Than Equal to: $' . $this->values['spent_amt_from'] . ' and Less Than Equal to: $' . $this->values['spent_amt_to'];
    } elseif (!$this->values['spent_amt_from'] && ($this->values['spent_amt_to'] || $this->values['spent_amt_to'] === "0")) {
      $this->form['filter']['amount_spent'] = array(
        '#markup' => '<div><strong>Amount Spent:</strong> Less Than Equal to: $' . $this->values['spent_amt_to'] . '</div>',
      );
      $this->user_criteria['Amount Spent Less Than'] = $this->values['spent_amt_to'];
      $this->formatted_search_criteria['Amount Spent'] = 'Less Than Equal to: $' . $this->values['spent_amt_to'];
    } elseif (($this->values['spent_amt_from'] || $this->values['spent_amt_from'] === "0") && !$this->values['spent_amt_to']) {
      $this->form['filter']['amount_spent'] = array(
        '#markup' => '<div><strong>Amount Spent:</strong> Greater Than Equal to: $' . $this->values['spent_amt_from'] . '</div>',
      );
      $this->user_criteria['Amount Spent Greater Than'] = $this->values['spent_amt_to'];
      $this->formatted_search_criteria['Amount Spent'] = 'Greater Than Equal to: $' . $this->values['spent_amt_from'];
    }

    //Purchase Order Type
    if ($this->values['purchase_order_type'] && $this->values['nycha_expense_type'] != 'Payroll [PAYROLL]' && $this->values['nycha_expense_type'] != 'Section 8 [SECTION8]') {
      $po_type = trim($this->values['purchase_order_type']);
      $this->form['filter']['purchase_order_type'] = array('#markup' => '<div><strong>Purchase Order Type:</strong> ' . $po_type . '</div>');
      $this->user_criteria['Purchase Order Type'] = $po_type;
      $this->formatted_search_criteria['Purchase Order Type'] = $po_type;
    }

    //Contract ID
    if ($this->values['contractno']) {
      $this->form['filter']['contractno'] = array(
        '#markup' => '<div><strong>Contract ID:</strong> ' . $this->values['contractno'] . '</div>',
      );
      $this->user_criteria['Contract ID'] = $this->values['contractno'];
      $this->formatted_search_criteria['Contract ID'] = $this->values['contractno'];
    }

    //Document ID
    if ($this->values['document_id']) {
      $this->form['filter']['document_id'] = array(
        '#markup' => '<div><strong>Document ID:</strong> ' . $this->values['document_id'] . '</div>',
      );
      $this->user_criteria['Document ID'] = $this->values['document_id'];
      $this->formatted_search_criteria['Document ID'] = $this->values['document_id'];
    }

    //Year
    if ($this->values['nycha_year'] && $this->values['nycha_year'] != '0') {
      $this->form['filter']['nycha_year'] = array('#markup' => '<div><strong>Year:</strong> ' . substr($this->values['nycha_year'], 0, -4) .' '. substr($this->values['nycha_year'],-4) . '</div>');
      $this->formatted_search_criteria['Year'] = $this->values['nycha_year'];
      $this->user_criteria['Year'] = $this->values['nycha_year'];
    }else{
      $this->form['filter']['nycha_year'] = array('#markup' => '<div><strong>Year:</strong> All Years</div>');
      $this->formatted_search_criteria['Year'] = 'All Years';
      $this->user_criteria['Year'] = 'All Years';
    }
  }

  protected function _process_datasource_values()
  {
    if ($this->values['nycha_expense_type'] && $this->values['expense type'] != 'ts') {
      preg_match($this->bracket_value_pattern, $this->values['nycha_expense_type'], $etmatches);
      if($etmatches) {
        $this->criteria['value']['spending_category'] = trim($etmatches[1], '[ ]');
      }
    }

    if ($this->values['nycha_industry'] && $this->values['nycha_industry'] != 'Select Industry') {
      preg_match($this->bracket_value_pattern, $this->values['nycha_industry'], $imatches);
      if($imatches) {
        $this->criteria['value']['industry'] = trim($imatches[1], '[ ]');
      }
    }

    if ($this->values['funding_source'] && $this->values['funding_source'] != 'Select Funding Source') {
      preg_match($this->bracket_value_pattern, $this->values['funding_source'], $imatches);
      if($imatches) {
        $this->criteria['value']['funding_source'] = trim($imatches[1], '[ ]');
      }
    }

    if ($this->values['resp_center'] && $this->values['resp_center'] != 'Select Responsibility Center') {
      preg_match($this->bracket_value_pattern, $this->values['resp_center'], $imatches);
      if($imatches) {
        $this->criteria['value']['responsibility_center'] = trim($imatches[1], '[ ]');
      }
    }

    if ($this->values['purchase_order_type'] && $this->values['purchase_order_type'] != 'All') {
      preg_match($this->bracket_value_pattern, $this->values['purchase_order_type'], $imatches);
      if($imatches) {
        $this->criteria['value']['purchase_order_type'] = trim($imatches[1], '[ ]');
      }
    }

    if ($this->values['spent_amt_from'] !== '' || $this->values['spent_amt_to'] !== '') {
      $this->criteria['range']['amount_spent'] = array(
        checknull($this->values['spent_amt_from']),
        checknull($this->values['spent_amt_to']),
      );
    }

    if ($this->values['nycha_year'] && $this->values['nycha_year'] != '0') {
        $this->criteria['value']['fiscal_year'] = ltrim($this->values['nycha_year'], 'FY') ;
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    $spent_amount_from = $form_state['values']['spent_amt_from'];
    $spent_amount_to = $form_state['values']['spent_amt_to'];

    if ($spent_amount_from && !is_numeric($spent_amount_from)) {
      form_set_error('spent_amt_from', t('Spent Amount must be a number.'));
    }

    if ($spent_amount_to && !is_numeric($spent_amount_to)) {
      form_set_error('spent_amt_to', t('Spent Amount must be a number.'));
    }
    if (is_numeric($spent_amount_from) && is_numeric($spent_amount_to) && $spent_amount_to < $spent_amount_from) {
      form_set_error('spent_amt_to', t('Invalid range for Spent Amount.'));
    }

    $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
    if (!$multi_select_hidden) {
      form_set_error('nycha_column_select', t('You must select at least one column.'));
    }
  }
}
