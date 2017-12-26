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
    $value = strtoupper($node->widgetConfig->summaryView->templateLabelEval);

    $agency_name = WidgetUtil::getLabel("agency_name");
    $ytdspending = WidgetUtil::getLabel("ytd_spending");

    if(_getRequestParamValue('smnid') == 759 || _getRequestParamValue('smnid') == 746 || _getRequestParamValue('smnid') == 780){
        $percent_spending_value = $row['percent_spending'];
        $percent_spending = WidgetUtil::getLabel("percent_spending");
    }
    if(_getRequestParamValue('smnid') == 716){
        $percent_spending_value = '';
        $percent_spending = '';
        $ytdspending = WidgetUtil::getLabel("ytd_spending_sub_vendors");
        $percent_paid_value = $row['sub_vendors_percent_paid_formatted'];
        $percent_paid = WidgetUtil::getLabel("sub_vendors_percent_paid");
        $no_of_subvendors_value = $row['sub_vendor_count'];
        $no_of_subvendors = WidgetUtil::getLabel("num_sub_vendors");
        $ytd_spending_agency_value = $row['ytd_spending_agency_formatted'];
        $ytd_spending_agency = WidgetUtil::getLabel("ytd_spending_agency");

    }

    //sub_vendors_percent_paid_formatted


$summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$title}</h2>
		<div class="spending-tx-subtitle"><b>{$label}</b>: {$value}</div>
	</div>
	<div class="dollar-amounts">
        <div class="ytd-spending-amount">
            {$row['formatted_check_amount_sum']}
            <div class="amount-title">{$ytdspending}</div>
        </div>
        <div class="number-of-subvendors">
            {$no_of_subvendors_value}
            <div class="amount-title">{$no_of_subvendors}</div>
        </div>
        <div class="percent-paid-amount">
            {$percent_paid_value}
            <div class="amount-title">{$percent_paid}</div>
        </div>
        <div class="ytd-spending-agency">
            {$ytd_spending_agency_value}
            <div class="amount-title">{$ytd_spending_agency}</div>
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

?>
<script>
    (function ($) {
        $("div.contract-details-heading div").filter(function() {
            return this.childNodes.length === 0;
        }).hide();
    }(jQuery));
</script>