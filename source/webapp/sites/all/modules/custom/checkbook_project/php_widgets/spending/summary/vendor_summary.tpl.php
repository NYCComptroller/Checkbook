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
    $title = eval($node->widgetConfig->summaryView->templateTitleEval);
    $vendor_name = WidgetUtil::getLabel("vendor_name");
    $ytdspending = WidgetUtil::getLabel("ytd_spending");
    $totcontamnt = WidgetUtil::getLabel("total_contract_amount");
    
$summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$title}</h2>
		<div class="spending-tx-subtitle">{$vendor_name}: {$row['vendor_vendor_legal_name']}</div>
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
    </div>
</div>
EOD;

    print $summaryContent;
}