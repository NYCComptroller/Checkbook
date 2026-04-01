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

  public static function displayPayrollResult($payroll_results, $solr_datasource,$searchTerm = null) {

    $payroll_parameter_mapping = (array) CheckbookSolr::getSearchFields($solr_datasource, 'payroll');

    // Extract IDs
    $agency_id = $payroll_results['agency_id'];
    $dept_id = $payroll_results['department_id'];
    $emp_id = $payroll_results['employee_id'];

    // Handle broken Solr data
    if ('<unknown department>[001]' == $emp_id && is_numeric($payroll_results['employee_name'])) {
      // @TODO: remove when fixed
      $emp_id = $payroll_results['employee_name'];
    }

    $fiscal_year_id = is_array($payroll_results['calendar_fiscal_year_id'])
      ? $payroll_results['calendar_fiscal_year_id'][0]
      : $payroll_results['calendar_fiscal_year_id'];

    $salaried = $payroll_results['amount_basis_id'];
    $title = urlencode($payroll_results['civil_service_title']);

    // Configure datasource-specific settings
    $config = self::getDatasourceConfig($solr_datasource, $agency_id, $fiscal_year_id);

    // Prepare linkable fields configuration
    $linkable_fields = self::getLinkableFields(
      $solr_datasource,
      $agency_id,
      $fiscal_year_id,
      $config['yeartype'],
      $payroll_results
    );

    // Process each field
    $processed_fields = self::processFields(
      $payroll_results,
      $payroll_parameter_mapping,
      $linkable_fields,
      $searchTerm,
      $agency_id,
      $emp_id,
      $fiscal_year_id,
      $salaried,
      $config
    );

    return [
      'fields' => $processed_fields,
      'datasource' => $solr_datasource,
    ];
  }

  /**
   * Get datasource-specific configuration.
   */
  private static function getDatasourceConfig($solr_datasource, $agency_id, $fiscal_year_id) {
    switch ($solr_datasource) {
      case 'nycha':
      case 'oge':
        return [
          'yeartype' => 'C',
          'agency_landing_url' => '/agency_landing',
          'datasource_url' => '/datasource/checkbook_nycha/agency/' . $agency_id,
        ];

      default:
        return [
          'yeartype' => 'B',
          'agency_landing_url' => '',
          'datasource_url' => '',
        ];
    }
  }

  /**
   * Build linkable fields configuration.
   */
  private static function getLinkableFields($solr_datasource, $agency_id, $fiscal_year_id, $yeartype, $payroll_results) {
    switch ($solr_datasource) {
      case 'nycha':
      case 'oge':
        $base_url = "/payroll/agency_landing/datasource/checkbook_nycha/yeartype/C/year/" . $fiscal_year_id . "/agency/" . $agency_id;
        return [
          "oge_agency_name" => $base_url,
          "agency_name" => $base_url,
        ];

      default:
        return [
          "agency_name" => "/payroll/agency_landing/yeartype/" . $yeartype . "/year/" . $fiscal_year_id . "/agency/" . $agency_id,
        ];
    }
  }

  /**
   * Process all fields for display.
   */
  private static function processFields(
    $payroll_results,
    $payroll_parameter_mapping,
    $linkable_fields,
    $searchTerm,
    $agency_id,
    $emp_id,
    $fiscal_year_id,
    $salaried,
    $config
  ) {
    $date_fields = ["pay_date"];
    $amount_fields = ["gross_pay", "base_pay", "other_payments", "overtime_pay"];
    $salary_fields = ['Annual Salary', 'Hourly Rate', 'Daily Wage'];

    // Handle non-salaried hourly rate
    if (isset($payroll_results['payroll_type_text'][0])
      && $payroll_results['payroll_type_text'][0] == 'NON-SALARIED'
      && $payroll_results['hourly_rate'] == 0) {
      $payroll_results['hourly_rate'] = $payroll_results['daily_wage'];
    }

    // Disable links for years before 2010
    if ($payroll_results['fiscal_year'] < 2010) {
      $linkable_fields = [];
    }

    $processed = [];
    foreach ($payroll_parameter_mapping as $key => $field_title) {
      $value = $payroll_results[$key] ?? '';

      $processed[] = [
        'key' => $key,
        'label' => $field_title,
        'value' => self::formatFieldValue(
          $key,
          $value,
          $field_title,
          $date_fields,
          $amount_fields,
          $salary_fields,
          $linkable_fields,
          $searchTerm,
          $agency_id,
          $emp_id,
          $fiscal_year_id,
          $salaried,
          $config
        ),
        'raw_value' => $value,
      ];
    }

    return $processed;
  }

  /**
   * Format a single field value.
   */
  private static function formatFieldValue(
    $key,
    $value,
    $title,
    $date_fields,
    $amount_fields,
    $salary_fields,
    $linkable_fields,
    $searchTerm,
    $agency_id,
    $emp_id,
    $fiscal_year_id,
    $salaried,
    $config
  ) {
    // Apply search term highlighting
    if ($searchTerm && $value) {
      $value = self::highlightSearchTerm($value, $searchTerm);
    }

    // Format amount fields
    if (in_array($key, $amount_fields)) {
      return [
        'type' => 'amount',
        'formatted' => FormattingUtilities::custom_number_formatter_format($value, 2, '$'),
        'is_link' => false,
      ];
    }

    // Format date fields
    if (in_array($key, $date_fields)) {
      return [
        'type' => 'date',
        'formatted' => date("F j, Y", strtotime(substr($value, 0, 10))),
        'is_link' => false,
      ];
    }

    // Format salary-related fields with special linking
    if (in_array($title, $salary_fields)) {
      return self::formatSalaryField(
        $title,
        $value,
        $salaried,
        $agency_id,
        $emp_id,
        $fiscal_year_id,
        $config
      );
    }

    // Format linkable fields
    if (array_key_exists($key, $linkable_fields)) {
      return [
        'type' => 'link',
        'formatted' => _checkbook_smart_search_str_html_entities($value),
        'is_link' => true,
        'url' => $linkable_fields[$key],
      ];
    }

    // Default formatting
    return [
      'type' => 'text',
      'formatted' => $value,
      'is_link' => false,
    ];
  }

  /**
   * Format salary-related fields.
   */
  private static function formatSalaryField($title, $value, $salaried, $agency_id, $emp_id, $fiscal_year_id, $config) {
    $yeartype = $config['yeartype'];
    $agency_landing_url = $config['agency_landing_url'];
    $datasource_url = $config['datasource_url'];

    $show_value = false;
    if ('Annual Salary' == $title && $salaried == 1) {
      $show_value = true;
    }
    elseif (in_array($title, ['Hourly Rate', 'Daily Wage']) && $salaried !== 1 && $value) {
      $show_value = true;
    }

    if ($show_value) {
      $url = '/payroll' . $agency_landing_url . '/yeartype/' . $yeartype . '/year/' . $fiscal_year_id . $datasource_url
        . '?expandBottomContURL=/payroll/employee/transactions/agency/'
        . $agency_id . $datasource_url . '/abc/' . $emp_id . '/salamttype/' . $salaried . '/year/'
        . $fiscal_year_id . '/yeartype/' . $yeartype;

      return [
        'type' => 'salary',
        'formatted' => FormattingUtilities::custom_number_formatter_format($value, 2, '$'),
        'is_link' => true,
        'url' => $url,
      ];
    }

    return [
      'type' => 'salary',
      'formatted' => '-',
      'is_link' => false,
    ];
  }

  /**
   * Highlight search term in value.
   */
  private static function highlightSearchTerm($value, $searchTerm) {
    if (!$value || !$searchTerm) {
      return $value;
    }

    $pos = stripos($value, $searchTerm);
    if ($pos !== false) {
      $temp = substr($value, $pos, strlen($searchTerm));
      $value = str_ireplace($searchTerm, '<em>' . $temp . '</em>', $value);
    }

    return $value;
  }

}
