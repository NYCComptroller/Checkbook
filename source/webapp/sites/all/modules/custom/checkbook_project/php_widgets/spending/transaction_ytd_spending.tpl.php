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
    $title_label = WidgetUtil::getLabel("agency_name");

    $ytd_spending_label = WidgetUtil::getLabel("ytd_spending");
    $ytd_spending_amount = '$'.custom_number_formatter_format($node->data[0]['check_amount_sum'],2);

    $summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$title}</h2>
		<div class="spending-tx-subtitle">{$title_label}: {$row['agency_agency_agency_name']}</div>
	</div>
	<div class="dollar-amounts">
        <div class="ytd-spending-amount">
            {$ytd_spending_amount}
            <div class="amount-title">{$ytd_spending_label}</div>
        </div>
    </div>
</div>
EOD;

    print $summaryContent;
}