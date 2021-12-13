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

$records = $node->data;
if(is_array($records)){
    $row = $records[0];
    $title = eval($node->widgetConfig->summaryView->templateTitleEval);
    $label = $node->widgetConfig->summaryView->templateLabel;
    //$value = eval($node->widgetConfig->summaryView->templateLabelEval);
    $ytdspending = WidgetUtil::getLabel("ytd_spending");
    $spending_category_value= RequestUtilities::get('category');
    $dept = WidgetUtil::getLabel("dept_name");
    $percent_spending = WidgetUtil::getLabel("percent_spending");
    $percent_spending_value = $row['percent_spending'];
    if(in_array(RequestUtilities::get('smnid'), [29, 760])) {
        $percent_spending = '';
        $percent_spending_value = '';
        $agency_id = RequestUtilities::get('agency');
        $type_of_year = RequestUtilities::get('yeartype');
        $year_id = RequestUtilities::get('year');
        $deptcode = RequestUtilities::get('dept');
        $dept = "'".$deptcode."'";
        $datasource = RequestUtilities::get('datasource');
    }
    $spending_category = isset($spending_category_value) ? 'AND s0.spending_category_id ='.$spending_category_value : '';
          $query = "SELECT  j.agency_agency, j.department_department,j1.department_name AS department_department_department_name
                  FROM (SELECT s0.agency_id AS agency_agency,s0.department_code AS department_department,s0.department_id
                        FROM aggregateon_spending_coa_entities s0
                        WHERE s0.agency_id = ".$agency_id. $spending_category ."
                        AND s0.year_id = ".$year_id."AND s0.department_code = ".$dept."
                        GROUP BY s0.agency_id, s0.department_code, s0.year_id,s0.department_id) j
                 LEFT OUTER JOIN ref_department j1 ON j1.department_code = j.department_department and j1.department_id = j.department_id
                  LIMIT 1";

    if($datasource=='checkbook_oge'){
        $result = _checkbook_project_execute_sql_by_data_source($query, 'checkbook_oge');
    }
    else {
        $result = _checkbook_project_execute_sql_by_data_source($query, 'checkbook');
    }


   $value = htmlentities($result[0]['department_department_department_name']);

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
