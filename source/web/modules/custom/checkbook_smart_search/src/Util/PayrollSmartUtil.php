<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_smart_search\Util;

use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_solr\CheckbookSolr;

class PayrollSmartUtil {

  public static function displayPayrollResult($payroll_results, $solr_datasource) {

    $payroll_parameter_mapping = (array) CheckbookSolr::getSearchFields($solr_datasource, 'payroll');

    $agency_id = $payroll_results['agency_id'];
    $dept_id = $payroll_results['department_id'];
    $emp_id = $payroll_results['employee_id'];

    if ('<unknown department>[001]' == $emp_id && is_numeric($payroll_results['employee_name'])) {
      //  while solr is broken
      //  @TODO: remove when fixed
      $emp_id = $payroll_results['employee_name'];
    }

    $fiscal_year_id = is_array($payroll_results['calendar_fiscal_year_id']) ? $payroll_results['calendar_fiscal_year_id'][0] : $payroll_results['calendar_fiscal_year_id'];
    // id already returned by solr
    // $fiscal_year_id = _checkbook_get_year_id($fiscal_year_id);

    $salaried = $payroll_results['amount_basis_id'];
    $title = urlencode($payroll_results['civil_service_title']);

    switch ($solr_datasource) {
      case 'nycha':
        // This should be updated when the yeartype DB changes from C to B.
        $yeartype = "C";
      case 'oge':
        $linkable_fields = [
          "oge_agency_name" => "/payroll/agency_landing/datasource/checkbook_nycha/yeartype/C/year/" . $fiscal_year_id . "/agency/" . $agency_id,
          "agency_name" => "/payroll/agency_landing/datasource/checkbook_nycha/yeartype/C/year/" . $fiscal_year_id . "/agency/" . $agency_id,
        ];
        $agencyLandingUrl = "/agency_landing";
        $dataSourceUrl = "/datasource/checkbook_nycha/agency/" . $agency_id;
        break;
      default:
        $yeartype = "B";
        if ($payroll_results['payroll_type_text'][0] == 'NON-SALARIED' && $payroll_results['hourly_rate'] == 0) {
          $payroll_results['hourly_rate'] = $payroll_results['daily_wage'];
        }
        $linkable_fields = [
          "agency_name" => "/payroll/agency_landing/yeartype/" . $yeartype . "/year/" . $fiscal_year_id . "/agency/" . $agency_id,
        ];
        $agencyLandingUrl = "";
        $dataSourceUrl = "";
    }

    if ($payroll_results['fiscal_year'] < 2010) {
      $linkable_fields = [];
    }

    $date_fields = ["pay_date"];
    $amount_fields = [
      "gross_pay",
      "base_pay",
      "other_payments",
      "overtime_pay"
    ];

    $count = 1;
    $row = [];
    $rows = [];
    $out = "<div class=\"search-result-fields\"><div class=\"grid-row\">";
    foreach ($payroll_parameter_mapping as $key => $title) {
      $value = $payroll_results[$key];

      if (isset($searchTerm) && $searchTerm) {
        $temp = substr($value, strpos(strtoupper($value), strtoupper($searchTerm)), strlen($searchTerm));
        $value = str_ireplace($searchTerm, '<em>' . $temp . '</em>', $value);
      }

      if (in_array($key, $amount_fields)) {
        $value = FormattingUtilities::custom_number_formatter_format($value, 2, '$');
      }
      else {
        if (in_array($key, $date_fields)) {
          $value = date("F j, Y", strtotime(substr($value, 0, 10)));
        }
        else {
          if (array_key_exists($key, $linkable_fields)) {
            $value = "<a href='" . $linkable_fields[$key] . "'>" . _checkbook_smart_search_str_html_entities($value) . "</a>";
          }
        }
      }

      if (in_array($title, ['Annual Salary', 'Hourly Rate', 'Daily Wage'])) {
        //    $url = PayrollUrlService::annualSalaryPerAgencyUrl($agency_id, $emp_id);
        if (('Annual Salary' == $title && $salaried == 1)
          || (in_array($title, [
              'Hourly Rate',
              'Daily Wage'
            ]) && $salaried !== 1 && $value)) {
          $value = "<a  href='/payroll" . $agencyLandingUrl . "/yeartype/" . $yeartype . "/year/" . $fiscal_year_id . $dataSourceUrl
            . "?expandBottomContURL=/payroll/employee/transactions/agency/"
            . $agency_id . $dataSourceUrl . "/abc/" . $emp_id . "/salamttype/" . $salaried . "/year/"
            . $fiscal_year_id . "/yeartype/" . $yeartype . "'>" . FormattingUtilities::custom_number_formatter_format($value, 2, '$') . "</a>";
        }
        else {
          $value = '-';
        }
      }

      if ($count % 2 == 0) {
        $out .= "<div class=\"grid-col-6\">";
        if ($title) {
          $out .= '<div class="field-label">' . $title . ':</div><div class="field-content">' . html_entity_decode($value) . '</div>';
        }
        $rows[] = $row;
        $row = [];
      }
      else {
        $out .= "<div class=\"grid-col-6\">";
        if ($title) {
          $out .= '<div class="field-label">' . $title . ':</div><div class="field-content">' . html_entity_decode($value) . '</div>';
        }
      }
      $count++;
      $out .= "</div>";
    }
    $out .= "</div></div>";
    return $out;
  }

}
