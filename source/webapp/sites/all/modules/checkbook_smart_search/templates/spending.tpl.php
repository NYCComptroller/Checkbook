<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$spending_parameter_mapping = _checkbook_smart_search_domain_fields('spending');

$linkable_fields = array("agency_name" => "/spending_landing/category/".$spending_results['spending_category_id']."/year/" . _getCurrentYearID() . "/yeartype/B/agency/".$spending_results["agency_id"],
                         "vendor_name" => "/spending_landing/category/".$spending_results['spending_category_id']."/year/" . _getCurrentYearID() . "/yeartype/B/vendor/".$spending_results["vendor_id"],
                        );
$highlighting_fields = array("agency_name" => "agency_name_text",
                             "department_name" => "department_name_text",
                             "vendor_name" => "vendor_name_text",
                             "expenditure_object_name" => "expenditure_object_name_text");

$date_fields = array("check_eft_issued_date");
$amount_fields = array("check_amount");

$count = 1;
$row = array();
$rows = array();
foreach ($spending_parameter_mapping as $key=>$title){
  if($key == 'expenditure_object_name'){
    $value = $spending_results[$key][0];
  }
  else{
    $value = $spending_results[$key];
  }
    
  if($highlighting[$spending_results["id"]][$highlighting_fields[$key]]){
    $value = $highlighting[$spending_results["id"]][$highlighting_fields[$key]][0];
    $value = _checkbook_smart_search_str_html_entities($value);
  }

  if(array_key_exists($key, $linkable_fields)){
    $value = "<a href='" . $linkable_fields[$key] ."'>". $value ."</a>";
  }
  else if(in_array($key, $date_fields)){
    $value = date("F j, Y", strtotime($value));
  }else if(in_array($key, $amount_fields)){
    $value = custom_number_formatter_format($value, 2 , '$');
  }else{
      $value = ($highlighting[$spending_results["id"]][$highlighting_fields[$key]])?$value:htmlentities($value);
  }
  if($key == 'contract_number' &&  $spending_results['agreement_id']){
    $value = "<a class=\"new_window\" href=\"/contract_details"
      . _checkbook_project_get_contract_url($value, $spending_results['agreement_id']) .'/newwindow\">'
      . $value . "</a>";
  }
  if($key == "vendor_name" && !$spending_results["vendor_id"]){
    $value = $spending_results["vendor_name"];
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
/*print "<div class='search-result-fields'>";
foreach($spending_parameter_mapping as $key => $title){

        print "<div class='search-result-row'>";
        print "<div class='field-label'>". $title . "</div>";
        $value = $spending_results[$key];
        if($highlighting[$spending_results["id"]][$highlighting_fields[$key]]){
            $value = $highlighting[$spending_results["id"]][$highlighting_fields[$key]][0];
            $value = _checkbook_smart_search_str_html_entities($value);
        }
        
        if(array_key_exists($key, $linkable_fields)){
            $value = "<a href='" . $linkable_fields[$key] ."'>". $value ."</a>";
        }
        else if(in_array($key, $date_fields)){
            $value = date("F j, Y", strtotime($value));
        }else if(in_array($key, $amount_fields)){
            $value = custom_number_formatter_format($value, 2 , '$');
        }
        if($key == 'contract_number' &&  $spending_results['agreement_id']){
            $value = "<a class=\"new_window\" href=\"/contract_details"
                     . _checkbook_project_get_contract_url($value, $spending_results['agreement_id']) .'/newwindow\">'
                     . $value . "</a>";
        }
        if($key == "vendor_name" && !$spending_results["vendor_id"]){
            $value = $spending_results["vendor_name"];
        }
        print "<div class='field-content'>". $value . "</div>";
        print "</div>";

}
print "</div>";*/