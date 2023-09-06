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

namespace Drupal\checkbook_project\SpendingUtilities;

use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;

class VendorSpendingUtil
{
  /**
   * Returns Sub Vendor Name Link Url based on values from current path & data row,
   * @param $node
   * @param $row
   * @return string
   */
  public static function getSubVendorNameLinkUrl($node, $row)
  {
    $vendor_id = $row["sub_vendor_sub_vendor"] ?? $row["vendor_id"];
    $year_id = RequestUtilities::get("year");
    $year_type = RequestUtilities::get("yeartype");
    $agency_id = RequestUtilities::get("agency");
    $dashboard = RequestUtilities::get("dashboard");

    return self::getSubVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard);
  }

  /**
   * Returns Prime Vendor Name Link Url based on values from current path & data row
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getPrimeVendorNameLinkUrl($node, $row)
  {
    $vendor_id = $row["prime_vendor_id"] ?? $row["prime_vendor_prime_vendor"];
    if (!isset($vendor_id)) {
      $vendor_id = $row["vendor_id"] ?? $row["vendor_vendor"];
    }
    $year_id = RequestUtilities::get("year");
    $year_type = RequestUtilities::get("yeartype");
    $agency_id = RequestUtilities::get("agency");
    $dashboard = RequestUtilities::get("dashboard");
    $datasource = RequestUtilities::get("datasource");
    return self::getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard, '', $datasource);
  }

  /**
   * Returns Prime Vendor Name Link Url based on values from current path & data row.
   * This is for the advanced search page, if no year is provided, we use the current year
   *
   * @param $node
   * @param $row
   * @param null $category_id
   * @return string
   */
  public static function getPayeeNameLinkUrl($node, $row, $category_id = null)
  {
    $year_id = $row['check_eft_issued_nyc_year_id'] ?? CheckbookDateUtil::getCurrentFiscalYearId();
    $vendor_id = $row["vendor_id"];
    $agency_id = $row["agency"];
    $dashboard = RequestUtilities::get("dashboard");
    $year_type = 'B';
    $nid = $node->nid;

    $url = $row["is_sub_vendor"] == "No"
      ? self::getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard, true)
      : self::getSubVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard, true);

    // Title does not display correctly for payee name link from advanced search
    // when the category filter is added in the end of the url parameters
    // move the category parameter to the beginning of the url

    if ($nid == 766) {
      $replace = 'spending_landing/category/' . $category_id;
      $url = preg_replace('/spending_landing/', $replace, $url);
    }
    return $url;

  }


  /**
   * Returns Prime Vendor Name Link Url based on values from current path & data row
   *
   * if vendor is M/WBE certified - go to M/WBE dashboard
   * if vendor is NOT M/WBE certified - go to citywide (default) dashboard
   *
   * if switching from citywide->M/WBE OR M/WBE->citywide,
   * then persist only agency filter (mwbe & vendor if applicable)
   *
   * if remaining in the same dashboard persist all filters (drill-down) except sub vendor
   *
   * @param $vendor_id
   * @param $agency_id
   * @param $year_id
   * @param $year_type
   * @param $current_dashboard
   * @param bool $payee_name
   * @param $datasource
   * @return string
   */
  public static function getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $current_dashboard, $payee_name = false, $datasource = null)
  {
    $latest_certified_minority_type_id = MwbeSpendingUtil::getLatestMwbeCategoryByVendor($vendor_id, $agency_id, $year_id, $year_type, "P");
    $is_mwbe_certified = isset($latest_certified_minority_type_id);

    //if M/WBE certified, go to M/WBE dashboard else if NOT M/WBE certified, go to citywide
    $new_dashboard = $is_mwbe_certified ? "mp" : null;

    //if switching between dashboard, persist only agency filter (mwbe & vendor if applicable)
    if ($current_dashboard != $new_dashboard) {
      $override_params = array(
        "dashboard" => $new_dashboard,
        "mwbe" => $is_mwbe_certified ? MappingUtil::getTotalMinorityIds('url') : null,
        "agency" => $agency_id,
        "vendor" => $vendor_id,
        "subvendor" => null,
        "category" => null,
        "industry" => null,
        "year" => $year_id,
        "yeartype" => $year_type
      );
    } else {//if remaining in the same dashboard persist all filters (drill-down) except sub vendor
      $override_params = array(
        "dashboard" => $new_dashboard,
        "subvendor" => null,
        "agency" => $agency_id,
        "datasource" => $datasource,
        "vendor" => $vendor_id,
        "year" => $year_id,
        "yeartype" => $year_type
      );
      //payee name will never have a drill down, this is to avoid ajax issues on drill down
      if ($payee_name) {
        $override_params["mwbe"] = $is_mwbe_certified ? MappingUtil::getTotalMinorityIds('url') : null;
      }
    }
    return  SpendingUrlHelper::getLandingPageWidgetUrl($override_params);
  }

  /**
   * Returns Sub Vendor Name Link Url based on values from current path & data row
   *
   * if sub vendor is M/WBE certified - go to M/WBE (Sub Vendor) dashboard
   * if sub vendor is NOT M/WBE certified - go to Sub Vendor dashboard
   *
   * if switching from citywide->M/WBE OR M/WBE->citywide,
   * then persist only agency filter (mwbe & vendor if applicable)
   *
   * if remaining in the same dashboard persist all filters (drill-down) except sub vendor
   *
   * @param $vendor_id
   * @param $agency_id
   * @param $year_id
   * @param $year_type
   * @param $current_dashboard
   * @param $payee_name
   * @return string
   */
  public static function getSubVendorLink($vendor_id, $agency_id, $year_id, $year_type, $current_dashboard, $payee_name = false)
  {
    $latest_certified_minority_type_id = MwbeSpendingUtil::getLatestMwbeCategoryByVendor($vendor_id, $agency_id, $year_id, $year_type, "S");
    $is_mwbe_certified = isset($latest_certified_minority_type_id);

    //if M/WBE certified, go to M/WBE (Sub Vendor) else if NOT M/WBE certified, go to Sub Vendor dashboard
    $new_dashboard = $is_mwbe_certified ? "ms" : "ss";

    //if switching between dashboard, persist only agency filter (mwbe & subvendor if applicable)
    if ($current_dashboard != $new_dashboard) {
      $override_params = array(
        "dashboard" => $new_dashboard,
        "mwbe" => $is_mwbe_certified ? MappingUtil::getTotalMinorityIds('url') : null,
        "agency" => $agency_id,
        "subvendor" => $vendor_id,
        "vendor" => null,
        "category" => null,
        "industry" => null
      );
    } else {//if remaining in the same dashboard persist all filters (drill-down) except vendor
      $override_params = array(
        "dashboard" => $new_dashboard,
        "subvendor" => $vendor_id,
        "vendor" => null
      );
      //payee name will never have a drill down, this is to avoid ajax issues on drill down
      if ($payee_name) {
        $override_params["mwbe"] = $is_mwbe_certified ? MappingUtil::getTotalMinorityIds('url') : null;
      }
    }
    return SpendingUrlHelper::getLandingPageWidgetUrl($override_params);
  }


  /**
   * Returns Vendor Amount Link Url based on values from current path & data row
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getVendorAmountLinkUrl($node, $row)
  {
    $nid = $node->nid;
    $vendor = $row["vendor_vendor"] ?? $row["prime_vendor_prime_vendor"];
    $override_params = array(
      'vendor' => $vendor,
      "fvendor" => self::getVendorFacetParameter($node),
      'smnid' => $node->nid
    );
    return '/' . SpendingUrlHelper::getSpendingTransactionPageUrl($override_params);
  }

  /**
   * Returns Prime Vendor Amount Link Url based on values from current path & data row
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getPrimeVendorAmountLinkUrl($node, $row)
  {
    $override_params = array(
      'vendor' => $row["prime_vendor_prime_vendor"],
      "fvendor" => self::getVendorFacetParameter($node),
      "smnid" => $node->nid
    );
    return '/' . SpendingUrlHelper::getSpendingTransactionPageUrl($override_params);
  }


  /**
   * Returns Sub Vendor YTD Spending Link Url based on values from current path & data row
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getSubVendorYtdSpendingUrl($node, $row)
  {
    $override_params = array(
      'subvendor' => $row['sub_vendor_sub_vendor'],
      'fvendor' => $row['sub_vendor_sub_vendor'],
      'smnid' => $node->nid
    );
    return '/' . SpendingUrlHelper::getSpendingTransactionPageUrl($override_params);
  }

  /**
   * Returns Ytd Spending percent for both vendor and sub vendor spending
   *  NYCCHKBK-4263
   *
   * @param $node
   * @param $row
   * @param $data_set
   * @return string
   */
  public static function getPercentYtdSpendingVendorSubVendor($node, $row, $data_set)
  {
    $sum_vendor_sub_vendor = $row['check_amount_sum'] + $row['check_amount_sum@checkbook:' . $data_set];
    $sum_vendor_sub_vendor_total = $node->totalAggregateColumns['check_amount_sum'] + $node->totalAggregateColumns['check_amount_sum@checkbook:' . $data_set];

    $ytd_spending = $sum_vendor_sub_vendor / $sum_vendor_sub_vendor_total * 100;
    $ytd_spending = $ytd_spending < 0 ? 0.00 : $ytd_spending;
    return FormattingUtilities::custom_number_formatter_format($ytd_spending, 2, '', '%');
  }

  /**
   * Returns the vendor or sub vendor id for the vendor facets
   * @param $node
   * @return array|string
   */
  public static function getVendorFacetParameter($node)
  {
    $dashboard = RequestUtilities::get('dashboard');
    $facet_vendor_param = null;

    if ($dashboard == "mp") {
      $facet_vendor_param = RequestUtilities::get("vendor");
    } else if ($dashboard == "ss" || $dashboard == "ms") {
      $facet_vendor_param = RequestUtilities::get("subvendor");
    }
    return $facet_vendor_param;
  }

  /**
   * Transaction page from M/WBE Dashboard landing page:
   *
   * Top 10 agencies widget (759) - sub and prime data
   * Top 10 Sub Vendors widget (763) - sub data
   * All Others widgets - prime data
   *
   * @param $node
   * @return string
   */
  public static function getVendorTypeUrlParam($node)
  {
    $dashboard = RequestUtilities::get('dashboard');
    $vendortype = null;
    $nid = $node->nid;
    /**
     * Transaction page from M/WBE Dashboard landing page
     * Top 10 agencies widget (759) - sub and prime data
     * Top 10 Sub Vendors widget (763) - sub data
     * All Others widgets - prime data
     */
    if ($dashboard == 'mp') {
      switch ($nid) {
        case 759:
          $vendortype .= 'sv~pv~mv';
          break;
        case 763:
          $vendortype .= 'sv~mv';
          break;
        default:
          $vendortype .= 'pv~mv';
          break;
      }
    }
    return $vendortype;
  }

}
