<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php

$revenue_parameter_mapping = _checkbook_smart_search_domain_fields('revenue');

$linkable_fields = array("agency_name" => "/agency/". $revenue_results["agency_id"],);
$highlighting_fields = array("agency_name" => "agency_name_text",
                             "revenue_class_name" => "revenue_class_name_text",
                             "revenue_source_name" => "revenue_source_name_text",
                             "funding_class_name" => "funding_class_name_text",
                             "revenue_category_name" => "revenue_category_name_text");

$amount_fields = array("adopted_amount", "current_budget_amount", "posting_amount");

$count = 1;
$row = array();
$rows = array();
foreach ($revenue_parameter_mapping as $key=>$title){
    $value = $revenue_results[$key];
    if($highlighting[$revenue_results["id"]][$highlighting_fields[$key]]){
        $value = $highlighting[$revenue_results["id"]][$highlighting_fields[$key]][0];
        $value = _checkbook_smart_search_str_html_entities($value);
    }

    if(in_array($key, $amount_fields)){
        $value = custom_number_formatter_format($value, 2 , '$');
    }else if(array_key_exists($key, $linkable_fields)){
        $value = "<a href='/revenue/year/" . _getCurrentYearID() . $linkable_fields[$key] . "'>". $value ."</a>";
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
foreach($revenue_results as $key => $value){
    if(array_key_exists($key, $revenue_parameter_mapping)){
        print "<div class='search-result-row'>";
        print "<div class='field-label'>". $revenue_parameter_mapping[$key] . "</div>";

        if($highlighting[$revenue_results["id"]][$highlighting_fields[$key]]){
            $value = $highlighting[$revenue_results["id"]][$highlighting_fields[$key]][0];
            $value = _checkbook_smart_search_str_html_entities($value);
        }

        if(in_array($key, $amount_fields)){
            $value = custom_number_formatter_format($value, 2 , '$');
        }else if(array_key_exists($key, $linkable_fields)){
            $value = "<a href='/revenue/year/" .  $revenue_results["fiscal_year_id"] . $linkable_fields[$key] . "'>". $value ."</a>";
        }

        print "<div class='field-content'>". $value. "</div>";

        
        print "</div>";
    }
}
print "</div>";*/