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
    $total_annual_salary = custom_number_formatter_format($results['total_annual_salary'],2,'$');
    $total_gross_pay = custom_number_formatter_format($results['total_gross_pay'],2,'$');
    $total_base_pay = custom_number_formatter_format($results['total_base_pay'],2,'$');
    $total_other_payments = custom_number_formatter_format($results['total_other_payments'],2,'$');
    $total_overtime_pay = custom_number_formatter_format($results['total_overtime_pay'],2,'$');
    $total_salaried_employees = number_format($results['total_salaried_employees@checkbook:payroll_year_month']);
    $total_hourly_employees = number_format($results['total_hourly_employees@checkbook:payroll_year_month']);
    $total_employees = number_format($results['total_employees@checkbook:payroll_year_month']);
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
            <td class='label'>". WidgetUtil::getLabel('gross_pay') .":</td><td class='data'>{$total_gross_pay}</td>
            <td class='label'>". WidgetUtil::getLabel('total_no_of_sal_employees') .":</td><td class='data'>{$total_salaried_employees}</td>
        </tr>
        <tr>
            <td class='label'>". WidgetUtil::getLabel('base_pay') .":</td><td class='data'>{$total_base_pay}</td>
            <td class='label'>". WidgetUtil::getLabel('total_no_of_non_sal_employees') .":</td><td class='data'>{$total_hourly_employees}</td>
        </tr>
        <tr>
            <td class='label'>". WidgetUtil::getLabel('other_pay') .":</td><td class='data'>{$total_other_payments}</td>
        </tr>
        <tr>
            <td class='label'>". WidgetUtil::getLabel('overtime_pay') .":</td><td class='data'>{$total_overtime_pay}</td>
        </tr>
    </table>
</div>
";

    print $table;
}