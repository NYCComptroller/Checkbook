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
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_solr\CheckbookSolr;

class NychaContractsSmartUtil {

  public static function displayNychaContractsResult($contracts_results, $solr_datasource) {

    $contracts_parameter_mapping = CheckbookSolr::getSearchFields($solr_datasource, 'contracts');
    $current_year_id = CheckbookDateUtil::getCurrentFiscalYearId();
    $start_date = date("c", strtotime($contracts_results['start_date']));
    $end_date = date("c", strtotime($contracts_results['end_date']));
    if (strtoupper($contracts_results['agreement_type_name']) == 'PURCHASE ORDER') {
      $contracts_results['start_date'] = '';
      $contracts_results['end_date'] = '';
    }

    //Generate links
    $contract_id_link = '<a href=/nycha_contracts/year/' . $current_year_id .
      '/datasource/checkbook_nycha/agency/162?expandBottomContURL=/nycha_contract_details/contract/' . $contracts_results['contract_number'] . ">" . $contracts_results['contract_number'] . "</a>";

    //Year logic for NYCHA Vendor link
    if ($contracts_results['agreement_end_year_id'] > $current_year_id) {
      $nycha_year_id = $current_year_id;
    }
    else {
      $nycha_year_id = $contracts_results['agreement_end_year_id'];
    }

    $vendor_link = '<a href=/nycha_contracts/year/' . $nycha_year_id . '/agency/162/datasource/checkbook_nycha/vendor/' . $contracts_results['vendor_id'] . ">" . htmlspecialchars($contracts_results['vendor_name']) . "</a>";
    $linkable_fields = [
      "contract_number" => $contract_id_link,
      "vendor_name" => $vendor_link
    ];

    $date_fields = ["start_date", "end_date", "release_approved_date"];
    $amount_fields = [
      "agreement_original_amount",
      "agreement_total_amount",
      "agreement_spend_to_date",
      "release_original_amount",
      "release_total_amount",
      "release_spend_to_date",
      "release_line_original_amount",
      "release_line_total_amount",
      "release_line_spend_to_date",
      "current_amount",
      "original_amount",
      "invoiced_amount"
    ];

    $hyphenFields = [
      "Agreement" => [
        "release_number",
        "line_number",
        "commodity_category_name",
        "item_description",
        "item_qty_ordered",
        "shipment_number",
        "responsibility_center_name",
        "release_line_total_amount",
        "release_line_original_amount",
        "release_line_spend_to_date",
        "release_approved_date",
        "release_total_amount",
        "release_original_amount",
        "release_spend_to_date",
        "location_name",
        "grant_name",
        "expenditure_type_name",
        "funding_source_name",
        "program_phase_code",
        "gl_project_name"
      ],
      "Release" => [
        "number_of_releases",
        "line_number",
        "commodity_category_name",
        "item_description",
        "item_qty_ordered",
        "shipment_number",
        "responsibility_center_name",
        "release_line_total_amount",
        "release_line_original_amount",
        "release_line_spend_to_date",
        "agreement_total_amount",
        "agreement_original_amount",
        "agreement_spend_to_date",
        "location_name",
        "grant_name",
        "expenditure_type_name",
        "funding_source_name",
        "program_phase_code",
        "gl_project_name"
      ],
      "Line" => [
        "number_of_releases",
        "release_total_amount",
        "release_original_amount",
        "release_spend_to_date",
        "agreement_total_amount",
        "agreement_original_amount",
        "agreement_spend_to_date"
      ]
    ];

    $count = 1;
    $rows = [];
    $row = [];
    $out = "<div class=\"search-result-fields\"><div class=\"grid-row\">";
    foreach ($contracts_parameter_mapping as $key => $title) {
      if ($key == 'expenditure_object_name') {
        $value = "";
        foreach ($contracts_results[$key] as $a => $b) {
          $value .= strip_tags($b) . ',';
        }
        $value = substr($value, 0, -1);
      }
      else {
        $value = $contracts_results[$key];
      }
      if (isset($hyphenFields[$contracts_results['record_type']]) && in_array($key, $hyphenFields[$contracts_results['record_type']] ? $hyphenFields[$contracts_results['record_type']] : [])) {
        $value = "-";
      }
      if (isset($amount_fields) && in_array($key, $amount_fields)) {
        $value = FormattingUtilities::custom_number_formatter_format($value, 2, '$');
      }

      if (isset($date_fields) && in_array($key, $date_fields)) {
        if ($value != NULL && $value != "N/A") {
          $value = date("F j, Y", strtotime(substr($value, 0, 10)));
        }
        elseif ($value == NULL) {
          $value = '-';
        }
      }
      if (array_key_exists($key, $linkable_fields)) {
        $value = $linkable_fields[$key];
      }

      if (isset($hyphen_fields) && in_array($key, $hyphen_fields) && $value == NULL) {
        $value = '-';
      }

      if ($count % 2 == 0) {
        $out .= "<div class=\"grid-col-6\">";
        if ($title) {
          $out .= '<div class="field-label">' . $title . ':</div><div class="field-content">' . $value . '</div>';
        }
        $rows[] = $row;
        $row = [];
      }
      else {
        $out .= "<div class=\"grid-col-6\">";
        if ($title) {
          $out .= '<div class="field-label">' . $title . ':</div><div class="field-content">' . $value . '</div>';
        }
      }
      $count++;
      $out .= "</div>";
    }
    $out .= "</div></div>";
    return $out;
  }

}
