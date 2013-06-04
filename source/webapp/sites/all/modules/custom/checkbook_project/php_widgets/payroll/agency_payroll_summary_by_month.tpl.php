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
    $total_employees = number_format($results['total_employees']);
    $agencyId = $results['agency_agency'];
    $year = $results['year_year'];
    $yearType = $results['year_type_year_type'];
    $agencyUrl  = "<a href='/payroll/agency/$agencyId/yeartype/$yearType/year/$year'>{$results['agency_agency_agency_name']}</a>";

$table = "

<div id='payroll-tx-agency-name'>". WidgetUtil::getLabel('agency_name') .": {$agencyUrl}</div>

<div id='payroll-tx-static-content'>
    <table id='payroll-tx-static-content-table'>
        <tr>
            <td class='label'>". WidgetUtil::getLabel('annual_salary') .":</td><td class='data'>{$total_annual_salary}</td>
            <td class='label'>". WidgetUtil::getLabel('gross_pay') .":</td><td class='data'>{$total_gross_pay}</td>
        </tr>
        <tr>
            <td class='label'>". WidgetUtil::getLabel('base_pay') .":</td><td class='data'>{$total_base_pay}</td>
            <td class='label'>". WidgetUtil::getLabel('other_pay') .":</td><td class='data'>{$total_other_payments}</td>
        </tr>
        <tr>
            <td class='label'>". WidgetUtil::getLabel('overtime_pay') .":</td><td class='data'>{$total_overtime_pay}</td>
            <td class='label'>". WidgetUtil::getLabel('total_no_of_employees') .":</td><td class='data'>{$total_employees}</td>
        </tr>
    </table>
</div>
";

    print $table;
}