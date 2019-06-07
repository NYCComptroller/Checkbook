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
$year = RequestUtilities::get('year')?RequestUtilities::get('year'):RequestUtilities::get('calyear');
$year_type = RequestUtilities::get('yeartype');
$employeeID = RequestUtilities::get('abc');
$agencyId =RequestUtilities::get('agency');
$data_source =RequestUtilities::get('datasource');
$original_title= PayrollUtil::getTitleByEmployeeId($employeeID,$agencyId,$year_type,$year);
$titleLatest = mb_convert_case(strtolower($original_title), MB_CASE_TITLE, "UTF-8");
$all_data = array();

foreach($node->data as $data){

    $record = array();

    $amount_basis_id = $data['amount_basis_id_amount_basis_id'];
    $employment_type = $amount_basis_id == 1 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;
    if(RequestUtilities::get('year') > 0){
        $year = RequestUtilities::get('year');
    }
    if(RequestUtilities::get('calyear') > 0){
        $year = RequestUtilities::get('calyear');
    }
    $year_type = RequestUtilities::get('yeartype');
    $original_title = $data['civil_service_title_civil_service_title'];
    $title = mb_convert_case(strtolower($original_title), MB_CASE_TITLE, "UTF-8");
    $agency_name = _shorten_word_with_tooltip(strtoupper($data['agency_name_agency_name']),25);

    //$record['title_url'] = "<a href='/payroll/title_landing/yeartype/$year_type/year/$year/title/$original_title'>{$title}</a>";
    $record['title'] = $title;
    $record['agency_name']=$data['agency_name_agency_name'];
    $record['agency_url'] = "<a href='/payroll/agency_landing/yeartype/$year_type/year/$year/datasource/{$data_source}/agency/{$data['agency_agency']}'>{$agency_name}</a>";
    $record['employment_type'] = $employment_type;
    $record['max_annual_salary'] = $data['max_annual_salary'];
    $record['pay_frequency'] = $data['pay_frequency_pay_frequency'];
    $record['total_gross_pay'] = $data['total_gross_pay'];
    $record['total_base_salary'] = $data['total_base_salary'];
    $record['total_other_payments'] = $data['total_other_payments'];
    $record['total_overtime_amount'] = $data['total_overtime_amount'];

    $all_data[$employment_type][] = $record;
}
//Order data by pay frequency
foreach($all_data as $employment_type => $employment_data) {

    $ordered_data = array();
    $data = PayrollUtil::getDataByPayFrequency("BI-WEEKLY",$employment_data);
    if(isset($data)) $ordered_data[] = $data;
    $data = PayrollUtil::getDataByPayFrequency("SEMI-MONTHLY",$employment_data);
    if(isset($data)) $ordered_data[] = $data;
    $data = PayrollUtil::getDataByPayFrequency("WEEKLY",$employment_data);
    if(isset($data)) $ordered_data[] = $data;
    $data = PayrollUtil::getDataByPayFrequency("DAILY",$employment_data);
    if(isset($data)) $ordered_data[] = $data;
    $data = PayrollUtil::getDataByPayFrequency("HOURLY",$employment_data);
    if(isset($data)) $ordered_data[] = $data;
    $data = PayrollUtil::getDataByPayFrequency("SUPPLEMENTAL",$employment_data);
    if(isset($data)) $ordered_data[] = $data;

    $all_data[$employment_type] = $ordered_data;
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

    $employeeData .= "<div class='emp-record-$class'>"; //open

    if (($employment_type == PayrollType::$SALARIED && $salaried_count > 1) ||
        ($employment_type == PayrollType::$NON_SALARIED && $non_salaried_count >1)) {
        $employeeData .= "<div id='prev-emp-$class'></div>";
    }
    $employeeData .= "<div id='emp-agency-detail-records-$class'>";

    foreach($employment_data as $data) {

        //$title_url = $data['title_url'];
        $title = $data['title'];
        $agency_url = Datasource::isNycha()? $data['agency_name']:$data['agency_url'];
        $max_annual_salary =$data['max_annual_salary'];
        $pay_frequency = $data['pay_frequency'];
        $total_gross_pay = $data['total_gross_pay'];
        $total_base_salary = $data['total_base_salary'];
        $total_other_payments = $data['total_other_payments'];
        $total_overtime_amount = $data['total_overtime_amount'];

        $table = "<div class='emp-agency-detail-record'><table id='emp-agency-detail-record-table' class='center-align'>";


        $table .= "<div id='payroll-emp-trans-name'>
                        <span class='payroll-label'>Title: </span>
                        <span class='payroll-value'>{$titleLatest}</span>
                    </div>";


        $table .= "<tr>
                        <td width='56%'><strong>". WidgetUtil::getLabel('agency_name') ."</strong>: {$agency_url}</td>
                        <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($employment_type)."</td>

                   </tr>";
        $table .= "<tr>
                        <td><strong>". ( ($employment_type == PayrollType::$SALARIED) ? WidgetUtil::getLabel('annual_salary') : WidgetUtil::getLabel('pay_rate'))  ."</strong>: $". number_format($max_annual_salary,2) ."</td>
                        <td><strong>". WidgetUtil::getLabel('pay_frequency') ."</strong>: ". strtoupper($pay_frequency)."</td>
                   </tr>";
        $table .= "<tr>
                        <td><strong>". WidgetUtil::getLabel('gross_pay_ytd') ."</strong>:$". number_format($total_gross_pay,2)."</td>
                        <td></td>
                   </tr>";
        $table .= "<tr>
                        <td><strong>". WidgetUtil::getLabel('base_pay_ytd') ."</strong>: $". number_format($total_base_salary,2)."</td>
                        <td></td>
                   </tr>";

        $table .= "<tr>
                        <td><strong>". WidgetUtil::getLabel('other_pay_ytd') ."</strong>: $". number_format($total_other_payments,2)."</td>
                        <td></td>
                   </tr>";
        $table .= "<tr>
                        <td ><strong>". WidgetUtil::getLabel('overtime_pay_ytd') ."</strong>: $".number_format($total_overtime_amount,2)."</td>
                        <td></td>
                    </tr>";

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
