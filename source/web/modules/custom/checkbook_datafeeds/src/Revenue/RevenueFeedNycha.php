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

namespace Drupal\checkbook_datafeeds\Revenue;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;

class RevenueFeedNycha extends RevenueFeed
{
  protected $data_source = Datasource::NYCHA;
  protected string $type_of_data = 'Revenue_NYCHA';
  protected $filtered_columns_container = 'nycha_column_select';
  protected $oge_label = 'Other Government Entity';
  protected $oge_name_code = "NEW YORK CITY HOUSING AUTHORITY[996]";
  protected $oge_name = "NEW YORK CITY HOUSING AUTHORITY";

  protected function _process_user_criteria_by_datasource(){
    //OGE Display
    $this->form['filter']['agency'] = array('#markup' => '<div><strong>' . $this->oge_label .':</strong> ' . $this->oge_name_code . '</div>',);
    $this->formatted_search_criteria[$this->oge_label] = $this->oge_name;

    //Budget Fiscal Year
    $this->_process_user_criteria_by_datasource_single_field_and_check('nycha_budget_year', 'nycha_budget_year', 'Budget Fiscal Year', 'nycha_budget_year');

    //Expense Category
    if ($this->form_state->getValue('nycha_expense_category') && !in_array($this->form_state->getValue('nycha_expense_category'), ['Select Revenue Expense Category', '0'])) {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_expense_category', 'nycha_expense_category', 'Expense Category');
    }

    //Responsibility Center
    if ($this->form_state->getValue('nycha_resp_center') && !in_array($this->form_state->getValue('nycha_resp_center'), ['Select Responsibility Center', '0'])) {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_resp_center', 'nycha_resp_center', 'Responsibility Center');
    }

    //Funding Source
    if ($this->form_state->getValue('nycha_funding_source') && !in_array($this->form_state->getValue('nycha_funding_source'), ['Select Funding Source', '0'])) {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_funding_source', 'nycha_funding_source', 'Funding Source');
    }

    //Program
    if ($this->form_state->getValue('nycha_program') && !in_array($this->form_state->getValue('nycha_program'), ['Select Program', '0'])) {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_program', 'nycha_program', 'Program');
    }

    //Project
    if ($this->form_state->getValue('nycha_project') && !in_array($this->form_state->getValue('nycha_project'), ['Select Project', '0'])) {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_project', 'nycha_project', 'Project');
    }

    //Budget Type
    if ($this->form_state->getValue('nycha_budget_type') && !in_array($this->form_state->getValue('nycha_budget_type'), ['Select Budget Type', '0'])) {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_budget_type', 'nycha_budget_type', 'Budget Type');
    }

    //Budget Name
    if ($this->form_state->getValue('nycha_budget_name') && !in_array($this->form_state->getValue('nycha_budget_name'), ['Select Budget Name', '0'])) {
      $this->_process_user_criteria_by_datasource_single_field_convert_special_chars('nycha_budget_name', 'nycha_budget_name', 'Budget Name');
    }

    //Adopted
    $this->_process_user_criteria_by_datasource_ranged_amount_field('nycha_adopted_from', 'nycha_adopted_to', 'adopted_revenue', 'Adopted');

    //Modified
    $this->_process_user_criteria_by_datasource_ranged_amount_field('nycha_modified_from', 'nycha_modified_to', 'modified_revenue', 'Modified');

    //Recognized
    $this->_process_user_criteria_by_datasource_ranged_amount_field('nycha_recognized_from', 'nycha_recognized_to', 'recognized_revenue', 'Recognized');

    //Remaining
    $this->_process_user_criteria_by_datasource_ranged_amount_field('nycha_remaining_from', 'nycha_remaining_to', 'remaining_revenue', 'Remaining');

    //Revenue Category
    $this->_process_user_criteria_by_datasource_single_field_and_check('nycha_rev_cat', 'nycha_rev_cat', 'Revenue Category');

    //Revenue Class
    $this->_process_user_criteria_by_datasource_single_field_and_check('nycha_rev_class', 'nycha_rev_class', 'Revenue Class');
  }

  protected function _process_datasource_values(){
    //Budget Fiscal Year
    if ($this->form_state->getValue('nycha_budget_year') != 'All Years') {
      $this->criteria['value']['budget_fiscal_year'] = $this->form_state->getValue('nycha_budget_year');
    }

    $this->_process_datasource_values_pattern_search();

    //Budget Type
    if ($this->form_state->getValue('nycha_budget_type') && $this->form_state->getValue('nycha_budget_type') != "Select Budget Type" && $this->form_state->getValue('nycha_budget_type') != "") {
       $this->criteria['value']['budget_type'] = $this->form_state->getValue('nycha_budget_type');
    }
    //Budget Name
    if ($this->form_state->getValue('nycha_budget_name') && $this->form_state->getValue('nycha_budget_name') != "Select Budget Name" && $this->form_state->getValue('nycha_budget_name') != "") {
      $this->criteria['value']['budget_name'] = $this->form_state->getValue('nycha_budget_name');
    }
    //Revenue Category
    if ($this->form_state->getValue('nycha_rev_cat')) {
      $this->criteria['value']['revenue_category'] = $this->form_state->getValue('nycha_rev_cat');
    }
    //Revenue Class
    if ($this->form_state->getValue('nycha_rev_class')) {
      $this->criteria['value']['revenue_class'] = $this->form_state->getValue('nycha_rev_class');
    }
    //Adopted Revenue
    $this->_process_ranged_datasource_values('nycha_adopted_from', 'nycha_adopted_to', 'adopted');

    //Modified Amount
    $this->_process_ranged_datasource_values('nycha_modified_from', 'nycha_modified_to', 'modified');

    //Recognized Amount
    $this->_process_ranged_datasource_values('nycha_recognized_from', 'nycha_recognized_to', 'recognized');

    //Remaining Amount
    $this->_process_ranged_datasource_values('nycha_remaining_from', 'nycha_remaining_to', 'remaining');
  }

  protected function _process_datasource_values_pattern_search() {
    //Expense Category
    if ($this->form_state->getValue('nycha_expense_category')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('nycha_expense_category'), $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['revenue_expense_category'] = trim($ecmatches[1], '[ ]');
      }
    }
    //Responsibility Center
    if ($this->form_state->getValue('nycha_resp_center')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('nycha_resp_center'), $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['responsibility_center'] = trim($ecmatches[1], '[ ]');
      }
    }
    //Funding Source
    if ($this->form_state->getValue('nycha_funding_source')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('nycha_funding_source'), $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['funding_source'] = trim($ecmatches[1], '[ ]');
      }
    }
    //Program
    if ($this->form_state->getValue('nycha_program')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('nycha_program'), $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['program'] = trim($ecmatches[1], '[ ]');
      }
    }
    //Project
    if ($this->form_state->getValue('nycha_project'))  {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('nycha_project'), $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['project'] = trim($ecmatches[1], '[ ]');
      }
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state){
    //adopted Amount
    checkbook_datafeeds_check_ranged_amounts($form_state, 'nycha_adopted_from', 'nycha_adopted_to', 'Adopted', 'Adopted From', 'Adopted To');

    //Modified Amount
    checkbook_datafeeds_check_ranged_amounts($form_state, 'nycha_modified_from', 'nycha_modified_to', 'Modified', 'Modified From', 'Modified To');

    //Recognized
    checkbook_datafeeds_check_ranged_amounts($form_state, 'nycha_recognized_from', 'nycha_recognized_to', 'Recognized', 'Recognized From', 'Recognized To');

    //Remaining
    checkbook_datafeeds_check_ranged_amounts($form_state, 'nycha_remaining_from', 'nycha_remaining_to', 'Remaining', 'Remaining From', 'Remaining To');

    //Column Select
    $nycha_column_select = $form_state->getValue('nycha_column_select');
    if (empty(array_filter($nycha_column_select))) {
      $form_state->setErrorByName('nycha_column_select', t('You must select at least one column.'));
    }

    //Set the hidden filed values for Budget Name and Budget Type
    $form_state->setValue(['complete form', 'nycha_budget_type_hidden', '#value'], $form_state->getValue(['values', 'nycha_budget_type']));
    $form_state->setValue(['complete form', 'nycha_budget_name_hidden', '#value'], $form_state->getValue(['values', 'nycha_budget_name']));

  }
}
