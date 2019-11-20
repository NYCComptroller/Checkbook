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
/**
 * Constructs the URL for Contracts based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @param string $data_source
 * @return string
 */
function _checkbook_advanced_search_contracts_submit($form, &$form_state, $data_source = DataSource::CITYWIDE)
{
  $filter_dimension = $data_source . '_contracts';
  if( $data_source == Datasource::NYCHA){
    $redirect_url =   _checkbook_advanced_search_nycha_contracts_submit($form, $form_state);
    return $redirect_url;
  }

  $status = trim($form[$filter_dimension][$filter_dimension .'_status']['#value']);
  $category = trim($form[$filter_dimension][$filter_dimension .'_category']['#value']);
  $vendor_name = trim($form[$filter_dimension][$filter_dimension .'_vendor_name']['#value']);
  $vendor_name_exact = trim($form[$filter_dimension][$filter_dimension .'_vendor_name_exact']['#value']);
  $purpose = trim($form[$filter_dimension][$filter_dimension .'_purpose']['#value']);
  $type = trim($form[$filter_dimension][$filter_dimension .'_type']['#value']);
  $agency = trim($form[$filter_dimension][$filter_dimension .'_agency']['#value']);
  $contract_num = trim($form[$filter_dimension][$filter_dimension .'_contract_num']['#value']);
  $contract_num_exact = trim($form[$filter_dimension][$filter_dimension .'_contract_num_exact']['#value']);
  $apt_pin = trim($form[$filter_dimension][$filter_dimension .'_apt_pin']['#value']);
  $pin = trim($form[$filter_dimension][$filter_dimension .'_pin']['#value']);
  $award_method = trim($form[$filter_dimension][$filter_dimension .'_award_method']['#value']);
  $current_contract_amount_from = trim($form[$filter_dimension][$filter_dimension .'_current_contract_amount_from']['#value']);
  $current_contract_amount_to = trim($form[$filter_dimension][$filter_dimension .'_current_contract_amount_to']['#value']);
  $start_date_from = trim($form[$filter_dimension][$filter_dimension .'_start_date_from']['#value']['date']);
  $start_date_to = trim($form[$filter_dimension][$filter_dimension .'_start_date_to']['#value']['date']);
  $end_date_from = trim($form[$filter_dimension][$filter_dimension .'_end_date_from']['#value']['date']);
  $end_date_to = trim($form[$filter_dimension][$filter_dimension .'_end_date_to']['#value']['date']);
  $received_date_from = trim($form_state['input'][$filter_dimension .'_received_date_from']['date']);
  $received_date_to = trim($form_state['input'][$filter_dimension .'_received_date_to']['date']);
  $registration_date_from = trim($form[$filter_dimension][$filter_dimension .'_registration_date_from']['#value']['date']);
  $registration_date_to = trim($form[$filter_dimension][$filter_dimension .'_registration_date_to']['#value']['date']);
  $entity_contract_number = trim($form[$filter_dimension][$filter_dimension .'_entity_contract_number']['#value']);
  $entity_contract_number_exact = trim($form[$filter_dimension][$filter_dimension .'_entity_contract_number_exact']['#value']);
  $commodity_line = trim($form[$filter_dimension][$filter_dimension .'_commodity_line']['#value']);
  $commodity_line_exact = trim($form[$filter_dimension][$filter_dimension .'_commodity_line_exact']['#value']);
  $budget_name = trim($form[$filter_dimension][$filter_dimension .'_budget_name']['#value']);
  $budget_name_exact = trim($form[$filter_dimension][$filter_dimension .'_budget_name_exact']['#value']);
  $year = trim($form[$filter_dimension][$filter_dimension .'_year']['#value']);
  $mwbe_category = trim($form[$filter_dimension][$filter_dimension .'_mwbe_category']['#value']);
  $mwbe_category = $mwbe_category == "Select Category" ? 0 : $mwbe_category;
  $industry_type_id = trim($form[$filter_dimension][$filter_dimension .'_industry']['#value']);
  $industry_type_id = $industry_type_id == "Select Industry" ? 0 : $industry_type_id;
  $includes_sub_vendors = trim($form[$filter_dimension][$filter_dimension .'_includes_sub_vendors']['#value']);
  $includes_sub_vendors = $includes_sub_vendors == "Select Status" ||  $includes_sub_vendors == ""
    ? 0 : $includes_sub_vendors;
  $sub_vendor_status = trim($form[$filter_dimension][$filter_dimension .'_sub_vendor_status']['#value']);
  $sub_vendor_status = $sub_vendor_status == "Select Status" ||  $sub_vendor_status == ""
    ? 0 : $sub_vendor_status;


  if ($year == 'fy~all') {
    $redirect_url = 'contract/all/transactions';
    $year = null;
  } else {
    $redirect_url = 'contract/search/transactions';
  }

  //Pending Contracts should always be in the current FY.
  if($status == "P") {
    $year = "fy~" . CheckbookDateUtil::getCurrentFiscalYearId();
  }

  if (!empty($mwbe_category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($mwbe_category, 'mwbe');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($mwbe_category, 'psmwbe');
  }
  if (!empty($industry_type_id)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($industry_type_id, 'cindustry');
  }
  if (!empty($status)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($status, 'contstatus');
  }
  if($status != "P" && $category != 'revenue') {
    if (!empty($includes_sub_vendors)) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($includes_sub_vendors, 'subcontstatus');
    }
    if (!empty($sub_vendor_status)) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($sub_vendor_status, 'subvendorstatus');
    }
  }
  if (!empty($category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($category, 'contcat');
    if ($status != 'P'){
      if($category == 'revenue'){
        $redirect_url .= '/doctype/RCT1';
      }else if($category == 'expense'){
        $redirect_url .= '/doctype/MMA1~MA1~CTA1~CT1~DO1';
      }else {
        $redirect_url .= '/doctype/MMA1~MA1~CTA1~CT1~DO1~RCT1';
      }
    } else {
      if($category == 'revenue'){
        $redirect_url .= '/doctype/RCT1';
      }else if($category == 'expense'){
        $redirect_url .= '/doctype/MMA1~MA1~MAR~CTA1~CT1~CTR~DO1';
      } else {
        $redirect_url .= '/doctype/MMA1~MA1~MAR~CTA1~CT1~CTR~DO1~RCT1';
      }
    }
  }
  if (!empty($vendor_name)) {
    if ($vendor_name_exact == $vendor_name) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($vendor_name_exact, 'vendornm_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($vendor_name, 'vendornm');
    }
  }
  if (!empty($purpose)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($purpose, 'pcontdesc');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($purpose, 'scontdesc');
  }
  if (!empty($agency)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($agency, 'agency');
  }
  if (!empty($contract_num)) {
    if ($contract_num_exact == $contract_num) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url(strtoupper($contract_num_exact), 'contnum_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contract_num, 'contnum');
    }
  }
  if (!empty($apt_pin)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($apt_pin, 'aptpin');
  }
  if (!empty($pin)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($pin, 'pin');
  }
  if (!empty($award_method)) {
    $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($award_method);
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'awdmethod');
  }
  if (!empty($type)) {
    switch ($data_source) {
      case DataSource::OGE:
        $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($type);
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['code'], 'agrmnttypecode');
        break;
      default:
        $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($type);
        if ($status == 'P') {
          $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['code'], 'agrmnttypecode');
        } else {
          $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'agrmnttype');
        }
        break;
    }
  }
  if (!empty($year)) {
    $redirect_url .= _checkbook_advanced_search_year_arg($year);
  }
  if (!empty($entity_contract_number)) {
    if ($entity_contract_number_exact == $entity_contract_number) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($entity_contract_number, 'entcontnum_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($entity_contract_number, 'entcontnum');
    }
  }
  if (!empty($commodity_line)) {
    if ($commodity_line_exact == $commodity_line) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($commodity_line, 'comline_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($commodity_line, 'comline');
    }
  }
  if (!empty($budget_name)) {
    if ($budget_name_exact == $budget_name) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_name, 'budname_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_name, 'budname');
    }
  }

  $current_contract_amount_range_array = array('from' => $current_contract_amount_from, 'to' => $current_contract_amount_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($current_contract_amount_range_array, 'curamt', TRUE);

  $start_date_range_array = array('from' => $start_date_from, 'to' => $start_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($start_date_range_array, 'startdate', TRUE);

  $end_date_range_array = array('from' => $end_date_from, 'to' => $end_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($end_date_range_array, 'enddate', TRUE);

  $received_date_range_array = array('from' => $received_date_from, 'to' => $received_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($received_date_range_array, 'recdate', TRUE);

  $registration_date_range_array = array('from' => $registration_date_from, 'to' => $registration_date_to, 'type' => 'date');

  //append data source if not default (checkbook)
  if($data_source != DataSource::CITYWIDE) $redirect_url .= '/datasource/' . $data_source;

  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($registration_date_range_array, 'regdate', TRUE);

  return $redirect_url;
}

/**
 * Constructs the URL for Nycha Contracts based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @param string $data_source
 * @return string
 */
function _checkbook_advanced_search_nycha_contracts_submit($form, &$form_state)
{
  $filter_dimension =  'checkbook_nycha_contracts';
  $nycha_agency_id = Datasource::getNYCHAId();
  $type=trim($form[$filter_dimension][$filter_dimension .'_contract_type']['#value']);
  $vendor_name_exact = trim($form[$filter_dimension][$filter_dimension .'_vendor_name_exact']['#value']);
  $responsibility_center=trim($form[$filter_dimension][$filter_dimension .'_responsibility_center']['#value']);
  $purchase_order_type = trim($form[$filter_dimension][$filter_dimension .'_purchase_order_type']['#value']);
  $vendor_name = trim($form[$filter_dimension][$filter_dimension .'_vendor_name']['#value']);
  $purpose = trim($form[$filter_dimension][$filter_dimension .'_purpose']['#value']);
  $contract_num = trim($form[$filter_dimension][$filter_dimension .'_contract_num']['#value']);
  $contract_num_exact = trim($form[$filter_dimension][$filter_dimension .'_contract_num_exact']['#value']);
  $pin = trim($form[$filter_dimension][$filter_dimension .'_pin']['#value']);
  $award_method = trim($form[$filter_dimension][$filter_dimension .'_award_method']['#value']);
  $current_contract_amount_from = trim($form[$filter_dimension][$filter_dimension .'_current_contract_amount_from']['#value']);
  $current_contract_amount_to = trim($form[$filter_dimension][$filter_dimension .'_current_contract_amount_to']['#value']);
  $start_date_from = trim($form[$filter_dimension][$filter_dimension .'_start_date_from']['#value']['date']);
  $start_date_to = trim($form[$filter_dimension][$filter_dimension .'_start_date_to']['#value']['date']);
  $end_date_from = trim($form[$filter_dimension][$filter_dimension .'_end_date_from']['#value']['date']);
  $end_date_to = trim($form[$filter_dimension][$filter_dimension .'_end_date_to']['#value']['date']);
  $approved_date_from = trim($form_state['input'][$filter_dimension .'_approved_date_from']['date']);
  $approved_date_to = trim($form_state['input'][$filter_dimension .'_approved_date_to']['date']);
  $year = trim($form[$filter_dimension][$filter_dimension .'_year']['#value']);
  $industry_type_id = trim($form[$filter_dimension][$filter_dimension .'_industry']['#value']);
  $industry_type_id = $industry_type_id == "Select Industry" ? 0 : $industry_type_id;

  if ($year == 'fy~all') {
    $redirect_url = 'nycha_contracts/all/transactions';
    $year = null;
  } else {
    $redirect_url = 'nycha_contracts/search/transactions';
  }

  if(!empty($nycha_agency_id)){
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($nycha_agency_id, 'agency');
  }

  if(!empty($purchase_order_type)){
    if($purchase_order_type!=="All"){
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($purchase_order_type, 'agreement_type');
    }
  }
  if(!empty( $type)) {
    $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($type);
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['code'], 'contract_type');
  }

  //Pending Contracts should always be in the current FY.
  if (!empty($industry_type_id)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($industry_type_id, 'industry');
  }

  if (!empty($vendor_name)) {
    if ($vendor_name_exact == $vendor_name) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($vendor_name_exact, 'vendornm_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($vendor_name, 'vendornm');
    }
  }
  if (!empty($purpose)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($purpose, 'pcontdesc');
  }

  if (!empty($contract_num)) {
    if ($contract_num_exact == $contract_num) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contract_num, 'po_num_exact');
    } else{
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contract_num, 'po_num');
    }
  }
  if (!empty($responsibility_center)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($responsibility_center, 'responsibilitynm_exact');
  }

  if (!empty($pin)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($pin, 'pin');
  }
  if (!empty($award_method)) {
    $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($award_method);
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'awdmethod');
  }

  if (!empty($year)) {
    $redirect_url .= _checkbook_advanced_search_year_arg($year);
  }

  $current_contract_amount_range_array = array('from' => $current_contract_amount_from, 'to' => $current_contract_amount_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($current_contract_amount_range_array, 'curamt', TRUE);

  $start_date_range_array = array('from' => $start_date_from, 'to' => $start_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($start_date_range_array, 'startdate', TRUE);

  $end_date_range_array = array('from' => $end_date_from, 'to' => $end_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($end_date_range_array, 'enddate', TRUE);

  $approved_date_range_array = array('from' => $approved_date_from, 'to' => $approved_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($approved_date_range_array, 'appdate', TRUE);


  $redirect_url .= '/datasource/checkbook_nycha';

  return $redirect_url;
}
