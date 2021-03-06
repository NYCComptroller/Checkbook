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


class SpendingFeedNycedc extends SpendingFeed
{
  protected $data_source = 'checkbook_oge';
  protected $type_of_data = 'Spending_OGE';
  protected $filtered_columns_container = 'oge_column_select';
  protected $oge_label = 'Other Government Entity';
  protected $oge_name_code = "NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION[z81]";
  protected $oge_name = "NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION";

  protected function _process_user_criteria_by_datasource(){
    //EDC
    $this->form['filter']['agency'] = array(
      '#markup' => '<div><strong>' . $this->oge_label .':</strong> ' . $this->oge_name_code . '</div>',
    );
    $this->formatted_search_criteria[$this->oge_label] = $this->oge_name_code;

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
    if ($this->values['nycedc_expense_type']) {
      $this->form['filter']['nycedc_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> ' . $this->values['nycedc_expense_type'] . '</div>');
      $this->user_criteria['Expense Type'] = $this->values['nycedc_expense_type'];
      $this->formatted_search_criteria['Spending Category'] = $this->values['nycedc_expense_type'];
    } else {
      $this->form['filter']['nycedc_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> Total Spending</div>');
      $this->formatted_search_criteria['Spending Category'] = 'Total Spending';
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

    //Contract ID
    if ($this->values['contractno']) {
      $this->form['filter']['contractno'] = array(
        '#markup' => '<div><strong>Contract ID:</strong> ' . $this->values['contractno'] . '</div>',
      );
      $this->user_criteria['Contract ID'] = $this->values['contractno'];
      $this->formatted_search_criteria['Contract ID'] = $this->values['contractno'];
    }

    //Commodity Line
    if ($this->values['commodity_line']) {
      $this->form['filter']['commodity_line'] = array(
        '#markup' => '<div><strong>Commodity Line:</strong> ' . $this->values['commodity_line'] . '</div>',
      );
      $this->user_criteria['Commodity Line'] = $this->values['commodity_line'];
      $this->formatted_search_criteria['Commodity Line'] = $this->values['commodity_line'];
    }

    //Entity Contract Number
    if ($this->values['entity_contract_number']) {
      $this->form['filter']['entity_contract_number'] = array(
        '#markup' => '<div><strong>Entity Contract #:</strong> ' . $this->values['entity_contract_number'] . '</div>',
      );
      $this->user_criteria['Entity Contract #'] = $this->values['entity_contract_number'];
      $this->formatted_search_criteria['Entity Contract #'] = $this->values['entity_contract_number'];
    }

    //capital Project
    if ($this->values['capital_project']) {
      $this->form['filter']['capital_project'] = array(
        '#markup' => '<div><strong>Capital Project:</strong> ' . $this->values['capital_project'] . '</div>',
      );
      $this->user_criteria['Capital Project'] = $this->values['capital_project'];
      $this->formatted_search_criteria['Capital Project'] = $this->values['capital_project'];
    }

    //Budget Name
    if ($this->values['budget_name']) {
      $this->form['filter']['budget_name'] = array(
        '#markup' => '<div><strong>Budget Name:</strong> ' . $this->values['budget_name'] . '</div>',
      );
      $this->user_criteria['Budget Name'] = $this->values['budget_name'];
      $this->formatted_search_criteria['Budget Name'] = $this->values['budget_name'];
    }

    //Year Filter
    if ($this->values['year'] && $this->values['year'] !== '0') {
      $this->form['filter']['year'] = array(
        '#markup' => '<div><strong>Year:</strong> ' . substr($this->values['year'], 0, -4) . ' ' . substr($this->values['year'], -4) . '</div>',
      );
      $this->user_criteria['Fiscal Year'] = $this->values['year'];
      $this->formatted_search_criteria['Year'] = $this->values['year'];
    } else{
      $this->form['filter']['year'] = array(
        '#markup' => '<div><strong>Year:</strong> All Years</div>',
      );
      $this->formatted_search_criteria['Year'] = ' All Years';
    }

  }

  protected function _process_datasource_values()
  {
    if ($this->values['nycedc_expense_type'] && $this->values['expense type'] != 'ts') {
      preg_match($this->bracket_value_pattern, $this->values['nycedc_expense_type'], $etmatches);
      $this->criteria['value']['spending_category'] = trim($etmatches[1], '[ ]');
    }

    if ($this->values['capital_project']) {
      $this->criteria['value']['capital_project_code'] = $this->values['capital_project'];
    }

    if ($this->values['commodity_line']) {
      $this->criteria['value']['commodity_line'] = $this->values['commodity_line'];
    }

    if ($this->values['budget_name']) {
      $this->criteria['value']['budget_name'] = $this->values['budget_name'];
    }

    if ($this->values['year'] && $this->values['year'] != '0') {
      $this->criteria['value']['fiscal_year'] = ltrim($this->values['year'], 'FY');
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    //Validate Commodity Line
    $entity_contractno = $form_state['values']['entity_contract_number'];
    $commodity_line = $form_state['values']['commodity_line'];
    if ($commodity_line && !is_numeric($commodity_line)) {
      form_set_error('commodity_line', t('Commodity Line must be a number.'));
    }
    if ($entity_contractno && !is_numeric($entity_contractno)) {
      form_set_error('entity_contract_number', t('Entity Contract # must be a number'));
    }

    $multi_select_hidden = isset($form_state['input']['oge_column_select']) ? '|' . implode('||', $form_state['input']['oge_column_select']) . '|' : '';
    if (!$multi_select_hidden) {
      form_set_error('oge_column_select', t('You must select at least one column.'));
    }
  }
}
