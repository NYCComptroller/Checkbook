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
use Drupal\checkbook_datafeeds\Utilities\FormUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;

class SpendingFeedCitywide extends SpendingFeed
{
  protected $data_source = 'citywide';
  protected $type_of_data = 'Spending';
  protected $filtered_columns_container = 'column_select';

  protected function _process_user_criteria_by_datasource(){
    // Agency.
    $this->_process_user_criteria_by_datasource_single_field_and_check('agency', 'agency', 'Agency');

    // Department.
    $this->_process_user_criteria_by_datasource_department();

    // Expense Category.
    $this->_process_user_criteria_by_datasource_expense_category();

    // Spending Category.
    if ($this->form_state->getValue('expense_type')) {
      $this->_process_user_criteria_by_datasource_single_field('expense_type', 'expense_type', 'Spending Category', 'Expense Type');
    }
    else {
      $this->form['filter']['expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> Total Spending</div>');
      $this->formatted_search_criteria['Spending Category'] = 'Total Spending';
    }

    // Conditional Category filter - Not applicable for categories Payroll and Others.
    $this->_process_user_criteria_by_datasource_conditional_category();

    // Industry.
    if ($this->form_state->getValue('industry')) {
      preg_match("/.*?(\\[.*?])/is", $this->form_state->getValue('industry'), $matches);
      $industry_type_name = str_replace($matches[1], "", $matches[0]);
      $industry_type_id = trim($matches[1], '[ ]');
      $this->form['filter']['industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $this->form_state->getValue('industry') . '</div>');
      $this->user_criteria['Industry'] = $industry_type_id;
      $this->formatted_search_criteria['Industry'] = $industry_type_name;
    }

    // M/WBE Category.
    if ($this->form_state->getValue('mwbe_category')) {
      $this->form['filter']['mwbe_category'] = array('#markup' => '<div><strong>M/WBE Category:</strong> ' . MappingUtil::getCurrenEthnicityName(explode('~', $this->form_state->getValue('mwbe_category'))) . '</div>');
      $this->user_criteria['M/WBE Category'] = $this->form_state->getValue('mwbe_category');
      $this->formatted_search_criteria['M/WBE Category'] = MappingUtil::getCurrenEthnicityName(explode('~', $this->form_state->getValue('mwbe_category')));
    }

    // Vendor.
    $this->_process_user_criteria_by_datasource_single_field_and_check('payee_name', 'payee_name', 'Payee Name');

    // Check Amount.
    $this->_process_user_criteria_by_datasource_ranged_amount_field('check_amt_from', 'check_amt_to', 'chkamount', 'Check Amount');

    // Contract ID.
    $this->_process_user_criteria_by_datasource_single_field_and_check('contractno', 'contractno', 'Contract ID');

    // Document ID.
    $this->_process_user_criteria_by_datasource_single_field_and_check('document_id', 'document_id', 'Document ID');

    // capital Project.
    $this->_process_user_criteria_by_datasource_single_field_and_check('capital_project', 'capital_project', 'Capital Project');

    // Year Filter.
    if ($this->form_state->getValue('date_filter') == 0) {
      if ($this->form_state->getValue('year') && $this->form_state->getValue('year') !== '0') {
        $this->form['filter']['year'] = array(
          '#markup' => '<div><strong>Year:</strong> ' . substr($this->form_state->getValue('year'), 0, -4) . ' ' . substr($this->form_state->getValue('year'), -4) . '</div>',
        );
        $this->user_criteria['Fiscal Year'] = $this->form_state->getValue('year');
        $this->formatted_search_criteria['Year'] = $this->form_state->getValue('year');
      }
      else{
        $this->form['filter']['year'] = array(
          '#markup' => '<div><strong>Year:</strong> All Years</div>',
        );
        $this->formatted_search_criteria['Year'] = ' All Years';
      }
    }
  }

  protected function _process_user_criteria_by_datasource_department(){
    if ($this->form_state->getValue('dept') && $this->form_state->getValue('dept') != 'Select Department' && $this->form_state->getValue('dept') != '0' && $this->form_state->getValue('agency') != 'Citywide (All Agencies)') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('dept'))) {
        $this->form_state->setValue('dept', htmlspecialchars($this->form_state->getValue('dept')));
      }
      $this->_process_user_criteria_by_datasource_single_field('dept', 'dept', 'Department');

    }
  }

  protected function _process_user_criteria_by_datasource_expense_category() {
    if ($this->form_state->getValue('expense_category') && $this->form_state->getValue('expense_category') != 'Select Expense Category' && $this->form_state->getValue('expense_category') != '0' && $this->form_state->getValue('agency') != 'Citywide (All Agencies)') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('expense_category'))) {
        $this->form_state->setValue('expense_category', htmlspecialchars($this->form_state->getValue('expense_category')));
      }
      $this->_process_user_criteria_by_datasource_single_field('expense_category', 'expense_category', 'Expense Category');
    }
  }

  protected function _process_user_criteria_by_datasource_conditional_category() {
    if ($this->form_state->getValue('expense_type') != 'Payroll [p]' && $this->form_state->getValue('expense_type') != 'Others [o]' && ($this->form_state->getValue('conditional_category') && ($this->form_state->getValue('year') == '0' || substr($this->form_state->getValue('year'), -4) >= 2020 || $this->form_state->getValue('date_filter') == 1))) {
        $conditional_categories = FormUtil::getEventNameAndId();
        $conditional_category = $conditional_categories[$this->form_state->getValue('conditional_category')] . "[" . $this->form_state->getValue('conditional_category') . "]";
        $this->form['filter']['conditional_category'] = array('#markup' => '<div><strong>Conditional Category:</strong> ' . $conditional_category . '</div>');
        $this->user_criteria['Conditional Category'] = $this->form_state->getValue('conditional_category');
        $this->formatted_search_criteria['Conditional Category'] = $this->form_state->getValue('conditional_category');
    }
  }

  protected function _process_datasource_values()
  {
    if ($this->form_state->getValue('expense_type') && $this->form_state->getValue('expense_type') != 'ts') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('expense_type'), $etmatches);
      $this->criteria['value']['spending_category'] = trim($etmatches[1], '[ ]');
    }

    if ($this->form_state->getValue('expense_type') != 'Payroll [p]' && $this->form_state->getValue('expense_type') != 'Others [o]' && ($this->form_state->getValue('conditional_category') && ($this->form_state->getValue('year') == '0' || substr($this->form_state->getValue('year'), -4) >= 2020))) {
      $this->criteria['value']['conditional_category'] = $this->form_state->getValue('conditional_category');
    }
    if ($this->form_state->getValue('agency') != 'Citywide (All Agencies)') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('agency'), $agency_matches);
      $this->criteria['value']['agency_code'] = trim($agency_matches[1], '[ ]');
    }

    if ($this->form_state->getValue('mwbe_category') && $this->form_state->getValue('mwbe_category') != 'Select Category') {
      $this->criteria['value']['mwbe_category'] = $this->form_state->getValue('mwbe_category');
    }
    if ($this->form_state->getValue('industry') && $this->form_state->getValue('industry') != 'Select Industry') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('industry'), $imatches);
      $this->criteria['value']['industry'] = trim($imatches[1], '[ ]');
    }

    if ($this->form_state->getValue('capital_project')) {
      $this->criteria['value']['capital_project_code'] = $this->form_state->getValue('capital_project');
    }

    if ($this->form_state->getValue('year') && $this->form_state->getValue('year') != '0') {
      $this->criteria['value']['fiscal_year'] = ltrim($this->form_state->getValue('year'), 'FY');
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    // Check Columns
    $responseColumns = $form_state->getValue('column_select');
    if (empty(array_filter($responseColumns))) {
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }

    $multi_select_hidden = $form_state->hasValue('column_select') ? '|' . implode('||', $form_state->getValue('column_select')) . '|' : '';

    if (!$multi_select_hidden) {
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }
  }

}
