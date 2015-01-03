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
    $vendor_name = WidgetUtil::getLabel("sub_vendor_name");
    $ytdspending = WidgetUtil::getLabel("ytd_spending");
    $mwbe_category_label = WidgetUtil::getLabel("mwbe_category");
    $mwbe_category = MappingUtil::getMinorityCategoryById($row['minority_type_minority_type']);
    if(_getRequestParamValue('smnid') == 759){
        $percent_spending_value = $row['percent_spending'];
        $percent_spending = WidgetUtil::getLabel("percent_spending");
    }
    if(_getRequestParamValue('smnid') == 719){
        $percent_spending_value = '';
        $percent_spending = '';
        $associated_prime_vendor_value = $row['prime_vendor_prime_vendor_legal_name'];
        $associated_prime_vendor = WidgetUtil::getLabel("associated_prime_vendor");
        $no_of_subcontracts_value = $row['total_sub_contracts'];
        $no_of_subcontracts =  WidgetUtil::getLabel("num_sub_contracts");
    }

    
$summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$title}</h2>
		<div class="spending-tx-subtitle">{$vendor_name}: {$row['sub_vendor_sub_vendor_legal_name']} <br> {$associated_prime_vendor}: {$associated_prime_vendor_value}<br> {$mwbe_category_label}: {$mwbe_category}</div>
	</div>
	<div class="dollar-amounts">
        <div class="ytd-spending-amount">
            {$row['formatted_check_amount_sum']}
            <div class="amount-title">{$ytdspending}</div>
        </div>
        <div class="number-of-subcontracts">
            {$no_of_subcontracts_value}
            <div class="amount-title">{$no_of_subcontracts}</div>
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