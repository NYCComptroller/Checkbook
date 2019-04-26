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


$all_data = array();
$total_annual_salary = 0;
$total_gross_pay = 0;
$total_base_pay = 0;
$total_other_payments = 0;
$total_overtime_pay = 0;
$total_salaried_employees = 0;
$total_hourly_employees = 0;
$total_employees = 0;
$employment_type = '';

foreach($node->data as $data) {

    $employment_type = $data['type_of_employment'];
    $total_annual_salary += $node->total_annual_salary;
    $total_gross_pay += $data['total_gross_pay'];
    $total_base_pay += $data['total_base_pay'];
    $total_other_payments += $data['total_other_payments'];
    $total_overtime_pay += $data['total_overtime_pay'];

    $total_employees +=$node->total_employees;
    $total_salaried_employees += $node->salaried_employees;
    $total_hourly_employees += $node->non_salaried_employees;
}

$total_annual_salary = custom_number_formatter_format($total_annual_salary,2,'$');
$total_gross_pay = custom_number_formatter_format($total_gross_pay,2,'$');
$total_base_pay = custom_number_formatter_format($total_base_pay,2,'$');
$total_other_payments = custom_number_formatter_format($total_other_payments,2,'$');
$total_overtime_pay = custom_number_formatter_format($total_overtime_pay,2,'$');
$total_salaried_employees = number_format($total_salaried_employees);
$total_hourly_employees = number_format($total_hourly_employees);
$total_employees =  number_format($total_employees);
$total_overtime_employees = number_format($total_overtime_employees);
$total_overtime_employees_label = WidgetUtil::getLabel('total_no_of_ot_employees').':';
$payroll_type = strtoupper($employment_type);


if($employment_type == PayrollType::$SALARIED ) {
    $table = "
<div id='payroll-tx-static-content'>
    <table id='payroll-tx-static-content-table'>
  
      <tr>
            <td width='50%'><strong>" . WidgetUtil::getLabel('total_combined_annual_salary') . "</strong>: {$total_annual_salary}</td>
            <td width='50%'><strong>" . WidgetUtil::getLabel('payroll_type') . "</strong>: {$payroll_type}</td>
        </tr>
        <tr>
            <td><strong>" . WidgetUtil::getLabel('total_combined_gross_pay_ytd') . "</strong>: {$total_gross_pay}</td>
            <td><strong>" . WidgetUtil::getLabel('total_no_of_employees') . "</strong>: {$total_employees}</td>

        </tr>
        <tr>
            <td><strong>" . WidgetUtil::getLabel('total_combined_base_pay_ytd') . "</strong>: {$total_base_pay}</td>
            <td><strong>" . WidgetUtil::getLabel('total_no_of_sal_employees') . "</strong>: {$total_salaried_employees}</td>

        </tr>
        <tr>
            <td><strong>" . WidgetUtil::getLabel('total_combined_other_pay_ytd') . "</strong>: {$total_other_payments}</td>
             <td><strong>" . WidgetUtil::getLabel('total_no_of_non_sal_employees') . "</strong>: {$total_hourly_employees}</td>

        </tr>
        <tr>
            <td><strong>" . WidgetUtil::getLabel('total_combined_overtime_pay_ytd') . "</strong>: {$total_overtime_pay}</td>
            <td></td>
        </tr>";

    $table .= "</table></div>";
}
else{
    $table = "
 <div id='payroll-tx-static-content'>
    <table id='payroll-tx-static-content-table'>
  
      <tr>
             <td  width='50%'><strong>" . WidgetUtil::getLabel('total_combined_gross_pay_ytd') . "</strong>: {$total_gross_pay}</td>
            <td width='50%'><strong>" . WidgetUtil::getLabel('payroll_type') . "</strong>: {$payroll_type}</td>
        </tr>
        <tr>
            <td><strong>" . WidgetUtil::getLabel('total_combined_base_pay_ytd') . "</strong>: {$total_base_pay}</td>
            <td><strong>" . WidgetUtil::getLabel('total_no_of_employees') . "</strong>: {$total_employees}</td>

        </tr>
        <tr> 
         <td><strong>" . WidgetUtil::getLabel('total_combined_other_pay_ytd') . "</strong>: {$total_other_payments}</td>
        <td><strong>" . WidgetUtil::getLabel('total_no_of_sal_employees') . "</strong>: {$total_salaried_employees}</td>

        </tr>
        <tr>
            <td><strong>" . WidgetUtil::getLabel('total_combined_overtime_pay_ytd') . "</strong>: {$total_overtime_pay}</td>
             <td><strong>" . WidgetUtil::getLabel('total_no_of_non_sal_employees') . "</strong>: {$total_hourly_employees}</td>

        </tr>
     ";

    $table .= "</table></div>";
}

    print $table;