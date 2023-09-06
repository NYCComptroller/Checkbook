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

namespace Drupal\checkbook_datafeeds\Budget;

class BudgetFeedCitywide extends BudgetFeed{
  protected string $data_source = 'citywide';
  protected string $type_of_data = 'Budget';
  protected $filtered_columns_container = 'column_select_expense';

  protected function _process_user_criteria_by_datasource(){
    if ($this->form_state->getValue('agency')) {
      $this->_process_user_criteria_by_datasource_single_field('agency', 'agency', 'Agency');
    }
    if ($this->form_state->getValue('dept') && $this->form_state->getValue('dept') != 'Select Department') {
      $this->_process_user_criteria_by_datasource_single_field('dept', 'department', 'Department');
    }
    if ($this->form_state->getValue('expense_category') && $this->form_state->getValue('expense_category') != 'Select Expense Category') {
      $this->_process_user_criteria_by_datasource_single_field('expense_category', 'expense_category', 'Expense Category');
    }

    if ($this->form_state->getValue('budget_code')) {
      $this->_process_user_criteria_by_datasource_single_field('budget_code', 'budget_code', 'Budget Code');
    }
    if ($this->form_state->getValue('catastrophic_event') !== "0" &&  $this->form_state->getValue('fiscal_year') >= 2020) {
      $this->_process_user_criteria_by_datasource_single_field('catastrophic_event', 'catastrophic_event', 'Catastrophic Event');
    }
    if ($this->form_state->getValue('fiscal_year')) {
      $this->_process_user_criteria_by_datasource_single_field('fiscal_year', 'fiscal_year', 'Year');
    }

    $this->_process_ranged_amounts_user_criteria('currentfrom', 'currentto', 'current_budget', 'Modified Budget');

    $this->_process_ranged_amounts_user_criteria('adoptedfrom', 'adoptedto', 'adopted_budget', 'Adopted Budget');

    $this->_process_ranged_amounts_user_criteria('preencumberedfrom', 'preencumberedto', 'preencumbered', 'Pre-encumbered');

    $this->_process_ranged_amounts_user_criteria('encumberedfrom', 'encumberedto', 'encumbered', 'Encumbered');

    $this->_process_ranged_amounts_user_criteria('accruedexpensefrom', 'accruedexpenseto', 'accrued_expense', 'Accrued Expense');

    $this->_process_ranged_amounts_user_criteria('cashfrom', 'cashto', 'cash_amount', 'Cash Expense');

    $this->_process_ranged_amounts_user_criteria('postadjustmentsfrom', 'postadjustmentsto', 'post_adjustments', 'Post Adjustments');
  }

  protected function _process_datasource_values(){
    if ($this->form_state->getValue('fiscal_year') != 'All Years') {
      $this->criteria['value']['year'] = $this->form_state->getValue('fiscal_year');
    }
    if ($this->form_state->getValue('agency') != 'Citywide (All Agencies)') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('agency'), $amatches);
      if ($amatches) {
        $this->criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
      }
    }

    $this->_process_single_field_datasource_values('dept', 'department_code');

    $this->_process_single_field_datasource_values('expense_category', 'expense_category');

    if ($this->form_state->getValue('budget_code')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('budget_code'), $bcmatches);
      if ($bcmatches) {
        $this->criteria['value']['budget_code'] = trim($bcmatches[1], '[ ]');
        $this->criteria['value']['budget_code_name'] = str_replace($bcmatches[1], "", $this->form_state->getValue('budget_code'));
      } else {
        $this->criteria['value']['budget_code_name'] = $this->form_state->getValue('budget_code');
      }
    }
    if ($this->form_state->getValue('catastrophic_event') !== '0' && $this->form_state->getValue('fiscal_year') >= 2020) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('catastrophic_event'), $evcmatches);
      if ($evcmatches) {
        $this->criteria['value']['catastrophic_event'] = trim($evcmatches[1], '[ ]');
      }
    }

    $this->_process_ranged_datasource_values('adoptedfrom', 'adoptedto', 'adopted');

    $this->_process_ranged_datasource_values('currentfrom', 'currentto', 'modified');

    $this->_process_ranged_datasource_values('preencumberedfrom', 'preencumberedto', 'pre_encumbered');

    $this->_process_ranged_datasource_values('encumberedfrom', 'encumberedto', 'encumbered');

    $this->_process_ranged_datasource_values('cashfrom', 'cashto', 'cash_expense');

    $this->_process_ranged_datasource_values('postadjustmentsfrom', 'postadjustmentsto', 'post_adjustment');

    $this->_process_ranged_datasource_values('accruedexpensefrom', 'accruedexpenseto', 'accrued_expense');
  }

  protected function _validate_by_datasource(&$form, &$form_state){
    $expensecolumns = $form_state->getValue('column_select_expense');

    // Adopted:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'adoptedfrom', 'adoptedto', 'Adopted Budget');

    // Current:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'currentfrom', 'currentto', 'Modified Budget');

    // Preencumbered:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'preencumberedfrom', 'preencumberedto', 'Pre-encumbered');

    // Encumbered:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'encumberedfrom', 'encumberedto', 'Encumbered');

    // Cash:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'cashfrom', 'cashto', 'Cash Expense');

    // Post Adjustments:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'postadjustmentsfrom', 'postadjustmentsto', 'Post Adjustment');

    // Accrued Expense:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'accruedexpensefrom', 'accruedexpenseto', 'Accrued Expense');

    // Columns:
    if (empty(array_filter($expensecolumns))) {
      $form_state->setErrorByName('column_select_expense', t('You must select at least one column.'));
    }

    //Set the hidden filed values on Budget form
      $form_state->setValue(['complete form','dept_hidden','#value'], $form_state->getValue(['values','dept']));
      $form_state->setValue(['complete form','expense_category_hidden','#value'], $form_state->getValue('expense_category'));
  }
}
