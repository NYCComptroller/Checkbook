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
 * Constructs the URL for Spending transactions based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @param string $data_source
 * @return string
 */
function _checkbook_advanced_search_spending_submit($form, &$form_state, $data_source = Datasource::CITYWIDE)
{
  $filter_dimension = $data_source . '_spending';
  $spending_agencies = _checkbook_get_selected_value($form_state, $filter_dimension, 'agency');

  if($data_source == Datasource::NYCHA){
    return generateNYCHASpendingSearchUrl($form, $form_state);
  }
  $spending_payee_name = _checkbook_get_selected_value($form_state, $filter_dimension, 'payee_name');

  //@ToDo: need to add back exact field
  $spending_payee_name_exact = _checkbook_get_selected_value($form_state, $filter_dimension, 'payee_name_exact');
  $spending_contract_num = _checkbook_get_selected_value($form_state, $filter_dimension, 'contract_num');
  $spending_contract_num_exact = _checkbook_get_selected_value($form_state, $filter_dimension, 'contract_num_exact');
  $spending_document_id = _checkbook_get_selected_value($form_state, $filter_dimension, 'document_id');
  $spending_document_id_exact = _checkbook_get_selected_value($form_state, $filter_dimension, 'document_id_exact');

  $spending_expense_category = _checkbook_get_selected_value($form_state, $filter_dimension, 'expense_category');
  $spending_expense_category = (empty($spending_expense_category) || in_array($spending_expense_category, ["Select Expense Category", "$"])) ? 0 : $spending_expense_category;

  $spending_check_amount_from = _checkbook_get_selected_value($form_state, $filter_dimension, 'check_amount_from');
  $spending_check_amount_to = _checkbook_get_selected_value($form_state, $filter_dimension, 'check_amount_to');
  $spending_issue_date_from = _checkbook_get_selected_value($form_state, $filter_dimension, 'issue_date_from');
  $spending_issue_date_to = _checkbook_get_selected_value($form_state, $filter_dimension, 'issue_date_to');
  $spending_fund_class = _checkbook_get_selected_value($form_state, $filter_dimension, 'fund_class');

  $spending_department = _checkbook_get_selected_value($form_state, $filter_dimension, 'department');
  $spending_department = $spending_department == "Select Department" ? 0 : $spending_department;

  $spending_budget_code = _checkbook_get_selected_value($form_state, $filter_dimension, 'budget_code');
  $spending_capital_project = _checkbook_get_selected_value($form_state, $filter_dimension, 'capital_project');
  $spending_capital_project_exact = _checkbook_get_selected_value($form_state, $filter_dimension, 'capital_project_exact');
  $spending_expense_type = _checkbook_get_selected_value($form_state, $filter_dimension, 'expense_type');
  $spending_fiscal_year = _checkbook_get_selected_value($form_state, $filter_dimension, 'fiscal_year');
  $spending_date_filter = _checkbook_get_selected_value($form_state, $filter_dimension, 'date_filter');

  $spending_entity_contract_number = _checkbook_get_selected_value($form_state, $filter_dimension, 'entity_contract_number');
  $spending_entity_contract_number_exact = _checkbook_get_selected_value($form_state, $filter_dimension, 'entity_contract_number_exact');
  $spending_commodity_line = _checkbook_get_selected_value($form_state, $filter_dimension, 'commodity_line');
  $spending_commodity_line_exact = _checkbook_get_selected_value($form_state, $filter_dimension, 'commodity_line_exact');
  $spending_budget_name = _checkbook_get_selected_value($form_state, $filter_dimension, 'budget_name_text');
  $spending_budget_name_exact = _checkbook_get_selected_value($form_state, $filter_dimension, 'budget_name_text_exact');

  $spending_mwbe_category = _checkbook_get_selected_value($form_state, $filter_dimension, 'mwbe_category');
  $spending_mwbe_category = $spending_mwbe_category == "Select Category" ? 0 : $spending_mwbe_category;
  $spending_conditional_category = _checkbook_get_selected_value($form_state, $filter_dimension, 'conditional_categories');
  $spending_industry_type_id = _checkbook_get_selected_value($form_state, $filter_dimension, 'industry');
  $spending_industry_type_id = $spending_industry_type_id == "Select Industry" ? 0 : $spending_industry_type_id;

  if ($spending_fiscal_year == 'fy~all') {
    $spending_fiscal_year = null;
  }

  $redirect_url = '/spending/search/transactions';

  //append data source if not default (checkbook)
  $redirect_url .= ($data_source != Datasource::CITYWIDE) ? '/datasource/' . $data_source : "";
  $redirect_url .= ($data_source == Datasource::OGE) ? '/agency/'. Datasource::getEDCId() : "";

  if (!empty($spending_mwbe_category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_mwbe_category, 'mwbe');
  }
  if(!empty($spending_conditional_category) && strtolower($spending_conditional_category) != 'select event'){
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_conditional_category, 'cevent');
  }
  if (!empty($spending_industry_type_id)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_industry_type_id, 'industry');
  }
  if (!empty($spending_fiscal_year) && $spending_date_filter == 0) {
    $redirect_url .= _checkbook_advanced_search_year_arg($spending_fiscal_year, 'year');
  }
  if (!empty($spending_payee_name)) {
    if ($spending_payee_name_exact == $spending_payee_name) {
      $redirect_url .= _checkbook_advanced_search_encode_url(_checkbook_advanced_search_generate_redirect_url($spending_payee_name, 'vendornm_exact'));
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_payee_name, 'vendornm');
    }
  }
  if (!empty($spending_contract_num)) {
    if ($spending_contract_num_exact == $spending_contract_num) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_contract_num, 'contnum_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_contract_num, 'contnum');
    }
  }
  if (!empty($spending_document_id)) {
    if ($spending_document_id_exact == $spending_document_id) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_document_id, 'expdoc_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_document_id, 'expdoc');
    }
  }
  if (!empty($spending_agencies)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_agencies, 'agency');
  }
  if (!empty($spending_expense_category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_expense_category, 'expcategorycode');
  }
  if (!empty($spending_fund_class)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_fund_class, 'fundcls');
  }
  if (!empty($spending_department)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_department, 'dept');
  }
  if (!empty($spending_budget_code)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_budget_code, 'bdgcodenm');
  }
  if (!empty($spending_capital_project)) {
    if ($spending_capital_project_exact == $spending_capital_project) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_capital_project, 'captprj_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_capital_project, 'captprj');
    }
  }
  if (!empty($spending_expense_type)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_expense_type, 'category');
  }

  if (!empty($spending_entity_contract_number)) {
    if ($spending_entity_contract_number_exact == $spending_entity_contract_number) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_entity_contract_number, 'entcontnum_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_entity_contract_number, 'entcontnum');
    }
  }
  if (!empty($spending_commodity_line)) {
    if ($spending_commodity_line_exact == $spending_commodity_line) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_commodity_line, 'comline_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_commodity_line, 'comline');
    }
  }
  if (!empty($spending_budget_name)) {
    if ($spending_budget_name_exact == $spending_budget_name) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_budget_name, 'budname_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_budget_name, 'budname');
    }
  }

  //URL for range parameters, need to specify whether the range is an amount or a date
  $spending_check_amount_range_array = array('from' => $spending_check_amount_from, 'to' => $spending_check_amount_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_check_amount_range_array, 'checkamt', TRUE);

  if ($spending_date_filter == 1) {
    $spending_issue_date_range_array = array('from' => $spending_issue_date_from, 'to' => $spending_issue_date_to, 'type' => 'date');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_issue_date_range_array, 'chkdate', TRUE);
  }

  return $redirect_url;
}

function generateNYCHASpendingSearchUrl($form, $form_state){
  $filterDimension = 'checkbook_nycha_spending';
  $redirectUrl = '/nycha_spending/search/transactions/datasource/' . Datasource::NYCHA.'/agency/'.Datasource::getNYCHAId();
  //Department
  $department = _checkbook_get_selected_value($form_state, $filterDimension, 'department');
  $redirectUrl .= ($department != "Select Department" && !empty($department)) ?  _checkbook_advanced_search_generate_redirect_url($department, 'dept'): "";
  //Expense Category
  $expenseCategory = _checkbook_get_selected_value($form_state, $filterDimension, 'expense_category');
  $redirectUrl .= ((!in_array($expenseCategory, ["Select Expense Category", "$"])) && ($expenseCategory === "0" || !empty($expenseCategory))) ?  _checkbook_advanced_search_generate_redirect_url($expenseCategory, 'expcategorycode'): "";
  //Spending Category
  $spendingCategory = _checkbook_get_selected_value($form_state, $filterDimension, 'expense_type');
  $redirectUrl .= !empty($spendingCategory) ? _checkbook_advanced_search_generate_redirect_url($spendingCategory, 'category') : "";
  //Industry
  $industry = _checkbook_get_selected_value($form_state, $filterDimension, 'industry');
  $redirectUrl .= (!empty($industry) && strtolower($industry) != 'select industry') ? _checkbook_advanced_search_generate_redirect_url($industry, 'industry') : "";
  //Funding Source
  $fundSrc = _checkbook_get_selected_value($form_state, $filterDimension, 'fundsrc');
  $redirectUrl .= (!empty($fundSrc) && strtolower($fundSrc) != 'select funding source') ? _checkbook_advanced_search_generate_redirect_url($fundSrc, 'fundsrc') : "";
  //Responsibility Center
  $respCenter = _checkbook_get_selected_value($form_state, $filterDimension, 'responsibility_center');
  $redirectUrl .= (!empty($respCenter) && strtolower($respCenter) != 'select responsibility center') ? _checkbook_advanced_search_generate_redirect_url($respCenter, 'resp_center') : "";
  //Vendor Name
  $vendorName = _checkbook_get_selected_value($form_state, $filterDimension, 'vendor_name');
  $vendorNameExact = _checkbook_get_selected_value($form_state, $filterDimension, 'vendor_name_exact');
  if (!empty($vendorName)) {
    if ($vendorNameExact == $vendorName) {
      $redirectUrl .= _checkbook_advanced_search_encode_url(_checkbook_advanced_search_generate_redirect_url($vendorName, 'vendornm_exact'));
    } else {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($vendorName, 'vendornm');
    }
  }
  //Check Amount
  $checkAmountFrom = _checkbook_get_selected_value($form_state, $filterDimension, 'check_amount_from');
  $checkAmountTo = _checkbook_get_selected_value($form_state, $filterDimension, 'check_amount_to');
  $checkAmountRangeArray = array('from' => $checkAmountFrom, 'to' => $checkAmountTo, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($checkAmountRangeArray, 'checkamt', TRUE);
  //Amount Spent
  $amountSpentFrom = _checkbook_get_selected_value($form_state, $filterDimension, 'amount_spent_from');
  $amountSpentTo = _checkbook_get_selected_value($form_state, $filterDimension, 'amount_spent_to');
  $amountSpentRangeArray = array('from' => $amountSpentFrom, 'to' => $amountSpentTo, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($amountSpentRangeArray, 'amount_spent', TRUE);
  //Purchase Order Type
  $nycha_po_type = _checkbook_get_selected_value($form_state, $filterDimension, 'purchase_order_type');
  $redirectUrl .= (!empty($nycha_po_type) && strtolower($nycha_po_type) != 'all') ? _checkbook_advanced_search_generate_redirect_url($nycha_po_type, 'agreement_type') : "";
  //Contract ID
  $contractId = _checkbook_get_selected_value($form_state, $filterDimension, 'contract_num');
  $contractId_exact = _checkbook_get_selected_value($form_state, $filterDimension, 'contract_num_exact');
  if (!empty($contractId)) {
    if ($contractId_exact == $contractId) {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($contractId, 'po_num_exact');
    } else {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($contractId, 'po_num');
    }
  }
  //Document ID
  $documentId = _checkbook_get_selected_value($form_state, $filterDimension, 'document_id');
  $documentIdExact = _checkbook_get_selected_value($form_state, $filterDimension, 'document_id_exact');
  if (!empty($documentId)) {
    if ($documentIdExact == $documentId) {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($documentId, 'doc_id_exact');
    } else {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($documentId, 'doc_id');
    }
  }
  //Date Filter
  $dateFilter = _checkbook_get_selected_value($form_state, $filterDimension, 'date_filter');
  //When Year is selected
  $yearId = _checkbook_get_selected_value($form_state, $filterDimension, 'fiscal_year');
  $redirectUrl .=  (!empty($yearId) && $dateFilter == 0 && $yearId != 'fy~all') ? _checkbook_advanced_search_year_arg($yearId, 'year') : "";
  //When Issue Date is selected
  $issueDateFrom = _checkbook_get_selected_value($form_state, $filterDimension, 'issue_date_from');
  $issueDateTo = _checkbook_get_selected_value($form_state, $filterDimension, 'issue_date_to');
  if ($dateFilter == 1) {
    $issueDateRangeArray = array('from' => $issueDateFrom, 'to' => $issueDateTo, 'type' => 'date');
    $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($issueDateRangeArray, 'issue_date', TRUE);
  }

  return $redirectUrl;
}

