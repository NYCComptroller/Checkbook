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

$records = $node->data;
if(is_array($records)){
    $row = $records[0];
    $title = eval($node->widgetConfig->summaryView->templateTitleEval);
    $label = $node->widgetConfig->summaryView->templateLabel;
    $value = strtoupper(eval($node->widgetConfig->summaryView->templateLabelEval));
    $vendor_name = WidgetUtil::getLabel("prime_vendor_name");
    $ytdspending = WidgetUtil::getLabel("ytd_spending");
    $totcontamnt = WidgetUtil::getLabel("total_contract_amount");
    $mwbe_category = "";

    if(_getRequestParamValue('smnid') == 717){
        $no_of_subvendor_value = $row['sub_vendor_count'];
        $no_of_subvendor = WidgetUtil::getLabel("num_sub_vendors");
        $mwbe_category_label = WidgetUtil::getLabel("mwbe_category");
        $mwbe_category = strtoupper(MappingUtil::getMinorityCategoryById($row['prime_minority_type_prime_minority_type']));
        $mwbe_category = '<br><b>'.$mwbe_category_label .':</b> '.$mwbe_category ;
    }
    if(_getRequestParamValue('smnid') == 747){
        $percent_spending_value = $row['percent_spending'];
        $percent_spending = WidgetUtil::getLabel("percent_spending");
        $mwbe_category_label = WidgetUtil::getLabel("mwbe_category");
        $mwbe_category = strtoupper(MappingUtil::getMinorityCategoryById($row['prime_minority_type_prime_minority_type']));
        $mwbe_category = '<br><b>'.$mwbe_category_label .':</b> '.$mwbe_category ;
    }

    
$summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$title}</h2>
		<div class="spending-tx-subtitle"><b>{$label}</b>: {$value} {$mwbe_category}</div>
	</div>
	<div class="dollar-amounts">
        <div class="total-spending-contract-amount">
            {$row['formatted_total_contract_amount_sum']}
            <div class="amount-title">{$totcontamnt}</div>
        </div>
        <div class="ytd-spending-amount">
            {$row['formatted_check_amount_sum']}
            <div class="amount-title">{$ytdspending}</div>
        </div>
        <div class="number-of-subvendors">
            {$no_of_subvendor_value}
            <div class="amount-title">{$no_of_subvendor}</div>
        </div>
        <div class="percent-spending-amount">
            {$percent_spending_value}
            <div class="amount-title">{$percent_spending}</div>
        </div>
    </div>
</div>
EOD;

    print $summaryContent;
}