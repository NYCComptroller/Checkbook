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
foreach($node->data as $data){

    $record = array();
    $year = $data['year_year'];
    $year_type = $data['year_type_year_type'];
    $employment_type = $data['type_of_employment'];

    $record['total_annual_salary'] = $node->total_annual_salary;
    $record['total_gross_pay'] = $data['total_gross_pay'];
    $record['total_base_pay'] = $data['total_base_pay'];
    $record['total_other_payments'] = $data['total_other_payments'];
    $record['total_overtime_pay'] = $data['total_overtime_pay'];
    $record['total_employees'] = $data['total_employees'];
    $record['number_employees'] = $data['number_employees'];
    $record['total_overtime_employees'] = $data['total_overtime_employees'];

    $all_data[$employment_type][] = $record;
}

$salaried_count = count($all_data[PayrollType::$SALARIED]);
$non_salaried_count = count($all_data[PayrollType::$NON_SALARIED]);

//Default view based on salamttype in url
$default_view = $salaried_count > 0 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;
$salamttype = RequestUtilities::get('salamttype');
if(isset($salamttype)) {
    $salamttype = explode('~',$salamttype);
    if (!in_array(1, $salamttype)) {
        $default_view = PayrollType::$NON_SALARIED;
    }
}


$js = "";
$employeeData = '<div class="payroll-emp-wrapper">';

foreach($all_data as $employment_type => $employment_data) {

    if(($employment_type == PayrollType::$SALARIED && $salaried_count > 1)
    || ($employment_type == PayrollType::$NON_SALARIED && $non_salaried_count > 1)) {
        $class = strtolower($employment_type);
        $js .= "

                jQuery(document).ready(function() {
                    if (jQuery('#emp-agency-detail-records-$class').filter(':first').length > 0) {
                        jQuery('#emp-agency-detail-records-$class').filter(':first')
                            .cycle({
                                slideExpr:'.emp-agency-detail-record',
                                prev: '#prev-emp-$class',
                                next: '#next-emp-$class',
                                fx: 'scrollVert',
                                speed: 0,
                                width:'640px',
                                timeout: 0
                            });
                    }
                });";
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

foreach($all_data as $employment_type => $employment_data) {

    $class = strtolower($employment_type);

    $employeeData .= "<div class='emp-record-$class'>";

    if (($employment_type == PayrollType::$SALARIED && $salaried_count > 1) ||
        ($employment_type == PayrollType::$NON_SALARIED && $non_salaried_count >1)) {
        $employeeData .= "<div id='prev-emp-$class'></div>";
    }
    $employeeData .= "<div id='emp-agency-detail-records-$class'>";

    foreach($employment_data as $data) {

        $total_annual_salary = custom_number_formatter_format($data['total_annual_salary'],2,'$');
        $total_gross_pay = custom_number_formatter_format($data['total_gross_pay'],2,'$');
        $total_base_pay = custom_number_formatter_format($data['total_base_pay'],2,'$');
        $total_other_payments = custom_number_formatter_format($data['total_other_payments'],2,'$');
        $total_overtime_pay = custom_number_formatter_format($data['total_overtime_pay'],2,'$');
        $number_employees = number_format($data['number_employees']);
        $total_employees =  number_format($node->total_employees);

        //Amount labels
        $lbl_annual_salary = WidgetUtil::getLabel('combined_annual_salary');
        $lbl_gross_pay_ytd = WidgetUtil::getLabel('combined_gross_pay_ytd');
        $lbl_base_pay_ytd = WidgetUtil::getLabel('combined_base_pay_ytd');
        $lbl_other_pay_ytd = WidgetUtil::getLabel('combined_other_pay_ytd');
        $lbl_overtime_pay_ytd = WidgetUtil::getLabel('combined_overtime_pay_ytd');

        if(RequestUtilities::get('smnid') == 322){
            $total_overtime_employees_label = WidgetUtil::getLabel('total_no_of_ot_employees').':';
            $total_overtime_employees = number_format($data['total_overtime_employees']);
        }

        $table = "<div class='emp-agency-detail-record'><table id='emp-agency-detail-record-table' class='center-align'>";

        if($employment_type == PayrollType::$SALARIED) {
            $table .=
                "<tr>
                    <td width='60%'><strong>". $lbl_annual_salary ."</strong>: {$total_annual_salary}</td>
                    <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($employment_type)."</td>
                </tr>
                <tr>
                    <td><strong>". $lbl_gross_pay_ytd ."</strong>: {$total_gross_pay}</td>
                    <td><strong>". WidgetUtil::getLabel('total_no_of_employees') ."</strong>: {$total_employees}</td>
                </tr>
                <tr>
                    <td><strong>". $lbl_base_pay_ytd ."</strong>: {$total_base_pay}</td>
                    <td><strong>". WidgetUtil::getLabel('total_no_of_sal_employees') ."</strong>: {$number_employees}</td>
                </tr>
                <tr>
                    <td><strong>". $lbl_other_pay_ytd ."</strong>: {$total_other_payments}</td>";
            if(isset($total_overtime_employees)){
                $table .= "<td><strong>{$total_overtime_employees_label} </strong> {$total_overtime_employees}</td></tr>";
            }
            else {
                $table .= "<td></td></tr>";
            }
            $table .= "
                <tr>
                    <td><strong>". $lbl_overtime_pay_ytd ."</strong>: {$total_overtime_pay}</td>
                    <td></td>
                </tr>";
        }
        else {
            $table .=
                "<tr>
                    <td width='60%'><strong>". $lbl_gross_pay_ytd ."</strong>: {$total_gross_pay}</td>
                    <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($employment_type)."</td>
                </tr>
                <tr>
                    <td><strong>". $lbl_base_pay_ytd ."</strong>: {$total_base_pay}</td>
                    <td><strong>". WidgetUtil::getLabel('total_no_of_employees') ."</strong>: {$total_employees}</td>
                </tr>
                <tr>
                    <td><strong>". $lbl_other_pay_ytd ."</strong>: {$total_other_payments}</td>
                    <td><strong>". WidgetUtil::getLabel('total_no_of_non_sal_employees') ."</strong>: {$number_employees}</td>
                </tr>
                <tr>
                    <td><strong>". $lbl_overtime_pay_ytd ."</strong>: {$total_overtime_pay}</td>";
            if(isset($total_overtime_employees)){
                $table .= "<td><strong>{$total_overtime_employees_label} </strong> {$total_overtime_employees}</td></tr>";
            }
            else {
                $table .= "<td></td></tr>";
            }
        }
        $table .= "</table></div>";

        $employeeData .= $table;
    }

    $employeeData .= '</div>';
    if (($employment_type == PayrollType::$SALARIED && $salaried_count > 1) ||
        ($employment_type == PayrollType::$NON_SALARIED && $non_salaried_count >1)) {
        $employeeData .= "<div id='next-emp-$class'></div>";
    }
    $employeeData .= "</div>";
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
