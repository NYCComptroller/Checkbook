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
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;

/**
 * Constructs the URL for Contracts based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @param string $data_source
 * @return string
 */
function _checkbook_advanced_search_contracts_submit($form, &$form_state, $data_source = Datasource::CITYWIDE){
  $filter_dimension = $data_source . '_contracts';
  if( $data_source == Datasource::NYCHA){
    $redirect_url =   _checkbook_advanced_search_nycha_contracts_submit($form, $form_state);
    return $redirect_url;
  }

  $status = _checkbook_get_selected_value($form_state, $filter_dimension, 'status');
  $category = _checkbook_get_selected_value($form_state, $filter_dimension, 'category');
  $catastrophic_event =  _checkbook_get_selected_value($form_state, $filter_dimension,'catastrophic_events');
  $catastrophic_event = $catastrophic_event == "Select Event" ? 0 : $catastrophic_event;
  $vendor_name =  _checkbook_get_selected_value($form_state, $filter_dimension,'vendor_name');
  $vendor_name_exact =  _checkbook_get_selected_value($form_state, $filter_dimension,'vendor_name_exact');
  $purpose =  _checkbook_get_selected_value($form_state, $filter_dimension,'purpose');
  $type =  _checkbook_get_selected_value($form_state, $filter_dimension,'type');
  $agency =  _checkbook_get_selected_value($form_state, $filter_dimension,'agency');
  $contract_num =  _checkbook_get_selected_value($form_state, $filter_dimension,'contract_num');
  $contract_num_exact =  _checkbook_get_selected_value($form_state, $filter_dimension,'contract_num_exact');
  $apt_pin =  _checkbook_get_selected_value($form_state, $filter_dimension,'apt_pin');
  $pin =  _checkbook_get_selected_value($form_state, $filter_dimension,'pin');
  $award_method =  _checkbook_get_selected_value($form_state, $filter_dimension,'award_method');
  $current_contract_amount_from =  _checkbook_get_selected_value($form_state, $filter_dimension,'current_contract_amount_from');
  $current_contract_amount_to =  _checkbook_get_selected_value($form_state, $filter_dimension,'current_contract_amount_to');
  $start_date_from =  _checkbook_get_selected_value($form_state, $filter_dimension,'start_date_from');
  $start_date_to =  _checkbook_get_selected_value($form_state, $filter_dimension,'start_date_to');
  $end_date_from =  _checkbook_get_selected_value($form_state, $filter_dimension,'end_date_from');
  $end_date_to =  _checkbook_get_selected_value($form_state, $filter_dimension,'end_date_to');
  $received_date_from =  _checkbook_get_selected_value($form_state, $filter_dimension,'received_date_from');
  $received_date_to =  _checkbook_get_selected_value($form_state, $filter_dimension,'received_date_to');
  $registration_date_from =  _checkbook_get_selected_value($form_state, $filter_dimension,'registration_date_from');
  $registration_date_to =  _checkbook_get_selected_value($form_state, $filter_dimension,'registration_date_to');
  $entity_contract_number = _checkbook_get_selected_value($form_state, $filter_dimension,'entity_contract_number');
  $entity_contract_number_exact = _checkbook_get_selected_value($form_state, $filter_dimension,'entity_contract_number_exact');
  $commodity_line = _checkbook_get_selected_value($form_state, $filter_dimension,'commodity_line');
  $commodity_line_exact = _checkbook_get_selected_value($form_state, $filter_dimension,'commodity_line_exact');
  $budget_name = _checkbook_get_selected_value($form_state, $filter_dimension,'budget_name_text');
  $budget_name_exact = _checkbook_get_selected_value($form_state, $filter_dimension,'budget_name_text_exact');
  $year = _checkbook_get_selected_value($form_state, $filter_dimension,'year');
  $mwbe_category = _checkbook_get_selected_value($form_state, $filter_dimension,'mwbe_category');
  $mwbe_category = $mwbe_category == "Select Category" ? 0 : $mwbe_category;
  $industry_type_id = _checkbook_get_selected_value($form_state, $filter_dimension,'industry');
  $industry_type_id = $industry_type_id == "Select Industry" ? 0 : $industry_type_id;
  $includes_sub_vendors = _checkbook_get_selected_value($form_state, $filter_dimension,'includes_sub_vendors');
  $includes_sub_vendors = $includes_sub_vendors == "Select Status" ||  $includes_sub_vendors == ""
    ? 0 : $includes_sub_vendors;
  $sub_vendor_status = _checkbook_get_selected_value($form_state, $filter_dimension,'sub_vendor_status');
  $sub_vendor_status = $sub_vendor_status == "Select Status" ||  $sub_vendor_status == ""
    ? 0 : $sub_vendor_status;


  if ($year == 'fy~all') {
    $redirect_url = '/contract/all/transactions';
    $year = null;
  } else {
    $redirect_url = '/contract/search/transactions';
  }

  //Pending Contracts should always be in the current FY.
  if($status == "P") {
    $year = "fy~" . CheckbookDateUtil::getCurrentFiscalYearId();
  }

  if (!empty($mwbe_category)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($mwbe_category, 'mwbe');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($mwbe_category, 'psmwbe');
  }
  if (!empty($catastrophic_event)) {
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($catastrophic_event, 'cevent');
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
      $redirect_url .= _checkbook_advanced_search_encode_url(_checkbook_advanced_search_generate_redirect_url($vendor_name_exact, 'vendornm_exact'));
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
      case Datasource::OGE:
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
  $redirect_url .= ($data_source != Datasource::CITYWIDE) ? '/datasource/' . $data_source : "";
  $redirect_url .= ($data_source == Datasource::OGE) ? '/agency/'. Datasource::getEDCId() : "";

  $redirect_url .= _checkbook_advanced_search_generate_redirect_url($registration_date_range_array, 'regdate', TRUE);

  return $redirect_url;
}

/**
 * Constructs the URL for Nycha Contracts based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @return string
 */
function _checkbook_advanced_search_nycha_contracts_submit($form, &$form_state){
  $filter_dimension =  'checkbook_nycha_contracts';
  $nycha_agency_id = Datasource::getNYCHAId();
  $type= _checkbook_get_selected_value($form_state, $filter_dimension,'type');
  $vendor_name_exact =  _checkbook_get_selected_value($form_state, $filter_dimension,'vendor_name_exact');
  $responsibility_center= _checkbook_get_selected_value($form_state, $filter_dimension,'responsibility_center');
  $purchase_order_type =  _checkbook_get_selected_value($form_state, $filter_dimension,'purchase_order_type');
  $vendor_name =  _checkbook_get_selected_value($form_state, $filter_dimension,'vendor_name');
  $purpose =  _checkbook_get_selected_value($form_state, $filter_dimension,'purpose');
  $contract_num =  _checkbook_get_selected_value($form_state, $filter_dimension,'contract_num');
  $contract_num_exact =  _checkbook_get_selected_value($form_state, $filter_dimension,'contract_num_exact');
  $pin =  _checkbook_get_selected_value($form_state, $filter_dimension,'pin');
  $award_method =  _checkbook_get_selected_value($form_state, $filter_dimension,'award_method');
  $current_contract_amount_from =  _checkbook_get_selected_value($form_state, $filter_dimension,'current_contract_amount_from');
  $current_contract_amount_to =  _checkbook_get_selected_value($form_state, $filter_dimension,'current_contract_amount_to');
  $start_date_from =  _checkbook_get_selected_value($form_state, $filter_dimension,'start_date_from');
  $start_date_to =  _checkbook_get_selected_value($form_state, $filter_dimension,'start_date_to');
  $end_date_from =  _checkbook_get_selected_value($form_state, $filter_dimension,'end_date_from');
  $end_date_to =  _checkbook_get_selected_value($form_state, $filter_dimension,'end_date_to');
  $approved_date_from =  _checkbook_get_selected_value($form_state, $filter_dimension,'approved_date_from');
  $approved_date_to =  _checkbook_get_selected_value($form_state, $filter_dimension,'approved_date_to');
  $year =  _checkbook_get_selected_value($form_state, $filter_dimension,'year');
  $industry_type_id =  _checkbook_get_selected_value($form_state, $filter_dimension,'industry');
  $industry_type_id = $industry_type_id == "Select Industry" ? 0 : $industry_type_id;

  if ($year == 'fy~all') {
    $redirect_url = '/nycha_contracts/all/transactions';
    $year = null;
  } else {
    $redirect_url = '/nycha_contracts/search/transactions';
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
      $redirect_url .= _checkbook_advanced_search_encode_url(_checkbook_advanced_search_generate_redirect_url($vendor_name_exact, 'vendornm_exact'));
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

/**
 * Used for Contracts Advanced Search, for some elements like "Contract Type" which is a drop down on Advanced Search, we need contract_type_code
 * if status is "Pending" and contract_type_id if status is "Active" / "Registered". So $code_id_string is always in the format "id=>13~code=>41".
 * From this we have to separate out id and code
 *
 * @param $code_id_string
 * @return array('code' => 41, 'id' => 13)
 *
 */

function _checkbook_advanced_search_autocomplete_get_code_id($code_id_string){
  $code_id_array = explode('~',$code_id_string);
  $id = substr($code_id_array[0],4);
  $code = substr($code_id_array[1],6);
  return array('code' => $code, 'id' => $id);
}
