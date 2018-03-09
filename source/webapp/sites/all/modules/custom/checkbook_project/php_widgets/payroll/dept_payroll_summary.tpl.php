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
    $total_annual_salary = custom_number_formatter_format($results['min_total_annual_salary@checkbook:payroll_employee_dept'],2,'$');
    $total_gross_pay = custom_number_formatter_format($results['total_gross_pay'],2,'$');
    $total_base_pay = custom_number_formatter_format($results['total_base_pay'],2,'$');
    $total_other_payments = custom_number_formatter_format($results['total_other_payments'],2,'$');
    $total_overtime_pay = custom_number_formatter_format($results['total_overtime_pay'],2,'$');
    $total_salaried_employees = number_format($results['total_salaried_employees']);
    $total_hourly_employees = number_format($results['total_hourly_employees']);
    $total_employees = number_format($results['total_employees']);
    $agencyId = $results['agency_agency'];
    $deptId = $results['dept_dept'];
    $year = $results['year_year'];
    $yearType = $results['year_type_year_type'];
    //$agencyUrl  = "<a href='/payroll/agency/$agencyId/year/$year/yeartype/$yearType'>{$results['agency_agency_agency_short_name']}</a>";
    $deptUrl  = "<a href='/payroll/dept/$deptId/agency/$agencyId/year/$year/yeartype/$yearType'>{$results['dept_dept_department_short_name']}</a>";

$table = <<<EOD
<table>
    <tr>
        <td  colspan='2'>Department: {$deptUrl}(Agency: {$results['agency_agency_agency_short_name']})</td>
    </tr>
    <tr>
        <td>Annual Salary: {$total_annual_salary}</td>
        <td>Total Number of employees: {$total_employees}</td>
    </tr>
    <tr>
        <td>Total Gross Pay YTD: {$total_gross_pay}</td>
        <td>Total Number of salaried employees: {$total_salaried_employees}</td>
    </tr>
    <tr>
        <td>Base Pay: {$total_base_pay}</td>
        <td>Total Number of non-salaried employees: {$total_hourly_employees}</td>
    </tr>
    <tr>
        <td colspan='2'>Other Payments: {$total_other_payments}</td>
    </tr>
    <tr>
        <td colspan='2'>OT Payments: {$total_overtime_pay}</td>
    </tr>
</table>
EOD;

    print $table.'<br/><br/>';
}