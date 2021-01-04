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


class RevenueFeedNycha extends RevenueFeed
{
  protected $data_source = \Datasource::NYCHA;
  protected $type_of_data = 'Revenue_NYCHA';
  protected $filtered_columns_container = 'nycha_column_select';
  protected $oge_label = 'Other Government Entity';
  protected $oge_name_code = "NEW YORK CITY HOUSING AUTHORITY[996]";
  protected $oge_name = "NEW YORK CITY HOUSING AUTHORITY";

  protected function _process_user_criteria_by_datasource(){
    //OGE Display
    $this->form['filter']['agency'] = array('#markup' => '<div><strong>' . $this->oge_label .':</strong> ' . $this->oge_name_code . '</div>',);
    $this->formatted_search_criteria[$this->oge_label] = $this->oge_name;

    //Budget Fiscal Year
    if ($this->values['nycha_budget_year']) {
      $this->form['filter']['nycha_budget_year'] = array('#markup' => '<div><strong>Budget Fiscal Year:</strong> ' . $this->values['nycha_budget_year'] . '</div>');
      $this->user_criteria['nycha_budget_year'] = $this->values['nycha_budget_year'];
      $this->formatted_search_criteria['Budget Fiscal Year'] = $this->values['nycha_budget_year'];
    }
    //Expense Category
    if ($this->values['nycha_expense_category'] && $this->values['nycha_expense_category'] != 'Select Revenue Expense Category' && $this->values['nycha_expense_category'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['nycha_expense_category'])) {
        $this->values['nycha_expense_category'] = htmlspecialchars($this->values['nycha_expense_category']);
      }
      $this->form['filter']['nycha_expense_category'] = array('#markup' => '<div><strong>Revenue Expense Category:</strong> ' . $this->values['nycha_expense_category'] . '</div>');
      $this->user_criteria['Expense Category'] = $this->values['nycha_expense_category'];
      $this->formatted_search_criteria['Expense Category'] = $this->values['nycha_expense_category'];
    }

    //Responsibility Center
    if ($this->values['nycha_resp_center'] && $this->values['nycha_resp_center'] != 'Select Responsibility Center' && $this->values['nycha_resp_center'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['nycha_resp_center'])) {
        $this->values['nycha_resp_center'] = htmlspecialchars($this->values['nycha_resp_center']);
      }
      $this->form['filter']['nycha_resp_center'] = array('#markup' => '<div><strong>Responsibility Center:</strong> ' . $this->values['nycha_resp_center'] . '</div>');
      $this->user_criteria['Responsibility Center'] = $this->values['nycha_resp_center'];
      $this->formatted_search_criteria['Responsibility Center'] = $this->values['nycha_resp_center'];
    }

    //Funding Source
    if ($this->values['nycha_funding_source'] && $this->values['nycha_funding_source'] != 'Select Funding Source' && $this->values['nycha_funding_source'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['nycha_funding_source'])) {
        $this->values['nycha_funding_source'] = htmlspecialchars($this->values['nycha_funding_source']);
      }
      $this->form['filter']['nycha_funding_source'] = array('#markup' => '<div><strong>Funding Source:</strong> ' . $this->values['nycha_funding_source'] . '</div>');
      $this->user_criteria['Funding Source'] = $this->values['nycha_funding_source'];
      $this->formatted_search_criteria['Funding Source'] = $this->values['nycha_funding_source'];
    }

    //Program
    if ($this->values['nycha_program'] && $this->values['nycha_program'] != 'Select Program' && $this->values['nycha_program'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['nycha_program'])) {
        $this->values['nycha_program'] = htmlspecialchars($this->values['nycha_program']);
      }
      $this->form['filter']['nycha_program'] = array('#markup' => '<div><strong>Program:</strong> ' . $this->values['nycha_program'] . '</div>');
      $this->user_criteria['Program'] = $this->values['nycha_program'];
      $this->formatted_search_criteria['Program'] = $this->values['nycha_program'];
    }

    //Project
    if ($this->values['nycha_project'] && $this->values['nycha_project'] != 'Select Project' && $this->values['nycha_project'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['nycha_project'])) {
        $this->values['nycha_project'] = htmlspecialchars($this->values['nycha_project']);
      }
      $this->form['filter']['nycha_project'] = array('#markup' => '<div><strong>Project:</strong> ' . $this->values['nycha_project'] . '</div>');
      $this->user_criteria['Project'] = $this->values['nycha_project'];
      $this->formatted_search_criteria['Project'] = $this->values['nycha_project'];
    }

    //Budget Type
    if ($this->values['nycha_budget_type'] && $this->values['nycha_budget_type'] != 'Select Budget Type' && $this->values['nycha_budget_type'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['nycha_budget_type'])) {
        $this->values['nycha_budget_type'] = htmlspecialchars($this->values['nycha_budget_type']);
      }
      $this->form['filter']['nycha_budget_type'] = array('#markup' => '<div><strong>Budget Type:</strong> ' . $this->values['nycha_budget_type'] . '</div>');
      $this->user_criteria['Budget Type'] = $this->values['nycha_budget_type'];
      $this->formatted_search_criteria['Budget Type'] = $this->values['nycha_budget_type'];
    }

    //Budget Name
    if ($this->values['nycha_budget_name'] && $this->values['nycha_budget_name'] != 'Select Budget Name' && $this->values['nycha_budget_name'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['nycha_budget_name'])) {
        $this->values['nycha_budget_name'] = htmlspecialchars($this->values['nycha_budget_name']);
      }
      $this->form['filter']['nycha_budget_name'] = array('#markup' => '<div><strong>Budget Name:</strong> ' . $this->values['nycha_budget_name'] . '</div>');
      $this->user_criteria['Budget Name'] = $this->values['nycha_budget_name'];
      $this->formatted_search_criteria['Budget Name'] = $this->values['nycha_budget_name'];
    }

    //Adopted
    if (($this->values['nycha_adopted_from'] || $this->values['nycha_adopted_from'] === "0") && ($this->values['nycha_adopted_to'] || $this->values['nycha_adopted_to'] === "0")) {
      $this->form['filter']['adopted_revenue'] = array('#markup' => '<div><strong>Adopted:</strong> Greater Than Equal to: $' . $this->values['nycha_adopted_from'] . ' and Less Than Equal to: $' . $this->values['nycha_adopted_to'] . '</div>');
      $this->user_criteria['Adopted Greater Than'] = $this->values['nycha_adopted_from'];
      $this->user_criteria['Adopted Less Than'] = $this->values['nycha_adopted_to'];
      $this->formatted_search_criteria['Adopted'] = 'Greater Than Equal to: $' . $this->values['nycha_adopted_from'] . ' and Less Than Equal to: $' . $this->values['nycha_adopted_to'];
    } elseif (!$this->values['nycha_adopted_from'] && ($this->values['nycha_adopted_to'] || $this->values['nycha_adopted_to'] === "0")) {
      $this->form['filter']['adopted_revenue'] = array('#markup' => '<div><strong>Adopted:</strong> Less Than Equal to: $' . $this->values['nycha_adopted_to'] . '</div>');
      $this->user_criteria['Adopted Less Than'] = $this->values['nycha_adopted_to'];
      $this->formatted_search_criteria['Adopted'] = 'Less Than Equal to: $' . $this->values['nycha_adopted_to'];
    } elseif (($this->values['nycha_adopted_from'] || $this->values['nycha_adopted_from'] === "0") && !$this->values['nycha_adopted_to']) {
      $this->form['filter']['adopted_revenue'] = array('#markup' => '<div><strong>Adopted:</strong> Greater Than Equal to: $' . $this->values['nycha_adopted_from'] . '</div>');
      $this->user_criteria['Adopted Greater Than'] = $this->values['nycha_adopted_from'];
      $this->formatted_search_criteria['Adopted'] = 'Greater Than Equal to: $' . $this->values['nycha_adopted_from'];
    }

    //Modified
    if (($this->values['nycha_modified_from'] || $this->values['nycha_modified_from'] === "0") && ($this->values['nycha_modified_to'] || $this->values['nycha_modified_to'] === "0")) {
      $this->form['filter']['modified_revenue'] = array('#markup' => '<div><strong>Modified:</strong> Greater Than Equal to: $' . $this->values['nycha_modified_from'] . ' and Less Than Equal to: $' . $this->values['nycha_modified_to'] . '</div>');
      $this->user_criteria['Modified Greater Than'] = $this->values['nycha_modified_from'];
      $this->user_criteria['Modified Less Than'] = $this->values['nycha_modified_to'];
      $this->formatted_search_criteria['Modified'] = 'Greater Than Equal to: $' . $this->values['nycha_modified_from'] . ' and Less Than Equal to: $' . $this->values['nycha_modified_to'];
    } elseif (!$this->values['nycha_modified_from'] && ($this->values['nycha_modified_to'] || $this->values['nycha_modified_to'] === "0")) {
      $this->form['filter']['modified_revenue'] = array('#markup' => '<div><strong>Modified:</strong> Less Than Equal to: $' . $this->values['nycha_modified_to'] . '</div>');
      $this->user_criteria['Modified Less Than'] = $this->values['nycha_modified_to'];
      $this->formatted_search_criteria['Modified'] = 'Less Than Equal to: $' . $this->values['nycha_modified_to'];
    } elseif (($this->values['nycha_modified_from'] || $this->values['nycha_modified_from'] === "0") && !$this->values['nycha_modified_to']) {
      $this->form['filter']['modified_revenue'] = array('#markup' => '<div><strong>Modified:</strong> Greater Than Equal to: $' . $this->values['nycha_modified_from'] . '</div>');
      $this->user_criteria['Modified Greater Than'] = $this->values['nycha_modified_from'];
      $this->formatted_search_criteria['Modified'] = 'Greater Than Equal to: $' . $this->values['nycha_modified_from'];
    }

    //Recognized
    if (($this->values['nycha_recognized_from'] || $this->values['nycha_recognized_from'] === "0") && ($this->values['nycha_recognized_to'] || $this->values['nycha_recognized_to'] === "0")) {
      $this->form['filter']['recognized_revenue'] = array('#markup' => '<div><strong>Recognized:</strong> Greater Than Equal to: $' . $this->values['nycha_recognized_from'] . ' and Less Than Equal to: $' . $this->values['nycha_recognized_to'] . '</div>');
      $this->user_criteria['Recognized Greater Than'] = $this->values['nycha_recognized_from'];
      $this->user_criteria['Recognized Less Than'] = $this->values['nycha_recognized_to'];
      $this->formatted_search_criteria['Recognized'] = 'Greater Than Equal to: $' . $this->values['nycha_recognized_from'] . ' and Less Than Equal to: $' . $this->values['nycha_recognized_to'];
    } elseif (!$this->values['nycha_recognized_from'] && ($this->values['nycha_recognized_to'] || $this->values['nycha_recognized_to'] === "0")) {
      $this->form['filter']['recognized_revenue'] = array('#markup' => '<div><strong>Recognized:</strong> Less Than Equal to: $' . $this->values['nycha_recognized_to'] . '</div>');
      $this->user_criteria['Recognized Less Than'] = $this->values['nycha_recognized_to'];
      $this->formatted_search_criteria['Recognized'] = 'Less Than Equal to: $' . $this->values['nycha_recognized_to'];
    } elseif (($this->values['nycha_recognized_from'] || $this->values['nycha_recognized_from'] === "0") && !$this->values['nycha_recognized_to']) {
      $this->form['filter']['recognized_revenue'] = array('#markup' => '<div><strong>Recognized:</strong> Greater Than Equal to: $' . $this->values['nycha_recognized_from'] . '</div>');
      $this->user_criteria['Recognized Greater Than'] = $this->values['nycha_recognized_from'];
      $this->formatted_search_criteria['Recognized'] = 'Greater Than Equal to: $' . $this->values['nycha_recognized_from'];
    }

    //Remaining
    if (($this->values['nycha_remaining_from'] || $this->values['nycha_remaining_from'] === "0") && ($this->values['nycha_remaining_to'] || $this->values['nycha_remaining_to'] === "0")) {
      $this->form['filter']['remaining_revenue'] = array('#markup' => '<div><strong>Remaining:</strong> Greater Than Equal to: $' . $this->values['nycha_remaining_from'] . ' and Less Than Equal to: $' . $this->values['nycha_remaining_to'] . '</div>');
      $this->user_criteria['Remaining Greater Than'] = $this->values['nycha_remaining_from'];
      $this->user_criteria['Remaining Less Than'] = $this->values['nycha_remaining_to'];
      $this->formatted_search_criteria['Remaining'] = 'Greater Than Equal to: $' . $this->values['nycha_remaining_from'] . ' and Less Than Equal to: $' . $this->values['nycha_remaining_to'];
    } elseif (!$this->values['nycha_remaining_from'] && ($this->values['nycha_remaining_to'] || $this->values['nycha_remaining_to'] === "0")) {
      $this->form['filter']['remaining_revenue'] = array('#markup' => '<div><strong>Remaining:</strong> Less Than Equal to: $' . $this->values['nycha_remaining_to'] . '</div>');
      $this->user_criteria['Remaining Less Than'] = $this->values['nycha_remaining_to'];
      $this->formatted_search_criteria['Remaining'] = 'Less Than Equal to: $' . $this->values['nycha_remaining_to'];
    } elseif (($this->values['nycha_remaining_from'] || $this->values['nycha_remaining_from'] === "0") && !$this->values['nycha_remaining_to']) {
      $this->form['filter']['remaining_revenue'] = array('#markup' => '<div><strong>Remaining:</strong> Greater Than Equal to: $' . $this->values['nycha_remaining_from'] . '</div>');
      $this->user_criteria['Remaining Greater Than'] = $this->values['nycha_remaining_from'];
      $this->formatted_search_criteria['Remaining'] = 'Greater Than Equal to: $' . $this->values['nycha_remaining_from'];
    }

    //Revenue Category
    if ($this->values['nycha_rev_cat']) {
      $this->form['filter']['nycha_rev_cat'] = array(
        '#markup' => '<div><strong>Revenue Category:</strong> ' . $this->values['nycha_rev_cat'] . '</div>',
      );
      $this->user_criteria['Revenue Category'] = $this->values['nycha_rev_cat'];
      $this->formatted_search_criteria['Revenue Category'] = $this->values['nycha_rev_cat'];
    }
    
    //Revenue Class
    if ($this->values['nycha_rev_class']) {
      $this->form['filter']['nycha_rev_class'] = array(
        '#markup' => '<div><strong>Revenue Class:</strong> ' . $this->values['nycha_rev_class'] . '</div>',
      );
      $this->user_criteria['Revenue Class'] = $this->values['nycha_rev_class'];
      $this->formatted_search_criteria['Revenue Class'] = $this->values['nycha_rev_class'];
    }
  }

  protected function _process_datasource_values(){
    //Budget Fiscal Year
    if ($this->values['nycha_budget_year'] != 'All Years') {
      $this->criteria['value']['budget_fiscal_year'] = $this->values['nycha_budget_year'];
    }
    //Expense Category
    if ($this->values['nycha_expense_category']) {
      preg_match($this->bracket_value_pattern, $this->values['nycha_expense_category'], $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['revenue_expense_category'] = trim($ecmatches[1], '[ ]');
      }
    }
    //Responsibility Center
    if ($this->values['nycha_resp_center']) {
      preg_match($this->bracket_value_pattern, $this->values['nycha_resp_center'], $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['responsibility_center'] = trim($ecmatches[1], '[ ]');
      }
    }
    //Funding Source
    if ($this->values['nycha_funding_source']) {
      preg_match($this->bracket_value_pattern, $this->values['nycha_funding_source'], $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['funding_source'] = trim($ecmatches[1], '[ ]');
      }
    }
    //Program
    if ($this->values['nycha_program']) {
      preg_match($this->bracket_value_pattern, $this->values['nycha_program'], $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['program'] = trim($ecmatches[1], '[ ]');
      }
    }
    //Project
    if ($this->values['nycha_project'])  {
      preg_match($this->bracket_value_pattern, $this->values['nycha_project'], $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['project'] = trim($ecmatches[1], '[ ]');
      }
    }
    //Budget Type
    if ($this->values['nycha_budget_type'] && $this->values['nycha_budget_type'] != "Select Budget Type" && $this->values['nycha_budget_type'] != "") {
       $this->criteria['value']['budget_type'] = $this->values['nycha_budget_type'];
    }
    //Budget Name
    if ($this->values['nycha_budget_name'] && $this->values['nycha_budget_name'] != "Select Budget Name" && $this->values['nycha_budget_name'] != "") {
      $this->criteria['value']['budget_name'] = $this->values['nycha_budget_name'];
    }
    //Revenue Category
    if ($this->values['nycha_rev_cat']) {
      $this->criteria['value']['revenue_category'] = $this->values['nycha_rev_cat'];
    }
    //Revenue Class
    if ($this->values['nycha_rev_class']) {
      $this->criteria['value']['revenue_class'] = $this->values['nycha_rev_class'];
    }
    //Adopted Revenue
    if ($this->values['nycha_adopted_from'] !== '' || $this->values['nycha_adopted_to'] !== '') {
      $this->criteria['range']['adopted'] = array(
        checknull($this->values['nycha_adopted_from']),
        checknull($this->values['nycha_adopted_to']),
      );
    }
    //Modified Amount
    if ($this->values['nycha_modified_from'] !== '' || $this->values['nycha_modified_to'] !== '') {
      $this->criteria['range']['modified'] = array(
        checknull($this->values['nycha_modified_from']),
        checknull($this->values['nycha_modified_to']),
      );
    }
    
    //Recognized Amount
    if ($this->values['nycha_recognized_from'] !== '' || $this->values['nycha_recognized_to'] !== '') {
      $this->criteria['range']['recognized'] = array(
        checknull($this->values['nycha_recognized_from']),
        checknull($this->values['nycha_recognized_to']),
      );
    }

    //Remaining Amount
    if ($this->values['nycha_remaining_from'] !== '' || $this->values['nycha_remaining_to'] !== '') {
      $this->criteria['range']['remaining'] = array(
        checknull($this->values['nycha_remaining_from']),
        checknull($this->values['nycha_remaining_to']),
      );
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state){

    //adopted Amount
    $adoptedFrom = $form_state['values']['nycha_adopted_from'];
    $adoptedTo = $form_state['values']['nycha_adopted_to'];
    if ($adoptedFrom && !is_numeric($adoptedFrom)) {
      form_set_error('nycha_adopted_from', t('Adopted From value must be a number.'));
    }
    if ($adoptedTo && !is_numeric($adoptedTo)) {
      form_set_error('nycha_adopted_to', t('Adopted To value must be a number.'));
    }
    if (is_numeric($adoptedFrom) && is_numeric($adoptedTo) && $adoptedTo < $adoptedFrom) {
      form_set_error('nycha_adopted_to', t('Invalid range for Adopted.'));
    }

    //Modified Amount
    $modifiedFrom = $form_state['values']['nycha_modified_from'];
    $modifiedTo = $form_state['values']['nycha_modified_to'];
    if ($modifiedFrom && !is_numeric($modifiedFrom)) {
      form_set_error('nycha_modified_from', t('Modified From value must be a number.'));
    }
    if ($modifiedTo && !is_numeric($modifiedTo)) {
      form_set_error('nycha_modified_to', t('Modified To value must be a number.'));
    }
    if (is_numeric($modifiedFrom) && is_numeric($modifiedTo) && $modifiedTo < $modifiedFrom) {
      form_set_error('nycha_modified_to', t('Invalid range for Modified.'));
    }

    //Recognized
    $recognizedFrom = $form_state['values']['nycha_recognized_from'];
    $recognizedTo = $form_state['values']['nycha_recognized_to'];
    if ($recognizedFrom && !is_numeric($recognizedFrom)) {
      form_set_error('nycha_recognized_from', t('Recognized From value must be a number.'));
    }
    if ($recognizedTo && !is_numeric($recognizedTo)) {
      form_set_error('nycha_recognized_to', t('Recognized To value must be a number.'));
    }
    if (is_numeric($recognizedFrom) && is_numeric($recognizedTo) && $recognizedTo < $recognizedFrom) {
      form_set_error('nycha_recognized_to', t('Invalid range for Recognized.'));
    }

    //Remaining
    $remainingFrom = $form_state['values']['nycha_remaining_from'];
    $remainingTo = $form_state['values']['nycha_remaining_to'];
    if ($remainingFrom && !is_numeric($remainingFrom)) {
      form_set_error('nycha_remaining_from', t('Remaining From value must be a number.'));
    }
    if ($remainingTo && !is_numeric($remainingTo)) {
      form_set_error('nycha_remaining_to', t('Remaining To value must be a number.'));
    }
    if (is_numeric($remainingFrom) && is_numeric($remainingTo) && $remainingTo < $remainingFrom) {
      form_set_error('nycha_remaining_to', t('Invalid range for Remaining.'));
    }

    //Column Select
    $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
    if (!$multi_select_hidden) {
      form_set_error('nycha_column_select', t('You must select at least one column.'));
    }

    //Set the hidden filed values for Budget Name and Budget Type
    $form_state['complete form']['nycha_budget_type_hidden']['#value'] = $form_state['values']['nycha_budget_type'];
    $form_state['complete form']['nycha_budget_name_hidden']['#value'] = $form_state['values']['nycha_budget_name'];
  }
}
