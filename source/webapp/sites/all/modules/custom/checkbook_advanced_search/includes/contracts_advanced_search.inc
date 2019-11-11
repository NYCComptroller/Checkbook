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

  $contracts_contract_status = trim($form[$filter_dimension][$filter_dimension .'_status']['#value']);
  $contracts_contract_category = trim($form[$filter_dimension][$filter_dimension .'_category']['#value']);
  $contracts_contract_vendor_name = trim($form[$filter_dimension][$filter_dimension .'_vendor_name']['#value']);
  $contracts_contract_vendor_name_exact = trim($form[$filter_dimension][$filter_dimension .'_vendor_name_exact']['#value']);
  $contracts_contract_purpose = trim($form[$filter_dimension][$filter_dimension .'_purpose']['#value']);
  $contracts_contract_type = trim($form[$filter_dimension][$filter_dimension .'_type']['#value']);
  $contracts_contract_agency = trim($form[$filter_dimension][$filter_dimension .'_agency']['#value']);
  $contracts_contract_contract_num = trim($form[$filter_dimension][$filter_dimension .'_contract_num']['#value']);
  $contracts_contract_contract_num_exact = trim($form[$filter_dimension][$filter_dimension .'_contract_num_exact']['#value']);
  $contracts_contract_apt_pin = trim($form[$filter_dimension][$filter_dimension .'_apt_pin']['#value']);
  $contracts_contract_pin = trim($form[$filter_dimension][$filter_dimension .'_pin']['#value']);
  $contracts_contract_award_method = trim($form[$filter_dimension][$filter_dimension .'_award_method']['#value']);
  $contracts_contract_current_contract_amount_from = trim($form[$filter_dimension][$filter_dimension .'_current_contract_amount_from']['#value']);
  $contracts_contract_current_contract_amount_to = trim($form[$filter_dimension][$filter_dimension .'_current_contract_amount_to']['#value']);
  $contracts_contract_start_date_from = trim($form[$filter_dimension][$filter_dimension .'_start_date_from']['#value']['date']);
  $contracts_contract_start_date_to = trim($form[$filter_dimension][$filter_dimension .'_start_date_to']['#value']['date']);
  $contracts_contract_end_date_from = trim($form[$filter_dimension][$filter_dimension .'_end_date_from']['#value']['date']);
  $contracts_contract_end_date_to = trim($form[$filter_dimension][$filter_dimension .'_end_date_to']['#value']['date']);
  $contracts_contract_received_date_from = trim($form_state['input'][$filter_dimension .'_received_date_from']['date']);
  $contracts_contract_received_date_to = trim($form_state['input'][$filter_dimension .'_received_date_to']['date']);
  $contracts_contract_registration_date_from = trim($form[$filter_dimension][$filter_dimension .'_registration_date_from']['#value']['date']);
  $contracts_contract_registration_date_to = trim($form[$filter_dimension][$filter_dimension .'_registration_date_to']['#value']['date']);
  $contracts_entity_contract_number = trim($form[$filter_dimension][$filter_dimension .'_entity_contract_number']['#value']);
  $contracts_entity_contract_number_exact = trim($form[$filter_dimension][$filter_dimension .'_entity_contract_number_exact']['#value']);
  $contracts_commodity_line = trim($form[$filter_dimension][$filter_dimension .'_commodity_line']['#value']);
  $contracts_commodity_line_exact = trim($form[$filter_dimension][$filter_dimension .'_commodity_line_exact']['#value']);
  $contracts_budget_name = trim($form[$filter_dimension][$filter_dimension .'_budget_name']['#value']);
  $contracts_budget_name_exact = trim($form[$filter_dimension][$filter_dimension .'_budget_name_exact']['#value']);
  $contracts_year = trim($form[$filter_dimension][$filter_dimension .'_year']['#value']);
  $contracts_mwbe_category = trim($form[$filter_dimension][$filter_dimension .'_mwbe_category']['#value']);
  $contracts_mwbe_category = $contracts_mwbe_category == "Select Category" ? 0 : $contracts_mwbe_category;
  $contracts_industry_type_id = trim($form[$filter_dimension][$filter_dimension .'_industry']['#value']);
  $contracts_industry_type_id = $contracts_industry_type_id == "Select Industry" ? 0 : $contracts_industry_type_id;
  $contracts_includes_sub_vendors = trim($form[$filter_dimension][$filter_dimension .'_includes_sub_vendors']['#value']);
  $contracts_includes_sub_vendors = $contracts_includes_sub_vendors == "Select Status" ||  $contracts_includes_sub_vendors == ""
    ? 0 : $contracts_includes_sub_vendors;
  $contracts_sub_vendor_status = trim($form[$filter_dimension][$filter_dimension .'_sub_vendor_status']['#value']);
  $contracts_sub_vendor_status = $contracts_sub_vendor_status == "Select Status" ||  $contracts_sub_vendor_status == ""
    ? 0 : $contracts_sub_vendor_status;


  if ($contracts_year == 'fy~all') {
    $redirect_url = 'contract/all/transactions';
    $contracts_year = null;
  } else {
    $redirect_url = 'contract/search/transactions';
  }

  //Pending Contracts should always be in the current FY.
  if($contracts_contract_status == "P") {
    $contracts_year = "fy~" . CheckbookDateUtil::getCurrentFiscalYearId();
  }

  if (!empty($contracts_mwbe_category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_mwbe_category, 'mwbe');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_mwbe_category, 'psmwbe');
  }
  if (!empty($contracts_industry_type_id)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_industry_type_id, 'cindustry');
  }
  if (!empty($contracts_contract_status)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_status, 'contstatus');
  }
  if($contracts_contract_status != "P" && $contracts_contract_category != 'revenue') {
    if (!empty($contracts_includes_sub_vendors)) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_includes_sub_vendors, 'subcontstatus');
    }
    if (!empty($contracts_sub_vendor_status)) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_sub_vendor_status, 'subvendorstatus');
    }
  }
  if (!empty($contracts_contract_category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_category, 'contcat');
    if ($contracts_contract_status != 'P'){
      if($contracts_contract_category == 'revenue'){
        $redirect_url .= '/doctype/RCT1';
      }else if($contracts_contract_category == 'expense'){
        $redirect_url .= '/doctype/MMA1~MA1~CTA1~CT1~DO1';
      }
      else {
        $redirect_url .= '/doctype/MMA1~MA1~CTA1~CT1~DO1~RCT1';
      }
    }
    else {
      if($contracts_contract_category == 'revenue'){
        $redirect_url .= '/doctype/RCT1';
      }else if($contracts_contract_category == 'expense'){
        $redirect_url .= '/doctype/MMA1~MA1~MAR~CTA1~CT1~CTR~DO1';
      }
      else {
        $redirect_url .= '/doctype/MMA1~MA1~MAR~CTA1~CT1~CTR~DO1~RCT1';
      }
    }
  }
  if (!empty($contracts_contract_vendor_name)) {
    if ($contracts_contract_vendor_name_exact == $contracts_contract_vendor_name) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_vendor_name_exact, 'vendornm_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_vendor_name, 'vendornm');
    }
  }
  if (!empty($contracts_contract_purpose)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_purpose, 'pcontdesc');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_purpose, 'scontdesc');
  }
  if (!empty($contracts_contract_agency)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_agency, 'agency');
  }
  if (!empty($contracts_contract_contract_num)) {
    if ($contracts_contract_contract_num_exact == $contracts_contract_contract_num) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url(strtoupper($contracts_contract_contract_num_exact), 'contnum_exact');
    }
    else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_contract_num, 'contnum');
    }
  }
  if (!empty($contracts_contract_apt_pin)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_apt_pin, 'aptpin');
  }
  if (!empty($contracts_contract_pin)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_pin, 'pin');
  }
  if (!empty($contracts_contract_award_method)) {
    $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_contract_award_method);
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'awdmethod');
  }
  if (!empty($contracts_contract_type)) {
    switch ($data_source) {
      case DataSource::OGE:
        $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_contract_type);
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['code'], 'agrmnttypecode');
        break;
      default:
        $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_contract_type);
        if ($contracts_contract_status == 'P') {
          $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['code'], 'agrmnttypecode');
        }
        else {
          $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'agrmnttype');
        }
        break;
    }
  }
  if (!empty($contracts_year)) {
    $redirect_url .= _checkbook_advanced_search_year_arg($contracts_year);
  }
  if (!empty($contracts_entity_contract_number)) {
    if ($contracts_entity_contract_number_exact == $contracts_entity_contract_number) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_entity_contract_number, 'entcontnum_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_entity_contract_number, 'entcontnum');
    }
  }
  if (!empty($contracts_commodity_line)) {
    if ($contracts_commodity_line_exact == $contracts_commodity_line) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_commodity_line, 'comline_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_commodity_line, 'comline');
    }
  }
  if (!empty($contracts_budget_name)) {
    if ($contracts_budget_name_exact == $contracts_budget_name) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_budget_name, 'budname_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_budget_name, 'budname');
    }
  }

  $contracts_contract_current_contract_amount_range_array = array('from' => $contracts_contract_current_contract_amount_from, 'to' => $contracts_contract_current_contract_amount_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_current_contract_amount_range_array, 'curamt', TRUE);

  $contracts_contract_start_date_range_array = array('from' => $contracts_contract_start_date_from, 'to' => $contracts_contract_start_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_start_date_range_array, 'startdate', TRUE);

  $contracts_contract_end_date_range_array = array('from' => $contracts_contract_end_date_from, 'to' => $contracts_contract_end_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_end_date_range_array, 'enddate', TRUE);

  $contracts_contract_received_date_range_array = array('from' => $contracts_contract_received_date_from, 'to' => $contracts_contract_received_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_received_date_range_array, 'recdate', TRUE);

  $contracts_contract_registration_date_range_array = array('from' => $contracts_contract_registration_date_from, 'to' => $contracts_contract_registration_date_to, 'type' => 'date');

  //append data source if not default (checkbook)
  if($data_source != DataSource::CITYWIDE) $redirect_url .= '/datasource/' . $data_source;

  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_registration_date_range_array, 'regdate', TRUE);

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
  $contracts_nycha_agency_id = Datasource::getNYCHAId();
  $contracts_contract_type=trim($form[$filter_dimension][$filter_dimension .'_contract_type']['#value']);
  $contracts_contract_vendor_name_exact = trim($form[$filter_dimension][$filter_dimension .'_vendor_name_exact']['#value']);
  $contracts_responsibility_center=trim($form[$filter_dimension][$filter_dimension .'_responsibility_center']['#value']);
  $contracts_purchase_order_type = trim($form[$filter_dimension][$filter_dimension .'_purchase_order_type']['#value']);
  $contracts_contract_vendor_name = trim($form[$filter_dimension][$filter_dimension .'_vendor_name']['#value']);
  $contracts_contract_purpose = trim($form[$filter_dimension][$filter_dimension .'_purpose']['#value']);
  $contracts_contract_contract_num = trim($form[$filter_dimension][$filter_dimension .'_purchase_order_number']['#value']);
  $contracts_contract_contract_num_exact = trim($form[$filter_dimension][$filter_dimension .'_purchase_order_number_exact']['#value']);
  $contracts_contract_pin = trim($form[$filter_dimension][$filter_dimension .'_pin']['#value']);
  $contracts_contract_award_method = trim($form[$filter_dimension][$filter_dimension .'_award_method']['#value']);
  $contracts_contract_current_contract_amount_from = trim($form[$filter_dimension][$filter_dimension .'_current_contract_amount_from']['#value']);
  $contracts_contract_current_contract_amount_to = trim($form[$filter_dimension][$filter_dimension .'_current_contract_amount_to']['#value']);
  $contracts_contract_start_date_from = trim($form[$filter_dimension][$filter_dimension .'_start_date_from']['#value']['date']);
  $contracts_contract_start_date_to = trim($form[$filter_dimension][$filter_dimension .'_start_date_to']['#value']['date']);
  $contracts_contract_end_date_from = trim($form[$filter_dimension][$filter_dimension .'_end_date_from']['#value']['date']);
  $contracts_contract_end_date_to = trim($form[$filter_dimension][$filter_dimension .'_end_date_to']['#value']['date']);
  $contracts_contract_approved_date_from = trim($form_state['input'][$filter_dimension .'_approved_date_from']['date']);
  $contracts_contract_approved_date_to = trim($form_state['input'][$filter_dimension .'_approved_date_to']['date']);
  $contracts_year = trim($form[$filter_dimension][$filter_dimension .'_year']['#value']);
  $contracts_industry_type_id = trim($form[$filter_dimension][$filter_dimension .'_industry']['#value']);
  $contracts_industry_type_id = $contracts_industry_type_id == "Select Industry" ? 0 : $contracts_industry_type_id;

  if ($contracts_year == 'fy~all') {
    $redirect_url = 'nycha_contracts/all/transactions';
    $contracts_year = null;
  } else {
    $redirect_url = 'nycha_contracts/search/transactions';
  }

  if(!empty($contracts_nycha_agency_id)){
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_nycha_agency_id, 'agency');
  }

  if(!empty($contracts_purchase_order_type)){
    if($contracts_purchase_order_type!=="All"){
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_purchase_order_type, 'agreement_type');
    }
  }
  if(!empty( $contracts_contract_type))
  {
    $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_contract_type);
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['code'], 'contract_type');
  }

  //Pending Contracts should always be in the current FY.
  if (!empty($contracts_industry_type_id)) {
    $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_industry_type_id);
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'industry');
  }

  if (!empty($contracts_contract_vendor_name)) {
    if ($contracts_contract_vendor_name_exact == $contracts_contract_vendor_name) {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_vendor_name_exact, 'vendornm_exact');
    } else {
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_vendor_name, 'vendornm');
    }
  }
  if (!empty($contracts_contract_purpose)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_purpose, 'pcontdesc');
  }

  if (!empty($contracts_contract_contract_num)) {
    if ($contracts_contract_contract_num_exact == $contracts_contract_contract_num) {

      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_contract_num, 'po_num_exact');
    } else{
      $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_contract_num, 'po_num');
    }
  }
  if (!empty($contracts_responsibility_center)) {
    $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_responsibility_center);
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'responsibilitynm_exact');
  }

  if (!empty($contracts_contract_pin)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_pin, 'pin');
  }
  if (!empty($contracts_contract_award_method)) {
    $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_contract_award_method);
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'awdmethod');
  }

  if (!empty($contracts_year)) {
    $redirect_url .= _checkbook_advanced_search_year_arg($contracts_year);
  }

  $contracts_contract_current_contract_amount_range_array = array('from' => $contracts_contract_current_contract_amount_from, 'to' => $contracts_contract_current_contract_amount_to, 'type' => 'amount');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_current_contract_amount_range_array, 'curamt', TRUE);

  $contracts_contract_start_date_range_array = array('from' => $contracts_contract_start_date_from, 'to' => $contracts_contract_start_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_start_date_range_array, 'startdate', TRUE);

  $contracts_contract_end_date_range_array = array('from' => $contracts_contract_end_date_from, 'to' => $contracts_contract_end_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_end_date_range_array, 'enddate', TRUE);

  $contracts_contract_approved_date_range_array = array('from' => $contracts_contract_approved_date_from, 'to' => $contracts_contract_approved_date_to, 'type' => 'date');
  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_approved_date_range_array, 'appdate', TRUE);


  $redirect_url .= '/datasource/checkbook_nycha';

  return $redirect_url;
}
