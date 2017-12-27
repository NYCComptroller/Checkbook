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
    $ytdspending = WidgetUtil::getLabel("ytd_spending");
    $cid = WidgetUtil::getLabel("contract_id");
    $sub_contract_purpose = WidgetUtil::getLabel("sub_contract_purpose");
    $sub_vendor_name= WidgetUtil::getLabel("sub_vendor_name");
    $prime_vendor_name= WidgetUtil::getLabel("prime_vendor");
    $sub_contract_purpose_value = strtoupper($row['sub_contract_purpose_sub_contract_purpose']);
    $smmnid = _getRequestParamValue('smnid');
    switch($smmnid) {
        case 718:
        case 749:
        $prime_vendor_name= WidgetUtil::getLabel("associated_prime_vendor");
            break;
    }
$summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$title}</h2>
	</div>
	<div class="dollar-amounts">
        <div class="ytd-spending-amount">
            {$row['formatted_check_amount_sum']}
            <div class="amount-title">{$ytdspending}</div>
        </div>
    </div>
</div>
<div class="contract-information">
	<ul>
	    <li class="spendingtxsubtitle">
	        <span class="gi-list-item">{$cid}:</span> {$row['document_id_document_id']}
	    </li>
		<li class="contract-purpose">
			<span class="gi-list-item">{$sub_vendor_name}:</span> {$row['sub_vendor_sub_vendor_legal_name']}
        </li>
		<li class="agency">
			<span class="gi-list-item">{$sub_contract_purpose}:</span> {$sub_contract_purpose_value}
		</li>
		<li class="vendor">
			<span class="gi-list-item">{$prime_vendor_name}:</span> {$row['prime_vendor_prime_vendor_legal_name']}
		</li>
	</ul>
</div>
EOD;

    print $summaryContent;

}