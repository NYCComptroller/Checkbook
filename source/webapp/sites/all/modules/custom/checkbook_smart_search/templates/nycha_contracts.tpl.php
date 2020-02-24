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
$contracts_parameter_mapping = CheckbookSolr::getSearchFields($solr_datasource, 'contracts');
$current_year_id = _getFiscalYearID();
$start_date = date("c", strtotime($contracts_results['start_date']));
$end_date = date("c", strtotime($contracts_results['end_date']));
if(strtoupper($contracts_results['agreement_type_name']) == 'PURCHASE ORDER') {
  $contracts_results['start_date'] = '';
  $contracts_results['end_date'] = '';
}

//Generate links
$contract_id_link = '<a href=/nycha_contracts/year/'.$current_year_id.
  '/datasource/checkbook_nycha/agency/162?expandBottomContURL=/panel_html/nycha_contract_details/contract/'.$contracts_results['contract_number'].">".$contracts_results['contract_number']. "</a>";

//Year logic for NYCHA Vendor link
if($contracts_results['agreement_end_year_id'] > $current_year_id){
  $nycha_year_id = $current_year_id;
}else{
  $nycha_year_id = $contracts_results['agreement_end_year_id'];
}

$vendor_link = '<a href=/nycha_contracts/year/'.$nycha_year_id. '/agency/162/datasource/checkbook_nycha/vendor/'.$contracts_results['vendor_id'].">".htmlspecialchars($contracts_results['vendor_name']) . "</a>";
$linkable_fields = array("contract_number" => $contract_id_link, "vendor_name" => $vendor_link);

$date_fields = array("start_date", "end_date","release_approved_date");
$amount_fields = array("agreement_original_amount",
  "agreement_total_amount",
  "agreement_spend_to_date",
  "release_original_amount",
  "release_total_amount",
  "release_spend_to_date",
  "release_line_original_amount",
  "release_line_total_amount",
  "release_line_spend_to_date",
  "current_amount",
  "original_amount",
  "invoiced_amount");

$hyphen_fields = array("agreement_type_name",
  "po_header_id",
  "number_of_releases",
  "release_number",
  "item_qty_ordered",
  "shipment_number"
  );

$count = 1;
$rows = array();
$row = array();
foreach ($contracts_parameter_mapping as $key => $title){
  if($key == 'expenditure_object_name'){
    $value = "";
    foreach($contracts_results[$key] as $a => $b){
      $value .= strip_tags($b).',';
    }
    $value = substr($value, 0, -1);
  }
  else{
    $value = $contracts_results[$key];
  }

  if(in_array($key, $amount_fields)){
    $value = custom_number_formatter_format($value, 2 , '$');
  }

  if(in_array($key, $date_fields)){
    if($value != null && $value != "N/A" ){
      $value = date("F j, Y", strtotime(substr($value, 0, 10)));
    }
    elseif($value == null){
      $value ='-';
    }
  }
  if (array_key_exists($key, $linkable_fields)) {
    $value = $linkable_fields[$key];
  }

  if(in_array($key, $hyphen_fields)) {
    if ($value == null) {$value = '-';}
  }

  if ($count % 2 == 0){
    if($title)
      $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'. $value .'</div>';
    $rows[] = $row;
    $row = array();
  } else {
    if($title)
      $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'. $value .'</div>';
  }
  $count++;
}

print theme('table', array('rows' => $rows, 'attributes' => array('class' => array('search-result-fields'))));
