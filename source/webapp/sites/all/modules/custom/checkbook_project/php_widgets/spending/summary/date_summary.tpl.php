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
    $title = eval($node->widgetConfig->summaryView->templateTitleEval);
    $month = '';
    $year = 'FY'._getYearValueFromID(_getRequestParamValue('year'));
    $amount = custom_number_formatter_format(_getRequestParamValue('amt'),2,'$');
    $catname = RequestUtil::getSpendingTransactionTitle();

    $records = $node->data;
    if(is_array($records)){
        $row = $records[0];
        $month = strtoupper($row['month_month_month_name']);
    }
    if(!isset($month)) {
        $monthDetails = CheckbookDateUtil::getMonthDetails(_getRequestParamValue('month'));
        if(isset($monthDetails)){
            $month = strtoupper($monthDetails[0]['month_name']);
        }
    }
    $summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$title}</h2>
		<div class="spending-tx-subtitle"><b>Year</b>: {$year}<br><b>Month</b>: {$month}</div>
	</div>
	<div class="dollar-amounts"><div class="total-spending-amount">{$amount}<div class="amount-title">{$catname} Amount</div></div></div>
</div>



EOD;

print $summaryContent;
