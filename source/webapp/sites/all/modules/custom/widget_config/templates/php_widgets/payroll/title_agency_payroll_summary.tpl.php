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
    $original_title = $results['civil_service_title_civil_service_title'];
    $title = strtolower($original_title);
    $title = mb_convert_case($title, MB_CASE_TITLE, "UTF-8");
    $titleUrl  = "<a href='/payroll'"._checkbook_project_get_year_url_param_string() ."/title/" . urlencode($results['civil_service_title_civil_service_title']) . ">".$title."</a>";
    $total_annual_salary = custom_number_formatter_format($results['total_annual_salary'],2,'$');
    $total_gross_pay = custom_number_formatter_format($results['total_gross_pay'],2,'$');
    $total_base_pay = custom_number_formatter_format($results['total_base_salary'],2,'$');
    $total_other_payments = custom_number_formatter_format($results['total_other_payments'],2,'$');
    $total_overtime_pay = custom_number_formatter_format($results['total_overtime_amount'],2,'$');
    $total_salaried_employees = number_format($results['employee_count']);
    $agencyId = $results['agency_agency'];
    $year = $results['year_year'];
    $yearType = $results['year_type_year_type'];
    $agency = strtoupper($results['agency_agency_agency_name']);
    $original_title = urlencode($original_title);
    $agencyUrl  = "<a href='/payroll/agency_landing/yeartype/$yearType/year/$year/agency/$agencyId'>{$agency}</a>";
    $title_url =  "<a href='/payroll/title_landing/yeartype/$yearType/year/$year/title/$original_title'>{$title}</a>";

    $table = "";
    $table .= "<div id='payroll-emp-trans-name'>
                        <span class='payroll-label'>Title: </span>
                        <span class='payroll-value'>{$title_url}</span>
                    </div>";
    if(RequestUtilities::get('agency')){
        $agencyId = RequestUtilities::get('agency');
    }
    else{
        $agencyId = null;
    }

    $table .= "

<div id='payroll-tx-static-content'>
    <table id='payroll-tx-static-content-table'>";
    if(isset($agencyId)){
        $table .= "<tr>
            <td width='60%'><strong>". WidgetUtil::getLabel('agency_name') ."</strong>: {$agencyUrl}</td>
            <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($results['employment_type_employment_type'])."</td>
        </tr>
        <tr>
             <td> <strong>". WidgetUtil::getLabel('combined_annual_salary') ."</strong>: {$total_annual_salary} </td>
            <td><strong>". WidgetUtil::getLabel('total_no_of_sal_employees') ."</strong>: {$total_salaried_employees}</td>
        </tr>
        <tr>
            <td> <strong>". WidgetUtil::getLabel('combined_gross_pay_ytd') ."</strong>: {$total_gross_pay}</td>
            <td></td>
        </tr>";
        $table .= "<tr>
            <td><strong>". WidgetUtil::getLabel('combined_base_pay_ytd') ."</strong>: {$total_base_pay}</td>
            <td></td>
        </tr>
        <tr>
            <td><strong>". WidgetUtil::getLabel('combined_other_pay_ytd') ."</strong>: {$total_other_payments}</td>
            <td></td>
        </tr>
        <tr>
            <td><strong>". WidgetUtil::getLabel('combined_overtime_pay_ytd') ."</strong>: {$total_overtime_pay}</td>
            <td></td>
        </tr>";
    }else{
        $table .= "<tr>
            <td width='60%'><strong>". WidgetUtil::getLabel('combined_annual_salary') ."</strong>: {$total_annual_salary} </td>
            <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($results['employment_type_employment_type'])."</td>
        </tr>
        <tr>
            <td> <strong>". WidgetUtil::getLabel('combined_gross_pay_ytd') ."</strong>: {$total_gross_pay}</td>
            <td><strong>". WidgetUtil::getLabel('total_no_of_sal_employees') ."</strong>: {$total_salaried_employees}</td>
        </tr>
        <tr>
            <td><strong>". WidgetUtil::getLabel('combined_base_pay_ytd') ."</strong>: {$total_base_pay}</td>
            <td></td>
        </tr>";
        $table .= "<tr>
            <td><strong>". WidgetUtil::getLabel('combined_other_pay_ytd') ."</strong>: {$total_other_payments}</td>
            <td></td>
        </tr>
        <tr>
            <td><strong>". WidgetUtil::getLabel('combined_overtime_pay_ytd') ."</strong>: {$total_overtime_pay}</td>
            <td></td>
        </tr>";
    }



    $table .= "</table></div>";

    print $table;
}
