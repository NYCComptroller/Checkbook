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

$payroll_parameter_mapping = _checkbook_smart_search_domain_fields('payroll');

$agency_id = $payroll_results['_source']['agency_id'];
$dept_id = $payroll_results['_source']['department_id'];
$emp_id = $payroll_results['_source']['employee_id'];
$fiscal_year_id = $payroll_results['_source']['fiscal_year_id'];
$salaried = $payroll_results['_source']['amount_basis_id'];
$title = $payroll_results['_source']['civil_service_title'];


$linkable_fields = array(
                         "agency_name" => "/payroll/agency_landing/yeartype/B/year/". $fiscal_year_id ."/agency/". $agency_id,
                        );

if($payroll_results['fiscal_year'] < 2010){
    $linkable_fields = array();
}

$date_fields = array("pay_date");
$amount_fields = array("gross_pay", "base_pay", "other_payments", "overtime_pay");

$count = 1;
$row = array();
$rows = array();
foreach ($payroll_parameter_mapping as $key => $title){
  $value = $payroll_results['_source'][$key];

 // $temp = substr($value, strpos(strtoupper($value), strtoupper($SearchTerm)),strlen($SearchTerm));
  $value = str_ireplace($SearchTerm,'<em>'. $temp . '</em>', $value);

  if(in_array($key, $amount_fields)){
    $value = custom_number_formatter_format($value, 2 , '$');
  }else if(in_array($key, $date_fields)){
    $value = date("F j, Y", strtotime($value));
  }else if(array_key_exists($key, $linkable_fields)){
    $value = "<a href='" .$linkable_fields[$key]."'>". _checkbook_smart_search_str_html_entities($value) ."</a>";
  }

  if($title == 'Annual Salary'){
      if($salaried == 1){
          $value = "<a  href='/payroll/yeartype/B/year/" . $fiscal_year_id . "?expandBottomContURL=/panel_html/payroll_employee_transactions/payroll/employee/transactions/agency/" .$agency_id. "/abc/" .$emp_id. "/salamttype/".$salaried."/year/" . $fiscal_year_id . "/yeartype/B'>". custom_number_formatter_format($value, 2 , '$') ."</a>";
      }
      else{
          $value = '';
      }
  }

  if($title == 'Hourly Rate'){
        if($salaried != 1){
            $value = "<a  href='/payroll/yeartype/B/year/" . $fiscal_year_id . "?expandBottomContURL=/panel_html/payroll_employee_transactions/payroll/employee/transactions/agency/" .$agency_id. "/abc/" .$emp_id. "/salamttype/".$salaried."/year/" . $fiscal_year_id . "/yeartype/B'>". custom_number_formatter_format($value, 2 , '$') ."</a>";
        }
        else{
            $value = '';
        }
  }

  if ($count % 2 == 0){
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.html_entity_decode($value).'</div>';
    $rows[] = $row;
    $row = array();
  } else {
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.html_entity_decode($value).'</div>';
  }
  $count++;
}
print theme('table',array('rows'=>$rows,'attributes'=>array('class'=>array('search-result-fields'))));
