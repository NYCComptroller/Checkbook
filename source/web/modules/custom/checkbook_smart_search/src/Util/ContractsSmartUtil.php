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
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;
use Drupal\checkbook_solr\CheckbookSolr;

class ContractsSmartUtil {

  public static function displayContractsResult($contracts_results, $solr_datasource) {

    $contracts_parameter_mapping = CheckbookSolr::getSearchFields($solr_datasource, 'contracts');

    $isNycha = ('nycha' === $solr_datasource);
    $isEdc = ('edc' === $solr_datasource);
    $isOge = $isNycha || $isEdc;
    $current_year_id = CheckbookDateUtil::getCurrentFiscalYearId();

    if (strtolower($contracts_results['contract_status']) == 'registered') {

      $search_terms = explode('*!*', $_REQUEST['search_term']);
      $contract_status = '';
      foreach ($search_terms as $id => $keyvaluepair) {
        $keys = explode("=", $keyvaluepair);
        if ($keys[0] == 'registered_fiscal_years') {
          $reg_year = $keys[1];
        }
        if ($keys[0] == 'contract_status' && $keys[1] == 'active') {
          $contract_status = 'Active';

        }
        if ($keys[0] == 'contract_status' && $keys[1] == 'registered') {
          $contract_status = 'Registered';
        }
      }

      $act_fiscal_year = $contracts_results['fiscal_year'];
      $reg_fiscal_year = $contracts_results['registered_fiscal_year'];
      $current_date = date("c") . 'Z';
      $start_date = date("c", strtotime($contracts_results['start_date']));
      $end_date = date("c", strtotime($contracts_results['end_date']));

      /*if($contract_status == 'Active'){
             $contracts_results['contract_status'] = 'Active';
             $status = "A";
     }*/
      if ($contract_status == 'Registered') {
        $contracts_results['contract_status'] = 'Registered';
        $status = "R";
      }
      else {
        $contracts_results['contract_status'] = 'Active';
        $status = "A";
      }
      $effective_end_year_id = $contracts_results['effective_end_year_id'];
      if ($effective_end_year_id != '' && $effective_end_year_id < $current_year_id) {
        $fiscal_year_id = $effective_end_year_id;
        $contract_id_year = CustomURLHelper::_checkbook_project_get_year_url_param_string();
        $contract_id_link_year = preg_replace("/\/year\/[^\/]*/", "/year/" . $fiscal_year_id, $contract_id_year);

      }
      else {
        $fiscal_year_id = $current_year_id;
        $contract_id_link_year = '/year/' . $fiscal_year_id;
      }

      if (strtolower($contracts_results['contract_category_name']) == 'expense') {
        if ($isOge) {
          $vendor_link = "/contracts_landing/status/" . $status . "/yeartype/B/datasource/checkbook_oge/year/" . $current_year_id . '/agency/' . $contracts_results['agency_id'] . '/vendor/' . $contracts_results['vendor_id'];
          $agency_link = "/contracts_landing/status/" . $status . "/yeartype/B/datasource/checkbook_oge/year/" . $current_year_id . '/agency/' . $contracts_results['agency_id'];
        }
        else {
          if ($contracts_results["is_minority_vendor"] == 'Y' && $contracts_results["is_prime_or_sub"] == 'Yes') {
            $vendor_link = "/contracts_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . '/subvendor/' . $contracts_results['vendor_id'] . "/dashboard/ss";
          }
          else {
            if ($contracts_results["is_minority_vendor"] == 'Y' && $contracts_results["is_prime_or_sub"] == 'No') {
              $vendor_link = "/contracts_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . "/mwbe/2~3~4~5~6~9~99/dashboard/mp/vendor/" . $contracts_results["vendor_id"];
            }
            else {
              if ($contracts_results["is_minority_vendor"] == 'Y' && $contracts_results["is_prime_or_sub"] == 'Yes') {
                $vendor_link = "/contracts_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . "/mwbe/2~3~4~5~6~9~99/dashboard/ms/subvendor/" . $contracts_results["vendor_id"];
              }
              else {
                if ($contracts_results["is_minority_vendor"] == 'N' && $contracts_results["is_prime_or_sub"] == 'Yes') {
                  $vendor_link = "/contracts_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . "/subvendor/" . $contracts_results["vendor_id"] . "/dashboard/ss";
                }
                else {
                  $vendor_link = "/contracts_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . '/vendor/' . $contracts_results['vendor_id'];
                }
              }
            }
          }
          $agency_link = "/contracts_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . '/agency/' . $contracts_results['agency_id'];
        }
        $contract_id_link = "/contracts_landing/status/" . $status;

      }
      else {
        if ($isOge) {
          $vendor_link = "/contracts_revenue_landing/status/" . $status . "/yeartype/B/datasource/checkbook_oge/year/" . $current_year_id . '/agency/' . $contracts_results['agency_id'] . '/vendor/' . $contracts_results['vendor_id'];
          $agency_link = "/contracts_revenue_landing/status/" . $status . "/yeartype/B/datasource/checkbook_oge/year/" . $current_year_id . '/agency/' . $contracts_results['agency_id'];
        }
        else {
          if ($contracts_results["is_minority_vendor"] == 'Y' && $contracts_results["is_prime_or_sub"] == 'Yes') {
            $vendor_link = "/contracts_revenue_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . '/subvendor/' . $contracts_results['vendor_id'] . "/dashboard/ss";
          }
          else {
            if ($contracts_results["is_minority_vendor"] == 'Y' && $contracts_results["is_prime_or_sub"] == 'No') {
              $vendor_link = "/contracts_revenue_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . "/mwbe/2~3~4~5~6~9~99/dashboard/mp/vendor/" . $contracts_results["vendor_id"];
            }
            else {
              if ($contracts_results["is_minority_vendor"] == 'Y' && $contracts_results["is_prime_or_sub"] == 'Yes') {
                $vendor_link = "/contracts_revenue_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . "/mwbe/2~3~4~5~6~9~99/dashboard/ms/subvendor/" . $contracts_results["vendor_id"];
              }
              else {
                if ($contracts_results["is_minority_vendor"] == 'N' && $contracts_results["is_prime_or_sub"] == 'Yes') {
                  $vendor_link = "/contracts_revenue_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . "/subvendor/" . $contracts_results["vendor_id"] . "/dashboard/ss";
                }
                else {
                  $vendor_link = "/contracts_revenue_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . '/vendor/' . $contracts_results['vendor_id'];
                }
              }
            }
          }
          $agency_link = "/contracts_revenue_landing/status/" . $status . "/yeartype/B/year/" . $fiscal_year_id . '/agency/' . $contracts_results['agency_id'];
        }
        $contract_id_link = "/contracts_revenue_landing/status/" . $status;
      }
      if (isset($contracts_results['is_prime_or_sub']) && $contracts_results['is_prime_or_sub'] == 'Yes') {
        $contract_id_link .= $contract_id_link_year . (($isOge) ? '/datasource/checkbook_oge/agency/' . $contracts_results['oge_agency_id'] : '') . "/dashboard/ss?expandBottomContURL=/contract_details";
      }
      else {
        $contract_id_link .= $contract_id_link_year . (($isOge) ? '/datasource/checkbook_oge/agency/' . $contracts_results['oge_agency_id'] : '') . "?expandBottomContURL=/contract_details";
      }
      if ($contracts_results['document_code'] == 'MA1' || $contracts_results['document_code'] == 'MMA1' || $contracts_results['document_code'] == 'RCT1') {
        $contract_id_link .= "/magid/" . $contracts_results['original_agreement_id'] . "/doctype/" . $contracts_results["document_code"];
      }
      else {
        $master_contract_Id_link = $contract_id_link . "/magid/" . $contracts_results['master_agreement_id'] . "/doctype/MMA1";
        if (isset($contracts_results['is_prime_or_sub']) && $contracts_results['is_prime_or_sub'] == 'Yes') {
          $contract_id_link .= "/agid/" . $contracts_results['contract_original_agreement_id'] . "/doctype/" . $contracts_results["document_code"];
        }
        else {
          $contract_id_link .= "/agid/" . $contracts_results['original_agreement_id'] . "/doctype/" . $contracts_results["document_code"];
        }
      }
      $contract_id_link = ($isOge) ? $contract_id_link . '/datasource/checkbook_oge' : $contract_id_link;

      if ($contracts_results['original_agreement_id']) {
        if(!isset($master_contract_Id_link)) {$master_contract_Id_link = '';};
        $contracts_results['contract_number'] = "<a href='" . $contract_id_link . "'>" . $contracts_results['contract_number'] . "</a>";
        $master_contract_Id_link = ($isOge) ? $master_contract_Id_link . '/datasource/checkbook_oge' : $master_contract_Id_link;
        if(isset($contracts_results['parent_contract_number'])) {
          $contracts_results['parent_contract_number'] = "<a href='" . $master_contract_Id_link . "'>" . $contracts_results['parent_contract_number'] . "</a>";
        }
      }

    }
    else {
      if (strtolower($contracts_results['contract_status']) == 'pending') {
        $current_year = "/yeartype/B/year/" . $current_year_id;
        if (strtolower($contracts_results['contract_category_name']) == 'expense') {
          $agency_link = "/contracts_pending_exp_landing" . $current_year . "/agency/" . $contracts_results['agency_id'];
          $vendor_link = "/contracts_pending_exp_landing" . $current_year . "/vendor/" . $contracts_results['vendor_id'];
          $contract_id_link = "/contracts_pending_exp_landing";

        }
        else {
          $agency_link = "/contracts_pending_rev_landing" . $current_year . "/agency/" . $contracts_results['agency_id'];
          $vendor_link = "/contracts_pending_rev_landing" . $current_year . "/vendor/" . $contracts_results['vendor_id'];
          $contract_id_link = "/contracts_pending_rev_landing";
        }

        if ($contracts_results['original_agreement_id']) {
          $contract_id_link .= CustomURLHelper::_checkbook_project_get_year_url_param_string() . (($isOge) ? '/datasource/checkbook_oge/agency/' . $contracts_results['agency_id'] : '') . "?expandBottomContURL=/contract_details";
          if ($contracts_results['document_code'] == 'MA1' || $contracts_results['document_code'] == 'MMA1' || $contracts_results['document_code'] == 'RCT1') {
            $contract_id_link .= "/magid/" . $contracts_results['original_agreement_id'] . "/doctype/" . $contracts_results["document_code"];
          }
          else {
            $master_contract_Id_link = $contract_id_link . "/magid/" . $contracts_results['master_agreement_id'] . "/doctype/MMA1";
            $contract_id_link .= "/agid/" . $contracts_results['original_agreement_id'] . "/doctype/" . $contracts_results["document_code"];
          }
          $contracts_results['contract_number'] = "<a href='" . $contract_id_link . "'>" . $contracts_results['contract_number'] . "</a>";
          $contracts_results['parent_contract_number'] = "<a href='" . $master_contract_Id_link . "'>" . $contracts_results['parent_contract_number'] . "</a>";
        }
        else {
          $contract_id_link .= CustomURLHelper::_checkbook_project_get_year_url_param_string() . "?expandBottomContURL=/minipanels/pending_contract_transactions/contract/" .
            $contracts_results['fms_pending_contract_number'] . "/version/" . $contracts_results['document_version'];
          $contracts_results['contract_number'] = "<a href='" . $contract_id_link . "'>" . $contracts_results['contract_number'] . "</a>";
          $contracts_results['parent_contract_number'] = "<a href='" . ($master_contract_Id_link ?? '') . "'>" . $contracts_results['parent_contract_number'] . "</a>";
        }

        $contracts_results['status'] = "Pending";
      }
    }

    if ($isOge && !in_array($contracts_results['contract_type_code'], [
        'MMA1',
        'MA1'
      ])) {
      $linkable_fields = [
        "oge_contracting_agency_name" => $agency_link,
        "agency_name" => $agency_link,
        "vendor_name" => $vendor_link,
      ];
    }
    elseif (!$isOge) {
      $linkable_fields = [
        "agency_name" => $agency_link,
        "vendor_name" => $vendor_link,
      ];
    }

    // for contracts with fiscal year 2009 and earlier, links should be disabled
    if (($contract_status == 'Registered' && $reg_fiscal_year < 2010) || ($effective_end_year_id < 111)) {
      $linkable_fields = [];
    }

    if ($isNycha) {
      $contract_id_link = '/nycha_contracts/year/' . $current_year_id .
        '/datasource/checkbook_nycha/agency/162?expandBottomContURL=/nycha_contract_details/contract/' . $contracts_results['contract_number'];
      //Year logic for NYCHA Vendor link
      if ($contracts_results['agreement_end_year_id'] > $current_year_id) {
        $nycha_year_id = $current_year_id;
      }
      else {
        $nycha_year_id = $contracts_results['agreement_end_year_id'];
      }
      // display '-' for start-date and end-date nycchkbk - 9264
      if ($contracts_results['agreement_type_name'] == 'PURCHASE ORDER') {
        $contracts_results['start_date'] = $contracts_results['agreement_start_date'];
        $contracts_results['end_date'] = $contracts_results['agreement_end_date'];

      }

      $vendor_link = '/nycha_contracts/year/' . $nycha_year_id . '/agency/162/datasource/checkbook_nycha/vendor/' . $contracts_results['vendor_id'];
      $linkable_fields = ["vendor_name" => $vendor_link,];
    }
    // This is throwing: Error: Cannot use object of type stdClass as array.
    if ($isOge && in_array($contracts_results['contract_type_code'], ['MMA1'])) {
      // Error: Cannot use object of type stdClass as array i
      // $contracts_parameter_mapping['oge_contracting_agency_name'] = "Contracting Agency";
      $contracts_parameter_mapping->oge_contracting_agency_name = "Contracting Agency";
    }

    $contracts_results["registration_date"] = ($isOge) ? "N/A" : $contracts_results["registration_date"];

    $date_fields = [
      "start_date_orig",
      "end_date_orig",
      "received_date",
      "registration_date",
      "start_date",
      "end_date",
      "release_approved_date"
    ];
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

    $name_fields = [
      "agency_name",
      "vendor_name",
      "award_method_name",
      "contract_purpose",
      "expenditure_object_name"
    ];

    $count = 1;
    $rows = [];
    $row = [];
    $out = "<div class=\"search-result-fields\"><div class=\"grid-row\">";
    foreach ($contracts_parameter_mapping as $key => $title) {
      unset($value);
      if ($key == 'expenditure_object_name') {
        $value = "";
        foreach ($contracts_results[$key] as $a => $b) {
          $value .= strip_tags($b) . ',';
        }
        $value = substr($value, 0, -1);
      }
      else {
        // Occasional errors seen.
        // Warning: Undefined array key "apt_pin".
        if(isset($contracts_results[$key])) {
          $value = $contracts_results[$key];
        }
      }

      if (is_array($value)) {
        $value = implode(', ', $value);
      }

      $temp = '';
      if (isset($searchTerm)) {
        $temp = substr($value, strpos(strtoupper($value), strtoupper($searchTerm)), strlen($searchTerm));
      }

      if ($key == "contract_number") {
        $value = "<a href='" . $contract_id_link . "'>" . $contracts_results['contract_number'] . "</a>";
      }
      else {
        if ($key == "parent_contract_number") {
          $value = "<a href='" . $master_contract_Id_link . "'>" . $contracts_results['parent_contract_number'] . "</a>";
        }
        else {
          // This was throwing occasional errors of search results page/
          // Warning: Undefined variable $searchTerm.
          if(isset($searchTerm)) {
            $value = str_ireplace($searchTerm, '<em>' . $temp . '</em>', $value);
          }
        }
      }

      if ($key == "vendor_name") {
        $a = $key;
      }

      if (in_array($key, $amount_fields)) {
        $value = FormattingUtilities::custom_number_formatter_format($value, 2, '$');
      }
      else {
        if (in_array($key, $date_fields)) {
          if ($value != NULL && $value != "N/A") {
            $value = date("F j, Y", strtotime(substr($value, 0, 10)));
          }
          elseif ($value == NULL) {
            $value = '-';
          }
        }
        else {
          if (is_array($linkable_fields) && array_key_exists($key, $linkable_fields)) {
            $value = "<a href='" . $linkable_fields[$key] . "'>" . $value . "</a>";
          }
        }
      }

      if (!$isOge && $title == "Vendor") {
        $title = ($contracts_results["is_prime_or_sub"] == 'Yes') ? "Sub Vendor" : "Prime Vendor";
      }

      if ($key == "minority_type_name" && !$contracts_results["minority_type_name"]) {
        $value = 'N/A';
      }
      elseif ($key == "minority_type_name" && $contracts_results["minority_type_name"]) {
        $id = $contracts_results["minority_type_id"];
        if ($id == '4' || $id == '5' || $id == '10') {
          $id = '4~5';
        }
        if ($contracts_results['minority_type_id'] != '7' && $contracts_results['minority_type_id'] != '11') {
          if ($contracts_results['is_prime_or_sub'] == 'Yes') {
            $value = "<a href='/contracts_landing/status/A/yeartype/B/year/" . $fiscal_year_id . "/mwbe/" . $id . "/dashboard/ms'>" . $contracts_results["minority_type_name"] . "</a>";
          }
          else {
            if (strtolower($contracts_results['contract_status']) == 'pending') {
              $value = "<a href='/contracts_pending_exp_landing/yeartype/B/year/" . $current_year_id . "/mwbe/" . $id . "/dashboard/mp'>" . $contracts_results["minority_type_name"] . "</a>";
            }
            else {
              $value = "<a href='/contracts_landing/status/A/yeartype/B/year/" . $fiscal_year_id . "/mwbe/" . $id . "/dashboard/mp'>" . $contracts_results["minority_type_name"] . "</a>";
            }
          }
          if (($contract_status == 'Registered' && $reg_fiscal_year < 2010) || ($effective_end_year_id < 111)) {
            $value = $contracts_results["minority_type_name"];
          }
        }
        else {
          $value = $contracts_results["minority_type_name"];
        }
      }

      if ($key == "contract_class_description") {
        $value = $value ?: 'N/A';
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
