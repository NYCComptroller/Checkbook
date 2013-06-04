<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$payroll_parameter_mapping = _checkbook_smart_search_domain_fields('payroll');

$agency_id = $payroll_results['agency_id'];
$dept_id = $payroll_results['department_id'];
$emp_id = $payroll_results['employee_id'];
//$year_id = $payroll_results['fiscal_year_id'];
$year_id = _getCurrentYearID();

$linkable_fields = array("civil_service_title" => "/payroll/employee/transactions/xyz/" .$emp_id . "/agency/" . $agency_id,
                         "agency_name" => "/payroll/agency/". $agency_id
                        );
$highlighting_fields = array("agency_name" => "agency_name_text",
                             "civil_service_title" => "civil_service_title_text");

$date_fields = array("pay_date");
$amount_fields = array("annual_salary", "gross_pay", "base_pay", "other_payments", "overtime_pay");

$count = 1;
$row = array();
$rows = array();
foreach ($payroll_parameter_mapping as $key => $title){
  $value = $payroll_results[$key];
  if($highlighting[$payroll_results["id"]][$highlighting_fields[$key]]){
    $value = $highlighting[$payroll_results["id"]][$highlighting_fields[$key]][0];
    $value = _checkbook_smart_search_str_html_entities($value);
  }
  if(in_array($key, $amount_fields)){
    $value = custom_number_formatter_format($value, 2 , '$');
  }else if(in_array($key, $date_fields)){
    $value = date("F j, Y", strtotime($value));
  }else if(array_key_exists($key, $linkable_fields)){
    $value = "<a href='" . $linkable_fields[$key] . "/year/" . $year_id . "/yeartype/B'>". _checkbook_smart_search_str_html_entities($value) ."</a>";
  }
  if ($count % 2 == 0){
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.html_entity_decode($value).'</div>';
    $rows[] = $row;
    $row = array();
  } else {
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.html_entity_decode($value).'</div>';
  }
  $count++;
}
print theme('table',array('rows'=>$rows,'attributes'=>array('class'=>array('search-result-fields'))));
/*print "<div class='search-result-fields'>";
foreach($payroll_parameter_mapping as $key => $title){

        print "<div class='search-result-row'>";
        print "<div class='field-label'>". $title . "</div>";

        $value = $payroll_results[$key];
        if($highlighting[$payroll_results["id"]][$highlighting_fields[$key]]){
            $value = $highlighting[$payroll_results["id"]][$highlighting_fields[$key]][0];
            $value = _checkbook_smart_search_str_html_entities($value);
        }
        if(in_array($key, $amount_fields)){
            $value = custom_number_formatter_format($value, 2 , '$');
        }else if(in_array($key, $date_fields)){
            $value = date("F j, Y", strtotime($value));
        }else if(array_key_exists($key, $linkable_fields)){
                $value = "<a href='" . $linkable_fields[$key] . "/year/" . $year_id . "/yeartype/B'>". _checkbook_smart_search_str_html_entities($value) ."</a>";
        }
        print "<div class='field-content'>". $value . "</div>";
        print "</div>";

}
print "</div>";*/