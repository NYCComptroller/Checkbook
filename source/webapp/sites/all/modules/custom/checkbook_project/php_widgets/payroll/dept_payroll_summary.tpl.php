<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php

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