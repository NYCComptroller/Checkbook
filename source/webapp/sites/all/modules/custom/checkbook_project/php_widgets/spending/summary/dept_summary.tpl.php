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
    //$value = eval($node->widgetConfig->summaryView->templateLabelEval);
    $ytdspending = WidgetUtil::getLabel("ytd_spending");
    $dept = WidgetUtil::getLabel("dept_name");
    $percent_spending = WidgetUtil::getLabel("percent_spending");
    $percent_spending_value = $row['percent_spending'];
    if(_getRequestParamValue('smnid') == 29) {
        $percent_spending = '';
        $percent_spending_value = '';

        $agency_id = _getRequestParamValue('agency');
        $type_of_year = _getRequestParamValue('yeartype');
        $year_id = _getRequestParamValue('year');
        $deptcode = _getRequestParamValue('dept');
        $dept = "'".$deptcode."'";
    }
       $query = "SELECT  j.agency_agency, j.department_department,j1.department_name AS department_department_department_name
                  FROM (SELECT s0.agency_id AS agency_agency,s0.department_code AS department_department
                        FROM aggregateon_spending_coa_entities s0
                        WHERE s0.agency_id = ".$agency_id."
                        AND s0.year_id = ".$year_id."AND s0.department_code = ".$deptcode."
                        GROUP BY s0.agency_id, s0.department_code, s0.year_id) j
                 LEFT OUTER JOIN ref_department j1 ON j1.department_code = j.department_department and j1.agency_id = j.agency_agency
                  LIMIT 1";
        $result = _checkbook_project_execute_sql_by_data_source($query,'checkbook');


   $value = $result[0]['department_department_department_name'].$result['agency_agency'];

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
        <div class="percent-spending-amount">
            {$percent_spending_value}
            <div class="amount-title">{$percent_spending}</div>
        </div>
    </div>
</div>
EOD;

    print $summaryContent;
}