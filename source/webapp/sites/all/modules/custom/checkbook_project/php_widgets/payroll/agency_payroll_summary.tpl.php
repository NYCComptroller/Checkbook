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

$results = $node->data[0];
if($results){
    $total_annual_salary = custom_number_formatter_format($results['total_annual_salary'],2,'$');
    $total_gross_pay = custom_number_formatter_format($results['total_gross_pay'],2,'$');
    $total_base_pay = custom_number_formatter_format($results['total_base_pay'],2,'$');
    $total_other_payments = custom_number_formatter_format($results['total_other_payments'],2,'$');
    $total_overtime_pay = custom_number_formatter_format($results['total_overtime_pay'],2,'$');
    $total_salaried_employees = number_format($results['total_salaried_employees']);
    $total_employees = number_format($results['total_employees']);
    $total_hourly_employees = number_format($results['total_hourly_employees']);
    $agencyId = $results['agency_agency'];
    $year = $results['year_year'];
    $yearType = $results['year_type_year_type'];
    $agency = strtoupper($results['agency_agency_agency_name']);
    $agencyUrl  = "<a href='/payroll/agency_landing/yeartype/$yearType/year/$year/agency/$agencyId'>{$agency}</a>";
    if(_getRequestParamValue('smnid') == 322){
        $total_overtime_employees = number_format($results['total_overtime_employees']);
        $total_overtime_employees_label = WidgetUtil::getLabel('total_no_of_ot_employees').':';
    }

$table = "

<div id='payroll-tx-static-content'>
    <table id='payroll-tx-static-content-table'>
        <tr>
            <td width='50%'> <strong>". WidgetUtil::getLabel('agency_name') ."</strong>: {$agencyUrl} </td>

            <td><strong>". WidgetUtil::getLabel('total_no_of_employees') ."</strong>: {$total_employees}</td>
        </tr>
        <tr>
            <td width='50%'> <strong>". WidgetUtil::getLabel('annual_salary') ."</strong>: {$total_annual_salary} </td>
            <td><strong>". WidgetUtil::getLabel('total_no_of_sal_employees') ."</strong>: {$total_salaried_employees}</td>
        </tr>
        <tr>
            <td width='50%'> <strong>". WidgetUtil::getLabel('gross_pay_ytd') ."</strong>: {$total_gross_pay}</td>
            <td><strong>". WidgetUtil::getLabel('total_no_of_non_sal_employees') ."</strong>: {$total_hourly_employees}</td>
        </tr>
        <tr>
            <td><strong>". WidgetUtil::getLabel('base_pay_ytd') ."</strong>: {$total_base_pay}</td>";
        if(isset($total_overtime_employees)){
            $table .= "<td><strong>{$total_overtime_employees_label} </strong> {$total_overtime_employees}</td>";
        }else{
            $table .= "<td></td>";
        }

    $table .= "</tr>
        <tr>
            <td><strong>". WidgetUtil::getLabel('other_pay_ytd') ."</strong>: {$total_other_payments}</td>
            <td></td>
        </tr>
        <tr>
            <td><strong>". WidgetUtil::getLabel('overtime_pay_ytd') ."</strong>: {$total_overtime_pay}</td>
            <td></td>
        </tr>";

    $table .= "</table></div>";

    print $table;
}