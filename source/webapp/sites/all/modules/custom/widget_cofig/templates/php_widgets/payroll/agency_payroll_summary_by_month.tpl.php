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


$results = $node->data[0];
if($results){
    $total_annual_salary = custom_number_formatter_format($results['total_annual_salary'],2,'$');
    $total_gross_pay = custom_number_formatter_format($results['total_gross_pay'],2,'$');
    $total_base_pay = custom_number_formatter_format($results['total_base_pay'],2,'$');
    $total_other_payments = custom_number_formatter_format($results['total_other_payments'],2,'$');
    $total_overtime_pay = custom_number_formatter_format($results['total_overtime_pay'],2,'$');
    $total_employees = number_format($results['total_employees']);
    $total_salaried_employees = number_format($results['total_salaried_employees']);
    $total_hourly_employees = number_format($results['total_hourly_employees']);
    $agencyId = $results['agency_agency'];
    $year = $results['year_year'];
    $yearType = $results['year_type_year_type'];
    $agencyUrl  = "<a href='/payroll/agency_landing/yeartype/$yearType/year/$year/agency/$agencyId'>{$results['agency_agency_agency_name']}</a>";

    $month = $results['month_month'];
    $year_value = _getYearValueFromID($year);
    $month_num = _getMonthValueFromId($month);
    if(isset($month_num)) {
        $dateObj   = DateTime::createFromFormat('!m', $month_num);
        $month_value = $dateObj->format('F');
    }
    $yeartype = 'FY';
    if(RequestUtilities::get('yeartype') == 'C'){
        $yeartype = 'CY';
    }

    if(RequestUtilities::get('smnid') == 491){
        $total_overtime_employees_label = WidgetUtil::getLabel('total_no_of_ot_employees').':';
        $overtime_employees_value = number_format($results['total_overtime_employees']);
    }

$table = "


<div class='payroll-year-month'><span class='label'>". WidgetUtil::getLabel('month') .": </span><span class='data'>{$month_value} </span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class='label'>".WidgetUtil::getLabel('year') .":</span><span class='data'> {$yeartype} {$year_value}</span></div>

<div id='payroll-tx-static-content'>
    <table id='payroll-tx-static-content-table'>
        <tr>
            <td width='50%'><strong>". WidgetUtil::getLabel('annual_salary') ."</strong>: {$total_annual_salary}</td>
            <td><strong>". WidgetUtil::getLabel('total_no_of_employees') ."</strong>: {$total_employees}</td>
        </tr>
        <tr>
            <td width='50%'><strong>". WidgetUtil::getLabel('gross_pay') ."</strong>: {$total_gross_pay}</td>
            <td><strong>". WidgetUtil::getLabel('total_no_of_sal_employees') ."</strong>: {$total_salaried_employees}</td>
        </tr>
        <tr>
            <td><strong>". WidgetUtil::getLabel('base_pay') ."</strong>: {$total_base_pay}</td>
            <td><strong>". WidgetUtil::getLabel('total_no_of_non_sal_employees') ."</strong>: {$total_hourly_employees}</td>
        </tr>
        <tr>
            <td><strong>". WidgetUtil::getLabel('other_pay') ."</strong>: {$total_other_payments}</td>";
        if(isset($overtime_employees_value)){
            $table .= "<td><strong>{$total_overtime_employees_label} </strong> {$overtime_employees_value}</td>";
        }else{
            $table .= "<td></td>";
        }
        $table .= "</tr>
        <tr>
            <td><strong>". WidgetUtil::getLabel('overtime_pay') ."</strong>: {$total_overtime_pay}</td>
            <td></td>
        </tr>
    </table>
</div>
";

    print $table;
}
