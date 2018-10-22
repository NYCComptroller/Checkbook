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
            jQuery('.toggleEmployee').click(toggleEmployee);
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

        $employment_type = $results['type_of_employment'];
        $class = strtolower($employment_type);
        $total_annual_salary = custom_number_formatter_format($node->total_annual_salary,2,'$');
        $total_gross_pay = custom_number_formatter_format($results['total_gross_pay'],2,'$');
        $total_base_pay = custom_number_formatter_format($results['total_base_pay'],2,'$');
        $total_other_payments = custom_number_formatter_format($results['total_other_payments'],2,'$');
        $total_overtime_pay = custom_number_formatter_format($results['total_overtime_pay'],2,'$');
        $number_employees = number_format($results['number_employees']);
        $total_employees =  number_format($node->total_employees);

        //Amount labels
        $lbl_annual_salary = WidgetUtil::getLabel('combined_annual_salary');
        $lbl_gross_pay = WidgetUtil::getLabel('combined_gross_pay');
        $lbl_base_pay = WidgetUtil::getLabel('combined_base_pay');
        $lbl_other_pay = WidgetUtil::getLabel('combined_other_pay');
        $lbl_overtime_pay = WidgetUtil::getLabel('combined_overtime_pay');

        $year = $results['fiscal_year_id'];
        $month_id = $results['month_id'];
        $month_name = $results['month_name'];
        $type_of_year = $results['type_of_year'];
        $year_value = _getYearValueFromID($year);
        $year_type = 'FY';
        if($type_of_year == 'C') {
            $year_type = 'CY';
        }

        if(RequestUtilities::get('smnid') == 491 || RequestUtilities::get('smnid') == 492) {
            $total_overtime_employees_label = WidgetUtil::getLabel('total_no_of_ot_employees').':';
            $overtime_employees_value = number_format($results['total_overtime_employees']);
        }
        $table = "<div class='emp-agency-detail-record'><table id='emp-agency-detail-record-table' class='emp-record-$class'>

                <div class='payroll-year-month emp-record-$class'>
                    <span class='label'>". WidgetUtil::getLabel('month') .": </span><span class='data'> {$month_name} </span>
                     &nbsp;&nbsp;|&nbsp;&nbsp;
                    <span class='label'>".WidgetUtil::getLabel('year') .":</span><span class='data'> {$year_type} {$year_value}</span>
                </div>
                ";
        if($employment_type == PayrollType::$SALARIED) {
            $table .= "
                <tr>
                    <td width='40%'><strong>". $lbl_annual_salary ."</strong>: {$total_annual_salary}</td>
                    <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($employment_type)."</td>

                </tr>
                <tr>
                    <td><strong>". $lbl_gross_pay ."</strong>: {$total_gross_pay}</td>
                    <td width='50%'><strong>". WidgetUtil::getLabel('total_no_of_employees') ."</strong>: {$total_employees}</td>

                </tr>
                <tr>
                    <td><strong>". $lbl_base_pay ."</strong>:{$total_base_pay}</td>
                    <td><strong>". WidgetUtil::getLabel('total_no_of_sal_employees') ."</strong>: {$number_employees}</td>";
            $table .= "</tr>
                <tr>
                    <td><strong>". $lbl_other_pay ."</strong>: {$total_other_payments}</td>";
            if(isset($overtime_employees_value)){
                $table .= "<td><strong>{$total_overtime_employees_label} </strong> {$overtime_employees_value}</td>";
            }else{
                $table .= "<td></td>";
            }
            $table .="</tr>
                <tr>
                    <td><strong>". $lbl_overtime_pay ."</strong>: {$total_overtime_pay}</td>
                    <td></td>
                </tr>";
        } else {
            $table .= "
                <tr>
                    <td width='50%'><strong>". $lbl_gross_pay ."</strong>: {$total_gross_pay}</td>
                    <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($employment_type)."</td>

                </tr>
                <tr>
                    <td><strong>". $lbl_base_pay ."</strong>:{$total_base_pay}</td>
                    <td width='50%'><strong>". WidgetUtil::getLabel('total_no_of_employees') ."</strong>: {$total_employees}</td>

                </tr>
                <tr>
                    <td><strong>". $lbl_other_pay ."</strong>: {$total_other_payments}</td>
                    <td><strong>". WidgetUtil::getLabel('total_no_of_non_sal_employees') ."</strong>: {$number_employees}</td>";
            $table .= "</tr>
                <tr>
                    <td><strong>". $lbl_overtime_pay ."</strong>: {$total_overtime_pay}</td>";
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
        $employeeData .= "<div id='toggle-employee-salaried' class='emp-record-salaried toggleEmployee'>
                            <strong>Viewing Salaried Details</strong>&nbsp;|&nbsp;
                            <a>View Non-salaried Details</a>
                          </div>";
        $employeeData .= "<div id='toggle-employee-non-salaried' class='emp-record-non-salaried'>
                            <a>View Salaried Details</a>&nbsp;|&nbsp;
                            <strong>Viewing Non-salaried Details</strong>
                          </div>";
        $employeeData .= "</div></div>";
    }
    else {
        $employeeData .= "</div></div>";
    }

    print $employeeData;
}
