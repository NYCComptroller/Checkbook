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
      $this->form['filter']['agency'] = array('#markup' => '<div><strong>Agency:</strong> ' . $this->form_state->getValue('agency'). '</div>');
      $this->user_criteria['Agency'] = $this->form_state->getValue('agency');
      $this->formatted_search_criteria['Agency'] = $this->form_state->getValue('agency');
    }
    if ($this->form_state->getValue('dept') && $this->form_state->getValue('dept') != 'Select Department') {
      $this->form['filter']['department'] = array('#markup' => '<div><strong>Department:</strong> ' . $this->form_state->getValue('dept') . '</div>');
      $this->user_criteria['Department'] = $this->form_state->getValue('dept');
      $this->formatted_search_criteria['Department'] = $this->form_state->getValue('dept');
    }
    if ($this->form_state->getValue('expense_category') && $this->form_state->getValue('expense_category') != 'Select Expense Category') {
      $this->form['filter']['expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $this->form_state->getValue('expense_category') . '</div>');
      $this->user_criteria['Expense Category'] = $this->form_state->getValue('expense_category');
      $this->formatted_search_criteria['Expense Category'] = $this->form_state->getValue('expense_category');
    }

    if ($this->form_state->getValue('budget_code')) {
      $this->form['filter']['budget_code'] = array('#markup' => '<div><strong>Budget Code:</strong> ' .  $this->form_state->getValue('budget_code') . '</div>');
      $this->user_criteria['Budget Code'] = $this->form_state->getValue('budget_code');
      $this->formatted_search_criteria['Budget Code'] = $this->form_state->getValue('budget_code');
    }
    if ($this->form_state->getValue('catastrophic_event') !== "0" &&  $this->form_state->getValue('fiscal_year') >= 2020) {
      $this->form['filter']['catastrophic_event'] = array('#markup' => '<div><strong>Catastrophic Event:</strong> ' .  $this->form_state->getValue('catastrophic_event') . '</div>');
      $this->user_criteria['Catastrophic Event'] = $this->form_state->getValue('catastrophic_event');
      $this->formatted_search_criteria['Catastrophic Event'] = $this->form_state->getValue('catastrophic_event');
    }
    if ($this->form_state->getValue('fiscal_year')) {
      $this->form['filter']['fiscal_year'] = array('#markup' => '<div><strong>Year:</strong> ' .  $this->form_state->getValue('fiscal_year') . '</div>');
      $this->user_criteria['Year'] =$this->form_state->getValue('fiscal_year');
      $this->formatted_search_criteria['Year'] = $this->form_state->getValue('fiscal_year');
    }
    if (($this->form_state->getValue('currentfrom') || $this->form_state->getValue('currentfrom') === "0") && ($this->form_state->getValue('currentto') || $this->form_state->getValue('currentto') === "0")) {
      $this->form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Greater Than Equal to: $' . $this->form_state->getValue('currentfrom') . ' and Less Than Equal to: $' .  $this->form_state->getValue('currentto') . '</div>');
      $this->user_criteria['Modified Budget Greater Than'] = $this->form_state->getValue('currentfrom');
      $this->user_criteria['Modified Budget Less Than'] = $this->form_state->getValue('currentto');
      $this->formatted_search_criteria['Modified Budget'] = 'Greater Than Equal to: $' . $this->form_state->getValue('currentfrom') . ' and Less Than Equal to: $' .  $this->form_state->getValue('currentto');
    } elseif (!$this->form_state->getValue('currentfrom') && $this->form_state->getValue('currentfrom')) {
      $this->form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Less Than Equal to: $' . $this->form_state->getValue('currentto') . '</div>');
      $this->user_criteria['Modified Budget Less Than'] = $this->values['currentto'];
      $this->formatted_search_criteria['Modified Budget'] = 'Less Than Equal to: $' . $this->values['currentto'];
    } elseif ($this->form_state->getValue('currentfrom') && !$this->values['currentto']) {
      $this->form['filter']['current_budget'] = array('#markup' => '<div><strong>Modified Budget:</strong> Greater Than Equal to: $' . $this->form_state->getValue('currentfrom') . '</div>');
      $this->user_criteria['Modified Budget Greater Than'] = $this->form_state->getValue('currentfrom');
      $this->formatted_search_criteria['Modified Budget'] = 'Greater Than Equal to: $' . $this->form_state->getValue('currentfrom');
    }
    if (($this->form_state->getValue('adoptedfrom') || $this->form_state->getValue('adoptedfrom') === "0") && ($this->form_state->getValue('adoptedto') ||$this->form_state->getValue('adoptedto') === "0")) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Greater Than Equal to: $' . $this->form_state->getValue('adoptedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('adoptedto') . '</div>');
      $this->user_criteria['Adopted Budget Greater Than'] = $this->form_state->getValue('adoptedfrom');
      $this->user_criteria['Adopted Budget Less Than'] = $this->form_state->getValue('adoptedto');
      $this->formatted_search_criteria['Adopted Budget'] = 'Greater Than Equal to: $' . $this->form_state->getValue('adoptedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('adoptedto');
    } elseif (!$this->form_state->getValue('adoptedfrom') && ($this->form_state->getValue('adoptedto') || $this->form_state->getValue('adoptedto') === "0")) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Less Than Equal to: $' . $this->form_state->getValue('adoptedto') . '</div>');
      $this->user_criteria['Adopted Budget Less Than'] = $this->values['adoptedto'];
      $this->formatted_search_criteria['Adopted Budget'] = 'Less Than Equal to: $' . $this->form_state->getValue('adoptedto');
    } elseif (($this->form_state->getValue('adoptedfrom') || $this->form_state->getValue('adoptedfrom') === "0") && !$this->form_state->getValue('adoptedto')) {
      $this->form['filter']['adopted_budget'] = array('#markup' => '<div><strong>Adopted Budget:</strong> Greater Than Equal to: $' . $this->form_state->getValue('adoptedfrom') . '</div>');
      $this->user_criteria['Adopted Budget Greater Than'] = $this->values['adoptedfrom'];
      $this->formatted_search_criteria['Adopted Budget'] = 'Greater Than Equal to: $' . $this->values['adoptedfrom'];
    }
    if (($this->form_state->getValue('preencumberedfrom') || $this->form_state->getValue('preencumberedfrom') === "0") && ($this->form_state->getValue('preencumberedto') || $this->form_state->getValue('preencumberedto') === "0")) {
      $this->form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Greater Than Equal to: $' . $this->form_state->getValue('preencumberedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('preencumberedto') . '</div>');
      $this->user_criteria['Pre-encumbered Greater Than'] = $this->form_state->getValue('preencumberedfrom');
      $this->user_criteria['Pre-encumbered Less Than'] = $this->form_state->getValue('preencumberedto');
      $this->formatted_search_criteria['Pre-encumbered'] = 'Greater Than Equal to: $' . $this->form_state->getValue('preencumberedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('preencumberedfrom');
    } elseif (!$this->values['preencumberedfrom'] && ($this->form_state->getValue('preencumberedto') || $this->form_state->getValue('preencumberedto') === "0")) {
      $this->form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Less Than Equal to: $' . $this->form_state->getValue('preencumberedto') . '</div>');
      $this->user_criteria['Pre-encumbered Less Than'] = $this->form_state->getValue('preencumberedto');
      $this->formatted_search_criteria['Pre-encumbered'] = 'Less Than Equal to: $' . $this->form_state->getValue('preencumberedto');
    } elseif (($this->form_state->getValue('preencumberedfrom') || $this->form_state->getValue('preencumberedfrom') === "0")  && !$this->form_state->getValue('preencumberedto')) {
      $this->form['filter']['preencumbered'] = array('#markup' => '<div><strong>Pre-encumbered:</strong> Greater Than Equal to: $' . $this->form_state->getValue('preencumberedfrom') . '</div>');
      $this->user_criteria['Pre-encumbered Greater Than'] = $this->form_state->getValue('preencumberedfrom');
      $this->formatted_search_criteria['Pre-encumbered'] = 'Greater Than Equal to: $' . $this->form_state->getValue('preencumberedfrom');
    }
    if (($this->form_state->getValue('encumberedfrom') || $this->form_state->getValue('encumberedfrom') === "0") && ($this->form_state->getValue('encumberedto')  || $this->form_state->getValue('encumberedto') === "0")) {
      $this->form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Greater Than Equal to: $' . $this->form_state->getValue('encumberedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('encumberedto') . '</div>');
      $this->user_criteria['Encumbered Greater Than'] = $this->form_state->getValue('encumberedfrom');
      $this->user_criteria['Encumbered Less Than'] = $this->form_state->getValue('encumberedto');
      $this->formatted_search_criteria['Encumbered'] = 'Greater Than Equal to: $' . $this->form_state->getValue('encumberedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('encumberedto');
    } elseif (!$this->form_state->getValue('encumberedfrom') && $this->form_state->getValue('encumberedto')) {
      $this->form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Less Than Equal to: $' . $this->form_state->getValue('encumberedto') . '</div>');
      $this->user_criteria['Encumbered Less Than'] = $this->form_state->getValue('encumberedto');
      $this->formatted_search_criteria['Encumbered'] = 'Less Than Equal to: $' . $this->form_state->getValue('encumberedto');
    } elseif ($this->form_state->getValue('encumberedfrom') && !$this->form_state->getValue('encumberedto')) {
      $this->form['filter']['encumbered'] = array('#markup' => '<div><strong>Encumbered:</strong> Greater Than Equal to: $' . $this->form_state->getValue('encumberedfrom') . '</div>');
      $this->user_criteria['Encumbered Greater Than'] = $this->form_state->getValue('encumberedfrom');
      $this->formatted_search_criteria['Encumbered'] = 'Greater Than Equal to: $' . $this->form_state->getValue('encumberedfrom');
    }
    if (($this->form_state->getValue('accruedexpensefrom') || $this->form_state->getValue('accruedexpensefrom') === "0") && ($this->form_state->getValue('accruedexpenseto') || $this->form_state->getValue('accruedexpenseto') === "0")) {
      $this->form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Greater Than Equal to: $' . $this->form_state->getValue('accruedexpensefrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('accruedexpenseto') . '</div>');
      $this->user_criteria['Accrued Expense Greater Than'] = $this->form_state->getValue('accruedexpensefrom');
      $this->user_criteria['Accrued Expense Less Than'] = $this->form_state->getValue('accruedexpenseto');
      $this->formatted_search_criteria['Accrued Expense'] = 'Greater Than Equal to: $' . $this->form_state->getValue('accruedexpensefrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('accruedexpenseto');
    } elseif (!$this->form_state->getValue('accruedexpensefrom') && ($this->form_state->getValue('accruedexpenseto') || $this->form_state->getValue('accruedexpenseto') === "0")) {
      $this->form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Less Than Equal to: $' . $this->form_state->getValue('accruedexpenseto') . '</div>');
      $this->user_criteria['Accrued Expense Less Than'] = $this->form_state->getValue('accruedexpenseto');
      $this->formatted_search_criteria['Accrued Expense'] = 'Less Than Equal to: $' . $this->form_state->getValue('accruedexpenseto');
    } elseif (($this->form_state->getValue('accruedexpensefrom') || $this->form_state->getValue('accruedexpensefrom') === "0") && !$this->form_state->getValue('accruedexpenseto')) {
      $this->form['filter']['accrued_expense'] = array('#markup' => '<div><strong>Accrued Expense:</strong> Greater Than Equal to: $' . $this->form_state->getValue('accruedexpensefrom') . '</div>');
      $this->user_criteria['Accrued Expense Greater Than'] = $this->form_state->getValue('accruedexpensefrom');
      $this->formatted_search_criteria['Accrued Expense'] = 'Greater Than Equal to: $' . $this->form_state->getValue('accruedexpensefrom');
    }
    if (($this->form_state->getValue('cashfrom') || $this->form_state->getValue('cashfrom') === "0") && ($this->form_state->getValue('cashto') || $this->form_state->getValue('cashto') === "0")) {
      $this->form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Greater Than Equal to: $' . $this->form_state->getValue('cashfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('cashto') . '</div>');
      $this->user_criteria['Cash Expense Greater Than'] = $this->form_state->getValue('cashfrom');
      $this->user_criteria['Cash Expense Less Than'] = $this->form_state->getValue('cashto');
      $this->formatted_search_criteria['Cash Expense'] = 'Greater Than Equal to: $' . $this->form_state->getValue('cashfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('cashto');
    } elseif (!$this->form_state->getValue('cashfrom') && ($this->form_state->getValue('cashto') || $this->form_state->getValue('cashto') === "0")) {
      $this->form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Less Than Equal to: $' . $this->form_state->getValue('cashto') . '</div>');
      $this->user_criteria['Cash Expense Less Than'] = $this->form_state->getValue('cashto');
      $this->formatted_search_criteria['Cash Expense'] = 'Less Than Equal to: $' . $this->form_state->getValue('cashto');
    } elseif (($this->form_state->getValue('cashfrom') || $this->form_state->getValue('cashfrom') === "0") && !$this->form_state->getValue('cashto')) {
      $this->form['filter']['cash_amount'] = array('#markup' => '<div><strong>Cash Expense:</strong> Greater Than Equal to: $' . $this->form_state->getValue('cashfrom') . '</div>');
      $this->user_criteria['Cash Expense Greater Than'] = $this->form_state->getValue('cashfrom');
      $this->formatted_search_criteria['Cash Expense'] = 'Greater Than Equal to: $' . $this->form_state->getValue('cashfrom');
    }
    if (($this->form_state->getValue('postadjustmentsfrom') || $this->form_state->getValue('postadjustmentsfrom') === "0") && ($this->form_state->getValue('postadjustmentsto') || $this->form_state->getValue('postadjustmentsto') === "0")) {
      $this->form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Greater Than Equal to: $' . $this->form_state->getValue('postadjustmentsfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('postadjustmentsto') . '</div>');
      $this->user_criteria['Post Adjustments Greater Than'] = $this->form_state->getValue('postadjustmentsfrom');
      $this->user_criteria['Post Adjustments Less Than'] = $this->form_state->getValue('postadjustmentsto');
      $this->formatted_search_criteria['Post Adjustments'] = 'Greater Than Equal to: $' . $this->form_state->getValue('postadjustmentsfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('postadjustmentsto');
    } elseif (!$this->form_state->getValue('postadjustmentsfrom') && ($this->form_state->getValue('postadjustmentsto') || $this->form_state->getValue('postadjustmentsto') === "0")) {
      $this->form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Less Than Equal to: $' . $this->form_state->getValue('postadjustmentsto') . '</div>');
      $this->user_criteria['Post Adjustments Less Than'] = $this->form_state->getValue('postadjustmentsto');
      $this->formatted_search_criteria['Post Adjustments'] = 'Less Than Equal to: $' . $this->form_state->getValue('postadjustmentsto');
    } elseif (($this->form_state->getValue('postadjustmentsfrom') || $this->form_state->getValue('postadjustmentsfrom') === "0")  && !$this->form_state->getValue('postadjustmentsto')) {
      $this->form['filter']['post_adjustments'] = array('#markup' => '<div><strong>Post Adjustments:</strong> Greater Than Equal to: $' . $this->form_state->getValue('postadjustmentsfrom') . '</div>');
      $this->user_criteria['Post Adjustments Greater Than'] = $this->form_state->getValue('postadjustmentsfrom');
      $this->formatted_search_criteria['Post Adjustments'] = 'Greater Than Equal to: $' . $this->form_state->getValue('postadjustmentsfrom');
    }
  }

  protected function _process_datasource_values(){
//    kint($this->values);
    if ($this->form_state->getValue('fiscal_year') != 'All Years') {
      $this->criteria['value']['year'] = $this->form_state->getValue('fiscal_year');
    }
    if ($this->form_state->getValue('agency') != 'Citywide (All Agencies)') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('agency'), $amatches);
      if ($amatches) {
        $this->criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
      }
    }
    if ($this->form_state->getValue('dept')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('dept'), $dcmatches);
      if ($dcmatches) {
        $this->criteria['value']['department_code'] = trim($dcmatches[1], '[ ]');
      }
    }
    if ($this->form_state->getValue('expense_category')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('expense_category'), $ecmatches);
      if ($ecmatches) {
        $this->criteria['value']['expense_category'] = trim($ecmatches[1], '[ ]');
      }
    }

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
    if ($this->form_state->getValue('adoptedfrom') !== '' || $this->form_state->getValue('adoptedto') !== '') {
      $this->criteria['range']['adopted'] = array(
        checknull($this->form_state->getValue('adoptedfrom')),
        checknull($this->form_state->getValue('adoptedto')),
      );
    }
    if ($this->form_state->getValue('currentfrom') !== '' || $this->form_state->getValue('currentto') !== '') {
      $this->criteria['range']['modified'] = array(
        checknull($this->form_state->getValue('currentfrom')),
        checknull($this->form_state->getValue('currentto')),
      );
    }
    if ($this->form_state->getValue('preencumberedfrom') !== '' || $this->form_state->getValue('preencumberedto') !== ''){
      $this->criteria['range']['pre_encumbered'] = array(
        checknull($this->form_state->getValue('preencumberedfrom')),
        checknull($this->form_state->getValue('preencumberedto')),
      );
    }
    if ($this->form_state->getValue('encumberedfrom') !== '' || $this->form_state->getValue('encumberedto') !== '') {
      $this->criteria['range']['encumbered'] = array(
        checknull($this->form_state->getValue('encumberedfrom')),
        checknull($this->form_state->getValue('encumberedto')),
      );
    }
    if ($this->form_state->getValue('cashfrom') !== '' || $this->form_state->getValue('cashto') !== '') {
      $this->criteria['range']['cash_expense'] = array(
        checknull($this->form_state->getValue('cashfrom')),
        checknull($this->form_state->getValue('cashto')),
      );
    }
    if ($this->form_state->getValue('postadjustmentsfrom') !== '' || $this->form_state->getValue('postadjustmentsto') !== '') {
      $this->criteria['range']['post_adjustment'] = array(
        checknull($this->form_state->getValue('postadjustmentsfrom')),
        checknull($this->form_state->getValue('postadjustmentsto')),
      );
    }
    if ($this->form_state->getValue('accruedexpensefrom') !== '' || $this->form_state->getValue('accruedexpenseto') !== '') {
      $this->criteria['range']['accrued_expense'] = array(
        checknull($this->form_state->getValue('accruedexpensefrom')),
        checknull($this->form_state->getValue('accruedexpenseto')),
      );
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state){
    $adoptedfrom = $form_state->getValue('adoptedfrom');
    $adoptedto = $form_state->getValue('adoptedto');
    $currentfrom = $form_state->getValue('currentfrom');
    $currentto = $form_state->getValue('currentto');
    $preencfrom = $form_state->getValue('preencumberedfrom');
    $preencto = $form_state->getValue('preencumberedto');
    $encto = $form_state->getValue('encumberedto');
    $encfrom = $form_state->getValue('encumberedfrom');
    $cashfrom = $form_state->getValue('cashfrom');
    $cashto = $form_state->getValue('cashto');
    $postfrom = $form_state->getValue('postadjustmentsfrom');
    $postto = $form_state->getValue('postadjustmentsto');
    $accruedfrom = $form_state->getValue('accruedexpensefrom');
    $accruedto = $form_state->getValue('accruedexpenseto');
    $expensecolumns = $form_state->getValue('column_select_expense');

    // Adopted:
    if ($adoptedfrom && !is_numeric($adoptedfrom)) {
      $form_state->setErrorByName('adoptedfrom', t('Adopted Budget From value must be a number.'));
    }
    if ($adoptedto && !is_numeric($adoptedto)) {
      $form_state->setErrorByName('adoptedto', t('Adopted Budget To value must be a number.'));
    }
    if (is_numeric($adoptedfrom) && is_numeric($adoptedto) && $adoptedto < $adoptedfrom) {
      $form_state->setErrorByName('adoptedto', t('Invalid range for Adopted Budget.'));
    }
    // Current:
    if ($currentfrom && !is_numeric($currentfrom)) {
      $form_state->setErrorByName('currentfrom', t('Modified Budget From value must be a number.'));
    }
    if ($currentto && !is_numeric($currentto)) {
      $form_state->setErrorByName('currentto', t('Modified Budget To value must be a number.'));
    }
    if (is_numeric($currentfrom) && is_numeric($currentto) && $currentto < $currentfrom) {
      $form_state->setErrorByName('currentto', t('Invalid range for Modified Budget.'));
    }
    // Preencumbered:
    if ($preencfrom && !is_numeric($preencfrom)) {
      $form_state->setErrorByName('preencumberedfrom', t('Pre-encumbered From value must be a number.'));
    }
    if ($preencto && !is_numeric($preencto)) {
      $form_state->setErrorByName('preencumberedto', t('Pre-encumbered To value must be a number.'));
    }
    if (is_numeric($preencfrom) && is_numeric($preencto) && $preencto < $preencfrom) {
      $form_state->setErrorByName('preencumberedto', t('Invalid range for Pre-encumbered.'));
    }
    // Encumbered:
    if ($encfrom && !is_numeric($encfrom)) {
      $form_state->setErrorByName('encumberedfrom', t('Encumbered From value must be a number.'));
    }
    if ($encto && !is_numeric($encto)) {
      $form_state->setErrorByName('encumberedto', t('Encumbered To value must be a number.'));
    }
    if (is_numeric($encfrom) && is_numeric($encto) && $encto < $encfrom) {
      $form_state->setErrorByName('encumberedto', t('Invalid range for Encumbered.'));
    }
    // Cash:
    if ($cashfrom && !is_numeric($cashfrom)) {
      $form_state->setErrorByName('cashfrom', t('Cash Expense From value must be a number.'));
    }
    if ($cashto && !is_numeric($cashto)) {
      $form_state->setErrorByName('cashto', t('Cash Expense To value must be a number.'));
    }
    if (is_numeric($cashfrom) && is_numeric($cashto) && $cashto < $cashfrom) {
      $form_state->setErrorByName('cashto', t('Invalid range for Cash Expense.'));
    }
    // Post Adjustments:
    if ($postfrom && !is_numeric($postfrom)) {
      $form_state->setErrorByName('postadjustmentsfrom', t('Post Adjustment From value must be a number.'));
    }
    if ($postto && !is_numeric($postto)) {
      $form_state->setErrorByName('postadjustmentsto', t('Post Adjustment To value must be a number.'));
    }
    if (is_numeric($postfrom) && is_numeric($postto) && $postto < $postfrom) {
      $form_state->setErrorByName('postadjustmentsto', t('Invalid range for Post Adjustment.'));
    }
    // Accrued Expense:
    if ($accruedfrom && !is_numeric($accruedfrom)) {
      $form_state->setErrorByName('accruedexpensefrom', t('Accrued Expense From value must be a number.'));
    }
    if ($accruedto && !is_numeric($accruedto)) {
      $form_state->setErrorByName('accruedexpenseto', t('Accrued Expense To value must be a number.'));
    }
    if (is_numeric($accruedfrom) && is_numeric($accruedto) && $accruedto < $accruedfrom) {
      $form_state->setErrorByName('accruedexpenseto', t('Invalid range for Accrued Expense.'));
    }
    // Columns:
    if (empty(array_filter($expensecolumns))) {
      $form_state->setErrorByName('column_select_expense', t('You must select at least one column.'));
    }

    //Set the hidden filed values on Budget form
      $form_state->setValue(['complete form','dept_hidden','#value'], $form_state->getValue(['values','dept']));
      $form_state->setValue(['complete form','expense_category_hidden','#value'], $form_state->getValue('expense_category'));
  }
}
