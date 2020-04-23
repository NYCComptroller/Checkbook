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
    $redirect_url =   _checkbook_advanced_search_nycha_budget_submit($form, $form_state);
    return $redirect_url;
  }
  $budget_agency = _checkbook_get_selected_value($form, $filter_dimension, 'agency');
  $budget_department = _checkbook_get_selected_value($form, $filter_dimension, 'department');
  $budget_expense_category = _checkbook_get_selected_value($form, $filter_dimension, 'expense_category');
  $budget_budget_code = _checkbook_get_selected_value($form, $filter_dimension, 'budget_code');
  $budget_budget_name = _checkbook_get_selected_value($form, $filter_dimension, 'budget_name');
  $budget_fiscal_year = _checkbook_get_selected_value($form, $filter_dimension, 'year');
  $budget_adopted_budget_from = _checkbook_get_selected_value($form, $filter_dimension, 'adopted_from');
  $budget_adopted_budget_to = _checkbook_get_selected_value($form, $filter_dimension, 'adopted_to');
  $budget_current_modified_from = _checkbook_get_selected_value($form, $filter_dimension, 'modified_from');
  $budget_current_modified_to = _checkbook_get_selected_value($form, $filter_dimension, 'modified_to');
  $budget_pre_encumbered_from = _checkbook_get_selected_value($form, $filter_dimension, 'pre_encumbered_from');
  $budget_pre_encumbered_to = _checkbook_get_selected_value($form, $filter_dimension, 'pre_encumbered_to');
  $budget_encumbered_from = _checkbook_get_selected_value($form, $filter_dimension, 'encumbered_from');
  $budget_encumbered_to = _checkbook_get_selected_value($form, $filter_dimension, 'encumbered_to');
  $budget_accrued_expense_from = _checkbook_get_selected_value($form, $filter_dimension, 'accrued_expense_from');
  $budget_accrued_expense_to = _checkbook_get_selected_value($form, $filter_dimension, 'accrued_expense_to');
  $budget_cash_expense_from = _checkbook_get_selected_value($form, $filter_dimension, 'cash_payments_from');
  $budget_cash_expense_to = _checkbook_get_selected_value($form, $filter_dimension, 'cash_payments_to');
  $budget_post_adjustments_from = _checkbook_get_selected_value($form, $filter_dimension, 'post_adjustments_from');
  $budget_post_adjustments_to = _checkbook_get_selected_value($form, $filter_dimension, 'post_adjustments_to');
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


/**
 * Constructs the URL for Nycha Budget based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @return string
 */
function _checkbook_advanced_search_nycha_budget_submit($form, &$form_state){
  $filter_dimension =  'checkbook_nycha_contracts';
  $nycha_agency_id = Datasource::getNYCHAId();
  $redirect_url = "";
  if(!empty($nycha_agency_id)){
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($nycha_agency_id, 'agency');
  }
  $redirect_url .= '/datasource/checkbook_nycha';
  return $redirect_url;
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

