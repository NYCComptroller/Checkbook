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

if(is_array($node->data) && count($node->data) > 0){

    print  '<div class="payroll-emp-wrapper">';
    $employeeData = '';

    if(count($node->data) > 1){
        $js = "
            jQuery('.emp-record-salaried').show();
            jQuery('.emp-record-non-salaried').hide();

            function toggleEmployee() {
                jQuery('.emp-record-salaried').toggle();
                jQuery('.emp-record-non-salaried').toggle();
            };
        ";


        if($_REQUEST['appendScripts']){
            print "<script type='text/javascript'>" . $js . "</script>";
        }
        else{
            drupal_add_js($js,"inline");
        }
    }

    $employeeData .= "<div id='emp-agency-detail-records'>";

    foreach($node->data as $results) {

        $employment_type = $results['employment_type_employment_type'];
        $class = strtolower($employment_type);
        $total_annual_salary = custom_number_formatter_format($results['total_annual_salary'],2,'$');
        $total_gross_pay = custom_number_formatter_format($results['total_gross_pay'],2,'$');
        $total_base_pay = custom_number_formatter_format($results['total_base_salary'],2,'$');
        $total_other_payments = custom_number_formatter_format($results['total_other_payments'],2,'$');
        $total_overtime_pay = custom_number_formatter_format($results['total_overtime_pay'],2,'$');
        $total_salaried_employees = number_format($results['total_salaried_employees@checkbook:payroll_year_month']);
        $total_hourly_employees = number_format($results['total_hourly_employees@checkbook:payroll_year_month']);
        $total_employees = number_format($results['total_employees@checkbook:payroll_year_month']);
        $year = $results['year_year'];
        $month = $results['month_month'];
        $yearType = $results['year_type_year_type'];
        $year_value = _getYearValueFromID($year);
        $month_num = _getMonthValueFromId($month);
        if(isset($month_num)) {
            $dateObj   = DateTime::createFromFormat('!m', $month_num);
            $month_value = $dateObj->format('F');
        }
        $yeartype = 'FY';
        if(_getRequestParamValue('yeartype') == 'C') {
            $yeartype = 'CY';
        }

        if(_getRequestParamValue('smnid') == 491) {
            $total_overtime_employees_label = WidgetUtil::getLabel('total_no_of_ot_employees').':';
            $overtime_employees_value = number_format($results['total_overtime_employees@checkbook:payroll_year_month']);
        }
        $table = "<div class='emp-agency-detail-record'><table id='emp-agency-detail-record-table' class='emp-record-$class'>

                <div class='payroll-year-month emp-record-$class'>
                    <span class='label'>". WidgetUtil::getLabel('month') .": </span><span class='data'> {$month_value} </span>
                     &nbsp;&nbsp;|&nbsp;&nbsp;
                    <span class='label'>".WidgetUtil::getLabel('year') .":</span><span class='data'> {$yeartype} {$year_value}</span>
                </div>
                ";
        if($class == 'salaried') {
            $table .= "
                <tr>
                    <td width='50%'><strong>". WidgetUtil::getLabel('annual_salary') ."</strong>: {$total_annual_salary}</td>
                    <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($employment_type)."</td>

                </tr>
                <tr>
                    <td><strong>". WidgetUtil::getLabel('gross_pay') ."</strong>: {$total_gross_pay}</td>
                    <td width='50%'><strong>". WidgetUtil::getLabel('total_no_of_employees') ."</strong>: {$total_employees}</td>

                </tr>
                <tr>
                    <td><strong>". WidgetUtil::getLabel('base_pay') ."</strong>:{$total_base_pay}</td>
                    <td><strong>". WidgetUtil::getLabel('total_no_of_sal_employees') ."</strong>: {$total_salaried_employees}</td>";
            $table .= "</tr>
                <tr>
                    <td><strong>". WidgetUtil::getLabel('other_pay') ."</strong>: {$total_other_payments}</td>";
            if(isset($overtime_employees_value)){
                $table .= "<td><strong>{$total_overtime_employees_label} </strong> {$overtime_employees_value}</td>";
            }else{
                $table .= "<td></td>";
            }
            $table .="</tr>
                <tr>
                    <td><strong>". WidgetUtil::getLabel('overtime_pay') ."</strong>: {$total_overtime_pay}</td>
                    <td></td>
                </tr>";
        } else {
            $table .= "
                <tr>
                    <td width='50%'><strong>". WidgetUtil::getLabel('gross_pay') ."</strong>: {$total_gross_pay}</td>
                    <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($employment_type)."</td>

                </tr>
                <tr>
                    <td><strong>". WidgetUtil::getLabel('base_pay') ."</strong>:{$total_base_pay}</td>
                    <td width='50%'><strong>". WidgetUtil::getLabel('total_no_of_employees') ."</strong>: {$total_employees}</td>

                </tr>
                <tr>
                    <td><strong>". WidgetUtil::getLabel('other_pay') ."</strong>: {$total_other_payments}</td>
                    <td><strong>". WidgetUtil::getLabel('total_no_of_non_sal_employees') ."</strong>: {$total_hourly_employees}</td>";
            $table .= "</tr>
                <tr>
                    <td><strong>". WidgetUtil::getLabel('overtime_pay') ."</strong>: {$total_overtime_pay}</td>";
            if(isset($overtime_employees_value)){
                $table .= "<td><strong>{$total_overtime_employees_label} </strong> {$overtime_employees_value}</td>";
            }else{
                $table .= "<td></td>";
            }

            $table .="</tr>";
        }
        $table .= "</table></div>";
        $employeeData .= $table;
    }
    if (count($node->data) > 1) {
        $employeeData .= "<div id='toggle-employee-salaried' class='emp-record-salaried'>
                            <strong>Viewing Salaried Details</strong>&nbsp;|&nbsp;
                            <a href='javascript:toggleEmployee();'>View Non-salaried Details</a>
                          </div>";
        $employeeData .= "<div id='toggle-employee-non-salaried' class='emp-record-non-salaried'>
                            <a href='javascript:toggleEmployee();'>View Salaried Details</a>&nbsp;|&nbsp;
                            <strong>Viewing Non-salaried Details</strong>
                          </div>";
        $employeeData .= "</div></div>";
    }
    else {
        $employeeData .= "</div></div>";
    }

    print $employeeData;
}