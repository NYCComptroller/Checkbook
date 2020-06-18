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

    //Year
    if ($this->values['nycha_fiscal_year']) {
      $this->form['filter']['nycha_fiscal_year'] = array('#markup' => '<div><strong>Year:</strong> ' . $this->values['nycha_fiscal_year'] . '</div>');
      $this->user_criteria['Year'] = $this->values['nycha_fiscal_year'];
      $this->formatted_search_criteria['Year'] = $this->values['nycha_fiscal_year'];
    }
    //Expense Category
    if ($this->values['nycha_expense_category'] && $this->values['nycha_expense_category'] != 'Select Expense Category' && $this->values['nycha_expense_category'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['nycha_expense_category'])) {
        $this->values['nycha_expense_category'] = htmlspecialchars($this->values['nycha_expense_category']);
      }
      $this->form['filter']['nycha_expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $this->values['nycha_expense_category'] . '</div>');
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

    //Revenue Type
    if ($this->values['nycha_revenue_type'] && $this->values['nycha_revenue_type'] != 'Select Revenue Type' && $this->values['nycha_revenue_type'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['nycha_revenue_type'])) {
        $this->values['nycha_revenue_type'] = htmlspecialchars($this->values['nycha_revenue_type']);
      }
      $this->form['filter']['nycha_revenue_type'] = array('#markup' => '<div><strong>Revenue Type:</strong> ' . $this->values['nycha_revenue_type'] . '</div>');
      $this->user_criteria['Revenue Type'] = $this->values['nycha_revenue_type'];
      $this->formatted_search_criteria['Revenue Type'] = $this->values['nycha_revenue_type'];
    }

    //Revenue Name
    if ($this->values['nycha_revenue_name'] && $this->values['nycha_revenue_name'] != 'Select Revenue Name' && $this->values['nycha_revenue_name'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['nycha_revenue_name'])) {
        $this->values['nycha_revenue_name'] = htmlspecialchars($this->values['nycha_revenue_name']);
      }
      $this->form['filter']['nycha_revenue_name'] = array('#markup' => '<div><strong>Revenue Name:</strong> ' . $this->values['nycha_revenue_name'] . '</div>');
      $this->user_criteria['Revenue Name'] = $this->values['nycha_revenue_name'];
      $this->formatted_search_criteria['Revenue Name'] = $this->values['nycha_revenue_name'];
    }

    //Adopted
    if ($this->values['nycha_adopted_from'] && $this->values['nycha_adopted_to']) {
      $this->form['filter']['adopted_revenue'] = array('#markup' => '<div><strong>Adopted Revenue:</strong> Greater Than Equal to: $' . $this->values['nycha_adopted_from'] . ' Less Than Equal to: $' . $this->values['nycha_adopted_to'] . '</div>');
      $this->user_criteria['Adopted Revenue Greater Than'] = $this->values['nycha_adopted_from'];
      $this->user_criteria['Adopted Revenue Less Than'] = $this->values['nycha_adopted_to'];
      $this->formatted_search_criteria['Adopted Revenue'] = 'Greater Than Equal to: $' . $this->values['nycha_adopted_from'] . ' Less Than Equal to: $' . $this->values['nycha_adopted_to'];
    } elseif (!$this->values['nycha_adopted_from'] && $this->values['nycha_adopted_to']) {
      $this->form['filter']['adopted_revenue'] = array('#markup' => '<div><strong>Adopted Revenue:</strong> Less Than Equal to: $' . $this->values['nycha_adopted_to'] . '</div>');
      $this->user_criteria['Adopted Revenue Less Than'] = $this->values['nycha_adopted_to'];
      $this->formatted_search_criteria['Adopted Revenue'] = 'Less Than Equal to: $' . $this->values['nycha_adopted_to'];
    } elseif ($this->values['nycha_adopted_from'] && !$this->values['nycha_adopted_to']) {
      $this->form['filter']['adopted_revenue'] = array('#markup' => '<div><strong>Adopted Revenue:</strong> Greater Than Equal to: $' . $this->values['nycha_adopted_from'] . '</div>');
      $this->user_criteria['Adopted Revenue Greater Than'] = $this->values['nycha_adopted_from'];
      $this->formatted_search_criteria['Adopted Revenue'] = 'Greater Than Equal to: $' . $this->values['nycha_adopted_from'];
    }

    //Modified
    if ($this->values['nycha_modified_from'] && $this->values['nycha_modified_to']) {
      $this->form['filter']['modified_revenue'] = array('#markup' => '<div><strong>Modified Revenue:</strong> Greater Than Equal to: $' . $this->values['nycha_modified_from'] . ' Less Than Equal to: $' . $this->values['nycha_modified_to'] . '</div>');
      $this->user_criteria['Modified Revenue Greater Than'] = $this->values['nycha_modified_from'];
      $this->user_criteria['Modified Revenue Less Than'] = $this->values['nycha_modified_to'];
      $this->formatted_search_criteria['Modified Revenue'] = 'Greater Than Equal to: $' . $this->values['nycha_modified_from'] . ' Less Than Equal to: $' . $this->values['nycha_modified_to'];
    } elseif (!$this->values['nycha_modified_from'] && $this->values['nycha_modified_to']) {
      $this->form['filter']['modified_revenue'] = array('#markup' => '<div><strong>Modified Revenue:</strong> Less Than Equal to: $' . $this->values['nycha_modified_to'] . '</div>');
      $this->user_criteria['Modified Revenue Less Than'] = $this->values['nycha_modified_to'];
      $this->formatted_search_criteria['Modified Revenue'] = 'Less Than Equal to: $' . $this->values['nycha_modified_to'];
    } elseif ($this->values['nycha_modified_from'] && !$this->values['nycha_modified_to']) {
      $this->form['filter']['modified_revenue'] = array('#markup' => '<div><strong>Modified Revenue:</strong> Greater Than Equal to: $' . $this->values['nycha_modified_from'] . '</div>');
      $this->user_criteria['Modified Revenue Greater Than'] = $this->values['nycha_modified_from'];
      $this->formatted_search_criteria['Modified Revenue'] = 'Greater Than Equal to: $' . $this->values['nycha_modified_from'];
    }

    //Committed
    if ($this->values['nycha_committed_from'] && $this->values['nycha_committed_to']) {
      $this->form['filter']['committed_revenue'] = array('#markup' => '<div><strong>Committed Revenue:</strong> Greater Than Equal to: $' . $this->values['nycha_committed_from'] . ' Less Than Equal to: $' . $this->values['nycha_committed_to'] . '</div>');
      $this->user_criteria['Committed Revenue Greater Than'] = $this->values['nycha_committed_from'];
      $this->user_criteria['Committed Revenue Less Than'] = $this->values['nycha_committed_to'];
      $this->formatted_search_criteria['Committed Revenue'] = 'Greater Than Equal to: $' . $this->values['nycha_committed_from'] . ' Less Than Equal to: $' . $this->values['nycha_committed_to'];
    } elseif (!$this->values['nycha_committed_from'] && $this->values['nycha_committed_to']) {
      $this->form['filter']['committed_revenue'] = array('#markup' => '<div><strong>Committed Revenue:</strong> Less Than Equal to: $' . $this->values['nycha_committed_to'] . '</div>');
      $this->user_criteria['Committed Revenue Less Than'] = $this->values['nycha_committed_to'];
      $this->formatted_search_criteria['Committed Revenue'] = 'Less Than Equal to: $' . $this->values['nycha_committed_to'];
    } elseif ($this->values['nycha_committed_from'] && !$this->values['nycha_committed_to']) {
      $this->form['filter']['committed_revenue'] = array('#markup' => '<div><strong>Committed Revenue:</strong> Greater Than Equal to: $' . $this->values['nycha_committed_from'] . '</div>');
      $this->user_criteria['Committed Revenue Greater Than'] = $this->values['nycha_committed_from'];
      $this->formatted_search_criteria['Committed Revenue'] = 'Greater Than Equal to: $' . $this->values['nycha_committed_from'];
    }


    //Encumbered
    if ($this->values['nycha_encumbered_from'] && $this->values['nycha_encumbered_to']) {
      $this->form['filter']['encumbered_revenue'] = array('#markup' => '<div><strong>Encumbered Revenue:</strong> Greater Than Equal to: $' . $this->values['nycha_encumbered_from'] . ' Less Than Equal to: $' . $this->values['nycha_encumbered_to'] . '</div>');
      $this->user_criteria['Encumbered Revenue Greater Than'] = $this->values['nycha_encumbered_from'];
      $this->user_criteria['Encumbered Revenue Less Than'] = $this->values['nycha_encumbered_to'];
      $this->formatted_search_criteria['Encumbered Revenue'] = 'Greater Than Equal to: $' . $this->values['nycha_encumbered_from'] . ' Less Than Equal to: $' . $this->values['nycha_encumbered_to'];
    } elseif (!$this->values['nycha_encumbered_from'] && $this->values['nycha_encumbered_to']) {
      $this->form['filter']['encumbered_revenue'] = array('#markup' => '<div><strong>Encumbered Revenue:</strong> Less Than Equal to: $' . $this->values['nycha_encumbered_to'] . '</div>');
      $this->user_criteria['Encumbered Revenue Less Than'] = $this->values['nycha_encumbered_to'];
      $this->formatted_search_criteria['Encumbered Revenue'] = 'Less Than Equal to: $' . $this->values['nycha_encumbered_to'];
    } elseif ($this->values['nycha_encumbered_from'] && !$this->values['nycha_encumbered_to']) {
      $this->form['filter']['encumbered_revenue'] = array('#markup' => '<div><strong>Encumbered Revenue:</strong> Greater Than Equal to: $' . $this->values['nycha_encumbered_from'] . '</div>');
      $this->user_criteria['Encumbered Revenue Greater Than'] = $this->values['nycha_encumbered_from'];
      $this->formatted_search_criteria['Encumbered Revenue'] = 'Greater Than Equal to: $' . $this->values['nycha_encumbered_from'];
    }

    //Actual
    if ($this->values['nycha_actual_from'] && $this->values['nycha_actual_to']) {
      $this->form['filter']['actual_revenue'] = array('#markup' => '<div><strong>Actual Amount:</strong> Greater Than Equal to: $' . $this->values['nycha_actual_from'] . ' Less Than Equal to: $' . $this->values['nycha_actual_to'] . '</div>');
      $this->user_criteria['Actual Amount Greater Than'] = $this->values['nycha_actual_from'];
      $this->user_criteria['Actual Amount Less Than'] = $this->values['nycha_actual_to'];
      $this->formatted_search_criteria['Actual Amount'] = 'Greater Than Equal to: $' . $this->values['nycha_actual_from'] . ' Less Than Equal to: $' . $this->values['nycha_actual_to'];
    } elseif (!$this->values['nycha_actual_from'] && $this->values['nycha_actual_to']) {
      $this->form['filter']['actual_revenue'] = array('#markup' => '<div><strong>Actual Amount:</strong> Less Than Equal to: $' . $this->values['nycha_actual_to'] . '</div>');
      $this->user_criteria['Actual Amount Less Than Less Than'] = $this->values['nycha_actual_to'];
      $this->formatted_search_criteria['Actual Amount'] = 'Less Than Equal to: $' . $this->values['nycha_actual_to'];
    } elseif ($this->values['nycha_actual_from'] && !$this->values['nycha_actual_to']) {
      $this->form['filter']['actual_revenue'] = array('#markup' => '<div><strong>Actual Amount:</strong> Greater Than Equal to: $' . $this->values['nycha_actual_from'] . '</div>');
      $this->user_criteria['Actual Amount Greater Than Greater Than'] = $this->values['nycha_actual_from'];
      $this->formatted_search_criteria['Actual Amount'] = 'Greater Than Equal to: $' . $this->values['nycha_actual_from'];
    }
  }

  protected function _process_datasource_values(){
    //Year
    if ($this->values['nycha_fiscal_year'] != 'All Years') {
      $this->criteria['value']['year'] = $this->values['nycha_fiscal_year'];
    }
    //Expense Category
    if ($this->values['nycha_expense_category']) {
      preg_match($this->bracket_value_pattern, $this->values['nycha_expense_category'], $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['expense_category'] = trim($ecmatches[1], '[ ]');
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
    //Revenue Type
    if ($this->values['nycha_revenue_type'] && $this->values['nycha_revenue_type'] != "Select Revenue Type" && $this->values['nycha_revenue_type'] != "") {
       $this->criteria['value']['revenue_type'] = $this->values['nycha_revenue_type'];
    }
    //Revenue Name
    if ($this->values['nycha_revenue_name'] && $this->values['nycha_revenue_name'] != "Select Revenue Name" && $this->values['nycha_revenue_name'] != "") {
      $this->criteria['value']['revenue_name'] = $this->values['nycha_revenue_name'];
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
    //Committed Amount
    if ($this->values['nycha_committed_from'] !== '' || $this->values['nycha_committed_to'] !== '') {
      $this->criteria['range']['committed'] = array(
        checknull($this->values['nycha_committed_from']),
        checknull($this->values['nycha_committed_to']),
      );
    }
    //Encumbered Amount
    if ($this->values['nycha_encumbered_from'] !== '' || $this->values['nycha_encumbered_to'] !== '') {
      $this->criteria['range']['encumbered'] = array(
        checknull($this->values['nycha_encumbered_from']),
        checknull($this->values['nycha_encumbered_to']),
      );
    }
    //Actual Amount
    if ($this->values['nycha_actual_from'] !== '' || $this->values['nycha_actual_to'] !== '') {
      $this->criteria['range']['actual_amount'] = array(
        checknull($this->values['nycha_actual_from']),
        checknull($this->values['nycha_actual_to']),
      );
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state){

    //adopted Amount
    $adoptedFrom = $form_state['values']['nycha_adopted_from'];
    $adoptedTo = $form_state['values']['nycha_adopted_to'];
    if ($adoptedFrom && !is_numeric($adoptedFrom)) {
      form_set_error('nycha_adopted_from', t('Adopted Revenue From value must be a number.'));
    }
    if ($adoptedTo && !is_numeric($adoptedTo)) {
      form_set_error('nycha_adopted_to', t('Adopted Revenue To value must be a number.'));
    }
    if (is_numeric($adoptedFrom) && is_numeric($adoptedTo) && $adoptedTo < $adoptedFrom) {
      form_set_error('nycha_adopted_to', t('Invalid range for Adopted Revenue.'));
    }

    //Modified Amount
    $modifiedFrom = $form_state['values']['nycha_modified_from'];
    $modifiedTo = $form_state['values']['nycha_modified_to'];
    if ($modifiedFrom && !is_numeric($modifiedFrom)) {
      form_set_error('nycha_modified_from', t('Modified Revenue From value must be a number.'));
    }
    if ($modifiedTo && !is_numeric($modifiedTo)) {
      form_set_error('nycha_modified_to', t('Modified Revenue To value must be a number.'));
    }
    if (is_numeric($modifiedFrom) && is_numeric($modifiedTo) && $modifiedTo < $modifiedFrom) {
      form_set_error('nycha_modified_to', t('Invalid range for Modified Revenue.'));
    }

    //Committed Amount
    $committedFrom = $form_state['values']['nycha_committed_from'];
    $committedTo = $form_state['values']['nycha_committed_to'];
    if ($committedFrom && !is_numeric($committedFrom)) {
      form_set_error('nycha_committed_from', t('Committed Revenue From value must be a number.'));
    }
    if ($committedTo && !is_numeric($committedTo)) {
      form_set_error('nycha_committed_to', t('Committed Revenue To value must be a number.'));
    }
    if (is_numeric($committedFrom) && is_numeric($committedTo) && $committedTo < $committedFrom) {
      form_set_error('nycha_committed_to', t('Invalid range for Committed Revenue.'));
    }

    //Encumbered Amount
    $encumberedFrom = $form_state['values']['nycha_encumbered_from'];
    $encumberedTo = $form_state['values']['nycha_encumbered_to'];
    if ($encumberedFrom && !is_numeric($encumberedFrom)) {
      form_set_error('nycha_encumbered_from', t('Encumbered Revenue From value must be a number.'));
    }
    if ($encumberedTo && !is_numeric($encumberedTo)) {
      form_set_error('nycha_encumbered_to', t('Encumbered Revenue To value must be a number.'));
    }
    if (is_numeric($encumberedFrom) && is_numeric($encumberedTo) && $encumberedTo < $encumberedFrom) {
      form_set_error('nycha_encumbered_to', t('Invalid range for Encumbered Revenue.'));
    }

    //Actual Amount
    $actualFrom = $form_state['values']['nycha_actual_from'];
    $actualTo = $form_state['values']['nycha_actual_to'];
    if ($actualFrom && !is_numeric($actualFrom)) {
      form_set_error('nycha_actual_from', t('Actual Amount From value must be a number.'));
    }
    if ($actualTo && !is_numeric($actualTo)) {
      form_set_error('nycha_actual_to', t('Actual Amount To value must be a number.'));
    }
    if (is_numeric($actualFrom) && is_numeric($actualTo) && $actualTo < $actualFrom) {
      form_set_error('nycha_actual_to', t('Invalid range for Actual Amount.'));
    }

    $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
    if (!$multi_select_hidden) {
      form_set_error('nycha_column_select', t('You must select at least one column.'));
    }

    //Set the hidden filed values for Revenue Name and Revenue Type
    $form_state['complete form']['nycha_revenue_type_hidden']['#value'] = $form_state['values']['nycha_revenue_type'];
    $form_state['complete form']['nycha_revenue_name_hidden']['#value'] = $form_state['values']['nycha_revenue_name'];
  }
}
