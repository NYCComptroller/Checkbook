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

            jQuery(document).ready(function() {
                if (jQuery('#emp-agency-detail-records').filter(':first').length > 0) {
                    jQuery('#emp-agency-detail-records').filter(':first')
                        .cycle({
                            slideExpr:'.emp-agency-detail-record',
                            prev: '#prev-emp',
                            next: '#next-emp',
                            fx: 'scrollVert',
                            speed: 0,
                            width:'640px',
                            timeout: 0
                        });
                }
            });

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

    $employeeData .= "<div id='prev-emp' href='#'></div>";

    $employeeData .= "<div id='emp-agency-detail-records'>";

    foreach($node->data as $data){

        $typeOfEmployment = $data['employment_type_employment_type'];
        $class = strtolower($typeOfEmployment);
        $year = $data['year_year'];
        $yearType = $data['year_type_year_type'];
        $agency = strtoupper($data['agency_agency_agency_name']);
        $agencyUrl  = "<a href='/payroll/agency_landing/yeartype/$yearType/year/$year/agency/{$data['agency_agency']}'>{$agency}</a>";

        $original_title = $data['civil_service_title_civil_service_title'];
        $title = mb_convert_case(strtolower($original_title), MB_CASE_TITLE, "UTF-8");
        $original_title = urlencode($original_title);

        $titleUrl = "<a href='/payroll/title_landing/yeartype/$yearType/year/$year/title/$original_title'>{$title}</a>";

        $table = "<div class='emp-agency-detail-record'><table id='emp-agency-detail-record-table' class='emp-record-$class'>";

        $table .= "<div id='payroll-emp-trans-name' class='emp-record-$class'>
                        <span class='payroll-label'>Title: </span>
                        <span class='payroll-value'>{$titleUrl}</span>
                    </div>";


        $table .= "<tr>
                        <td width='60%'><strong>". WidgetUtil::getLabel('agency_name') ."</strong>: {$agencyUrl}</td>
                        <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($data['employment_type_employment_type'])."</td>

                   </tr>";
        $table .= "<tr>
                        <td><strong>". ( ($typeOfEmployment == 'Salaried') ? WidgetUtil::getLabel('annual_salary') : WidgetUtil::getLabel('pay_rate'))  ."</strong>: $". number_format($data['max_annual_salary'],2) ."</td>
                        <td><strong>". WidgetUtil::getLabel('pay_frequency') ."</strong>: ". strtoupper($data['pay_frequency_pay_frequency'])."</td>
                   </tr>";
        $table .= "<tr>
                        <td><strong>". WidgetUtil::getLabel('gross_pay_ytd') ."</strong>:$". number_format($data['total_gross_pay'],2)."</td>
                        <td></td>
                   </tr>";
        $table .= "<tr>
                        <td><strong>". WidgetUtil::getLabel('base_pay_ytd') ."</strong>: $". number_format($data['total_base_salary'],2)."</td>
                        <td></td>
                   </tr>";

        $table .= "<tr>
                        <td><strong>". WidgetUtil::getLabel('other_pay_ytd') ."</strong>: $". number_format($data['total_other_payments'],2)."</td>
                        <td></td>
                   </tr>";
        $table .= "<tr>
                        <td ><strong>". WidgetUtil::getLabel('overtime_pay_ytd') ."</strong>: $".number_format($data['total_overtime_amount'],2)."</td>
                        <td></td>
                    </tr>";

        $table .= "</table></div>";

        $employeeData .= $table;
    }
    if (count($node->data) > 1) {
        $employeeData .= "</div><div id='next-emp' href='#'></div>";
        $employeeData .= "<div id='toggle-employee-salaried' class='emp-record-salaried'>
                            <strong>Viewing Salaried Details</strong>&nbsp;|&nbsp;
                            <a href='javascript:toggleEmployee();'>View Non-salaried Details</a>
                          </div>";
        $employeeData .= "<div id='toggle-employee-non-salaried' class='emp-record-non-salaried'>
                            <a href='javascript:toggleEmployee();'>View Salaried Details</a>&nbsp;|&nbsp;
                            <strong>Viewing Non-salaried Details</strong>
                          </div>";
        $employeeData .= "</div>";
    }
    else {
        $employeeData .= "</div></div>";
    }

    print $employeeData;
}