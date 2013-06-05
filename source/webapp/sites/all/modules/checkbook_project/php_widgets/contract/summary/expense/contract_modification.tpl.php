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

    $originalAmount = custom_number_formatter_format($row['original_amount_sum'],2,'$');
    $currentAmount = custom_number_formatter_format($row['current_amount_sum'],2,'$');
    $spentToDateAmount = custom_number_formatter_format($row['spending_amount_sum'],2,'$');

    $diffAmount = custom_number_formatter_format($row['dollar_difference'],2,'$');
    $diffAmountPercent = round($row['percent_difference'],2). '%';
    $cont_id = WidgetUtil::getLabel("contract_id");
    $spnttodt = WidgetUtil::getLabel("spent_to_date");
    $oamnt = WidgetUtil::getLabel("original_amount");
    $camnt = WidgetUtil::getLabel("current_amount");
    $ddiff = WidgetUtil::getLabel("dollar_diff");
    $pdiff = WidgetUtil::getLabel("percent_diff");
    $purpose = WidgetUtil::getLabel("contract_purpose");
    $agency = WidgetUtil::getLabel("contract_agency");
    $vendor= WidgetUtil::getLabel("vendor_name");

$summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$node->widgetConfig->summaryView->templateTitle}</h2>
		<div class="contract-id">{$cont_id}: {$row['contract_number_contract_number']}</div>
	</div>
	<div class="dollar-amounts">
		<div class="spent-to-date">
			{$spentToDateAmount}
            <div class="amount-title">{$spnttodt}</div>
		</div>
		<div class="original-amount">
		    {$originalAmount}
            <div class="amount-title">{$oamnt}</div>
		</div>
		<div class="current-amount">
		    {$currentAmount}
            <div class="amount-title">{$camnt}</div>
		</div>
	</div>
</div>
<div class="contract-information">
    <div class="dollar-difference">
		{$diffAmount}
		<div class="amount-title">{$ddiff}</div>
	</div>
	<div class="percent-difference">
		{$diffAmountPercent}
	<div class="amount-title">{$pdiff}</div>
	</div>
	<ul>
		<li class="contract-purpose">
			<span class="gi-list-item">{$purpose}:</span> {$row['contract_purpose_contract_purpose']}
        </li>
		<li class="agency">
			<span class="gi-list-item">{$agency}:</span> {$row['agency_agency_agency_name']}
		</li>
		<li class="vendor">
			<span class="gi-list-item">{$vendor}:</span> {$row['vendor_vendor_legal_name']}
		</li>
	</ul>
</div>
EOD;

print $summaryContent;

}