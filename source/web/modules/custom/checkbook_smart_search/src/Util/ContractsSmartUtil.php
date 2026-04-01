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
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;
use Drupal\checkbook_solr\CheckbookSolr;

class ContractsSmartUtil {

  public static function displayContractsResult($contracts_results, $solr_datasource) :array {

    $contracts_parameter_mapping = (array) CheckbookSolr::getSearchFields($solr_datasource, 'contracts');

    $isNycha = ('nycha' === $solr_datasource);
    $isEdc = ('edc' === $solr_datasource);
    $isOge = $isNycha || $isEdc;
    $current_year_id = CheckbookDateUtil::getCurrentFiscalYearId();

    // Safely resolve commonly used fields once
    $contract_status_raw    = $contracts_results['contract_status']           ?? '';
    $contract_category_name = strtolower($contracts_results['contract_category_name'] ?? '');
    $agency_id              = $contracts_results['agency_id']                 ?? '';
    $vendor_id              = $contracts_results['vendor_id']                 ?? '';
    $vendor_name            = $contracts_results['vendor_name']               ?? '';
    $is_prime_or_sub        = $contracts_results['is_prime_or_sub']           ?? '';
    $is_minority_vendor     = $contracts_results['is_minority_vendor']        ?? '';
    $minority_type_name     = $contracts_results['minority_type_name']        ?? '';
    $minority_type_id       = $contracts_results['minority_type_id']          ?? '';
    $document_code          = $contracts_results['document_code']             ?? '';
    $original_agreement_id  = $contracts_results['original_agreement_id']    ?? '';
    $master_agreement_id    = $contracts_results['master_agreement_id']       ?? '';
    $contract_type_code     = $contracts_results['contract_type_code']        ?? '';
    $oge_agency_id          = $contracts_results['oge_agency_id']             ?? '';
    $contract_number        = $contracts_results['contract_number']           ?? '';
    $parent_contract_number = $contracts_results['parent_contract_number']    ?? '';
    $fms_pending_number     = $contracts_results['fms_pending_contract_number'] ?? '';
    $document_version       = $contracts_results['document_version']          ?? '';
    $contract_orig_agr_id   = $contracts_results['contract_original_agreement_id'] ?? '';
    $agreement_end_year_id  = $contracts_results['agreement_end_year_id']     ?? 0;
    $agreement_type_name    = $contracts_results['agreement_type_name']       ?? '';

    $contract_status        = '';
    $reg_fiscal_year        = '';
    $reg_fiscal_year_id     = '';
    $agency_link            = '';
    $vendor_link            = '';
    $contract_id_link       = '';
    $master_contract_id_link = '';
    $linkable_fields        = [];

    // --- Registered contracts ---
    if (strtolower($contracts_results['contract_status']) == 'registered') {
      $contract_status = 'Registered';
      $reg_fiscal_year = $contracts_results['registered_fiscal_year'];
      $contracts_results['contract_status'] = 'Registered';
      $reg_fiscal_year_id = CheckbookDateUtil::_getYearIDFromValue($reg_fiscal_year);
      $landing_page = (strtolower($contracts_results['contract_category_name']) == 'revenue') ? '/contracts_revenue_landing' : '/contracts_landing';
      $landing_page_url =  $landing_page . "/status/R/yeartype/B/year/" . $reg_fiscal_year_id;

      if ($isOge) {
        $vendor_link = $landing_page_url . '/datasource/checkbook_oge/agency/' . $agency_id . '/vendor/' . $vendor_id;
        $agency_link = $landing_page_url . '/datasource/checkbook_oge/agency/' . $agency_id;
      }
      else {
        if ($is_minority_vendor === 'Y' && $is_prime_or_sub === 'Yes') {
          $vendor_link = $landing_page_url . '/subvendor/' . $vendor_id . '/dashboard/ss';
        }
        elseif ($is_minority_vendor === 'Y' && $is_prime_or_sub === 'No') {
          $vendor_link = $landing_page_url . '/mwbe/1~2~3~4~5~10~6~9~99/dashboard/mp/vendor/' . $vendor_id;
        }
        elseif ($is_minority_vendor === 'N' && $is_prime_or_sub === 'Yes') {
          $vendor_link = $landing_page_url . '/subvendor/' . $vendor_id . '/dashboard/ss';
        }
        else {
          $vendor_link = $landing_page_url . '/vendor/' . $vendor_id;
        }
        $agency_link = $landing_page_url . '/agency/' . $agency_id;
      }

      // Build contract_id_link
      $contract_id_link      = $landing_page . '/status/R';
      $contract_id_link_year = '/year/' . $reg_fiscal_year_id;
      $oge_agency_segment    = $isOge ? '/datasource/checkbook_oge/agency/' . $oge_agency_id : '';

      if ($is_prime_or_sub === 'Yes') {
        $contract_id_link .= $contract_id_link_year . $oge_agency_segment . '/dashboard/ss?expandBottomContURL=/contract_details';
      }
      else {
        $contract_id_link .= $contract_id_link_year . $oge_agency_segment . '?expandBottomContURL=/contract_details';
      }

      if (in_array($document_code, ['MA1', 'MMA1', 'RCT1'])) {
        $contract_id_link .= '/magid/' . $original_agreement_id . '/doctype/' . $document_code;
      }
      else {
        if (!empty($master_agreement_id)) {
          $master_contract_id_link = $contract_id_link . '/magid/' . $master_agreement_id . '/doctype/MMA1';
        }
        $agid = ($is_prime_or_sub === 'Yes') ? $contract_orig_agr_id : $original_agreement_id;
        $contract_id_link .= '/agid/' . $agid . '/doctype/' . $document_code;
      }

      if ($isOge) {
        $contract_id_link        .= '/datasource/checkbook_oge';
        $master_contract_id_link .= '/datasource/checkbook_oge';
      }
    }

    // --- Pending contracts ---
    elseif (strtolower($contract_status_raw) === 'pending') {
      $current_year = '/yeartype/B/year/' . $current_year_id;

      if ($contract_category_name === 'expense') {
        $base         = '/contracts_pending_exp_landing';
        $agency_link  = $base . $current_year . '/agency/' . $agency_id;
        $vendor_link  = $base . $current_year . '/vendor/' . $vendor_id;
      }
      else {
        $base         = '/contracts_pending_rev_landing';
        $agency_link  = $base . $current_year . '/agency/' . $agency_id;
        $vendor_link  = $base . $current_year . '/vendor/' . $vendor_id;
      }

      $year_url = CustomURLHelper::_checkbook_project_get_year_url_param_string();
      $oge_segment = $isOge ? '/datasource/checkbook_oge/agency/' . $agency_id : '';

      if (!empty($original_agreement_id)) {
        $contract_id_link = $base . $year_url . $oge_segment . '?expandBottomContURL=/contract_details';

        if (in_array($document_code, ['MA1', 'MMA1', 'RCT1'])) {
          $contract_id_link .= '/magid/' . $original_agreement_id . '/doctype/' . $document_code;
        }
        else {
          $master_contract_id_link = $contract_id_link . '/magid/' . $master_agreement_id . '/doctype/MMA1';
          $contract_id_link       .= '/agid/' . $original_agreement_id . '/doctype/' . $document_code;
        }
      }
      else {
        $contract_id_link = $base . $year_url
          . '?expandBottomContURL=/minipanels/pending_contract_transactions/contract/'
          . $fms_pending_number . '/version/' . $document_version;
      }
    }

    // --- Build linkable_fields ---
    if ($isOge && !in_array($contract_type_code, ['MMA1', 'MA1'])) {
      $linkable_fields = [
        'oge_contracting_agency_name' => $agency_link,
        'agency_name'                 => $agency_link,
        'vendor_name'                 => $vendor_link,
      ];
    }
    elseif (!$isOge) {
      $linkable_fields = [
        'agency_name' => $agency_link,
        'vendor_name' => $vendor_link,
      ];
    }

    // Disable links for old registered contracts
    if ($contract_status === 'Registered' && $reg_fiscal_year < 2010) {
      $linkable_fields = [];
    }

    // NYCHA overrides
    if ($isNycha) {
      $contract_id_link = '/nycha_contracts/year/' . $current_year_id
        . '/datasource/checkbook_nycha/agency/162?expandBottomContURL=/nycha_contract_details/contract/' . $contract_number;

      $nycha_year_id = ($agreement_end_year_id > $current_year_id) ? $current_year_id : $agreement_end_year_id;
      $vendor_link   = '/nycha_contracts/year/' . $nycha_year_id . '/agency/162/datasource/checkbook_nycha/vendor/' . $vendor_id;
      $linkable_fields = ['vendor_name' => $vendor_link];
    }

    // OGE MMA1 label override
    if ($isOge && $contract_type_code === 'MMA1') {
      $contracts_parameter_mapping['oge_contracting_agency_name'] = 'Contracting Agency';
    }

    $date_fields = [
      'start_date_orig', 'end_date_orig', 'received_date', 'registration_date',
      'start_date', 'end_date', 'release_approved_date',
    ];

    $amount_fields = [
      'agreement_original_amount', 'agreement_total_amount', 'agreement_spend_to_date',
      'release_original_amount',   'release_total_amount',   'release_spend_to_date',
      'release_line_original_amount', 'release_line_total_amount', 'release_line_spend_to_date',
      'current_amount', 'original_amount', 'invoiced_amount',
    ];

    // NYCHA purchase order date fix
    if ($isNycha && $agreement_type_name === 'PURCHASE ORDER') {
      $contracts_results['start_date'] = $contracts_results['agreement_start_date'] ?? '';
      $contracts_results['end_date']   = $contracts_results['agreement_end_date']   ?? '';
    }

    $contracts_results['registration_date'] = $isOge ? 'N/A' : ($contracts_results['registration_date'] ?? '');

    // --- Build structured fields array ---
    $fields = [];
    $is_in_newwindow = preg_match('/newwindow/', RequestUtilities::getCurrentPageUrl());

    foreach ($contracts_parameter_mapping as $key => $title) {
      if (!$title) {
        continue;
      }

      $value = '';

      // Safely get value
      if ($key === 'expenditure_object_name') {
        $parts = $contracts_results[$key] ?? [];
        $value = implode(',', array_map('strip_tags', (array) $parts));
      }
      else {
        $value = $contracts_results[$key] ?? '';
      }

      if (is_array($value)) {
        $value = implode(', ', $value);
      }

      $title_link    = NULL;
      $value_link    = NULL;
      $is_new_window = FALSE;
      $minority_link = NULL;

      switch ($key) {
        case 'contract_number':
          $value_link = $contract_id_link ?: NULL;
          break;

        case 'parent_contract_number':
          // FIX: use pre-resolved $parent_contract_number variable, not $contracts_results[]
          $value      = $parent_contract_number ?? '';
          $value_link = $master_contract_id_link ?: NULL;
          if (!$is_in_newwindow && in_array($document_code, ['MA1', 'MMA1', 'DO1', 'CTA1'])) {
            $title_link = 'https://comptroller.nyc.gov/reports/contract-primer/#master-agreements';
          }
          break;

        case 'award_method_name':
          if (!$is_in_newwindow) {
            $title_link = 'https://comptroller.nyc.gov/reports/contract-primer/#contract-categories-overview';
          }
          break;
      }
      // Format amounts
      if (in_array($key, $amount_fields)) {
        $value = FormattingUtilities::custom_number_formatter_format($value, 2, '$');
        $value_link = NULL; // amounts are never linked
      }
      // Format dates
      elseif (in_array($key, $date_fields)) {
        if ($value !== NULL && $value !== 'N/A' && $value !== '') {
          $value = date('F j, Y', strtotime(substr($value, 0, 10)));
        }
        elseif ($value === NULL || $value === '') {
          $value = '-';
        }
        $value_link = NULL;
      }
      // Linkable fields (agency/vendor) — only if not already set by switch
      elseif ($value_link === NULL && array_key_exists($key, $linkable_fields)) {
        $value_link = $linkable_fields[$key];
      }

      // Vendor label override
      $vendor_title_link = NULL;
      if (!$isOge && $key === 'vendor_name') {
        if ($is_prime_or_sub === 'Yes') {
          $title = 'Sub Vendor';
          if (!$is_in_newwindow) {
            $vendor_title_link = 'https://comptroller.nyc.gov/reports/contract-primer/#subcontracts';
          }
        }
        else {
          $title = 'Prime Vendor';
        }
        $title_link = $vendor_title_link;
      }

      // Minority type
      if ($key === 'minority_type_name') {
        if (empty($minority_type_name)) {
          $value = 'N/A';
        }
        else {
          $mwbe_id = $minority_type_id;
          if (in_array($mwbe_id, ['4', '5', '10'])) {
            $mwbe_id = '4~5~10';
          }
          if (!in_array($minority_type_id, ['7', '11'])) {
            if ($is_prime_or_sub === 'Yes') {
              $minority_link = '/contracts_landing/status/R/yeartype/B/year/' . $reg_fiscal_year_id . '/mwbe/' . $mwbe_id . '/dashboard/ms';
            }
            elseif (strtolower($contract_status_raw) === 'pending') {
              $minority_link = '/contracts_pending_exp_landing/yeartype/B/year/' . $current_year_id . '/mwbe/' . $mwbe_id . '/dashboard/mp';
            }
            else {
              $minority_link = '/contracts_landing/status/R/yeartype/B/year/' . $reg_fiscal_year_id . '/mwbe/' . $mwbe_id . '/dashboard/mp';
            }
            // Disable for old contracts
            if ($contract_status === 'Registered' && $reg_fiscal_year < 2010) {
              $minority_link = NULL;
            }
          }
        }
        $value_link = NULL; // minority uses its own link slot
      }

      // contract_class_description fallback
      if ($key === 'contract_class_description' && empty($value)) {
        $value = 'N/A';
      }

      $fields[] = [
        'key'           => $key,
        'title'         => $title,
        'title_link'    => $title_link,
        'value'         => $value,
        'value_link'    => $value_link,
        'minority_link' => $minority_link,
        'is_new_window' => $is_new_window,
      ];
    }

    return [
      'domain' => 'contracts',
      'fields' => $fields,
    ];
  }
}
