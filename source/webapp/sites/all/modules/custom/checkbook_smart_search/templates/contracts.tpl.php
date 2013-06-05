<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
//$rows = array(array('<div class="field-label">Status:</div><div class="field-content">'.$contracts_results['status'].'</div>','<div class="field-label">Category:</div><div class="field-content">'.$contracts_results['contract_category_name'].'</div>'));
$count = 1;
$rows = array();
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
