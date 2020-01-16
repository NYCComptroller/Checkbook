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
  protected $oge_name = "NEW YORK CITY HOUSING AUTHORITY";

  protected function _process_user_criteria_by_datasource(){

    //OGE Display
    $this->form['filter']['agency'] = array('#markup' => '<div><strong>' . $this->oge_label .':</strong> ' . $this->oge_name . '</div>',);

    //Department
    if ($this->values['dept'] && $this->values['dept'] != 'Select Department' && $this->values['dept'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['dept'])) {
        $this->values['dept'] = htmlspecialchars($this->values['dept']);
      }
      $this->form['filter']['dept'] = array('#markup' => '<div><strong>Department:</strong>' . $this->values['dept'] . '</div>');
      $this->user_criteria['Department'] = $this->values['dept'];
      $this->formatted_search_criteria['Department'] = $this->values['dept'];
    }

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
    if ($this->values['nycha_expense_type'] && $this->values['nycha_expense_type'] !== 'Total Spending') {
      preg_match("/.*?(\\[.*?])/is", $this->values['nycha_expense_type'], $matches);
      $expense_type_name = str_replace($matches[1], "", $matches[0]);
      $this->form['filter']['nycha_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> ' . $expense_type_name . '</div>');
      $this->user_criteria['Expense Type'] = $this->values['nycha_expense_type'];
      $this->formatted_search_criteria['Spending Category'] = $expense_type_name;
    }else{
      $this->form['filter']['nycha_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> Total Spending</div>');
      $this->formatted_search_criteria['Spending Category'] = 'Total Spending';
    }

    //Industry
    if ($this->values['nycha_industry']) {
      $industry_type = trim($this->values['nycha_industry']);
      $this->form['filter']['nycha_industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $industry_type . '</div>');
      $this->user_criteria['Industry'] = $industry_type;
      $this->formatted_search_criteria['Industry'] = $industry_type;
    }

    //Funding Source
    if ($this->values['funding_source']) {
      $funding_source = trim($this->values['funding_source']);
      $this->form['filter']['funding_source'] = array('#markup' => '<div><strong>Funding Source:</strong> ' . $funding_source . '</div>');
      $this->user_criteria['funding_source'] = $funding_source;
      $this->formatted_search_criteria['funding_source'] = $funding_source;
    }

    //Responsibility Center
    if ($this->values['resp_center']) {
      $resp_center = trim($this->values['resp_center']);
      $this->form['filter']['resp_center'] = array('#markup' => '<div><strong>Responsibility Center:</strong> ' . $resp_center . '</div>');
      $this->user_criteria['resp_center'] = $resp_center;
      $this->formatted_search_criteria['resp_center'] = $resp_center;
    }

    //Vendor
    if ($this->values['payee_name']) {
      $this->form['filter']['payee_name'] = array(
        '#markup' => '<div><strong>Payee Name:</strong> ' . $this->values['payee_name'] . '</div>',
      );
      $this->user_criteria['Payee Name'] = $this->values['payee_name'];
      $this->formatted_search_criteria['Payee Name'] = $this->values['payee_name'];
    }

    //Check Amount
    if ($this->values['check_amt_from'] && $this->values['check_amt_to']) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Greater Than Equal to: $' . $this->values['check_amt_from'] . ' Less Than Equal to: $' . $this->values['check_amt_to'] . '</div>',
      );
      $this->user_criteria['Check Amount Greater Than'] = $this->values['check_amt_from'];
      $this->user_criteria['Check Amount Less Than'] = $this->values['check_amt_to'];
      $this->formatted_search_criteria['Check Amount'] = 'Greater Than Equal to: $' . $this->values['check_amt_from'] . ' Less Than Equal to: $' . $this->values['check_amt_to'];
    } elseif (!$this->values['check_amt_from'] && $this->values['check_amt_to']) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Less Than Equal to: $' . $this->values['check_amt_to'] . '</div>',
      );
      $this->user_criteria['Check Amount Less Than'] = $this->values['check_amt_to'];
      $this->formatted_search_criteria['Check Amount'] = 'Less Than Equal to: $' . $this->values['check_amt_to'];
    } elseif ($this->values['check_amt_from'] && !$this->values['check_amt_from']) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Greater Than Equal to: $' . $this->values['check_amt_from'] . '</div>',
      );
      $this->user_criteria['Check Amount Greater Than'] = $this->values['check_amt_to'];
      $this->formatted_search_criteria['Check Amount'] = 'Greater Than Equal to: $' . $this->values['check_amt_from'];
    }

    //Amount Spent
    if ($this->values['spent_amt_from'] && $this->values['spent_amt_to']) {
      $this->form['filter']['amount_spent'] = array(
        '#markup' => '<div><strong>Amount Spent:</strong> Greater Than Equal to: $' . $this->values['spent_amt_from'] . ' Less Than Equal to: $' . $this->values['spent_amt_to'] . '</div>',
      );
      $this->user_criteria['Amount Spent Greater Than'] = $this->values['spent_amt_from'];
      $this->user_criteria['Amount Spent Less Than'] = $this->values['spent_amt_to'];
      $this->formatted_search_criteria['Amount Spent'] = 'Greater Than Equal to: $' . $this->values['spent_amt_from'] . ' Less Than Equal to: $' . $this->values['spent_amt_to'];
    } elseif (!$this->values['spent_amt_from'] && $this->values['spent_amt_to']) {
      $this->form['filter']['amount_spent'] = array(
        '#markup' => '<div><strong>Amount Spent:</strong> Less Than Equal to: $' . $this->values['spent_amt_to'] . '</div>',
      );
      $this->user_criteria['Amount Spent Less Than'] = $this->values['spent_amt_to'];
      $this->formatted_search_criteria['Amount Spent'] = 'Less Than Equal to: $' . $this->values['spent_amt_to'];
    } elseif ($this->values['spent_amt_from'] && !$this->values['spent_amt_from']) {
      $this->form['filter']['amount_spent'] = array(
        '#markup' => '<div><strong>Amount Spent:</strong> Greater Than Equal to: $' . $this->values['spent_amt_from'] . '</div>',
      );
      $this->user_criteria['Amount Spent Greater Than'] = $this->values['spent_amt_to'];
      $this->formatted_search_criteria['Amount Spent'] = 'Greater Than Equal to: $' . $this->values['spent_amt_from'];
    }

    //Purchase Order Type
    if ($this->values['purchase_order_type']) {
      $po_type = trim($this->values['purchase_order_type']);
      $this->form['filter']['purchase_order_type'] = array('#markup' => '<div><strong>Purchase Order Type:</strong> ' . $po_type . '</div>');
      $this->user_criteria['purchase_order_type'] = $po_type;
      $this->formatted_search_criteria['resp_center'] = $po_type;
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
        $this->criteria['value']['industry_type'] = trim($imatches[1], '[ ]');
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
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
    if (!$multi_select_hidden) {
      form_set_error('nycha_column_select', t('You must select at least one column.'));
    }
  }
}
