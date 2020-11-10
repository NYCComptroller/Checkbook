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


class BudgetFeedCitywide extends BudgetFeed{
  protected $data_source = 'citywide';
  protected $type_of_data = 'Budget';
  protected $filtered_columns_container = 'column_select_expense';

  protected function _process_user_criteria_by_datasource(){
    if ($this->values['agency']) {
      $this->form['filter']['agency'] = array('#markup' => '<div><strong>Agency:</strong> ' . $this->values['agency'] . '</div>');
      $this->user_criteria['Agency'] = $this->values['agency'];
      $this->formatted_search_criteria['Agency'] = $this->values['agency'];
    }
    if ($this->values['dept'] && $this->values['dept'] != 'Select Department') {
      $this->form['filter']['department'] = array('#markup' => '<div><strong>Department:</strong> ' . $this->values['dept'] . '</div>');
      $this->user_criteria['Department'] = $this->values['dept'];
      $this->formatted_search_criteria['Department'] = $this->values['dept'];
    }
    if ($this->values['expense_category'] && $this->values['expense_category'] != 'Select Expense Category') {
      $this->form['filter']['expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $this->values['expense_category'] . '</div>');
      $this->user_criteria['Expense Category'] = $this->values['expense_category'];
      $this->formatted_search_criteria['Expense Category'] = $this->values['expense_category'];
    }
    if ($this->values['budget_code']) {
      $this->form['filter']['budget_code'] = array('#markup' => '<div><strong>Budget Code:</strong> ' . $this->values['budget_code'] . '</div>');
      $this->user_criteria['Budget Code'] = $this->values['budget_code'];
      $this->formatted_search_criteria['Budget Code'] = $this->values['budget_code'];
    }
    if ($this->values['fiscal_year']) {
      $this->form['filter']['fiscal_year'] = array('#markup' => '<div><strong>Year:</strong> ' . $this->values['fiscal_year'] . '</div>');
      $this->user_criteria['Year'] = $this->values['fiscal_year'];
      $this->formatted_search_criteria['Year'] = $this->values['fiscal_year'];
    }
    if ($this->values['currentfrom'] && $this->values['currentto']) {
      $this->form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Greater Than Equal to: $' . $this->values['currentfrom'] . ' Less Than Equal to: $' . $this->values['currentto'] . '</div>');
      $this->user_criteria['Modified Budget Greater Than'] = $this->values['currentfrom'];
      $this->user_criteria['Modified Budget Less Than'] = $this->values['currentto'];
      $this->formatted_search_criteria['Modified Budget'] = 'Greater Than Equal to: $' . $this->values['currentfrom'] . ' Less Than Equal to: $' . $this->values['currentto'];
    } elseif (!$this->values['currentfrom'] && $this->values['currentto']) {
      $this->form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Less Than Equal to: $' . $this->values['currentto'] . '</div>');
      $this->user_criteria['Modified Budget Less Than'] = $this->values['currentto'];
      $this->formatted_search_criteria['Modified Budget'] = 'Less Than Equal to: $' . $this->values['currentto'];
    } elseif ($this->values['currentfrom'] && !$this->values['currentto']) {
      $this->form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Greater Than Equal to: $' . $this->values['currentfrom'] . '</div>');
      $this->user_criteria['Modified Budget Greater Than'] = $this->values['currentfrom'];
      $this->formatted_search_criteria['Modified Budget'] = 'Greater Than Equal to: $' . $this->values['currentfrom'];
    }
    if (($this->values['adoptedfrom'] || $this->values['adoptedfrom'] === "0") && ($this->values['adoptedto'] || $this->values['adoptedto'] === "0")) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Greater Than Equal to: $' . $this->values['adoptedfrom'] . ' Less Than Equal to: $' . $this->values['adoptedto'] . '</div>');
      $this->user_criteria['Adopted Budget Greater Than'] = $this->values['adoptedfrom'];
      $this->user_criteria['Adopted Budget Less Than'] = $this->values['adoptedto'];
      $this->formatted_search_criteria['Adopted Budget'] = 'Greater Than Equal to: $' . $this->values['adoptedfrom'] . ' Less Than Equal to: $' . $this->values['adoptedto'];
    } elseif (!$this->values['adoptedfrom'] && ($this->values['adoptedto'] || $this->values['adoptedto'] === "0")) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Less Than Equal to: $' . $this->values['adoptedto'] . '</div>');
      $this->user_criteria['Adopted Budget Less Than'] = $this->values['adoptedto'];
      $this->formatted_search_criteria['Adopted Budget'] = 'Less Than Equal to: $' . $this->values['adoptedto'];
    } elseif (($this->values['adoptedfrom'] || $this->values['adoptedfrom'] === "0") && !$this->values['adoptedto']) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Greater Than Equal to: $' . $this->values['adoptedfrom'] . '</div>');
      $this->user_criteria['Adopted Budget Greater Than'] = $this->values['adoptedfrom'];
      $this->formatted_search_criteria['Adopted Budget'] = 'Greater Than Equal to: $' . $this->values['adoptedfrom'];
    }
    if (($this->values['preencumberedfrom'] || $this->values['preencumberedfrom'] === "0") && ($this->values['preencumberedto'] || $this->values['preencumberedto'] === "0")) {
      $this->form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Greater Than Equal to: $' . $this->values['preencumberedfrom'] . ' Less Than Equal to: $' . $this->values['preencumberedto'] . '</div>');
      $this->user_criteria['Pre-encumbered Greater Than'] = $this->values['preencumberedfrom'];
      $this->user_criteria['Pre-encumbered Less Than'] = $this->values['preencumberedto'];
      $this->formatted_search_criteria['Pre-encumbered'] = 'Greater Than Equal to: $' . $this->values['preencumberedfrom'] . ' Less Than Equal to: $' . $this->values['preencumberedto'];
    } elseif (!$this->values['preencumberedfrom'] && ($this->values['preencumberedto'] || $this->values['preencumberedto'] === "0")) {
      $this->form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Less Than Equal to: $' . $this->values['preencumberedto'] . '</div>');
      $this->user_criteria['Pre-encumbered Less Than'] = $this->values['preencumberedto'];
      $this->formatted_search_criteria['Pre-encumbered'] = 'Less Than Equal to: $' . $this->values['preencumberedto'];
    } elseif (($this->values['preencumberedfrom'] || $this->values['preencumberedfrom'] === "0")  && !$this->values['preencumberedto']) {
      $this->form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Greater Than Equal to: $' . $this->values['preencumberedfrom'] . '</div>');
      $this->user_criteria['Pre-encumbered Greater Than'] = $this->values['preencumberedfrom'];
      $this->formatted_search_criteria['Pre-encumbered'] = 'Greater Than Equal to: $' . $this->values['preencumberedfrom'];
    }
    if (($this->values['encumberedfrom'] || $this->values['encumberedfrom'] === "0") && ($this->values['encumberedto'] || $this->values['encumberedto'] === "0")) {
      $this->form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Greater Than Equal to: $' . $this->values['encumberedfrom'] . ' Less Than Equal to: $' . $this->values['encumberedto'] . '</div>');
      $this->user_criteria['Encumbered Greater Than'] = $this->values['encumberedfrom'];
      $this->user_criteria['Encumbered Less Than'] = $this->values['encumberedto'];
      $this->formatted_search_criteria['Encumbered'] = 'Greater Than Equal to: $' . $this->values['encumberedfrom'] . ' Less Than Equal to: $' . $this->values['encumberedto'];
    } elseif (!$this->values['encumberedfrom'] && $this->values['encumberedto']) {
      $this->form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Less Than Equal to: $' . $this->values['encumberedto'] . '</div>');
      $this->user_criteria['Encumbered Less Than'] = $this->values['encumberedto'];
      $this->formatted_search_criteria['Encumbered'] = 'Less Than Equal to: $' . $this->values['encumberedto'];
    } elseif ($this->values['encumberedfrom'] && !$this->values['encumberedto']) {
      $this->form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Greater Than Equal to: $' . $this->values['encumberedfrom'] . '</div>');
      $this->user_criteria['Encumbered Greater Than'] = $this->values['encumberedfrom'];
      $this->formatted_search_criteria['Encumbered'] = 'Greater Than Equal to: $' . $this->values['encumberedfrom'];
    }
    if (($this->values['accruedexpensefrom'] || $this->values['accruedexpensefrom'] === "0") && ($this->values['accruedexpenseto'] || $this->values['accruedexpenseto'] === "0")) {
      $this->form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Greater Than Equal to: $' . $this->values['accruedexpensefrom'] . ' Less Than Equal to: $' . $this->values['accruedexpenseto'] . '</div>');
      $this->user_criteria['Accrued Expense Greater Than'] = $this->values['accruedexpensefrom'];
      $this->user_criteria['Accrued Expense Less Than'] = $this->values['accruedexpenseto'];
      $this->formatted_search_criteria['Accrued Expense'] = 'Greater Than Equal to: $' . $this->values['accruedexpensefrom'] . ' Less Than Equal to: $' . $this->values['accruedexpenseto'];
    } elseif (!$this->values['accruedexpensefrom'] && ($this->values['accruedexpenseto'] || $this->values['accruedexpenseto'] === "0")) {
      $this->form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Less Than Equal to: $' . $this->values['accruedexpenseto'] . '</div>');
      $this->user_criteria['Accrued Expense Less Than'] = $this->values['accruedexpenseto'];
      $this->formatted_search_criteria['Accrued Expense'] = 'Less Than Equal to: $' . $this->values['accruedexpenseto'];
    } elseif (($this->values['accruedexpensefrom'] || $this->values['accruedexpensefrom'] === "0") && !$this->values['accruedexpenseto']) {
      $this->form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Greater Than Equal to: $' . $this->values['accruedexpensefrom'] . '</div>');
      $this->user_criteria['Accrued Expense Greater Than'] = $this->values['accruedexpensefrom'];
      $this->formatted_search_criteria['Accrued Expense'] = 'Greater Than Equal to: $' . $this->values['accruedexpensefrom'];
    }
    if (($this->values['cashfrom'] || $this->values['cashfrom'] === "0") && ($this->values['cashto'] || $this->values['cashto'] === "0")) {
      $this->form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Greater Than Equal to: $' . $this->values['cashfrom'] . ' Less Than Equal to: $' . $this->values['cashto'] . '</div>');
      $this->user_criteria['Cash Expense Greater Than'] = $this->values['cashfrom'];
      $this->user_criteria['Cash Expense Less Than'] = $this->values['cashto'];
      $this->formatted_search_criteria['Cash Expense'] = 'Greater Than Equal to: $' . $this->values['cashfrom'] . ' Less Than Equal to: $' . $this->values['cashto'];
    } elseif (!$this->values['cashfrom'] && ($this->values['cashto'] || $this->values['cashto'] === "0")) {
      $this->form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Less Than Equal to: $' . $this->values['cashto'] . '</div>');
      $this->user_criteria['Cash Expense Less Than'] = $this->values['cashto'];
      $this->formatted_search_criteria['Cash Expense'] = 'Less Than Equal to: $' . $this->values['cashto'];
    } elseif (($this->values['cashfrom'] || $this->values['cashfrom'] === "0") && !$this->values['cashto']) {
      $this->form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Greater Than Equal to: $' . $this->values['cashfrom'] . '</div>');
      $this->user_criteria['Cash Expense Greater Than'] = $this->values['cashfrom'];
      $this->formatted_search_criteria['Cash Expense'] = 'Greater Than Equal to: $' . $this->values['cashfrom'];
    }
    if (($this->values['postadjustmentsfrom'] || $this->values['postadjustmentsfrom'] === "0") && ($this->values['postadjustmentsto'] || $this->values['postadjustmentsto'] === "0")) {
      $this->form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Greater Than Equal to: $' . $this->values['postadjustmentsfrom'] . ' Less Than Equal to: $' . $this->values['postadjustmentsto'] . '</div>');
      $this->user_criteria['Post Adjustments Greater Than'] = $this->values['postadjustmentsfrom'];
      $this->user_criteria['Post Adjustments Less Than'] = $this->values['postadjustmentsto'];
      $this->formatted_search_criteria['Post Adjustments'] = 'Greater Than Equal to: $' . $this->values['postadjustmentsfrom'] . ' Less Than Equal to: $' . $this->values['postadjustmentsto'];
    } elseif (!$this->values['postadjustmentsfrom'] && ($this->values['postadjustmentsto'] || $this->values['postadjustmentsto'] === "0")) {
      $this->form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Less Than Equal to: $' . $this->values['postadjustmentsto'] . '</div>');
      $this->user_criteria['Post Adjustments Less Than'] = $this->values['postadjustmentsto'];
      $this->formatted_search_criteria['Post Adjustments'] = 'Less Than Equal to: $' . $this->values['postadjustmentsto'];
    } elseif (($this->values['postadjustmentsfrom'] || $this->values['postadjustmentsfrom'] === "0")  && !$this->values['postadjustmentsto']) {
      $this->form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Greater Than Equal to: $' . $this->values['postadjustmentsfrom'] . '</div>');
      $this->user_criteria['Post Adjustments Greater Than'] = $this->values['postadjustmentsfrom'];
      $this->formatted_search_criteria['Post Adjustments'] = 'Greater Than Equal to: $' . $this->values['postadjustmentsfrom'];
    }
  }

  protected function _process_datasource_values(){
    if ($this->values['fiscal_year'] != 'All Years') {
      $this->criteria['value']['year'] = $this->values['fiscal_year'];
    }
    if ($this->values['agency'] != 'Citywide (All Agencies)') {
      preg_match($this->bracket_value_pattern, $this->values['agency'], $amatches);
      if ($amatches) {
        $this->criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
      }
    }
    if ($this->values['dept']) {
      preg_match($this->bracket_value_pattern, $this->values['dept'], $dcmatches);
      if ($dcmatches) {
        $this->criteria['value']['department_code'] = trim($dcmatches[1], '[ ]');
      }
    }
    if ($this->values['expense_category']) {
      preg_match($this->bracket_value_pattern, $this->values['expense_category'], $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['expense_category'] = trim($ecmatches[1], '[ ]');
      }
    }
    if ($this->values['budget_code']) {
      preg_match($this->bracket_value_pattern, $this->values['budget_code'], $bcmatches);
      if ($bcmatches) {
        $this->criteria['value']['budget_code'] = trim($bcmatches[1], '[ ]');
        $this->criteria['value']['budget_code_name'] = str_replace($bcmatches[1], "", $this->values['budget_code']);
      } else {
        $this->criteria['value']['budget_code_name'] = $this->values['budget_code'];
      }
    }
    if ($this->values['adoptedfrom'] !== '' || $this->values['adoptedto'] !== '') {
      $this->criteria['range']['adopted'] = array(
        checknull($this->values['adoptedfrom']),
        checknull($this->values['adoptedto']),
      );
    }
    if ($this->values['currentfrom'] !== '' || $this->values['currentto'] !== '') {
      $this->criteria['range']['modified'] = array(
        checknull($this->values['currentfrom']),
        checknull($this->values['currentto']),
      );
    }
    if ($this->values['preencumberedfrom'] !== '' || $this->values['preencumberedto'] !== '') {
      $this->criteria['range']['pre_encumbered'] = array(
        checknull($this->values['preencumberedfrom']),
        checknull($this->values['preencumberedto']),
      );
    }
    if ($this->values['encumberedfrom'] !== '' || $this->values['encumberedto'] !== '') {
      $this->criteria['range']['encumbered'] = array(
        checknull($this->values['encumberedfrom']),
        checknull($this->values['encumberedto']),
      );
    }
    if ($this->values['cashfrom'] !== '' || $this->values['cashto'] !== '') {
      $this->criteria['range']['cash_expense'] = array(
        checknull($this->values['cashfrom']),
        checknull($this->values['cashto']),
      );
    }
    if ($this->values['postadjustmentsfrom'] !== '' || $this->values['postadjustmentsto'] !== '') {
      $this->criteria['range']['post_adjustment'] = array(
        checknull($this->values['postadjustmentsfrom']),
        checknull($this->values['postadjustmentsto']),
      );
    }
    if ($this->values['accruedexpensefrom'] !== '' || $this->values['accruedexpenseto'] !== '') {
      $this->criteria['range']['accrued_expense'] = array(
        checknull($this->values['accruedexpensefrom']),
        checknull($this->values['accruedexpenseto']),
      );
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state){
    $adoptedfrom = $form_state['values']['adoptedfrom'];
    $adoptedto = $form_state['values']['adoptedto'];
    $currentfrom = $form_state['values']['currentfrom'];
    $currentto = $form_state['values']['currentto'];
    $preencfrom = $form_state['values']['preencumberedfrom'];
    $preencto = $form_state['values']['preencumberedto'];
    $encto = $form_state['values']['encumberedto'];
    $encfrom = $form_state['values']['encumberedfrom'];
    $cashfrom = $form_state['values']['cashfrom'];
    $cashto = $form_state['values']['cashto'];
    $postfrom = $form_state['values']['postadjustmentsfrom'];
    $postto = $form_state['values']['postadjustmentsto'];
    $accruedfrom = $form_state['values']['accruedexpensefrom'];
    $accruedto = $form_state['values']['accruedexpenseto'];
    $expensecolumns = $form_state['values']['column_select_expense'];
    // Adopted:
    if ($adoptedfrom && !is_numeric($adoptedfrom)) {
      form_set_error('adoptedfrom', t('Adopted Budget From value must be a number.'));
    }
    if ($adoptedto && !is_numeric($adoptedto)) {
      form_set_error('adoptedto', t('Adopted Budget To value must be a number.'));
    }
    if (is_numeric($adoptedfrom) && is_numeric($adoptedto) && $adoptedto < $adoptedfrom) {
      form_set_error('adoptedto', t('Invalid range for Adopted Budget.'));
    }
    // Current:
    if ($currentfrom && !is_numeric($currentfrom)) {
      form_set_error('currentfrom', t('Modified Budget From value must be a number.'));
    }
    if ($currentto && !is_numeric($currentto)) {
      form_set_error('currentto', t('Modified Budget To value must be a number.'));
    }
    if (is_numeric($currentfrom) && is_numeric($currentto) && $currentto < $currentfrom) {
      form_set_error('currentto', t('Invalid range for Modified Budget.'));
    }
    // Preencumbered:
    if ($preencfrom && !is_numeric($preencfrom)) {
      form_set_error('preencumberedfrom', t('Pre-encumbered From value must be a number.'));
    }
    if ($preencto && !is_numeric($preencto)) {
      form_set_error('preencumberedto', t('Pre-encumbered To value must be a number.'));
    }
    if (is_numeric($preencfrom) && is_numeric($preencto) && $preencto < $preencfrom) {
      form_set_error('preencumberedto', t('Invalid range for Pre-encumbered.'));
    }
    // Encumbered:
    if ($encfrom && !is_numeric($encfrom)) {
      form_set_error('encumberedfrom', t('Encumbered From value must be a number.'));
    }
    if ($encto && !is_numeric($encto)) {
      form_set_error('encumberedto', t('Encumbered To value must be a number.'));
    }
    if (is_numeric($encfrom) && is_numeric($encto) && $encto < $encfrom) {
      form_set_error('encumberedto', t('Invalid range for Encumbered.'));
    }
    // Cash:
    if ($cashfrom && !is_numeric($cashfrom)) {
      form_set_error('cashfrom', t('Cash Expense From value must be a number.'));
    }
    if ($cashto && !is_numeric($cashto)) {
      form_set_error('cashto', t('Cash Expense To value must be a number.'));
    }
    if (is_numeric($cashfrom) && is_numeric($cashto) && $cashto < $cashfrom) {
      form_set_error('cashto', t('Invalid range for Cash Expense.'));
    }
    // Post Adjustments:
    if ($postfrom && !is_numeric($postfrom)) {
      form_set_error('postadjustmentsfrom', t('Post Adjustment From value must be a number.'));
    }
    if ($postto && !is_numeric($postto)) {
      form_set_error('postadjustmentsto', t('Post Adjustment To value must be a number.'));
    }
    if (is_numeric($postfrom) && is_numeric($postto) && $postto < $postfrom) {
      form_set_error('postadjustmentsto', t('Invalid range for Post Adjustment.'));
    }
    // Accrued Expense:
    if ($accruedfrom && !is_numeric($accruedfrom)) {
      form_set_error('accruedexpensefrom', t('Accrued Expense From value must be a number.'));
    }
    if ($accruedto && !is_numeric($accruedto)) {
      form_set_error('accruedexpenseto', t('Accrued Expense To value must be a number.'));
    }
    if (is_numeric($accruedfrom) && is_numeric($accruedto) && $accruedto < $accruedfrom) {
      form_set_error('accruedexpenseto', t('Invalid range for Accrued Expense.'));
    }
    // Columns:
    if (!$expensecolumns) {
      form_set_error('column_select_expense', t('You must select at least one column.'));
    }
    //Set the hidden filed values on Budget form
    $form_state['complete form']['dept_hidden']['#value'] = $form_state['values']['dept'];
    $form_state['complete form']['expense_category_hidden']['#value'] = $form_state['values']['expense_category'];
  }
}
