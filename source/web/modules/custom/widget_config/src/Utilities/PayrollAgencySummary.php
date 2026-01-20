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

namespace Drupal\widget_config\Utilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\PayrollUtilities\PayrollType;
use Drupal\checkbook_project\PayrollUtilities\PayrollUtil;
use Drupal\checkbook_project\WidgetUtilities\WidgetUtil;

class PayrollAgencySummary
{
  /**
   * Display payroll agency summary.
   *
   * @param $node
   * @return void
   */
  public static function payrollAgency($node)
  {
    $all_data = array();

    foreach ($node->data as $data) {
      $record = array();
      $year = $data['fiscal_year_id'];
      $year_type = $data['type_of_year'];
      $employment_type = $data['type_of_employment'];
      $agency_name = FormattingUtilities::_shorten_word_with_tooltip(strtoupper($data['agency_name']), 25);
      $data_source = Datasource::isNycha() ? RequestUtilities::_getUrlParamString('datasource') : null;

      // Store both raw and formatted values
      $record['data_source'] = Datasource::isNycha() ? 'NYCHA' : 'citywide';
      $record['total_annual_salary'] = $node->total_annual_salary;
      $record['total_annual_salary_formatted'] = FormattingUtilities::custom_number_formatter_format($node->total_annual_salary, 2, '$');
      $record['total_gross_pay'] = $data['total_gross_pay'];
      $record['total_gross_pay_formatted'] = FormattingUtilities::custom_number_formatter_format($data['total_gross_pay'], 2, '$');
      $record['total_base_pay'] = $data['total_base_pay'];
      $record['total_base_pay_formatted'] = FormattingUtilities::custom_number_formatter_format($data['total_base_pay'], 2, '$');
      $record['total_other_payments'] = $data['total_other_payments'];
      $record['total_other_payments_formatted'] = FormattingUtilities::custom_number_formatter_format($data['total_other_payments'], 2, '$');
      $record['total_overtime_pay'] = $data['total_overtime_pay'];
      $record['total_overtime_pay_formatted'] = FormattingUtilities::custom_number_formatter_format($data['total_overtime_pay'], 2, '$');
      $record['total_employees'] = $data['total_employees'];
      $record['total_employees_formatted'] = number_format($data['total_employees']);
      $record['total_overtime_employees'] = isset($data['total_overtime_employees']) ? $data['total_overtime_employees'] : 0;
      $record['total_overtime_employees_formatted'] = number_format($record['total_overtime_employees']);
      $record['number_employees'] = $data['number_employees'];
      $record['number_employees_formatted'] = number_format($data['number_employees']);
      $record['agency_name'] = $data['agency_name'];
      $record['agency_url'] = "<a href='/payroll/agency_landing/yeartype/$year_type/year/$year{$data_source}/agency/{$data['agency_id']}'>{$agency_name}</a>";

      $all_data[$employment_type][] = $record;
    }

    $salaried_count = is_array($all_data[PayrollType::$SALARIED]) ? count($all_data[PayrollType::$SALARIED]) : 0;
    $non_salaried_count = is_array($all_data[PayrollType::$NON_SALARIED]) ? count($all_data[PayrollType::$NON_SALARIED]) : 0;

    // Default view based on salamttype in url
    $default_view = $salaried_count > 0 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;
    $salamttype = RequestUtilities::getTransactionsParams('salamttype');
    $payroll_type = PayrollUtil::getPayrollType();

    if (isset($salamttype)) {
      $salamttype = explode('~', $salamttype);
      if (!in_array(1, $salamttype)) {
        $default_view = PayrollType::$NON_SALARIED;
      }
    }
    if ($payroll_type == PayrollType::$NON_SALARIED) {
      $default_view = PayrollType::$NON_SALARIED;
    }

    // RETURN data array, don't print HTML
    return [
      'all_data' => $all_data,
      'node' => $node,
      'salaried_count' => $salaried_count,
      'non_salaried_count' => $non_salaried_count,
      'default_view' => $default_view,
      'show_agency' => (RequestUtilities::getTransactionsParams('dtsmnid') != 325),
      'smnid' => RequestUtilities::getTransactionsParams('smnid'),
      'total_employees_formatted' => number_format($node->total_employees),
    ];
  }

  /**
   * Display employee agency payroll summary.
   *
   * @param $node
   * @return void
   */
  public static function payrollEmpAgency($node)
  {
    $year = RequestUtilities::getTransactionsParams('year') ?: RequestUtilities::getTransactionsParams('calyear');
    $year_type = RequestUtilities::getTransactionsParams('yeartype');
    $employeeID = RequestUtilities::getTransactionsParams('abc');
    $agencyId = RequestUtilities::getTransactionsParams('agency');
    $data_source = Datasource::isNycha() ? RequestUtilities::_getUrlParamString('datasource') : null;
    $original_title = PayrollUtil::getTitleByEmployeeId($employeeID, $agencyId, $year_type, $year);
    $titleLatest = mb_convert_case(strtolower($original_title), MB_CASE_TITLE, "UTF-8");
    $all_data = array();

    foreach ($node->data as $data) {
      $record = array();
      $amount_basis_id = $data['amount_basis_id_amount_basis_id'];
      $employment_type = $amount_basis_id == 1 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;

      if (RequestUtilities::getTransactionsParams('year') > 0) {
        $year = RequestUtilities::getTransactionsParams('year');
      }
      if (RequestUtilities::getTransactionsParams('calyear') > 0) {
        $year = RequestUtilities::getTransactionsParams('calyear');
      }

      $year_type = RequestUtilities::getTransactionsParams('yeartype');
      $original_title = $data['civil_service_title_civil_service_title'];
      $title = mb_convert_case(strtolower($original_title), MB_CASE_TITLE, "UTF-8");
      $agency_name = FormattingUtilities::_shorten_word_with_tooltip(strtoupper($data['agency_name_agency_name']), 25);

      // Store both raw and formatted values
      $record['data_source'] = Datasource::isNycha() ? 'NYCHA' : 'citywide';
      $record['title'] = $title;
      $record['agency_name'] = $data['agency_name_agency_name'];
      $record['agency_url'] = "<a href='/payroll/agency_landing/yeartype/$year_type/year/$year{$data_source}/agency/{$data['agency_agency']}'>{$agency_name}</a>";
      $record['employment_type'] = $employment_type;
      $record['max_annual_salary'] = $data['max_annual_salary'];
      $record['max_annual_salary_formatted'] = number_format($data['max_annual_salary'], 2);
      $record['pay_frequency'] = $data['pay_frequency_pay_frequency'];
      $record['total_gross_pay'] = $data['total_gross_pay'];
      $record['total_gross_pay_formatted'] = number_format($data['total_gross_pay'], 2);
      $record['total_base_salary'] = $data['total_base_salary'];
      $record['total_base_salary_formatted'] = number_format($data['total_base_salary'], 2);
      $record['total_other_payments'] = $data['total_other_payments'];
      $record['total_other_payments_formatted'] = number_format($data['total_other_payments'], 2);
      $record['total_overtime_amount'] = $data['total_overtime_amount'];
      $record['total_overtime_amount_formatted'] = number_format($data['total_overtime_amount'], 2);

      $all_data[$employment_type][] = $record;
    }

    // Order data by pay frequency
    foreach ($all_data as $employment_type => $employment_data) {
      $ordered_data = array();

      $frequencies = ["BI-WEEKLY", "SEMI-MONTHLY", "WEEKLY", "DAILY", "HOURLY", "SUPPLEMENTAL"];
      foreach ($frequencies as $frequency) {
        $data = PayrollUtil::getDataByPayFrequency($frequency, $employment_data);
        if (isset($data)) {
          $ordered_data[] = $data;
        }
      }

      $all_data[$employment_type] = $ordered_data;
    }

    $salaried_count = is_array($all_data[PayrollType::$SALARIED]) ? count($all_data[PayrollType::$SALARIED]) : 0;
    $non_salaried_count = is_array($all_data[PayrollType::$NON_SALARIED]) ? count($all_data[PayrollType::$NON_SALARIED]) : 0;

    // Default view based on salamttype in url
    $default_view = $salaried_count > 0 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;
    $salamttype = RequestUtilities::getTransactionsParams('salamttype');

    if (isset($salamttype)) {
      $salamttype = explode('~', $salamttype);
      if (!in_array(1, $salamttype)) {
        $default_view = PayrollType::$NON_SALARIED;
      }
    }

    // RETURN data array, don't print HTML
    return [
      'all_data' => $all_data,
      'titleLatest' => $titleLatest,
      'salaried_count' => $salaried_count,
      'non_salaried_count' => $non_salaried_count,
      'default_view' => $default_view,
    ];
  }
}
