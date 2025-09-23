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

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;

/**
 * Constructs the URL for Revenue transactions based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @param null $dataSource
 * @return string
 */
function _checkbook_advanced_search_revenue_submit($form, &$form_state, $dataSource = NULL){
  $filterDimension = $dataSource . '_revenue';
  if($dataSource == Datasource::NYCHA){
    $redirect_url = _checkbook_advanced_search_nycha_revenue_submit($form, $form_state, $filterDimension);
    return $redirect_url;
  }else{
    $redirect_url = "revenue/transactions";
  }

  $budget_fiscal_year = _checkbook_get_selected_value($form_state, $filterDimension, 'budget_fiscal_year');
  $agency = _checkbook_get_selected_value($form_state, $filterDimension, 'agency');
  $revenue_category = _checkbook_get_selected_value($form_state, $filterDimension, 'revenue_category');
  $revenue_source = _checkbook_get_selected_value($form_state, $filterDimension, 'revenue_source');
  $conditional_category = _checkbook_get_selected_value($form_state, $filterDimension, 'conditional_categories');
  $conditional_category = ($conditional_category == "Select Event") ? 0 : $conditional_category;
  $revenue_source_exact = _checkbook_get_selected_value($form_state, $filterDimension, 'revenue_source_exact');
  $adopted_from = _checkbook_get_selected_value($form_state, $filterDimension, 'adopted_budget_from');
  $adopted_to = _checkbook_get_selected_value($form_state, $filterDimension, 'adopted_budget_to');
  $recognized_from = _checkbook_get_selected_value($form_state, $filterDimension, 'recognized_from');
  $recognized_to = _checkbook_get_selected_value($form_state, $filterDimension, 'recognized_to');
  $fiscal_year = _checkbook_get_selected_value($form_state, $filterDimension, 'fiscal_year');
  $funding_class = _checkbook_get_selected_value($form_state, $filterDimension, 'funding_class');
  $revenue_class = _checkbook_get_selected_value($form_state, $filterDimension, 'revenue_class');
  $revenue_class_exact = _checkbook_get_selected_value($form_state, $filterDimension, 'revenue_class_exact');
  $fund_class = _checkbook_get_selected_value($form_state, $filterDimension, 'fund_class');
  $modified_from = _checkbook_get_selected_value($form_state, $filterDimension, 'modified_from');
  $modified_to = _checkbook_get_selected_value($form_state, $filterDimension, 'modified_to');

  if (!empty($agency)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($agency, 'agency');
  }
  if (!empty($fiscal_year)) {
    if (strtolower($fiscal_year) != 'all fiscal years')
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($fiscal_year, 'fy');
  }
  if (!empty($budget_fiscal_year)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_fiscal_year, 'year');
  }
  if (!empty($fund_class)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($fund_class, 'fundcls');
  }
  if (!empty($revenue_category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_category, 'revcat');
  }
  if (!empty($revenue_class)) {
    if ($revenue_class == $revenue_class_exact) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_class, 'revclsnm_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_class, 'revclsnm');
    }
  }

  if (!empty($revenue_source)) {
    if ($revenue_source == $revenue_source_exact) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_source, 'revsrcnm_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_source, 'revsrcnm');
    }
  }
  if(!empty($conditional_category)){
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($conditional_category, 'cevent');
  }
  if (!empty($funding_class)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($funding_class, 'fundsrccode');
  }

  //URL for ranges
  $adopted_range_array = array('from' => $adopted_from, 'to' => $adopted_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($adopted_range_array, 'adopted_amt', TRUE);

  $recognized_range_array = array('from' => $recognized_from, 'to' => $recognized_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($recognized_range_array, 'amt', TRUE);

  $modified_range_array = array('from' => $modified_from, 'to' => $modified_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($modified_range_array, 'modified_amt', TRUE);


  return $redirect_url;
}

/**
 * Constructs the URL for Revenue transactions based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @param $filterDimension
 * @return string
 */
function _checkbook_advanced_search_nycha_revenue_submit($form, $form_state, $filterDimension){
  $redirectUrl = 'nycha_revenue/search/transactions/datasource/checkbook_nycha';

  $agencyId = Datasource::getNYCHAId();
  $redirectUrl .= (!empty($agencyId))? _checkbook_advanced_search_generate_redirect_url($agencyId, 'agency') : "";

  $budget_fiscal_year = _checkbook_get_selected_value($form_state, $filterDimension, 'budget_fiscal_year');
  $redirectUrl .= (!empty($budget_fiscal_year))? _checkbook_advanced_search_generate_redirect_url($budget_fiscal_year, 'year') : "";

  $expenseCategory = _checkbook_get_selected_value($form_state, $filterDimension, 'expense_category');
  $expenseCategory = ($expenseCategory == "Select Expense Category") ? 0 : $expenseCategory;
  $redirectUrl .= (!empty($expenseCategory))? _checkbook_advanced_search_generate_redirect_url($expenseCategory, 'expcategory') : "";

  $responsibilityCenter = _checkbook_get_selected_value($form_state, $filterDimension, 'responsibility_center');
  $responsibilityCenter = ($responsibilityCenter == "Select Responsibility Center") ? 0 : $responsibilityCenter;
  $redirectUrl .= (!empty($responsibilityCenter))? _checkbook_advanced_search_generate_redirect_url($responsibilityCenter, 'respcenter') : "";

  $fundSrc = _checkbook_get_selected_value($form_state, $filterDimension, 'fundsrc');
  $fundSrc = ($fundSrc == "Select Funding Source") ? 0 : $fundSrc;
  $redirectUrl .= (!empty($fundSrc))? _checkbook_advanced_search_generate_redirect_url($fundSrc, 'fundsrc') : "";

  $program = _checkbook_get_selected_value($form_state, $filterDimension, 'program');
  $program = ($program == "Select Program") ? 0 : $program;
  $redirectUrl .= (!empty($program))? _checkbook_advanced_search_generate_redirect_url($program, 'program') : "";

  $project = _checkbook_get_selected_value($form_state, $filterDimension, 'project');
  $project = ($project == "Select Project") ? 0 : $project;
  $redirectUrl .= (!empty($project))? _checkbook_advanced_search_generate_redirect_url($project, 'project') : "";

  $budgetType = _checkbook_get_selected_value($form_state, $filterDimension, 'budget_type');
  $budgetType = ($budgetType == "Select Budget Type") ? 0 : $budgetType;
  $redirectUrl .= (!empty($budgetType))? _checkbook_advanced_search_generate_redirect_url($budgetType, 'bdgtyp') : "";

  $budgetName = _checkbook_get_selected_value($form_state, $filterDimension, 'nycha_budget_name');
  $budgetName = ($budgetName == "Select Budget Name") ? 0 : $budgetName;
  $redirectUrl .= (!empty($budgetName))? _checkbook_advanced_search_generate_redirect_url($budgetName, 'bdgnm') : "";

  $revenue_class = _checkbook_get_selected_value($form_state, $filterDimension, 'revenue_class');
  $revenue_class_exact = _checkbook_get_selected_value($form_state, $filterDimension, 'revenue_class_exact');
  if (!empty($revenue_class)) {
    if ($revenue_class == $revenue_class_exact) {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($revenue_class, 'revclass_nm_exact');
    } else {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($revenue_class, 'revclass_nm');
    }
  }

  $revenue_cat = _checkbook_get_selected_value($form_state, $filterDimension, 'nycha_revenue_category');
  $revenue_cat_exact = _checkbook_get_selected_value($form_state, $filterDimension, 'nycha_revenue_category_exact');
  if (!empty($revenue_cat)) {
    if ($revenue_cat == $revenue_cat_exact) {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($revenue_cat, 'revcat_nm_exact');
    } else {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($revenue_cat, 'revcat_nm');
    }
  }

  //URL for ranges
  $adopted_from = _checkbook_get_selected_value($form_state, $filterDimension, 'adopted_budget_from');
  $adopted_to = _checkbook_get_selected_value($form_state, $filterDimension, 'adopted_budget_to');
  $adopted_range_array = array('from' => $adopted_from, 'to' => $adopted_to, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($adopted_range_array, 'adopted_amt', TRUE);

  $modified_from = _checkbook_get_selected_value($form_state, $filterDimension, 'modified_from');
  $modified_to = _checkbook_get_selected_value($form_state, $filterDimension, 'modified_to');
  $modified_range_array = array('from' => $modified_from, 'to' => $modified_to, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($modified_range_array, 'modified_amt', TRUE);

  $recognized_from = _checkbook_get_selected_value($form_state, $filterDimension, 'recognized_from');
  $recognized_to = _checkbook_get_selected_value($form_state, $filterDimension, 'recognized_to');
  $recognized_range_array = array('from' => $recognized_from, 'to' => $recognized_to, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($recognized_range_array, 'recognized_amt', TRUE);

  $remaining_from = _checkbook_get_selected_value($form_state, $filterDimension, 'remaining_from');
  $remaining_to = _checkbook_get_selected_value($form_state, $filterDimension, 'remaining_to');
  $remaining_range_array = array('from' => $remaining_from, 'to' => $remaining_to, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($remaining_range_array, 'remaining_amt', TRUE);

  return $redirectUrl;
}
