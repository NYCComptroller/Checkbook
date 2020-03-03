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

    $title = eval($node->widgetConfig->summaryView->templateTitleEval);
    $month = '';
    $yearType = RequestUtilities::get('yeartype');
    $year = RequestUtilities::get('calyear');
    $yearLabel = $yearType == 'C' ? 'CY' : 'FY';
    $year = isset($year)
        ? _getYearValueFromID(RequestUtilities::get('calyear'))
        : _getYearValueFromID(RequestUtilities::get('year'));
    $year = $yearLabel.$year;
    $amount = _checkbook_project_pre_process_aggregation($node,'check_amount_sum');
    $amount = custom_number_formatter_format($amount,2,'$');
    $catname = SpendingUtil::getSpendingCategoryName();
    $monthDetails = CheckbookDateUtil::getMonthDetails(RequestUtilities::get('month'));
    if(isset($monthDetails)){
        $month = strtoupper($monthDetails[0]['month_name']);
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
