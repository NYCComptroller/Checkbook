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
    $spnttodt = WidgetUtil::getLabel("spent_to_date");
    $oamnt = WidgetUtil::getLabel("original_amount");
    $camnt = WidgetUtil::getLabel("current_amount");
    $noContr = WidgetUtil::getLabel("no_of_contracts");
    $vendor= WidgetUtil::getLabel("vendor_name");
    $totalContracts = number_format($row['total_contracts']);
$summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$node->widgetConfig->summaryView->templateTitle}</h2>
		<div class="contract-id">{$row[$node->widgetConfig->summaryView->entityColumnName]}</div>
	</div>
	<div class="dollar-amounts">
		<div class="spent-to-date">
			{$spentToDateAmount}
            <div class="amount-title">{$spnttodt}</div>
		</div>
		<div class="current-amount">
		    {$currentAmount}
            <div class="amount-title">{$camnt}</div>
		</div>
		<div class="original-amount">
		    {$originalAmount}
            <div class="amount-title">{$oamnt}</div>
		</div>
		<div class="no-of-contracts">
			{$totalContracts}
			<div class="amount-title">{$noContr}</div>
		</div>
	</div>
</div>
EOD;

print $summaryContent;

}