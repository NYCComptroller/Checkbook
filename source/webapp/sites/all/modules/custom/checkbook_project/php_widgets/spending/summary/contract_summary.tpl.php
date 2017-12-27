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
    $value = eval($node->widgetConfig->summaryView->templateLabelEval);

    $ytdspending = WidgetUtil::getLabel("ytd_spending");
    $oamnt = WidgetUtil::getLabel("original_amount");
    $camnt = WidgetUtil::getLabel("current_amount");
    $puprose = WidgetUtil::getLabel("contract_purpose");
    $vendor= WidgetUtil::getLabel("prime_vendor");
    $agency= WidgetUtil::getLabel("contract_agency");
    $purpose_value = strtoupper($row['contract_purpose_contract_purpose']);
    $agency_value = strtoupper($row['agency_agency_agency_name']);
$summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$title}</h2>
	</div>
	<div class="dollar-amounts">
        <div class="total-spending-contract-amount">
            {$row['formatted_total_contract_amount_sum']}
            <div class="amount-title">Total Contract<div align="center">{$amount}Amount</div></div>
        </div>
        <div class="ytd-spending-amount">
            {$row['formatted_check_amount_sum']}
            <div class="amount-title">{$ytdspending}</div>
        </div>
    </div>
</div>
<div class="contract-information contract-summary-block">
	<ul>
	    <li class="spendingtxsubtitle">
	        <span class="gi-list-item">{$label}:</span> {$value}
	    </li>
		<li class="contract-purpose">
			<span class="gi-list-item">{$puprose}:</span> {$purpose_value}
        </li>
		<li class="agency">
			<span class="gi-list-item">{$agency}:</span> {$agency_value}
		</li>
		<li class="vendor">
			<span class="gi-list-item">{$vendor}:</span> {$row['vendor_vendor_legal_name']}
		</li>
	</ul>
</div>
EOD;

    print $summaryContent;

}