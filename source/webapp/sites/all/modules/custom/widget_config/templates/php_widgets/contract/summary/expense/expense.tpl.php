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

$records = $node->data;
if(is_array($records)){
    $row = $records[0];
    $noContr = WidgetUtil::getLabel("no_of_contracts");
    $smnid = RequestUtilities::get('smnid');
    $dynamicLabel = $node->widgetConfig->summaryView->entityColumnLabel;
    $dynamicValue = strtoupper($row[$node->widgetConfig->summaryView->entityColumnName]);

    if($smnid == 720) {
        $noContr = WidgetUtil::getLabel("num_sub_contracts");
        $mwbe_category = '<strong>'.WidgetUtil::getLabel("mwbe_category").'</strong>: '.strtoupper(MappingUtil::getMinorityCategoryById($row['minority_type_minority_type']));
    }
    else if($smnid == 725) {
        $noContr = WidgetUtil::getLabel("num_sub_contracts");
        $mwbe_category = '<strong>'.WidgetUtil::getLabel("mwbe_category").'</strong>: '.strtoupper(MappingUtil::getMinorityCategoryById($row['prime_minority_type_prime_minority_type']));
    }
    else if($smnid == 726 || $smnid == 727 || $smnid == 728 || $smnid == 729) {
        $noContr = WidgetUtil::getLabel("num_sub_contracts");
    }
    else if($smnid == 783) {
        $mwbe_category = '<strong>'.WidgetUtil::getLabel("mwbe_category").'</strong>: '.strtoupper(MappingUtil::getMinorityCategoryById($row['current_prime_minority_type_id']));
    }
    else if($smnid == 784) {
        $mwbe_category = '<strong>'.WidgetUtil::getLabel("mwbe_category").'</strong>: '.strtoupper(MappingUtil::getMinorityCategoryById($row['minority_type_minority_type']));
    }
    else if($smnid == 791) {
        $noContr = WidgetUtil::getLabel("no_of_contracts");
        $mwbe_category = '<strong>'.WidgetUtil::getLabel("mwbe_category").'</strong>: '.strtoupper(MappingUtil::getMinorityCategoryById($row['minority_type_minority_type']));
    }

    if($smnid == 369 || $smnid == 785 || $smnid == 726 ) {
        $dynamicLabel= WidgetUtil::getLabel("award_method");
    }
    if($smnid == 370 || $smnid == 786 || $smnid == 727 ){
        $dynamicLabel= WidgetUtil::getLabel("contract_agency");
    }
    if($smnid == 454 || $smnid == 787 || $smnid == 728 ){
        $dynamicLabel= WidgetUtil::getLabel("industry_name");
    }
    if($smnid == 453 || $smnid == 788 || $smnid == 729 ){
        $dynamicLabel= WidgetUtil::getLabel("contract_size");
    }


    $originalAmount = custom_number_formatter_format($row['original_amount_sum'],2,'$');
    $currentAmount = custom_number_formatter_format($row['current_amount_sum'],2,'$');
    $spentToDateAmount = custom_number_formatter_format($row['spending_amount_sum'],2,'$');
    $spnttodt = WidgetUtil::getLabel("spent_to_date");
    $oamnt = WidgetUtil::getLabel("original_amount");
    $camnt = WidgetUtil::getLabel("current_amount");
    $totalContracts = number_format($row['total_contracts']);
$summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$node->widgetConfig->summaryView->templateTitle}</h2>
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
	<div class="no-of-contracts">
			{$totalContracts}
			<div class="amount-title">{$noContr}</div>
	</div>
	<ul>
	    <li class="contractid">
	        <span class="gi-list-item">{$dynamicLabel}:</span> {$dynamicValue}<br>{$mwbe_category}
		</li>
	</ul>
</div>
EOD;

print $summaryContent;

}
