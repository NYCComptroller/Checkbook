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

  protected function _process_user_criteria_by_datasource(){
    //OGE Display
    $this->form['filter']['agency'] = array('#markup' => '<div><strong>' . $this->oge_label .':</strong> ' . $this->oge_name_code . '</div>',);
    $this->formatted_search_criteria[$this->oge_label] = $this->oge_name;

    //Year
    if ($this->form_state->getValue('nycha_fiscal_year')) {
      $this->form['filter']['nycha_fiscal_year'] = array('#markup' => '<div><strong>Year:</strong> ' . $this->form_state->getValue('nycha_fiscal_year') . '</div>');
      $this->user_criteria['Year'] = $this->form_state->getValue('nycha_fiscal_year');
      $this->formatted_search_criteria['Year'] = $this->form_state->getValue('nycha_fiscal_year');
    }
    //Expense Category
    if ($this->form_state->getValue('nycha_expense_category') && $this->form_state->getValue('nycha_expense_category') != 'Select Expense Category' && $this->form_state->getValue('nycha_expense_category') != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('nycha_expense_category'))) {
        $this->form_state->setValue('nycha_expense_category', htmlspecialchars($this->form_state->getValue('nycha_expense_category')));
      }
      $this->form['filter']['nycha_expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $this->form_state->getValue('nycha_expense_category') . '</div>');
      $this->user_criteria['Expense Category'] = $this->form_state->getValue('nycha_expense_category');
      $this->formatted_search_criteria['Expense Category'] = $this->form_state->getValue('nycha_expense_category');
    }

    //Responsibility Center
    if ($this->form_state->getValue('nycha_resp_center') && $this->form_state->getValue('nycha_resp_center') != 'Select Responsibility Center' && $this->form_state->getValue('nycha_resp_center') != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('nycha_resp_center'))) {
        $this->form_state->setValue('nycha_resp_center', htmlspecialchars($this->form_state->getValue('nycha_resp_center')));
      }
      $this->form['filter']['nycha_resp_center'] = array('#markup' => '<div><strong>Responsibility Center:</strong> ' . $this->form_state->getValue('nycha_resp_center') . '</div>');
      $this->user_criteria['Responsibility Center'] = $this->form_state->getValue('nycha_resp_center');
      $this->formatted_search_criteria['Responsibility Center'] = $this->form_state->getValue('nycha_resp_center');
    }

    //Funding Source
    if ($this->form_state->getValue('nycha_funding_source') && $this->form_state->getValue('nycha_funding_source') != 'Select Funding Source' && $this->form_state->getValue('nycha_funding_source') != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('nycha_funding_source'))) {
        $this->form_state->setValue('nycha_funding_source', htmlspecialchars($this->form_state->getValue('nycha_funding_source')));
      }
      $this->form['filter']['nycha_funding_source'] = array('#markup' => '<div><strong>Funding Source:</strong> ' . $this->form_state->getValue('nycha_funding_source') . '</div>');
      $this->user_criteria['Funding Source'] = $this->form_state->getValue('nycha_funding_source');
      $this->formatted_search_criteria['Funding Source'] = $this->form_state->getValue('nycha_funding_source');
    }

    //Program
    if ($this->form_state->getValue('nycha_program') && $this->form_state->getValue('nycha_program') != 'Select Program' && $this->form_state->getValue('nycha_program') != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('nycha_program'))) {
        $this->form_state->setValue('nycha_program', htmlspecialchars($this->form_state->getValue('nycha_program')));
      }
      $this->form['filter']['nycha_program'] = array('#markup' => '<div><strong>Program:</strong> ' . $this->form_state->getValue('nycha_program') . '</div>');
      $this->user_criteria['Program'] = $this->form_state->getValue('nycha_program');
      $this->formatted_search_criteria['Program'] = $this->form_state->getValue('nycha_program');
    }

    //Project
    if ($this->form_state->getValue('nycha_project') && $this->form_state->getValue('nycha_project') != 'Select Project' && $this->form_state->getValue('nycha_project') != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('nycha_project'))) {
        $this->form_state->getValue('nycha_project', htmlspecialchars($this->form_state->getValue('nycha_project')));
      }
      $this->form['filter']['nycha_project'] = array('#markup' => '<div><strong>Project:</strong> ' . $this->form_state->getValue('nycha_project') . '</div>');
      $this->user_criteria['Project'] = $this->form_state->getValue('nycha_project');
      $this->formatted_search_criteria['Project'] = $this->form_state->getValue('nycha_project');
    }

    //Budget Type
    if ($this->form_state->getValue('nycha_budget_type') && $this->form_state->getValue('nycha_budget_type') != 'Select Budget Type' && $this->form_state->getValue('nycha_budget_type') != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('nycha_budget_type'))) {
        $this->form_state->setValue('nycha_budget_type', htmlspecialchars($this->form_state->getValue('nycha_budget_type')));
      }
      $this->form['filter']['nycha_budget_type'] = array('#markup' => '<div><strong>Budget Type:</strong> ' . $this->form_state->getValue('nycha_budget_type') . '</div>');
      $this->user_criteria['Budget Type'] = $this->form_state->getValue('nycha_budget_type');
      $this->formatted_search_criteria['Budget Type'] = $this->form_state->getValue('nycha_budget_type');
    }

    //Budget Name
    if ($this->form_state->getValue('nycha_budget_name') && $this->form_state->getValue('nycha_budget_name') != 'Select Budget Name' && $this->form_state->getValue('nycha_budget_name') != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('nycha_budget_name'))) {
        $this->form_state->setValue('nycha_budget_name', htmlspecialchars($this->form_state->getValue('nycha_budget_name')));
      }
      $this->form['filter']['nycha_budget_name'] = array('#markup' => '<div><strong>Budget Name:</strong> ' . $this->form_state->getValue('nycha_budget_name') . '</div>');
      $this->user_criteria['Budget Name'] = $this->form_state->getValue('nycha_budget_name');
      $this->formatted_search_criteria['Budget Name'] = $this->form_state->getValue('nycha_budget_name');
    }

    //Adopted
    if (($this->values['nycha_adopted_from'] || $this->values['nycha_adopted_from'] === "0") && ($this->form_state->getValue('nycha_adopted_to') || $this->form_state->getValue('nycha_adopted_to') === "0")) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Greater Than Equal to: $' . $this->values['nycha_adopted_from'] . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_adopted_to') . '</div>');
      $this->user_criteria['Adopted Budget Greater Than'] = $this->values['nycha_adopted_from'];
      $this->user_criteria['Adopted Budget Less Than'] = $this->form_state->getValue('nycha_adopted_to');
      $this->formatted_search_criteria['Adopted Budget'] = 'Greater Than Equal to: $' . $this->values['nycha_adopted_from'] . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_adopted_to');
    } elseif (!$this->values['nycha_adopted_from'] && ($this->form_state->getValue('nycha_adopted_to') || $this->form_state->getValue('nycha_adopted_to') === "0")) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Less Than Equal to: $' . $this->form_state->getValue('nycha_adopted_to') . '</div>');
      $this->user_criteria['Adopted Budget Less Than'] = $this->form_state->getValue('nycha_adopted_to');
      $this->formatted_search_criteria['Adopted Budget'] = 'Less Than Equal to: $' . $this->form_state->getValue('nycha_adopted_to');
    } elseif (($this->values['nycha_adopted_from'] || $this->values['nycha_adopted_from'] === "0") && !$this->form_state->getValue('nycha_adopted_to')) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Greater Than Equal to: $' . $this->values['nycha_adopted_from'] . '</div>');
      $this->user_criteria['Adopted Budget Greater Than'] = $this->values['nycha_adopted_from'];
      $this->formatted_search_criteria['Adopted Budget'] = 'Greater Than Equal to: $' . $this->values['nycha_adopted_from'];
    }

    //Modified
    if (($this->values['nycha_modified_from'] || $this->values['nycha_modified_from'] === "0") && ($this->form_state->getValue('nycha_modified_to') || $this->form_state->getValue('nycha_modified_to') === "0")) {
      $this->form['filter']['modified_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Greater Than Equal to: $' . $this->values['nycha_modified_from'] . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_modified_to') . '</div>');
      $this->user_criteria['Modified Budget Greater Than'] = $this->values['nycha_modified_from'];
      $this->user_criteria['Modified Budget Less Than'] = $this->form_state->getValue('nycha_modified_to');
      $this->formatted_search_criteria['Modified Budget'] = 'Greater Than Equal to: $' . $this->values['nycha_modified_from'] . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_modified_to');
    } elseif (!$this->values['nycha_modified_from'] && ($this->form_state->getValue('nycha_modified_to') || $this->form_state->getValue('nycha_modified_to') === "0")) {
      $this->form['filter']['modified_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Less Than Equal to: $' . $this->form_state->getValue('nycha_modified_to') . '</div>');
      $this->user_criteria['Modified Budget Less Than'] = $this->form_state->getValue('nycha_modified_to');
      $this->formatted_search_criteria['Modified Budget'] = 'Less Than Equal to: $' . $this->form_state->getValue('nycha_modified_to');
    } elseif (($this->values['nycha_modified_from'] || $this->values['nycha_modified_from'] === "0")  && !$this->form_state->getValue('nycha_modified_to')) {
      $this->form['filter']['modified_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Greater Than Equal to: $' . $this->values['nycha_modified_from'] . '</div>');
      $this->user_criteria['Modified Budget Greater Than'] = $this->values['nycha_modified_from'];
      $this->formatted_search_criteria['Modified Budget'] = 'Greater Than Equal to: $' . $this->values['nycha_modified_from'];
    }

    //Committed
    if (($this->values['nycha_committed_from'] || $this->values['nycha_committed_from'] === "0") && ($this->form_state->getValue('nycha_committed_to') || $this->form_state->getValue('nycha_committed_to') === "0")) {
      $this->form['filter']['committed_budget'] = array('#markup' => '<div><strong>Committed Budget:</strong> Greater Than Equal to: $' . $this->values['nycha_committed_from'] . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_committed_to') . '</div>');
      $this->user_criteria['Committed Budget Greater Than'] = $this->values['nycha_committed_from'];
      $this->user_criteria['Committed Budget Less Than'] = $this->form_state->getValue('nycha_committed_to');
      $this->formatted_search_criteria['Committed Budget'] = 'Greater Than Equal to: $' . $this->values['nycha_committed_from'] . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_committed_to');
    } elseif (!$this->values['nycha_committed_from'] && ($this->form_state->getValue('nycha_committed_to') || $this->form_state->getValue('nycha_committed_to') === "0")) {
      $this->form['filter']['committed_budget'] = array('#markup' => '<div><strong>Committed Budget:</strong> Less Than Equal to: $' . $this->form_state->getValue('nycha_committed_to') . '</div>');
      $this->user_criteria['Committed Budget Less Than'] = $this->form_state->getValue('nycha_committed_to');
      $this->formatted_search_criteria['Committed Budget'] = 'Less Than Equal to: $' . $this->form_state->getValue('nycha_committed_to');
    } elseif (($this->values['nycha_committed_from'] || $this->values['nycha_committed_from'] === "0") && !$this->form_state->getValue('nycha_committed_to')) {
      $this->form['filter']['committed_budget'] = array('#markup' => '<div><strong>Committed Budget:</strong> Greater Than Equal to: $' . $this->values['nycha_committed_from'] . '</div>');
      $this->user_criteria['Committed Budget Greater Than'] = $this->values['nycha_committed_from'];
      $this->formatted_search_criteria['Committed Budget'] = 'Greater Than Equal to: $' . $this->values['nycha_committed_from'];
    }


    //Encumbered
    if (($this->values['nycha_encumbered_from'] || $this->values['nycha_encumbered_from'] === "0") && ($this->form_state->getValue('nycha_encumbered_to') || $this->form_state->getValue('nycha_encumbered_to') === "0")) {
      $this->form['filter']['encumbered_budget'] = array('#markup' => '<div><strong>Encumbered Budget:</strong> Greater Than Equal to: $' . $this->values['nycha_encumbered_from'] . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_encumbered_to') . '</div>');
      $this->user_criteria['Encumbered Budget Greater Than'] = $this->values['nycha_encumbered_from'];
      $this->user_criteria['Encumbered Budget Less Than'] = $this->form_state->getValue('nycha_encumbered_to');
      $this->formatted_search_criteria['Encumbered Budget'] = 'Greater Than Equal to: $' . $this->values['nycha_encumbered_from'] . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_encumbered_to');
    } elseif (!$this->values['nycha_encumbered_from'] && ($this->form_state->getValue('nycha_encumbered_to') || $this->form_state->getValue('nycha_encumbered_to') === "0")) {
      $this->form['filter']['encumbered_budget'] = array('#markup' => '<div><strong>Encumbered Budget:</strong> Less Than Equal to: $' . $this->form_state->getValue('nycha_encumbered_to') . '</div>');
      $this->user_criteria['Encumbered Budget Less Than'] = $this->form_state->getValue('nycha_encumbered_to');
      $this->formatted_search_criteria['Encumbered Budget'] = 'Less Than Equal to: $' . $this->form_state->getValue('nycha_encumbered_to');
    } elseif (($this->values['nycha_encumbered_from'] || $this->values['nycha_encumbered_from'] === "0")  && !$this->form_state->getValue('nycha_encumbered_to')) {
      $this->form['filter']['encumbered_budget'] = array('#markup' => '<div><strong>Encumbered Budget:</strong> Greater Than Equal to: $' . $this->values['nycha_encumbered_from'] . '</div>');
      $this->user_criteria['Encumbered Budget Greater Than'] = $this->values['nycha_encumbered_from'];
      $this->formatted_search_criteria['Encumbered Budget'] = 'Greater Than Equal to: $' . $this->values['nycha_encumbered_from'];
    }

    //Actual
    if (($this->values['nycha_actual_from'] || $this->values['nycha_actual_from'] === "0") && ($this->form_state->getValue('nycha_actual_to') || $this->form_state->getValue('nycha_actual_to') === "0")) {
      $this->form['filter']['actual_budget'] = array('#markup' => '<div><strong>Actual Amount:</strong> Greater Than Equal to: $' . $this->values['nycha_actual_from'] . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_actual_to') . '</div>');
      $this->user_criteria['Actual Amount Greater Than'] = $this->values['nycha_actual_from'];
      $this->user_criteria['Actual Amount Less Than'] = $this->form_state->getValue('nycha_actual_to');
      $this->formatted_search_criteria['Actual Amount'] = 'Greater Than Equal to: $' . $this->values['nycha_actual_from'] . ' and Less Than Equal to: $' . $this->form_state->getValue('nycha_actual_to');
    } elseif (!$this->values['nycha_actual_from'] && ($this->form_state->getValue('nycha_actual_to') || $this->form_state->getValue('nycha_actual_to') === "0")) {
      $this->form['filter']['actual_budget'] = array('#markup' => '<div><strong>Actual Amount:</strong> Less Than Equal to: $' . $this->form_state->getValue('nycha_actual_to') . '</div>');
      $this->user_criteria['Actual Amount Less Than Less Than'] = $this->form_state->getValue('nycha_actual_to');
      $this->formatted_search_criteria['Actual Amount'] = 'Less Than Equal to: $' . $this->form_state->getValue('nycha_actual_to');
    } elseif (($this->values['nycha_actual_from'] || $this->values['nycha_actual_from'] === "0") && !$this->form_state->getValue('nycha_actual_to')) {
      $this->form['filter']['actual_budget'] = array('#markup' => '<div><strong>Actual Amount:</strong> Greater Than Equal to: $' . $this->values['nycha_actual_from'] . '</div>');
      $this->user_criteria['Actual Amount Greater Than Greater Than'] = $this->values['nycha_actual_from'];
      $this->formatted_search_criteria['Actual Amount'] = 'Greater Than Equal to: $' . $this->values['nycha_actual_from'];
    }
  }

  protected function _process_datasource_values(){
    //Year
    if ($this->form_state->getValue('nycha_fiscal_year') != 'All Years') {
      $this->criteria['value']['year'] = $this->form_state->getValue('nycha_fiscal_year');
    }
    //Expense Category
    if ($this->form_state->getValue('nycha_expense_category')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('nycha_expense_category'), $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['expense_category'] = trim($ecmatches[1], '[ ]');
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
    //Budget Type
    if ($this->form_state->getValue('nycha_budget_type') && $this->form_state->getValue('nycha_budget_type') != "Select Budget Type" && $this->form_state->getValue('nycha_budget_type') != "") {
       $this->criteria['value']['budget_type'] = $this->form_state->getValue('nycha_budget_type');
    }
    //Budget Name
    if ($this->form_state->getValue('nycha_budget_name') && $this->form_state->getValue('nycha_budget_name') != "Select Budget Name" && $this->form_state->getValue('nycha_budget_name') != "") {
      $this->criteria['value']['budget_name'] = $this->form_state->getValue('nycha_budget_name');
    }
    //Adopted Budget
    if ($this->values['nycha_adopted_from'] !== '' || $this->form_state->getValue('nycha_adopted_to') !== '') {
      $this->criteria['range']['adopted'] = array(
        checknull($this->values['nycha_adopted_from']),
        checknull($this->form_state->getValue('nycha_adopted_to')),
      );
    }
    //Modified Amount
    if ($this->values['nycha_modified_from'] !== '' || $this->form_state->getValue('nycha_modified_to') !== '') {
      $this->criteria['range']['modified'] = array(
        checknull($this->values['nycha_modified_from']),
        checknull($this->form_state->getValue('nycha_modified_to')),
      );
    }
    //Committed Amount
    if ($this->values['nycha_committed_from'] !== '' || $this->form_state->getValue('nycha_committed_to') !== '') {
      $this->criteria['range']['committed'] = array(
        checknull($this->values['nycha_committed_from']),
        checknull($this->form_state->getValue('nycha_committed_to')),
      );
    }
    //Encumbered Amount
    if ($this->values['nycha_encumbered_from'] !== '' || $this->form_state->getValue('nycha_encumbered_to') !== '') {
      $this->criteria['range']['encumbered'] = array(
        checknull($this->values['nycha_encumbered_from']),
        checknull($this->form_state->getValue('nycha_encumbered_to')),
      );
    }
    //Actual Amount
    if ($this->values['nycha_actual_from'] !== '' || $this->form_state->getValue('nycha_actual_to') !== '') {
      $this->criteria['range']['actual_amount'] = array(
        checknull($this->values['nycha_actual_from']),
        checknull($this->form_state->getValue('nycha_actual_to')),
      );
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state){

    //adopted Amount
      $adoptedFrom = $form_state->getValue('nycha_adopted_from');
      $adoptedTo = $form_state->getValue('nycha_adopted_to');
    if ($adoptedFrom && !is_numeric($adoptedFrom)) {
      $form_state->setErrorByName('nycha_adopted_from', t('Adopted Budget From value must be a number.'));
    }
    if ($adoptedTo && !is_numeric($adoptedTo)) {
      $form_state->setErrorByName('nycha_adopted_to', t('Adopted Budget To value must be a number.'));
    }
    if (is_numeric($adoptedFrom) && is_numeric($adoptedTo) && $adoptedTo < $adoptedFrom) {
      $form_state->setErrorByName('nycha_adopted_to', t('Invalid range for Adopted Budget.'));
    }

    //Modified Amount
    $modifiedFrom = $form_state->getValue('nycha_modified_from');
    $modifiedTo = $form_state->getValue('nycha_modified_to');
    if ($modifiedFrom && !is_numeric($modifiedFrom)) {
      $form_state->setErrorByName('nycha_modified_from', t('Modified Budget From value must be a number.'));
    }
    if ($modifiedTo && !is_numeric($modifiedTo)) {
      $form_state->setErrorByName('nycha_modified_to', t('Modified Budget To value must be a number.'));
    }
    if (is_numeric($modifiedFrom) && is_numeric($modifiedTo) && $modifiedTo < $modifiedFrom) {
      $form_state->setErrorByName('nycha_modified_to', t('Invalid range for Modified Budget.'));
    }

    //Committed Amount
      $committedFrom = $form_state->getValue('nycha_committed_from');
      $committedTo = $form_state->getValue('nycha_committed_to');
    if ($committedFrom && !is_numeric($committedFrom)) {
      $form_state->setErrorByName('nycha_committed_from', t('Committed Budget From value must be a number.'));
    }
    if ($committedTo && !is_numeric($committedTo)) {
      $form_state->setErrorByName('nycha_committed_to', t('Committed Budget To value must be a number.'));
    }
    if (is_numeric($committedFrom) && is_numeric($committedTo) && $committedTo < $committedFrom) {
      $form_state->setErrorByName('nycha_committed_to', t('Invalid range for Committed Budget.'));
    }

    //Encumbered Amount
    $encumberedFrom = $form_state->getValue('nycha_encumbered_from');
    $encumberedTo = $form_state->getValue('nycha_encumbered_to');
    if ($encumberedFrom && !is_numeric($encumberedFrom)) {
      $form_state->setErrorByName('nycha_encumbered_from', t('Encumbered Budget From value must be a number.'));
    }
    if ($encumberedTo && !is_numeric($encumberedTo)) {
      $form_state->setErrorByName('nycha_encumbered_to', t('Encumbered Budget To value must be a number.'));
    }
    if (is_numeric($encumberedFrom) && is_numeric($encumberedTo) && $encumberedTo < $encumberedFrom) {
      $form_state->setErrorByName('nycha_encumbered_to', t('Invalid range for Encumbered Budget.'));
    }

    //Actual Amount
    $actualFrom = $form_state->getValue('nycha_actual_from');
    $actualTo = $form_state->getValue('nycha_actual_to');

    if ($actualFrom && !is_numeric($actualFrom)) {
      $form_state->setErrorByName('nycha_actual_from', t('Actual Amount From value must be a number.'));
    }
    if ($actualTo && !is_numeric($actualTo)) {
      $form_state->setErrorByName('nycha_actual_to', t('Actual Amount To value must be a number.'));
    }
    if (is_numeric($actualFrom) && is_numeric($actualTo) && $actualTo < $actualFrom) {
      $form_state->setErrorByName('nycha_actual_to', t('Invalid range for Actual Amount.'));
    }
    $nycha_col_selected = array_filter($form_state->getValue('nycha_column_select'));
    $multi_select_hidden = $form_state->hasValue('nycha_column_select') ? '|' . implode('||', $form_state->getValue('nycha_column_select')) . '|' : '';

    if (empty($nycha_col_selected)) {
      $form_state->setErrorByName('nycha_column_select', t('You must select at least one column.'));
    }

    //Set the hidden field values for Budget Name and Budget Type
    $form_state->set(['complete form', 'nycha_budget_type_hidden', '#value'], $form_state->getValue('nycha_budget_type'));
    $form_state->set(['complete form', 'nycha_budget_name_hidden', '#value'], $form_state->getValue('nycha_budget_name'));
  }
}
