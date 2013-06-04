<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php

$revenue_budget_parameter_mapping = array("agency_name" => "Agency Name:",
                                  "revenue_category_name" => "Revenue Category:",
                                  "adopted_amount" => "Adopted Budget:",
                                  "current_budget_amount" => "Current Modified Budget:",
                                  "funding_class_name" => "Funding Source:",
                                  "fiscal_year" => "Fiscal Year:"
                                 );
$linkable_fields = array("agency_name" => "/budget/agency/".$revenue_budget_results["agency_id"]."/year/" . $revenue_budget_results["fiscal_year_id"],
                         "revenue_category_name" => "/revenue/year/" . $revenue_budget_results["fiscal_year_id"] . "/revcat/".$revenue_budget_results["revenue_category_id"],
                         "funding_class_name" => "/revenue/year/" . $revenue_budget_results["fiscal_year_id"] . "/fundsrccode/".$revenue_budget_results["funding_class_code"],
                        );
$highlighting_fields = array("agency_name" => "agency_name_text",
                             "revenue_category_name" => "revenue_category_name_text",
                             "funding_class_name" => "funding_class_name_text"
                            );

$amount_fields = array("adopted_amount", "current_budget_amount");

print "<div class='search-result-fields'>";
foreach($revenue_budget_results as $key => $value){
    if(array_key_exists($key, $revenue_budget_parameter_mapping)){
        print "<div class='search-result-row'>";
        print "<div class='field-label'>". $revenue_budget_parameter_mapping[$key] . "</div>";

        if($highlighting[$revenue_budget_results["id"]][$highlighting_fields[$key]]){
            $value = $highlighting[$budget_results["id"]][$highlighting_fields[$key]][0];
            $value = _checkbook_smart_search_str_html_entities($value);
        }

        if(in_array($key, $amount_fields)){
            $value = custom_number_formatter_format($value, 2 , '$');
        }else if(array_key_exists($key, $linkable_fields)){
            $value = "<a href='" . $linkable_fields[$key] . "'>". $value ."</a>";
        }

        print "<div class='field-content'>". $value . "</div>";
        print "</div>";
    }
}
print "</div>";