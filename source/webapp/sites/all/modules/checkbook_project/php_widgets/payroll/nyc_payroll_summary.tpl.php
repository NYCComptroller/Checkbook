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
    $total_salaried_employees = number_format($results['total_salaried_employees@checkbook:payroll_year']);
    $total_hourly_employees = number_format($results['total_hourly_employees@checkbook:payroll_year']);
    $total_employees = number_format($results['total_employees@checkbook:payroll_year']);
    $year = $results['year_year'];
    $yearType = $results['year_type_year_type'];

$table = "
<div id='payroll-tx-static-content'>
    <table id='payroll-tx-static-content-table'>
        <tr>
            <td class='label'>". WidgetUtil::getLabel('annual_salary') .":</td><td class='data'>{$total_annual_salary}</td>
            <td class='label'>". WidgetUtil::getLabel('total_no_of_employees') .":</td><td class='data'>{$total_employees}</td>
        </tr>
        <tr>
            <td class='label'>". WidgetUtil::getLabel('gross_pay_ytd') .":</td><td class='data'>{$total_gross_pay}</td>
            <td class='label'>". WidgetUtil::getLabel('total_no_of_sal_employees') .":</td><td class='data'>{$total_salaried_employees}</td>
        </tr>
        <tr>
            <td class='label'>". WidgetUtil::getLabel('base_pay_ytd') .":</td><td class='data'>{$total_base_pay}</td>
            <td class='label'>". WidgetUtil::getLabel('total_no_of_non_sal_employees') .":</td><td class='data'>{$total_hourly_employees}</td>
        </tr>
        <tr>
            <td class='label'>". WidgetUtil::getLabel('other_pay_1_ytd') .":</td><td class='data'>{$total_other_payments}</td>
        </tr>
        <tr>
            <td class='label'>". WidgetUtil::getLabel('overtime_pay_1_ytd') .":</td><td class='data'>{$total_overtime_pay}</td>
        </tr>
    </table>
</div>
";

    print $table;
}