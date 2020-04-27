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
 * @param $data_source
 * @return string
 */
function _checkbook_advanced_search_budget_submit($form, &$form_state, $data_source = DataSource::CITYWIDE){
  $filter_dimension = $data_source . '_budget';
  if( $data_source == Datasource::NYCHA){
    $redirect_url =   _checkbook_advanced_search_nycha_budget_submit($form, $form_state, $filter_dimension);
    return $redirect_url;
  }
  $redirect_url = "budget/transactions";
  $agency = _checkbook_get_selected_value($form, $filter_dimension, 'agency');
  $department = _checkbook_get_selected_value($form, $filter_dimension, 'department');
  $department = ($department == "Select Department") ? 0 : $department;
  $expense_category = _checkbook_get_selected_value($form, $filter_dimension, 'expense_category');
  $expense_category = ($expense_category == "Select Expense Category") ? 0 : $expense_category;
  $budget_code = _checkbook_get_selected_value($form, $filter_dimension, 'budget_code');
  $budget_name = _checkbook_get_selected_value($form, $filter_dimension, 'budget_name');
  $fiscal_year = _checkbook_get_selected_value($form, $filter_dimension, 'year');
  $adopted_budget_from = _checkbook_get_selected_value($form, $filter_dimension, 'adopted_budget_from');
  $adopted_budget_to = _checkbook_get_selected_value($form, $filter_dimension, 'adopted_budget_to');
  $current_modified_from = _checkbook_get_selected_value($form, $filter_dimension, 'modified_from');
  $current_modified_to = _checkbook_get_selected_value($form, $filter_dimension, 'modified_to');
  $pre_encumbered_from = _checkbook_get_selected_value($form, $filter_dimension, 'pre_encumbered_from');
  $pre_encumbered_to = _checkbook_get_selected_value($form, $filter_dimension, 'pre_encumbered_to');
  $encumbered_from = _checkbook_get_selected_value($form, $filter_dimension, 'encumbered_from');
  $encumbered_to = _checkbook_get_selected_value($form, $filter_dimension, 'encumbered_to');
  $accrued_expense_from = _checkbook_get_selected_value($form, $filter_dimension, 'accrued_expense_from');
  $accrued_expense_to = _checkbook_get_selected_value($form, $filter_dimension, 'accrued_expense_to');
  $cash_expense_from = _checkbook_get_selected_value($form, $filter_dimension, 'cash_payments_from');
  $cash_expense_to = _checkbook_get_selected_value($form, $filter_dimension, 'cash_payments_to');
  $post_adjustments_from = _checkbook_get_selected_value($form, $filter_dimension, 'post_adjustments_from');
  $post_adjustments_to = _checkbook_get_selected_value($form, $filter_dimension, 'post_adjustments_to');
  if (!empty($fiscal_year)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($fiscal_year, 'year');
  }
  if (!empty($expense_category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($expense_category, 'expcategorynm');
  }
  if (!empty($agency)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($agency, 'agency');
  }
  if (!empty($department)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($department, 'deptnm');
  }
  if (!empty($budget_code)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_code, 'bdgcode_code');
  }
  if (!empty($budget_name)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_name, 'bdgcodenm');
  }


  //URL for ranges
  $adopted_budget_range_array = array('from' => $adopted_budget_from, 'to' => $adopted_budget_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($adopted_budget_range_array, 'adpamt', TRUE);

  $pre_encumbered_range_array = array('from' => $pre_encumbered_from, 'to' => $pre_encumbered_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($pre_encumbered_range_array, 'preamt', TRUE);

  $encumbered_range_array = array('from' => $encumbered_from, 'to' => $encumbered_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($encumbered_range_array, 'encamt', TRUE);

  $cash_expense_range_array = array('from' => $cash_expense_from, 'to' => $cash_expense_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($cash_expense_range_array, 'cshamt', TRUE);

  $post_adjustments_range_array = array('from' => $post_adjustments_from, 'to' => $post_adjustments_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($post_adjustments_range_array, 'postamt', TRUE);

  $accrued_expense_range_array = array('from' => $accrued_expense_from, 'to' => $accrued_expense_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($accrued_expense_range_array, 'acramt', TRUE);

  $current_modified_range_array = array('from' => $current_modified_from, 'to' => $current_modified_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($current_modified_range_array, 'curamt', TRUE);

  return $redirect_url;
}


/**
 * Constructs the URL for Nycha Budget based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @param $filterDimension
 * @return string
 */
function _checkbook_advanced_search_nycha_budget_submit($form, &$form_state, $filterDimension){
  $redirectUrl = 'nycha_budget/search/transactions/datasource/checkbook_nycha';

  $agencyId = Datasource::getNYCHAId();
  $year = _checkbook_get_selected_value($form, $filterDimension, 'year');

  $expenseCategory = _checkbook_get_selected_value($form, $filterDimension, 'expense_category');
  $expenseCategory = ($expenseCategory == "Select Expense Category") ? 0 : $expenseCategory;

  $responsibilityCenter = _checkbook_get_selected_value($form, $filterDimension, 'responsibility_center');
  $responsibilityCenter = ($responsibilityCenter == "Select Responsibility Center") ? 0 : $responsibilityCenter;

  $fundSrc = _checkbook_get_selected_value($form, $filterDimension, 'fundsrc');
  $fundSrc = ($fundSrc == "Select Funding Source") ? 0 : $fundSrc;

  $program = _checkbook_get_selected_value($form, $filterDimension, 'program');
  $program = ($program == "Select Program") ? 0 : $program;

  $project = _checkbook_get_selected_value($form, $filterDimension, 'project');
  $project = ($project == "Select Project") ? 0 : $project;

  $budgetType = _checkbook_get_selected_value($form, $filterDimension, 'budget_type');
  $budgetType = ($budgetType == "Select Budget Type") ? 0 : $budgetType;

  $budgetName = _checkbook_get_selected_value($form, $filterDimension, 'nycha_budget_name');
  $budgetName = ($budgetName == "Select Budget Name") ? 0 : $budgetName;

  $adoptedFrom = _checkbook_get_selected_value($form, $filterDimension, 'adopted_budget_from');
  $adoptedTo = _checkbook_get_selected_value($form, $filterDimension, 'adopted_budget_to');

  $modifiedFrom = _checkbook_get_selected_value($form, $filterDimension, 'modified_from');
  $modifiedTo = _checkbook_get_selected_value($form, $filterDimension, 'modified_to');

  $committedFrom = _checkbook_get_selected_value($form, $filterDimension, 'committed_from');
  $committedTo = _checkbook_get_selected_value($form, $filterDimension, 'committed_to');

  $encumberedFrom = _checkbook_get_selected_value($form, $filterDimension, 'encumbered_from');
  $encumberedTo = _checkbook_get_selected_value($form, $filterDimension, 'encumbered_to');

  $actualFrom = _checkbook_get_selected_value($form, $filterDimension, 'actual_from');
  $actualTo = _checkbook_get_selected_value($form, $filterDimension, 'actual_to');

  $redirectUrl .= (!empty($agencyId))? _checkbook_advanced_search_generate_redirect_url($agencyId, 'agency') : "";
  $redirectUrl .= (!empty($year))? _checkbook_advanced_search_generate_redirect_url($year, 'year') : "";
  $redirectUrl .= (!empty($expenseCategory))? _checkbook_advanced_search_generate_redirect_url($expenseCategory, 'expcategory') : "";
  $redirectUrl .= (!empty($responsibilityCenter))? _checkbook_advanced_search_generate_redirect_url($responsibilityCenter, 'resp_center') : "";
  $redirectUrl .= (!empty($fundSrc))? _checkbook_advanced_search_generate_redirect_url($fundSrc, 'fundsrc') : "";
  $redirectUrl .= (!empty($program))? _checkbook_advanced_search_generate_redirect_url($program, 'prgm') : "";
  $redirectUrl .= (!empty($project))? _checkbook_advanced_search_generate_redirect_url($project, 'proj') : "";
  $redirectUrl .= (!empty($budgetType))? _checkbook_advanced_search_generate_redirect_url($budgetType, 'bdgtyp') : "";
  $redirectUrl .= (!empty($budgetName))? _checkbook_advanced_search_generate_redirect_url($budgetName, 'bdgnm') : "";

  //URL for ranges
  $adoptedRange = array('from' => $adoptedFrom, 'to' => $adoptedTo, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($adoptedRange, 'adpamt', TRUE);

  $modifiedRange = array('from' => $modifiedFrom, 'to' => $modifiedTo, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($modifiedRange, 'modamtr', TRUE);

  $committedRange = array('from' => $committedFrom, 'to' => $committedTo, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($committedRange, 'comamtr', TRUE);

  $encumberedRange = array('from' => $encumberedFrom, 'to' => $encumberedTo, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($encumberedRange, 'adpamt', TRUE);

  $actualRange = array('from' => $actualFrom, 'to' => $actualTo, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($actualRange, 'adpamt', TRUE);

  return $redirectUrl;
}

/**
 * Returns input value from drupal form
 *
 * @param $form
 * @param $filter_dimension
 * @param $field_name
 * @return string
 */
function _checkbook_get_selected_value($form, $filter_dimension, $field_name){
  return trim($form[$filter_dimension][$filter_dimension . '_' .$field_name]['#value']);
}

