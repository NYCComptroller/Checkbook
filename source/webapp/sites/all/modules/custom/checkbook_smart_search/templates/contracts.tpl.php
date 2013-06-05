<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
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
?>
<?php
$contracts_parameter_mapping = _checkbook_smart_search_domain_fields('contracts');

if(strtolower($contracts_results['contract_status']) == 'registered'){

   $current_date = date("c").'Z';
   $start_date = date("c", strtotime($contracts_results['start_date']));
   $end_date = date("c", strtotime($contracts_results['end_date']));

   if($start_date <= $current_date && $end_date >= $current_date){
           $contracts_results['status'] = 'Active';
           $status = "A";
   }else{
           $contracts_results['status'] =  'Registered';
           $status = "R";
   }

    if(strtolower($contracts_results['contract_category_name']) == 'expense'){
        $vendor_link = "/contracts_landing/status/" .$status."/yeartype/B/year/". _getFiscalYearID() .'/vendor/'.$contracts_results['vendor_id'];
        $agency_link = "/contracts_landing/status/" .$status."/yeartype/B/year/"._getFiscalYearID().'/agency/'.$contracts_results['agency_id'];
        $contract_Id_link = "/contracts_landing/status/" .$status;

    }else{
        $vendor_link = "/contracts_revenue_landing/status/" .$status."/yeartype/B/year/"._getFiscalYearID() .'/vendor/'.$contracts_results['vendor_id'];
        $agency_link = "/contracts_revenue_landing/status/" .$status."/yeartype/B/year/"._getFiscalYearID().'/agency/'.$contracts_results['agency_id'];
        $contract_Id_link = "/contracts_revenue_landing/status/" .$status;
    }

    $contract_Id_link .= _checkbook_project_get_year_url_param_string()."?expandBottomContURL=/panel_html/contract_transactions/"."/contract_details";
    if($contracts_results['document_code'] == 'MA1' || $contracts_results['document_code'] == 'MMA1' || $contracts_results['document_code'] == 'RCT1'){
        $contract_Id_link .= "/magid/".$contracts_results['original_agreement_id']."/doctype/".$contracts_results["document_code"];
    }else{
        $contract_Id_link .= "/agid/".$contracts_results['original_agreement_id']."/doctype/".$contracts_results["document_code"];
    }

    if($contracts_results['original_agreement_id']){
        $contracts_results['contract_number'] = "<a href='".$contract_Id_link ."'>".$contracts_results['contract_number']."</a>";
        $contracts_results['parent_contract_number'] = "<a href='". $contract_Id_link."'>".$contracts_results['parent_contract_number']."</a>";
    }


}else if(strtolower($contracts_results['contract_status']) == 'pending'){
    if(strtolower($contracts_results['contract_category_name']) == 'expense'){
        $agency_link = "/contracts_pending_exp_landing/agency/".$contracts_results['agency_id'];
        $vendor_link = "/contracts_pending_exp_landing/vendor/".$contracts_results['vendor_id'];
        $contract_Id_link = "/contracts_pending_exp_landing/";

    }else{
        $agency_link = "/contracts_pending_rev_landing/agency/".$contracts_results['agency_id'];
        $vendor_link = "/contracts_pending_rev_landing/vendor/".$contracts_results['vendor_id'];
        $contract_Id_link = "/contracts_pending_rev_landing/";

    }

    if($contracts_results['original_agreement_id']){
        $contract_Id_link .= _checkbook_project_get_year_url_param_string()."?expandBottomContURL=/panel_html/contract_transactions/"."/contract_details";
        if($contracts_results['document_code'] == 'MA1' || $contracts_results['document_code'] == 'MMA1' || $contracts_results['document_code'] == 'RCT1'){
            $contract_Id_link .= "/magid/".$contracts_results['original_agreement_id']."/doctype/".$contracts_results["document_code"];
        }else{
            $contract_Id_link .= "/agid/".$contracts_results['original_agreement_id']."/doctype/".$contracts_results["document_code"];
        }
        $contracts_results['contract_number'] = "<a href='".$contract_Id_link ."'>".$contracts_results['contract_number']."</a>";
        $contracts_results['parent_contract_number'] = "<a href='". $contract_Id_link."'>".$contracts_results['parent_contract_number']."</a>";
    }else{
       $contract_Id_link .= _checkbook_project_get_year_url_param_string()."?expandBottomContURL=/minipanels/pending_contract_transactions/contract/".
                            $contracts_results['fms_pending_contract_number']."/version/".$contracts_results['document_version'];
       $contracts_results['contract_number'] = "<a href='".$contract_Id_link ."'>".$contracts_results['contract_number']."</a>";
       $contracts_results['parent_contract_number'] = "<a href='". $contract_Id_link."'>".$contracts_results['parent_contract_number']."</a>";
    }

    $contracts_results['status'] =  "Pending";
}



$linkable_fields = array("agency_name" => $agency_link,
                         "vendor_name" => $vendor_link,
                        );
$highlighting_fields = array("agency_name" => "agency_name_text",
                             "vendor_name" => "vendor_name_text",
                             "award_method_name" => "award_method_name_text",
                             "contract_purpose" => "contract_purpose_text",
                             "contract_type" => "contract_type_text",
                             "contract_category_name" => "contract_category_name_text",);

$date_fields = array("start_date","end_date","received_date","registered_date");
$amount_fields = array("current_amount", "original_amount");

$name_fields = array("agency_name", "vendor_name", "award_method_name", "contract_purpose", "expenditure_object_name");
$rows = array(array('<div class="field-label">Status:</div><div class="field-content">'.$contracts_results['status'].'</div>','<div class="field-label">Category:</div><div class="field-content">'.$contracts_results['contract_category_name'].'</div>'));
$count = 1;
$row = array();
foreach ($contracts_parameter_mapping as $key => $title){
  if($key == 'expenditure_object_name'){
     $value = "";
     foreach($contracts_results[$key] as $a => $b){
         $value .= $b.',';
     }
     $value = substr($value, 0, -1);
  }else{
    $value = $contracts_results[$key];
  }

  if($highlighting[$contracts_results["id"]][$highlighting_fields[$key]]){
    $value = $highlighting[$contracts_results["id"]][$highlighting_fields[$key]][0];
    $value = _checkbook_smart_search_str_html_entities($value);
  }

  if(in_array($key, $name_fields)){
      $value = _get_tooltip_markup($value, 80, 2);
  }
  if(in_array($key, $amount_fields)){
    $value = custom_number_formatter_format($value, 2 , '$');
  }else if(in_array($key, $date_fields)){
    $value = date("F j, Y", strtotime($value));
  }else if(array_key_exists($key, $linkable_fields)){
    $value = "<a href='" . $linkable_fields[$key]. "'>". $value ."</a>";
  }

  if ($count % 2 == 0){
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.$value.'</div>';
    $rows[] = $row;
    $row = array();
  } else {
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.$value.'</div>';
  }
  $count++;
}
print theme('table',array('rows'=>$rows,'attributes'=>array('class'=>array('search-result-fields'))));
