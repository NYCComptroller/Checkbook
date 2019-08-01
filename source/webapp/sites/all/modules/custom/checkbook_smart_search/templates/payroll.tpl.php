<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2019 New York City
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

$payroll_parameter_mapping = (array)CheckbookSolr::getSearchFields($solr_datasource, 'payroll');

$agency_id = $payroll_results['agency_id'];
$dept_id = $payroll_results['department_id'];
$emp_id = $payroll_results['employee_id'];

if ('<unknown department>[001]' == $emp_id && is_numeric($payroll_results['employee_name'])) {
//  while solr is broken
//  @TODO: remove when fixed
  $emp_id = $payroll_results['employee_name'];
}

$fiscal_year_id = is_array($payroll_results['fiscal_year_id']) ? $payroll_results['fiscal_year_id'][0] : $payroll_results['fiscal_year_id'];

$fiscal_year_id = _checkbook_get_year_id($fiscal_year_id);

$salaried = $payroll_results['amount_basis_id'];
$title = urlencode($payroll_results['civil_service_title']);

switch($solr_datasource){
  case 'nycha':
  case 'oge':
    $linkable_fields = [
      "oge_agency_name" => "/payroll/agency_landing/datasource/checkbook_nycha/yeartype/C/year/" . $fiscal_year_id . "/agency/" . $agency_id,
      "agency_name" => "/payroll/agency_landing/datasource/checkbook_nycha/yeartype/C/year/" . $fiscal_year_id . "/agency/" . $agency_id,
    ];
  $agencyLandingUrl = "/agency_landing";
  $dataSourceUrl = "/datasource/checkbook_nycha/agency/" . $agency_id;
    break;
  default:
    $linkable_fields = [
      "agency_name" => "/payroll/agency_landing/yeartype/C/year/" . $fiscal_year_id . "/agency/" . $agency_id,
    ];
    $agencyLandingUrl = "";
    $dataSourceUrl = "";
}

if($payroll_results['fiscal_year'] < 2010){
    $linkable_fields = array();
}

$date_fields = array("pay_date");
$amount_fields = array("gross_pay", "base_pay", "other_payments", "overtime_pay");

$count = 1;
$row = array();
$rows = array();

foreach ($payroll_parameter_mapping as $key => $title){
  $value = $payroll_results[$key];

  if(isset($searchTerm) && $searchTerm) {
    $temp = substr($value, strpos(strtoupper($value), strtoupper($searchTerm)),strlen($searchTerm));
    $value = str_ireplace($searchTerm,'<em>'. $temp . '</em>', $value);
  }

  if(in_array($key, $amount_fields)){
    $value = custom_number_formatter_format($value, 2 , '$');
  }else if(in_array($key, $date_fields)){
    $value = date("F j, Y", strtotime($value));
  }else if(array_key_exists($key, $linkable_fields)){
    $value = "<a href='" .$linkable_fields[$key]."'>". _checkbook_smart_search_str_html_entities($value) ."</a>";
  }

  if (in_array($title, ['Annual Salary', 'Hourly Rate', 'Daily Wage'])) {
//    $url = PayrollUrlService::annualSalaryPerAgencyUrl($agency_id, $emp_id);
    if (('Annual Salary' == $title && $salaried == 1)
      || (in_array($title, ['Hourly Rate', 'Daily Wage']) && $salaried !== 1 && $value)) {
      $value = "<a  href='/payroll".$agencyLandingUrl."/yeartype/B/year/" . $fiscal_year_id . $dataSourceUrl
        . "?expandBottomContURL=/panel_html/payroll_employee_transactions/payroll/employee/transactions/agency/"
        .$agency_id . $dataSourceUrl . "/abc/" .$emp_id. "/salamttype/".$salaried."/year/"
        . $fiscal_year_id . "/yeartype/B'>". custom_number_formatter_format($value, 2 , '$') ."</a>";
    } else {
      $value = '';
    }
  }

  if ($count % 2 == 0){
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.html_entity_decode($value).'</div>';
    $rows[] = $row;
    $row = [];
  } else {
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.html_entity_decode($value).'</div>';
  }
  $count++;
}

if (sizeof($row)){
  $row[] ='';
  $rows[]=$row;
}

print theme('table',array('rows'=>$rows,'attributes'=>array('class'=>array('search-result-fields'))));
