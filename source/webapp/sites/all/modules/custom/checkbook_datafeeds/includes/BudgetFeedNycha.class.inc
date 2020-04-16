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


class BudgetFeedNycha extends BudgetFeed
{
  protected $data_source = \Datasource::NYCHA;
  protected $type_of_data = 'Budget_NYCHA';
  protected $filtered_columns_container = 'nycha_column_select';
  protected $oge_label = 'Other Government Entity';
  protected $oge_name_code = "NEW YORK CITY HOUSING AUTHORITY[996]";
  protected $oge_name = "NEW YORK CITY HOUSING AUTHORITY";

  protected function _process_user_criteria_by_datasource(){
    //OGE Display
    $this->form['filter']['agency'] = array('#markup' => '<div><strong>' . $this->oge_label .':</strong> ' . $this->oge_name_code . '</div>',);
    $this->formatted_search_criteria[$this->oge_label] = $this->oge_name;

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

  }

  protected function _process_datasource_values(){

  }

  protected function _validate_by_datasource(&$form, &$form_state){

    //Adopted Amount
    $adoptedFrom = $form_state['values']['nycha_adopted_from'];
    $adoptedTo = $form_state['values']['nycha_adopted_to'];
    if ($adoptedFrom && !is_numeric($adoptedFrom)) {
      form_set_error('nycha_adopted_from', t('Adopted Budget From value must be a number.'));
    }
    if ($adoptedTo && !is_numeric($adoptedTo)) {
      form_set_error('nycha_adopted_to', t('Adopted Budget To value must be a number.'));
    }
    if (is_numeric($adoptedFrom) && is_numeric($adoptedTo) && $adoptedTo < $adoptedFrom) {
      form_set_error('nycha_adopted_to', t('Invalid range for Adopted Budget.'));
    }

    //Modified Amount
    $modifiedFrom = $form_state['values']['nycha_modified_from'];
    $modifiedTo = $form_state['values']['nycha_modified_to'];
    if ($modifiedFrom && !is_numeric($modifiedFrom)) {
      form_set_error('nycha_modified_from', t('Modified Budget From value must be a number.'));
    }
    if ($modifiedTo && !is_numeric($modifiedTo)) {
      form_set_error('nycha_modified_to', t('Modified Budget To value must be a number.'));
    }
    if (is_numeric($modifiedFrom) && is_numeric($modifiedTo) && $modifiedTo < $modifiedFrom) {
      form_set_error('nycha_modified_to', t('Invalid range for Modified Budget.'));
    }

    //Committed Amount
    $committedFrom = $form_state['values']['nycha_committed_from'];
    $committedTo = $form_state['values']['nycha_committed_to'];
    if ($committedFrom && !is_numeric($committedFrom)) {
      form_set_error('nycha_committed_from', t('Committed Budget From value must be a number.'));
    }
    if ($committedTo && !is_numeric($committedTo)) {
      form_set_error('nycha_committed_to', t('Committed Budget To value must be a number.'));
    }
    if (is_numeric($committedFrom) && is_numeric($committedTo) && $committedTo < $committedFrom) {
      form_set_error('nycha_committed_to', t('Invalid range for Committed Budget.'));
    }

    //Encumbered Amount
    $encumberedFrom = $form_state['values']['nycha_encumbered_from'];
    $encumberedTo = $form_state['values']['nycha_encumbered_to'];
    if ($encumberedFrom && !is_numeric($encumberedFrom)) {
      form_set_error('nycha_encumbered_from', t('Encumbered Budget From value must be a number.'));
    }
    if ($encumberedTo && !is_numeric($encumberedTo)) {
      form_set_error('nycha_encumbered_to', t('Encumbered Budget To value must be a number.'));
    }
    if (is_numeric($encumberedFrom) && is_numeric($encumberedTo) && $encumberedTo < $encumberedFrom) {
      form_set_error('nycha_encumbered_to', t('Invalid range for Encumbered Budget.'));
    }

    //Actual Amount
    $actualFrom = $form_state['values']['nycha_actual_from'];
    $actualTo = $form_state['values']['nycha_actual_to'];
    if ($actualFrom && !is_numeric($actualFrom)) {
      form_set_error('nycha_actual_from', t('Actual Budget From value must be a number.'));
    }
    if ($actualTo && !is_numeric($actualTo)) {
      form_set_error('nycha_actual_to', t('Actual Budget To value must be a number.'));
    }
    if (is_numeric($actualFrom) && is_numeric($actualTo) && $actualTo < $actualFrom) {
      form_set_error('nycha_actual_to', t('Invalid range for Actual Budget.'));
    }

    $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
    if (!$multi_select_hidden) {
      form_set_error('nycha_column_select', t('You must select at least one column - nyhca.'));
    }
  }
}
