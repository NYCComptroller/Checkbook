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
    $ytdspending = WidgetUtil::getLabel("ytd_spending");
    $cid = WidgetUtil::getLabel("contract_id");
    $oamnt = WidgetUtil::getLabel("original_amount");
    $camnt = WidgetUtil::getLabel("current_amount");
    $puprose = WidgetUtil::getLabel("contract_purpose");
    $vendor= WidgetUtil::getLabel("vendor_name");
    $agency= WidgetUtil::getLabel("agency_name");
    
$summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$title}</h2>
		<div class="spending-tx-subtitle">{$cid}: {$row['document_id_document_id']}</div>
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
<div class="contract-information">
	<ul>
		<li class="contract-purpose">
			<span class="gi-list-item">{$puprose}:</span> {$row['contract_purpose_contract_purpose']}
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