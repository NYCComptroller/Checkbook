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
 * Constructs the URL for Spending transactions based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @param string $data_source
 * @return string
 */
function _checkbook_advanced_search_spending_submit($form, &$form_state, $data_source = DataSource::CITYWIDE)
{
  $filter_dimension = $data_source . '_spending';
  $spending_agencies = trim($form[$filter_dimension][$data_source . '_spending_agency']['#value']); //drop down
  if($data_source == DataSource::NYCHA){
    return generateNYCHASpendingSearchUrl($form);
  }
  $spending_payee_name = trim($form[$filter_dimension][$data_source . '_spending_payee_name']['#value']);
  $spending_payee_name_exact = trim($form[$filter_dimension][$data_source . '_spending_payee_name_exact']['#value']);
  $spending_contract_num = trim($form[$filter_dimension][$data_source . '_spending_contract_num']['#value']);
  $spending_contract_num_exact = trim($form[$filter_dimension][$data_source . '_spending_contract_num_exact']['#value']);
  $spending_document_id = trim($form[$filter_dimension][$data_source . '_spending_document_id']['#value']);
  $spending_document_id_exact = trim($form[$filter_dimension][$data_source . '_spending_document_id_exact']['#value']);

  $spending_expense_category = trim($form[$filter_dimension][$data_source . '_spending_expense_category']['#value']);
  $spending_expense_category = $spending_expense_category == "Select Expense Category" ? 0 : $spending_expense_category;

  $spending_check_amount_from = trim($form[$filter_dimension][$data_source . '_spending_check_amount_from']['#value']);
  $spending_check_amount_to = trim($form[$filter_dimension][$data_source . '_spending_check_amount_to']['#value']);
  $spending_issue_date_from = trim($form[$filter_dimension][$data_source . '_spending_issue_date_from']['#value']['date']);
  $spending_issue_date_to = trim($form[$filter_dimension][$data_source . '_spending_issue_date_to']['#value']['date']);
  $spending_fund_class = trim($form[$filter_dimension][$data_source . '_spending_fund_class']['#value']);

  $spending_department = trim($form[$filter_dimension][$data_source . '_spending_department']['#value']);
  $spending_department = $spending_department == "Select Department" ? 0 : $spending_department;

  $spending_budget_code = trim($form[$filter_dimension][$data_source . '_spending_budget_code']['#value']);
  $spending_capital_project = trim($form[$filter_dimension][$data_source . '_spending_capital_project']['#value']);
  $spending_capital_project_exact = trim($form[$filter_dimension][$data_source . '_spending_capital_project_exact']['#value']);
  $spending_expense_type = trim($form[$filter_dimension][$data_source . '_spending_expense_type']['#value']);
  $spending_fiscal_year = trim($form[$filter_dimension][$data_source . '_spending_fiscal_year']['#value']);
  $spending_date_filter = trim($form[$filter_dimension][$data_source . '_spending_date_filter']['#value']);

  $spending_entity_contract_number = trim($form[$filter_dimension][$data_source . '_spending_entity_contract_number']['#value']);
  $spending_entity_contract_number_exact = trim($form[$filter_dimension][$data_source . '_spending_entity_contract_number_exact']['#value']);
  $spending_commodity_line = trim($form[$filter_dimension][$data_source . '_spending_commodity_line']['#value']);
  $spending_commodity_line_exact = trim($form[$filter_dimension][$data_source . '_spending_commodity_line_exact']['#value']);
  $spending_budget_name = trim($form[$filter_dimension][$data_source . '_spending_budget_name']['#value']);
  $spending_budget_name_exact = trim($form[$filter_dimension][$data_source . '_spending_budget_name_exact']['#value']);

  $spending_mwbe_category = trim($form[$filter_dimension][$data_source . '_spending_mwbe_category']['#value']);
  $spending_mwbe_category = $spending_mwbe_category == "Select Category" ? 0 : $spending_mwbe_category;

  $spending_industry_type_id = trim($form[$filter_dimension][$data_source . '_spending_industry']['#value']);
  $spending_industry_type_id = $spending_industry_type_id == "Select Industry" ? 0 : $spending_industry_type_id;

  if ($spending_fiscal_year == 'fy~all') {
    $spending_fiscal_year = null;
  }

  $redirect_url = 'spending/search/transactions';
  //append data source if not default (checkbook)
  if($data_source != DataSource::CITYWIDE) $redirect_url .= '/datasource/' . $data_source;

  if (!empty($spending_mwbe_category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_mwbe_category, 'mwbe');
  }
  if (!empty($spending_industry_type_id)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_industry_type_id, 'industry');
  }
  if (!empty($spending_fiscal_year) && $spending_date_filter == 0) {
    $redirect_url .= _checkbook_advanced_search_year_arg($spending_fiscal_year, 'year');
  }
  if (!empty($spending_payee_name)) {
    if ($spending_payee_name_exact == $spending_payee_name) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_payee_name, 'vendornm_exact');
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
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_department, 'deptnm');
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

function generateNYCHASpendingSearchUrl($form){
  $filterDimension = 'checkbook_nycha_spending';
  $redirectUrl = 'nycha_spending/search/transactions/datasource/' . DataSource::NYCHA;
  //Department
  $department = trim($form[$filterDimension][$filterDimension  . '_department']['#value']);
  $redirectUrl .= ($department != "Select Department" && !empty($department)) ?  _checkbook_advanced_search_generate_redirect_url($department, 'deptnm'): "";
  //Expense Category
  $expenseCategory = trim($form[$filterDimension][$filterDimension . '_expense_category']['#value']);
  $redirectUrl .= ($expenseCategory != "Select Expense Category" && !empty($expenseCategory)) ?  _checkbook_advanced_search_generate_redirect_url($expenseCategory, 'expcategorycode'): "";
  //Spending Category
  $spendingCategory = trim($form[$filterDimension][$filterDimension . '_expense_type']['#value']);
  $redirectUrl .= !empty($spendingCategory) ? _checkbook_advanced_search_generate_redirect_url($spendingCategory, 'category') : "";
  //Industry
  $industry = trim($form[$filterDimension][$filterDimension . '_industry']['#value']);
  $redirectUrl .= (!empty($industry) && strtolower($industry) != 'select industry') ? _checkbook_advanced_search_generate_redirect_url($industry, 'industry') : "";
  //Funding Source
  $fundSrc = trim($form[$filterDimension][$filterDimension . '_fundsrc']['#value']);
  $redirectUrl .= (!empty($fundSrc) && strtolower($fundSrc) != 'select funding source') ? _checkbook_advanced_search_generate_redirect_url($fundSrc, 'fundsrc') : "";
  //Responsibility Center
  $respCenter = trim($form[$filterDimension][$filterDimension . '_responsibility_center']['#value']);
  $redirectUrl .= (!empty($respCenter) && strtolower($respCenter) != 'select responsibility center') ? _checkbook_advanced_search_generate_redirect_url($respCenter, 'resp_center') : "";
  //Vendor Name
  $vendorName = trim($form[$filterDimension][$filterDimension . '_vendor_name']['#value']);
  $vendorNameExact = trim($form[$filterDimension][$filterDimension . '_vendor_name_exact']['#value']);
  if (!empty($vendorName)) {
    if ($vendorNameExact == $vendorName) {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($vendorName, 'vendornm_exact');
    } else {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($vendorName, 'vendornm');
    }
  }
  //Check Amount
  $checkAmountFrom = trim($form[$filterDimension][$filterDimension. '_check_amount_from']['#value']);
  $checkAmountTo = trim($form[$filterDimension][$filterDimension. '_check_amount_to']['#value']);
  $checkAmountRangeArray = array('from' => $checkAmountFrom, 'to' => $checkAmountTo, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($checkAmountRangeArray, 'checkamt', TRUE);
  //Amount Spent
  $amountSpentFrom = trim($form[$filterDimension][$filterDimension. '_amount_spent_from']['#value']);
  $amountSpentTo = trim($form[$filterDimension][$filterDimension. '_amount_spent_to']['#value']);
  $amountSpentRangeArray = array('from' => $amountSpentFrom, 'to' => $amountSpentTo, 'type' => 'amount');
  $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($amountSpentRangeArray, 'amount_spent', TRUE);
  //Purchase Order Type
  $nycha_po_type = trim($form[$filterDimension][$filterDimension . '_purchase_order_type']['#value']);
  $redirectUrl .= (!empty($nycha_po_type) && strtolower($nycha_po_type) != 'all') ? _checkbook_advanced_search_generate_redirect_url($nycha_po_type, 'agreement_type') : "";
  //Contract ID
  $contractId = trim($form[$filterDimension][$filterDimension . '_contract_num']['#value']);
  $contractId_exact = trim($form[$filterDimension][$filterDimension . '_contract_num_exact']['#value']);
  if (!empty($contractId)) {
    if ($contractId_exact == $contractId) {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($contractId, 'po_num_exact');
    } else {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($contractId, 'po_num');
    }
  }
  //Document ID
  $documentId = trim($form[$filterDimension][$filterDimension . '_document_id']['#value']);
  $documentIdExact = trim($form[$filterDimension][$filterDimension . '_document_id_exact']['#value']);
  if (!empty($documentId)) {
    if ($documentIdExact == $documentId) {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($documentId, 'doc_id_exact');
    } else {
      $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($documentId, 'doc_id');
    }
  }
  //Date Filter
  $dateFilter = trim($form[$filterDimension][$filterDimension . '_date_filter']['#value']);
  //When Year is selected
  $yearId = trim($form[$filterDimension][$filterDimension . '_fiscal_year']['#value']);
  $redirectUrl .=  (!empty($yearId) && $dateFilter == 0 && $yearId != 'fy~all') ? _checkbook_advanced_search_year_arg($yearId, 'year') : "";
  //When Issue Date is selected
  $issueDateFrom = trim($form[$filterDimension][$filterDimension . '_issue_date_from']['#value']['date']);
  $issueDateTo = trim($form[$filterDimension][$filterDimension . '_issue_date_to']['#value']['date']);
  if ($dateFilter == 1) {
    $issueDateRangeArray = array('from' => $issueDateFrom, 'to' => $issueDateTo, 'type' => 'date');
    $redirectUrl .= _checkbook_advanced_search_generate_redirect_url($issueDateRangeArray, 'issue_date', TRUE);
  }

  return $redirectUrl;
}

