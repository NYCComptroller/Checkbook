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

$spending_parameter_mapping = CheckbookSolr::getSearchFields($solr_datasource, 'spending');

$isNycha = ('nycha' === $solr_datasource);
$isEdc = ('edc' === $solr_datasource);
$isOge = $isNycha || $isEdc;

if ($spending_results['fiscal_year_id'] != '') {
  $fiscal_year_id = $spending_results['fiscal_year_id'][0];
} else {
  $fiscal_year_id = _getCurrentYearID();
}

if ($isOge) {
  $linkable_fields = array(
    "oge_agency_name" => "/spending_landing/category/" . $spending_results['spending_category_id'] . '/datasource/checkbook_oge' . "/year/" . _getFiscalYearID() . "/yeartype/B/agency/" . $spending_results["agency_id"],
    "vendor_name" => "/spending_landing/category/" . $spending_results['spending_category_id'] . '/datasource/checkbook_oge' . '/agency/' . $spending_results['agency_id'] . "/year/" . _getFiscalYearID() . "/yeartype/B/vendor/" . $spending_results["vendor_id"],
  );
} else {
  if ($spending_results['spending_category_name'] == 'Payroll') {
    $linkable_fields = array(
      "agency_name" => "/spending_landing/category/" . $spending_results['spending_category_id'] . "/year/" . $fiscal_year_id . "/yeartype/B/agency/" . $spending_results["agency_id"],
      "vendor_name" => $spending_results['vendor_name'],
    );
  } elseif ($spending_results['vendor_id'] == 1) {
    $linkable_fields = array(
      "agency_name" => "/spending_landing/category/" . $spending_results['spending_category_id'] . "/year/" . $fiscal_year_id . "/yeartype/B/agency/" . $spending_results["agency_id"],
      "vendor_name" => "/spending_landing/category/" . $spending_results['spending_category_id'] . "/year/" . $fiscal_year_id . "/yeartype/B/vendor/" . $spending_results["vendor_id"],
    );
  } elseif ($spending_results['is_prime_or_sub'] == 'Yes' && SpendingUtil::getLatestMwbeCategoryBySpendingVendorByTransactionYear($spending_results["vendor_id"], $fiscal_year_id, 'B') == '') {
    $linkable_fields = array(
      "agency_name" => "/spending_landing/category/" . $spending_results['spending_category_id'] . "/year/" . $fiscal_year_id . "/yeartype/B/agency/" . $spending_results["agency_id"],
      "vendor_name" => "/spending_landing/category/" . $spending_results['spending_category_id'] . "/year/" . $fiscal_year_id . "/yeartype/B/subvendor/" . $spending_results["vendor_id"] . "/dashboard/ss",
    );
  } elseif ($spending_results['is_prime_or_sub'] == 'No' && SpendingUtil::getLatestMwbeCategoryBySpendingVendorByTransactionYear($spending_results["vendor_id"], $fiscal_year_id, 'B') != '') {
    $linkable_fields = array(
      "agency_name" => "/spending_landing/category/" . $spending_results['spending_category_id'] . "/year/" . $fiscal_year_id . "/yeartype/B/agency/" . $spending_results["agency_id"],
      "vendor_name" => "/spending_landing/yeartype/B/year/" . $fiscal_year_id . "/category/" . $spending_results['spending_category_id'] . "/mwbe/2~3~4~5~9/dashboard/mp/vendor/" . $spending_results["vendor_id"],
    );
  } elseif ($spending_results['is_prime_or_sub'] == 'Yes' && SpendingUtil::getLatestMwbeCategoryBySpendingVendorByTransactionYear($spending_results["vendor_id"], $fiscal_year_id, 'B') != '') {
    $linkable_fields = array(
      "agency_name" => "/spending_landing/category/" . $spending_results['spending_category_id'] . "/year/" . $fiscal_year_id . "/yeartype/B/agency/" . $spending_results["agency_id"],
      "vendor_name" => "/spending_landing/yeartype/B/year/" . $fiscal_year_id . "/category/" . $spending_results['spending_category_id'] . "/mwbe/2~3~4~5~9/dashboard/ms/subvendor/" . $spending_results["vendor_id"],
    );
  } else {
    $linkable_fields = array(
      "agency_name" => "/spending_landing/category/" . $spending_results['spending_category_id'] . "/year/" . $fiscal_year_id . "/yeartype/B/agency/" . $spending_results["agency_id"],
      "vendor_name" => "/spending_landing/category/" . $spending_results['spending_category_id'] . "/year/" . $fiscal_year_id . "/yeartype/B/vendor/" . $spending_results["vendor_id"],
    );
  }
}

if (!$isOge) {
  $date_fields = array("check_eft_issued_date");
}

$amount_fields = array("check_amount");
$count = 1;
$row = array();
$rows = array();
$spending_results["check_eft_issued_date"] = ($isOge) ? "N/A" : $spending_results["check_eft_issued_date"];

if ($fiscal_year_id < 111) {
  $linkable_fields = array();
}

foreach ($spending_parameter_mapping as $key => $title) {
  if ($key == 'expenditure_object_name') {
    $value = $spending_results[$key][0];
  } else {
    $value = $spending_results[$key];
  }

  // highlighting (italics) search term
  if (!empty($searchTerm)) {
    $temp = substr($value, strpos(strtoupper($value), strtoupper($searchTerm)), strlen($searchTerm));
    $value = str_ireplace($searchTerm, '<em>' . $temp . '</em>', $value);
  }

  if (array_key_exists($key, $linkable_fields)) {
    $value = "<a href='" . $linkable_fields[$key] . "'>" . $value . "</a>";
  } else if (is_array($date_fields) && in_array($key, $date_fields)) {
    $value = date("F j, Y", strtotime(substr($value, 0, 10)));
  } else if (is_array($amount_fields) && in_array($key, $amount_fields)) {
    $value = custom_number_formatter_format($value, 2, '$');
  }
  if ($key == 'contract_number' && $spending_results['agreement_id']) {
    if ($spending_results['is_prime_or_sub'] == 'Yes') {
      $value = "<a class=\"new_window\" href=\"/contract_details"
        . (($isOge) ? '/datasource/checkbook_oge' : '')
        . _checkbook_project_get_contract_url($spending_results['contract_number'], $spending_results['contract_original_agreement_id']) . '/newwindow">'
        . $value . "</a>";
    } else {
      $value = "<a class=\"new_window\" href=\"/contract_details"
        . (($isOge) ? '/datasource/checkbook_oge' : '')
        . _checkbook_project_get_contract_url($spending_results['contract_number'], $spending_results['agreement_id']) . '/newwindow">'
        . $value . "</a>";
    }
  }

  if ($key == "vendor_name" && !$spending_results["vendor_id"]) {
    $value = $spending_results["vendor_name"];
  }

  if ($key == "minority_type_name" && !$spending_results["minority_type_name"]) {
    $value = 'N/A';
  } elseif ($key == "minority_type_name" && $spending_results["minority_type_name"]) {
    $id = $spending_results["minority_type_id"];
    if ($id == '4' || $id == '5') {
      $id = '4~5';
    }
    if ($spending_results['minority_type_id'] != '7' && $spending_results['minority_type_id'] != '11') {
      if ($spending_results['is_prime_or_sub'] == 'Yes') {
        $value = "<a href='/spending_landing/yeartype/B/year/" . $fiscal_year_id . "/mwbe/" . $id . "/dashboard/ms'>" . $spending_results["minority_type_name"] . "</a>";
      } else {
        $value = "<a href='/spending_landing/yeartype/B/year/" . $fiscal_year_id . "/mwbe/" . $id . "/dashboard/mp'>" . $spending_results["minority_type_name"] . "</a>";
      }
      if ($fiscal_year_id < 111) {
        $value = $spending_results["minority_type_name"];
      }
    } else {
      $value = $spending_results["minority_type_name"];
    }
  }

  if ($count % 2 == 0) {
    if ($title)
      $row[] = '<div class="field-label">' . $title . ':</div><div class="field-content">' . $value . '</div>';
    $rows[] = $row;
    $row = array();
  } else {
    if ($title)
      $row[] = '<div class="field-label">' . $title . ':</div><div class="field-content">' . $value . '</div>';
  }
  $count++;
}

print theme('table', array('rows' => $rows, 'attributes' => array('class' => array('search-result-fields'))));
