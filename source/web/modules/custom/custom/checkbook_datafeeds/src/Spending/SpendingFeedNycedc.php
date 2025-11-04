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
    $this->_process_user_criteria_by_datasource_department();

    //Expense Category
    $this->_process_user_criteria_by_datasource_expense_category();

    //Spending Category
    if (!empty($this->form_state->getValue('nycedc_expense_type'))) {
      $this->_process_user_criteria_by_datasource_single_field('nycedc_expense_type', 'nycedc_expense_type', 'Spending Category', 'Expense Type');
    } else {
      $this->form['filter']['nycedc_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> Total Spending</div>');
      $this->formatted_search_criteria['Spending Category'] = 'Total Spending';
    }

    //Vendor
    $this->_process_user_criteria_by_datasource_single_field_and_check('payee_name', 'payee_name', 'Payee Name');

    //Check Amount
    $this->_process_user_criteria_by_datasource_ranged_amount_field('check_amt_from', 'check_amt_to', 'chkamount', 'Check Amount');

    //Contract ID
    $this->_process_user_criteria_by_datasource_single_field_and_check('contractno', 'contractno', 'Contract ID');

    //Commodity Line
    $this->_process_user_criteria_by_datasource_single_field_and_check('commodity_line', 'commodity_line', 'Commodity Line');

    //Entity Contract Number
    $this->_process_user_criteria_by_datasource_single_field_and_check('entity_contract_number', 'entity_contract_number', 'Entity Contract #');

    //capital Project
    $this->_process_user_criteria_by_datasource_single_field_and_check('capital_project', 'capital_project', 'Capital Project');

    //Budget Name
    $this->_process_user_criteria_by_datasource_single_field_and_check('budget_name', 'budget_name', 'Budget Name');

    //Year Filter
    if (!empty($this->form_state->getValue('year')) && $this->form_state->getValue('year') !== '0') {
      $this->form['filter']['year'] = array(
        '#markup' => '<div><strong>Year:</strong> ' . substr($this->form_state->getValue('year'), 0, -4) . ' ' . substr($this->form_state->getValue('year'), -4) . '</div>',
      );
      $this->user_criteria['Fiscal Year'] = $this->form_state->getValue('year');
      $this->formatted_search_criteria['Year'] = $this->form_state->getValue('year');
    } else{
      $this->form['filter']['year'] = array(
        '#markup' => '<div><strong>Year:</strong> All Years</div>',
      );
      $this->formatted_search_criteria['Year'] = ' All Years';
    }

  }

  protected function _process_user_criteria_by_datasource_department(){
    if (!empty($this->form_state->getValue('dept')) && $this->form_state->getValue('dept') != 'Select Department' && $this->form_state->getValue('dept') != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('dept'))) {
        $this->form_state->setValue('dept', htmlspecialchars($this->form_state->getValue('dept')));
      }
      $this->_process_user_criteria_by_datasource_single_field('dept', 'dept', 'Department');
    }
  }

  protected function _process_user_criteria_by_datasource_expense_category() {
    if (!empty($this->form_state->getValue('expense_category')) && $this->form_state->getValue('expense_category') != 'Select Expense Category' && $this->form_state->getValue('expense_category') != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('expense_category'))) {
        $this->form_state->setValue('expense_category', htmlspecialchars($this->form_state->getValue('expense_category')));
      }
      $this->_process_user_criteria_by_datasource_single_field('expense_category', 'expense_category', 'Expense Category');
    }
  }

  protected function _process_datasource_values()
  {
    if (!empty($this->form_state->getValue('expense_category')) && $this->form_state->getValue('expense_category') != 'Select Expense Category' && $this->form_state->getValue('expense_category') != '0') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('expense_category'), $expense_category_matches);
      $this->criteria['value']['expense_category'] = trim($expense_category_matches[1], '[ ]');
    }

    if (!empty($this->form_state->getValue('nycedc_expense_type')) && $this->form_state->getValue('expense type') != 'ts') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('nycedc_expense_type'), $etmatches);
      $this->criteria['value']['spending_category'] = trim($etmatches[1], '[ ]');
    }

    if (!empty($this->form_state->getValue('capital_project'))) {
      $this->criteria['value']['capital_project_code'] = $this->form_state->getValue('capital_project');
    }

    if (!empty($this->form_state->getValue('commodity_line'))) {
      $this->criteria['value']['commodity_line'] = $this->form_state->getValue('commodity_line');
    }

    if (!empty($this->form_state->getValue('capital_project'))) {
      $this->criteria['value']['budget_name'] = $this->form_state->getValue('capital_project');
    }

    if (!empty($this->form_state->getValue('year')) && $this->form_state->getValue('year') != '0') {
      $this->criteria['value']['fiscal_year'] = ltrim($this->form_state->getValue('year'), 'FY');
    }

    if (!empty($this->form_state->getValue('budget_name'))) {
      $this->criteria['value']['budget_name'] = $this->form_state->getValue('budget_name');
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    //Validate Commodity Line
    $entity_contractno = $form_state->getValue('entity_contract_number');
    $commodity_line = $form_state->getValue('commodity_line');
    if ($commodity_line && !is_numeric($commodity_line)) {
      $form_state->setErrorByName('commodity_line', t('Commodity Line must be a number.'));
    }
    if ($entity_contractno && !is_numeric($entity_contractno)) {
      $form_state->setErrorByName('entity_contract_number', t('Entity Contract # must be a number.'));
    }

    // Check Columns
    $responseColumns = $form_state->getValue('oge_column_select');
    if (empty(array_filter($responseColumns))) {
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }

    $multi_select_hidden = $form_state->hasValue('oge_column_select') ? '|' . implode('||', $form_state->getValue('oge_column_select')) . '|' : '';

    if (!$multi_select_hidden) {
      $form_state->setErrorByName('oge_column_select', t('You must select at least one column.'));
    }
  }
}
