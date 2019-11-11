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

/**
 * Constructs the URL for Budget transactions based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @return string
 */
function _checkbook_advanced_search_budget_submit($form, &$form_state)
{
  $budget_agency = trim($form['budget']['budget_agencies']['#value']);
  $budget_department = trim($form_state['input']['budget_department']);
  $budget_expense_category = trim($form_state['input']['budget_expense_category']);
  $budget_budget_code = trim($form['budget']['budget_budget_code']['#value']);
  $budget_budget_name = trim($form['budget']['budget_budget_name']['#value']);
  $budget_fiscal_year = trim($form['budget']['budget_fiscal_year']['#value']);
  $budget_adopted_budget_from = trim($form['budget']['budget_adopted_budget_from']['#value']);
  $budget_adopted_budget_to = trim($form['budget']['budget_adopted_budget_to']['#value']);
  $budget_current_modified_from = trim($form['budget']['budget_current_modified_from']['#value']);
  $budget_current_modified_to = trim($form['budget']['budget_current_modified_to']['#value']);
  $budget_pre_encumbered_from = trim($form['budget']['budget_pre_encumbered_from']['#value']);
  $budget_pre_encumbered_to = trim($form['budget']['budget_pre_encumbered_to']['#value']);
  $budget_encumbered_from = trim($form['budget']['budget_encumbered_from']['#value']);
  $budget_encumbered_to = trim($form['budget']['budget_encumbered_to']['#value']);
  $budget_accrued_expense_from = trim($form['budget']['budget_accrued_expense_from']['#value']);
  $budget_accrued_expense_to = trim($form['budget']['budget_accrued_expense_to']['#value']);
  $budget_cash_expense_from = trim($form['budget']['budget_cash_expense_from']['#value']);
  $budget_cash_expense_to = trim($form['budget']['budget_cash_expense_to']['#value']);
  $budget_post_adjustments_from = trim($form['budget']['budget_post_adjustments_from']['#value']);
  $budget_post_adjustments_to = trim($form['budget']['budget_post_adjustments_to']['#value']);
  $redirect_url = "budget/transactions";
  if (!empty($budget_fiscal_year)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_fiscal_year, 'year');
  }
  if (!empty($budget_expense_category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_expense_category, 'expcategorynm');
  }
  if (!empty($budget_agency)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_agency, 'agency');
  }
  if (!empty($budget_department)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_department, 'deptnm');
  }
  if (!empty($budget_budget_code)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_budget_code, 'bdgcode_code');
  }
  if (!empty($budget_budget_name)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_budget_name, 'bdgcodenm');
  }


  //URL for ranges
  $budget_adopted_budget_range_array = array('from' => $budget_adopted_budget_from, 'to' => $budget_adopted_budget_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_adopted_budget_range_array, 'adpamt', TRUE);

  $budget_pre_encumbered_range_array = array('from' => $budget_pre_encumbered_from, 'to' => $budget_pre_encumbered_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_pre_encumbered_range_array, 'preamt', TRUE);

  $budget_encumbered_range_array = array('from' => $budget_encumbered_from, 'to' => $budget_encumbered_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_encumbered_range_array, 'encamt', TRUE);

  $budget_cash_expense_range_array = array('from' => $budget_cash_expense_from, 'to' => $budget_cash_expense_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_cash_expense_range_array, 'cshamt', TRUE);

  $budget_post_adjustments_range_array = array('from' => $budget_post_adjustments_from, 'to' => $budget_post_adjustments_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_post_adjustments_range_array, 'postamt', TRUE);

  $budget_accrued_expense_range_array = array('from' => $budget_accrued_expense_from, 'to' => $budget_accrued_expense_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_accrued_expense_range_array, 'acramt', TRUE);

  $budget_current_modified_range_array = array('from' => $budget_current_modified_from, 'to' => $budget_current_modified_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_current_modified_range_array, 'curamt', TRUE);

  return $redirect_url;
}

