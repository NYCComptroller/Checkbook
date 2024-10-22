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
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;

class BudgetFeedNycha extends BudgetFeed
{
  protected string $data_source = Datasource::NYCHA;
  protected string $type_of_data = 'Budget_NYCHA';
  protected $filtered_columns_container = 'nycha_column_select';
  protected $oge_label = 'Other Government Entity';
  protected $oge_name_code = "NEW YORK CITY HOUSING AUTHORITY[996]";
  protected $oge_name = "NEW YORK CITY HOUSING AUTHORITY";
  protected string $special_characters_pattern = '/[\'^£$%&*()}{@#~?><,|=_+¬-]/';

  protected function _process_user_criteria_by_datasource(){
    //OGE Display
    $this->form['filter']['agency'] = array('#markup' => '<div><strong>' . $this->oge_label .':</strong> ' . $this->oge_name_code . '</div>',);
    $this->formatted_search_criteria[$this->oge_label] = $this->oge_name;

    //Year
    if ($this->form_state->getValue('nycha_fiscal_year')) {
      $this->_process_user_criteria_by_datasource_single_field('nycha_fiscal_year', 'nycha_fiscal_year', 'Year');
    }
    //Expense Category
    if ($this->form_state->getValue('nycha_expense_category') && $this->form_state->getValue('nycha_expense_category') != 'Select Expense Category' && $this->form_state->getValue('nycha_expense_category') != '0') {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_expense_category', 'nycha_expense_category', 'Expense Category');
    }

    //Responsibility Center
    if ($this->form_state->getValue('nycha_resp_center') && $this->form_state->getValue('nycha_resp_center') != 'Select Responsibility Center' && $this->form_state->getValue('nycha_resp_center') != '0') {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_resp_center', 'nycha_resp_center', 'Responsibility Center');
    }

    //Funding Source
    if ($this->form_state->getValue('nycha_funding_source') && $this->form_state->getValue('nycha_funding_source') != 'Select Funding Source' && $this->form_state->getValue('nycha_funding_source') != '0') {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_funding_source', 'nycha_funding_source', 'Funding Source');
    }

    //Program
    if ($this->form_state->getValue('nycha_program') && $this->form_state->getValue('nycha_program') != 'Select Program' && $this->form_state->getValue('nycha_program') != '0') {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_program', 'nycha_program', 'Program');
    }

    //Project
    if ($this->form_state->getValue('nycha_project') && $this->form_state->getValue('nycha_project') != 'Select Project' && $this->form_state->getValue('nycha_project') != '0') {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_project', 'nycha_project', 'Project');
    }

    //Budget Type
    if ($this->form_state->getValue('nycha_budget_type') && $this->form_state->getValue('nycha_budget_type') != 'Select Budget Type' && $this->form_state->getValue('nycha_budget_type') != '0') {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_budget_type', 'nycha_budget_type', 'Budget Type');
    }

    //Budget Name
    if ($this->form_state->getValue('nycha_budget_name') && $this->form_state->getValue('nycha_budget_name') != 'Select Budget Name' && $this->form_state->getValue('nycha_budget_name') != '0') {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_budget_name', 'nycha_budget_name', 'Budget Name');
    }

    //Adopted
    $this->_process_ranged_amounts_user_criteria('nycha_adopted_from', 'nycha_adopted_to', 'adopted_budget', 'Adopted Budget');

    //Modified
    $this->_process_ranged_amounts_user_criteria('nycha_modified_from', 'nycha_modified_to', 'modified_budget', 'Modified Budget');

    //Committed
    $this->_process_ranged_amounts_user_criteria('nycha_committed_from', 'nycha_committed_to', 'committed_budget', 'Committed Budget');

    //Encumbered
    $this->_process_ranged_amounts_user_criteria('nycha_encumbered_from', 'nycha_encumbered_to', 'encumbered_budget', 'Encumbered Budget');

    //Actual
    $this->_process_ranged_amounts_user_criteria('nycha_actual_from', 'nycha_actual_to', 'actual_budget', 'Actual Amount');
  }

  protected function _process_datasource_values(){
    //Year
    if ($this->form_state->getValue('nycha_fiscal_year') != 'All Years') {
      $this->criteria['value']['year'] = $this->form_state->getValue('nycha_fiscal_year');
    }
    //Expense Category
    $this->_process_single_field_datasource_values('nycha_expense_category', 'expense_category');

    //Responsibility Center
    $this->_process_single_field_datasource_values('nycha_resp_center', 'responsibility_center');

    //Funding Source
    $this->_process_single_field_datasource_values('nycha_funding_source', 'funding_source');

    //Program
    $this->_process_single_field_datasource_values('nycha_program', 'program');

    //Project
    $this->_process_single_field_datasource_values('nycha_project', 'project');

    //Budget Type
    if ($this->form_state->getValue('nycha_budget_type') && $this->form_state->getValue('nycha_budget_type') != "Select Budget Type" && $this->form_state->getValue('nycha_budget_type') != "") {
       $this->criteria['value']['budget_type'] = $this->form_state->getValue('nycha_budget_type');
    }
    //Budget Name
    if ($this->form_state->getValue('nycha_budget_name') && $this->form_state->getValue('nycha_budget_name') != "Select Budget Name" && $this->form_state->getValue('nycha_budget_name') != "") {
      $this->criteria['value']['budget_name'] = $this->form_state->getValue('nycha_budget_name');
    }
    //Adopted Budget
    $this->_process_ranged_datasource_values('nycha_adopted_from', 'nycha_adopted_to', 'adopted');

    //Modified Amount
    $this->_process_ranged_datasource_values('nycha_modified_from', 'nycha_modified_to', 'modified');

    //Committed Amount
    $this->_process_ranged_datasource_values('nycha_committed_from', 'nycha_committed_to', 'committed');

    //Encumbered Amount
    $this->_process_ranged_datasource_values('nycha_encumbered_from', 'nycha_encumbered_to', 'encumbered');

    //Actual Amount
    $this->_process_ranged_datasource_values('nycha_actual_from', 'nycha_actual_to', 'actual_amount');
  }

  protected function _validate_by_datasource(&$form, &$form_state){

    //adopted Amount
    checkbook_datafeeds_check_ranged_amounts($form_state, 'nycha_adopted_from', 'nycha_adopted_to', 'Adopted Budget');

    //Modified Amount
    checkbook_datafeeds_check_ranged_amounts($form_state, 'nycha_modified_from', 'nycha_modified_to', 'Modified Budget');

    //Committed Amount
    checkbook_datafeeds_check_ranged_amounts($form_state, 'nycha_committed_from', 'nycha_committed_to', 'Committed Budget');

    //Encumbered Amount
    checkbook_datafeeds_check_ranged_amounts($form_state, 'nycha_encumbered_from', 'nycha_encumbered_to', 'Encumbered Budget');

    //Actual Amount
    checkbook_datafeeds_check_ranged_amounts($form_state, 'nycha_actual_from', 'nycha_actual_to', 'Actual Amount');

    $nycha_col_selected = array_filter($form_state->getValue('nycha_column_select'));

    if (empty($nycha_col_selected)) {
      $form_state->setErrorByName('nycha_column_select', t('You must select at least one column.'));
    }

    //Set the hidden field values for Budget Name and Budget Type
    $form_state->setValue(['complete form', 'nycha_budget_type_hidden', '#value'], $form_state->getValue(['values', 'nycha_budget_type']));
    $form_state->setValue(['complete form', 'nycha_budget_name_hidden', '#value'], $form_state->getValue(['values', 'nycha_budget_name']));
  }
}
