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
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\ContractsUtilities\ContractURLHelper;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\checkbook_solr\CheckbookSolr;

class SpendingSmartUtil {

  public static function displaySpendingResult($spending_results, $solr_datasource):array{
    $spending_parameter_mapping = CheckbookSolr::getSearchFields($solr_datasource, 'spending');

    $isNycha = ('nycha' === $solr_datasource);
    $isEdc = ('edc' === $solr_datasource);
    $isOge = $isNycha || $isEdc;

    // Limit year id up unitl current year
    $actual_fiscal_year_id = CheckbookDateUtil::getCurrentFiscalYearId();
    $fiscal_year_id = ($spending_results['fiscal_year_id'][0] != '' && $spending_results['fiscal_year_id'][0] <= $actual_fiscal_year_id)
      ? $spending_results['fiscal_year_id'][0]
      : $actual_fiscal_year_id;

    // Safely resolve commonly used fields once
    $spending_category_id   = $spending_results['spending_category_id']   ?? '';
    $spending_category_name = $spending_results['spending_category_name']  ?? '';
    $agency_id              = $spending_results['agency_id']               ?? '';
    $vendor_id              = $spending_results['vendor_id']               ?? '';
    $vendor_name            = $spending_results['vendor_name']             ?? '';
    $is_prime_or_sub        = strtolower($spending_results['is_prime_or_sub']    ?? '');
    $is_minority_vendor     = strtolower($spending_results['is_minority_vendor'] ?? '');
    $minority_type_name     = $spending_results['minority_type_name']      ?? '';
    $minority_type_id       = $spending_results['minority_type_id']        ?? '';
    $agreement_id           = $spending_results['agreement_id']            ?? '';
    $contract_number        = $spending_results['contract_number']         ?? '';
    $contract_orig_agr_id   = $spending_results['contract_original_agreement_id'] ?? '';

    // --- Build linkable_fields ---
    if ($isOge) {
      $linkable_fields = [
        'oge_agency_name' => "/spending_landing/category/{$spending_category_id}/datasource/checkbook_oge/year/{$fiscal_year_id}/yeartype/B/agency/{$agency_id}",
        'vendor_name'     => "/spending_landing/category/{$spending_category_id}/datasource/checkbook_oge/agency/{$agency_id}/year/{$fiscal_year_id}/yeartype/B/vendor/{$vendor_id}",
      ];
    }
    else {
      $agency_link = "/spending_landing/category/{$spending_category_id}/year/{$fiscal_year_id}/yeartype/B/agency/{$agency_id}";
      $minority_ids = MappingUtil::getTotalMinorityIds('url');

      if ($spending_category_name === 'Payroll') {
        $linkable_fields = [
          'agency_name' => $agency_link,
          'vendor_name' => NULL, // plain text
        ];
      }
      elseif ($vendor_id == 1) {
        $linkable_fields = [
          'agency_name' => $agency_link,
          'vendor_name' => "/spending_landing/category/{$spending_category_id}/year/{$fiscal_year_id}/yeartype/B/vendor/{$vendor_id}",
        ];
      }
      elseif ($is_prime_or_sub === 'yes' && $is_minority_vendor === 'n') {
        $linkable_fields = [
          'agency_name' => $agency_link,
          'vendor_name' => "/spending_landing/category/{$spending_category_id}/year/{$fiscal_year_id}/yeartype/B/subvendor/{$vendor_id}/dashboard/ss",
        ];
      }
      elseif ($is_prime_or_sub === 'no' && $is_minority_vendor === 'y') {
        $linkable_fields = [
          'agency_name' => $agency_link,
          'vendor_name' => "/spending_landing/yeartype/B/year/{$fiscal_year_id}/category/{$spending_category_id}/mwbe/{$minority_ids}/dashboard/mp/vendor/{$vendor_id}",
        ];
      }
      elseif ($is_prime_or_sub === 'no' && $is_minority_vendor === 'n') {
        $linkable_fields = [
          'agency_name' => $agency_link,
          'vendor_name' => "/spending_landing/category/{$spending_category_id}/year/{$fiscal_year_id}/yeartype/B/vendor/{$vendor_id}",
        ];
      }
      elseif ($is_prime_or_sub === 'yes' && $is_minority_vendor === 'y') {
        $linkable_fields = [
          'agency_name' => $agency_link,
          'vendor_name' => "/spending_landing/yeartype/B/year/{$fiscal_year_id}/category/{$spending_category_id}/mwbe/{$minority_ids}/dashboard/ms/subvendor/{$vendor_id}",
        ];
      }
      else {
        $linkable_fields = [
          'agency_name' => $agency_link,
          'vendor_name' => "/spending_landing/category/{$spending_category_id}/year/{$fiscal_year_id}/yeartype/B/vendor/{$vendor_id}",
        ];
      }
    }

    // Disable all links for old fiscal years
    if ($fiscal_year_id < 111) {
      $linkable_fields = [];
    }

    $date_fields   = $isOge ? [] : ['check_eft_issued_date'];
    $amount_fields = ['check_amount'];

    $spending_results['check_eft_issued_date'] = $isOge ? 'N/A' : ($spending_results['check_eft_issued_date'] ?? '');

    // --- Build structured fields array ---
    $fields = [];
    foreach ($spending_parameter_mapping as $key => $title) {
      if (!$title) {
        continue;
      }

      // FIX: safely access array keys that may not exist in sparse Solr results
      $value = ($key === 'expenditure_object_name')
        ? ($spending_results[$key][0] ?? '')
        : ($spending_results[$key] ?? '');

      // Resolve is_prime_or_sub title link
      $title_link = NULL;
      if ($key === 'is_prime_or_sub' && !preg_match('/newwindow/', RequestUtilities::getCurrentPageUrl())) {
        $title_link = 'https://comptroller.nyc.gov/reports/contract-primer/#subcontracts';
      }

      // Resolve value link
      $value_link = NULL;
      if (array_key_exists($key, $linkable_fields) && $linkable_fields[$key] !== NULL) {
        $value_link = $linkable_fields[$key];
      }

      // Format dates
      if (in_array($key, $date_fields) && $value) {
        $value = date('F j, Y', strtotime(substr($value, 0, 10)));
      }

      // Format amounts
      if (in_array($key, $amount_fields)) {
        $value = FormattingUtilities::custom_number_formatter_format($value, 2, '$');
      }

      // Contract number link
      // FIX: use pre-resolved $agreement_id and $contract_number variables
      $contract_link = NULL;
      $is_new_window = FALSE;
      if ($key === 'contract_number' && !empty($agreement_id)) {
        $is_new_window = TRUE;
        $base = '/contract_details' . ($isOge ? '/datasource/checkbook_oge' : '');
        $resolved_agreement_id = ($is_prime_or_sub === 'yes')
          ? $contract_orig_agr_id
          : $agreement_id;
        $contract_link = $base
          . ContractURLHelper::_checkbook_project_get_contract_url($contract_number, $resolved_agreement_id)
          . '/newwindow';
      }

      // Vendor name fallback — no link if no vendor_id
      if ($key === 'vendor_name' && empty($vendor_id)) {
        $value_link = NULL;
      }

      // Minority type handling
      $minority_link = NULL;
      if ($key === 'minority_type_name') {
        if (empty($minority_type_name)) {
          $value = 'N/A';
        }
        else {
          // FIX: use pre-resolved $minority_type_id
          $mwbe_id = $minority_type_id;
          if (in_array($mwbe_id, ['4', '5', '10'])) {
            $mwbe_id = '4~5~10';
          }
          if (!in_array($minority_type_id, ['7', '11']) && $fiscal_year_id >= 111) {
            $dashboard    = ($is_prime_or_sub === 'yes') ? 'ms' : 'mp';
            $minority_link = "/spending_landing/yeartype/B/year/{$fiscal_year_id}/mwbe/{$mwbe_id}/dashboard/{$dashboard}";
          }
        }
      }

      $fields[] = [
        'key'           => $key,
        'title'         => $title,
        'title_link'    => $title_link,
        'value'         => $value,
        'value_link'    => $value_link ?? $contract_link,
        'minority_link' => $minority_link,
        'is_new_window' => $is_new_window,
      ];
    }

    return [
      'domain' => 'spending',
      'fields' => $fields,
    ];
  }

}
