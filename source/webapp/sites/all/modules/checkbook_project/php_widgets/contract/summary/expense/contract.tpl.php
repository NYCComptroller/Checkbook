<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$records = $node->data;
if(is_array($records)){
    $row = $records[0];

    $originalAmount = custom_number_formatter_format($row['original_amount_sum'],2,'$');
    $currentAmount = custom_number_formatter_format($row['current_amount_sum'],2,'$');
    $spentToDateAmount = custom_number_formatter_format($row['spending_amount_sum'],2,'$');
    $cont_id = WidgetUtil::getLabel("contract_id");
    $spnttodt = WidgetUtil::getLabel("spent_to_date");
    $oamnt = WidgetUtil::getLabel("original_amount");
    $camnt = WidgetUtil::getLabel("current_amount");
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