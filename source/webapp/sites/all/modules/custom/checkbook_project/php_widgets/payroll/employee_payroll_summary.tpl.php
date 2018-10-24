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


$js = "";


$salaried_count = 0;
$non_salaried_count = 0;
foreach($node->data as $data){

    $record = array();
    $employment_type = $data['type_of_employment'];

    if($employment_type == PayrollType::$SALARIED) $salaried_count++;
    else $non_salaried_count++;
}

//Default view based on salamttype in url
$default_view = $salaried_count > 0 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;
$salamttype = RequestUtilities::get('salamttype');
if(isset($salamttype)) {
    $salamttype = explode('~',$salamttype);
    if (!in_array(1, $salamttype)) {
        $default_view = PayrollType::$NON_SALARIED;
    }
}

if($default_view == PayrollType::$SALARIED) {
    $js .= "
        jQuery('.emp-record-salaried').show();
        jQuery('.emp-record-non-salaried').hide();
    ";
}
else {
    $js .= "
        jQuery('.emp-record-salaried').hide();
        jQuery('.emp-record-non-salaried').show();
    ";
}
$js .= "
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

$employeeData = '<div class="payroll-emp-wrapper">';
foreach($node->data as $data) {

    $employment_type = $data['type_of_employment'];
    $year = $data['fiscal_year_id'];
    $year_type = $data['type_of_year'];
   // $title = $data['civil_service_title'];
    $original_title = $data['civil_service_title'];
    $title = mb_convert_case(strtolower($original_title), MB_CASE_TITLE, "UTF-8");
    $class = strtolower($employment_type);
    $total_annual_salary = custom_number_formatter_format($node->total_annual_salary,2,'$');
    $total_gross_pay = custom_number_formatter_format($data['total_gross_pay'],2,'$');
    $total_base_pay = custom_number_formatter_format($data['total_base_pay'],2,'$');
    $total_other_payments = custom_number_formatter_format($data['total_other_payments'],2,'$');
    $total_overtime_pay = custom_number_formatter_format($data['total_overtime_pay'],2,'$');
    $number_employees = number_format($data['number_employees']);
    $total_employees =  number_format($node->total_employees);

    $title_url = "<a href='/payroll/title_landing/yeartype/$year_type/year/$year/title/$original_title'>{$title}</a>";

    $lbl_total_number_employees =
        $employment_type == PayrollType::$SALARIED
            ? WidgetUtil::getLabel('total_no_of_sal_employees')
            : WidgetUtil::getLabel('total_no_of_non_sal_employees');
    $employeeData .= "<div id='payroll-emp-trans-name' class='emp-record-$class'>
                        <span class='payroll-label'>Title: </span>
                        <span class='payroll-value'>{$title_url}</span>
                    </div>";

    $employeeData .= "<div id='payroll-tx-static-content' class='emp-record-$class'><table id='payroll-tx-static-content-table'>";

    if($employment_type == PayrollType::$SALARIED) {
        $employeeData .=
            "<tr>
                <td><strong>". WidgetUtil::getLabel('combined_annual_salary') ."</strong>: {$total_annual_salary}</td>
                <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($employment_type)."</td>
            </tr>
            <tr>
                <td><strong>". WidgetUtil::getLabel('combined_gross_pay_ytd') ."</strong>: {$total_gross_pay}</td>
                <td><strong>". $lbl_total_number_employees ."</strong>: {$number_employees}</td>
            </tr>
            <tr>
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
    }
    else {
        $employeeData .=
            "<tr>
                <td><strong>". WidgetUtil::getLabel('combined_gross_pay_ytd') ."</strong>: {$total_gross_pay}</td>
                <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($employment_type)."</td>
            </tr>
            <tr>
                <td><strong>". WidgetUtil::getLabel('combined_base_pay_ytd') ."</strong>: {$total_base_pay}</td>
                    <td><strong>". $lbl_total_number_employees ."</strong>: {$number_employees}</td>
            </tr>
            <tr>
                <td><strong>". WidgetUtil::getLabel('combined_other_pay_ytd') ."</strong>: {$total_other_payments}</td>
                <td></td>
            </tr>
            <tr>
                <td><strong>". WidgetUtil::getLabel('combined_overtime_pay_ytd') ."</strong>: {$total_overtime_pay}</td>
                <td></td>
            </tr>";
    }

    $employeeData .= "</table></div>";
}

if ($salaried_count && $non_salaried_count) {
    $employeeData .= "<div id='toggle-employee-salaried' class='emp-record-salaried toggleEmployee'>
                            <strong>Viewing Salaried Details</strong>&nbsp;|&nbsp;
                            <a>View Non-salaried Details</a>
                          </div>";
    $employeeData .= "<div id='toggle-employee-non-salaried' class='emp-record-non-salaried toggleEmployee'>
                            <a>View Salaried Details</a>&nbsp;|&nbsp;
                            <strong>Viewing Non-salaried Details</strong>
                          </div>";
}

$employeeData .= '</div>';

print $employeeData;
