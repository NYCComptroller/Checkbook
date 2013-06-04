<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$budget_parameter_mapping = _checkbook_smart_search_domain_fields('budget');

$linkable_fields = array("agency_name" => "/budget/year/" . _getCurrentYearID() . "/yeartype/B/agency/".$budget_results["agency_id"],
                         "expenditure_object_name" => "/budget/year/". _getCurrentYearID() . "/yeartype/B/expcategory/".$budget_results["expenditure_object_id"],
                        );
$highlighting_fields = array("agency_name" => "agency_name_text",
                             "department_name" => "department_name_text",
                             "expenditure_object_name" => "expenditure_object_name_text",
                             "budget_code_name" => "budget_code_name_text"
                            );

$amount_fields = array("adopted_amount", "current_budget_amount", "total_expenditure","pre_encumbered_amount","encumbered_amount","accrued_expense_amount","cash_expense_amount","post_closing_adjustment_amount");

$count = 1;
$row = array();
$rows = array();
foreach ($budget_parameter_mapping as $key=>$title){
    if($key == 'expenditure_object_name'){
        $value = $budget_results[$key][0];
      }
      else{
        $value = $budget_results[$key];
      }

    if($highlighting[$budget_results["id"]][$highlighting_fields[$key]]){
        $value = $highlighting[$budget_results["id"]][$highlighting_fields[$key]][0];
    }
    $value = _checkbook_smart_search_str_html_entities($value);

    if(in_array($key, $amount_fields)){
        $value = custom_number_formatter_format($value, 2 , '$');
    }else if(array_key_exists($key, $linkable_fields)){
        $value = "<a href='" . $linkable_fields[$key] . "'>". $value ."</a>";
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
foreach($budget_results as $key => $value){
    if(array_key_exists($key, $budget_parameter_mapping)){
        print "<div class='search-result-row'>";
        print "<div class='field-label'>". $budget_parameter_mapping[$key] . "</div>";

        if($highlighting[$budget_results["id"]][$highlighting_fields[$key]]){
            $value = $highlighting[$budget_results["id"]][$highlighting_fields[$key]][0];
            $value = _checkbook_smart_search_str_html_entities($value);
        }

        if(in_array($key, $amount_fields)){
            $value = custom_number_formatter_format($value, 2 , '$');
        }else if(array_key_exists($key, $linkable_fields)){
            $value = "<a href='" . $linkable_fields[$key]. "/year/" . $budget_results["fiscal_year_id"] . "'>". $value ."</a>";
        }

        print "<div class='field-content'>". $value . "</div>";
        print "</div>";
    }
}
print "</div>";*/



