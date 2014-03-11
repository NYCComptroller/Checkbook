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
$budget_parameter_mapping = _checkbook_smart_search_domain_fields('budget');

$linkable_fields = array("agency_name" => "/budget/year/" . _getCurrentYearID() . "/yeartype/B/agency/".$budget_results["agency_id"],
                         "expenditure_object_name" => "/budget/year/". _getCurrentYearID() . "/yeartype/B/expcategory/".$budget_results["expenditure_object_id"],
                        );
/*$highlighting_fields = array("agency_name" => "agency_name_text",
                             "department_name" => "department_name_text",
                             "expenditure_object_name" => "expenditure_object_name_text",
                             "budget_code_name" => "budget_code_name_text"
                            );*/

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

    $temp = substr($value, strpos(strtoupper($value), strtoupper($SearchTerm)),strlen($SearchTerm));
    $value = str_ireplace($SearchTerm,'<em>'. $temp . '</em>', $value);

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



