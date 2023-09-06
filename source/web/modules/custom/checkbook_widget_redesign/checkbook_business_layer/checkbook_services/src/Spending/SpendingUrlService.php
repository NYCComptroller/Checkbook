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

namespace Drupal\checkbook_services\Spending;

//require_once(\Drupal::service('extension.list.module')->getPath('checkbook_project') . "/includes/checkbook_project.inc");
use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Dashboard;
use Drupal\checkbook_infrastructure_layer\Constants\Contract\DocumentCode;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\checkbook_services\VendorUtil\PrimeVendorService;
use Drupal\checkbook_services\VendorUtil\SubVendorService;

class SpendingUrlService
{
  /**
   * Function to build the contract id url
   * @param $agreement_id
   * @param $document_code
   * @return string
   */
  static function contractIdUrl($agreement_id, $document_code)
  {
    $contractUrl = DocumentCode::isMasterAgreement($document_code)
      ? '/magid/' . $agreement_id . '/doctype/' . $document_code
      : '/agid/' . $agreement_id . '/doctype/' . $document_code;

    return '/contract_details'
      . $contractUrl
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . '/newwindow';
  }

  /**
   * Function to build the agency url
   * @param $agency_id
   * @return string
   */
  static function agencyUrl($agency_id)
  {
    return '/spending_landing'
      . RequestUtilities::buildUrlFromParam('vendor')
      . RequestUtilities::buildUrlFromParam('subvendor')
      . RequestUtilities::buildUrlFromParam('category')
      . RequestUtilities::buildUrlFromParam('industry')
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . '/agency/' . $agency_id;
  }

  /**
   * Payroll Agencies widget include spending category url parameter for payroll
   * @param $agency_id
   * @return string
   */
  static function payrollAgencyUrl($agency_id)
  {
    return '/spending_landing'
      . RequestUtilities::buildUrlFromParam('vendor')
      . RequestUtilities::buildUrlFromParam('category')
      . RequestUtilities::buildUrlFromParam('industry')
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . '/category/2/agency/' . $agency_id;
  }

  /**
   * Function to build the industry url
   * @param $industry_type_id
   * @return string
   */
  static function industryUrl($industry_type_id)
  {
    return '/spending_landing'
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . RequestUtilities::buildUrlFromParam('vendor')
      . RequestUtilities::buildUrlFromParam('subvendor')
      . RequestUtilities::buildUrlFromParam('category')
      . RequestUtilities::buildUrlFromParam('agency')
      . '/industry/' . $industry_type_id;
  }

  /**
   * Returns Prime Vendor Landing page URL for the given prime vendor, year and year type
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
   * @param $year_id
   * @return string
   */
  static function primeVendorUrl($vendor_id, $year_id = null)
  {
    if (!isset($vendor_id) || $vendor_id == "") {
      return null;
    }
    $year_type = RequestUtilities::get("yeartype");
    $industry = RequestUtilities::get("industry");
    $agency_id = RequestUtilities::get("agency");
    $category = RequestUtilities::get("category");
    $dashboard = RequestUtilities::get("dashboard");
    $datasource = RequestUtilities::get("datasource");

    $latest_minority_id = !isset($year_id)
      ? PrimeVendorService::getLatestMinorityType($vendor_id, $agency_id,'P',CheckbookDomain::$SPENDING)
      : PrimeVendorService::getLatestMinorityTypeByYear($vendor_id, $year_id, $year_type,'P',CheckbookDomain::$SPENDING);
    $is_mwbe_certified = MappingUtil::isMWBECertified($latest_minority_id);

    //if M/WBE certified, go to M/WBE dashboard else if NOT M/WBE certified, go to citywide
    $new_dashboard = $is_mwbe_certified ? "mp" : null;
    $url = '/spending_landing'. CustomURLHelper::_checkbook_project_get_year_url_param_string();

    $mwbe = $is_mwbe_certified ? "/mwbe/" . MappingUtil::getTotalMinorityIds('url') : "";
    $industry = isset($industry) ? "/industry/{$industry}" : "";
    $agency = isset($agency_id) ? "/agency/{$agency_id}" : "";
    $category = isset($category) ? "/category/{$category}" : "";
    $vendor = "/vendor/{$vendor_id}";
    $datasource = isset($datasource) ? "/datasource/{$datasource}" : "";
    $dashboard_param = isset($new_dashboard) ? "/dashboard/{$new_dashboard}" : "";

    /**
     * if switching between dashboard, persist only agency filter (mwbe & vendor if applicable),
     * if remaining in the same dashboard persist all filters (drill-down) except sub vendor
     */
    return $dashboard != $new_dashboard
      ? $url . $dashboard_param . $mwbe . $industry . $category . $agency . $vendor
      : $url . $datasource . $dashboard_param . $mwbe . $industry . $category . $agency . $vendor;
  }

  /**
   * Returns Sub Vendor Landing page URL for the given sub vendor, year and year type
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
   * @param $year_id
   * @return string
   */
  static function subVendorUrl($vendor_id, $year_id = null)
  {

    $year_type = RequestUtilities::get("yeartype");
    $agency_id = RequestUtilities::get("agency");
    $industry = RequestUtilities::get("industry");
    $dashboard = RequestUtilities::get("dashboard");
    $datasource = RequestUtilities::get("datasource");

    $latest_minority_id = !isset($year_id)
      ? SubVendorService::getLatestMinorityType($vendor_id, $agency_id,'S',CheckbookDomain::$SPENDING)
      : SubVendorService::getLatestMinorityTypeByYear($vendor_id, $year_id, $year_type,'S',CheckbookDomain::$SPENDING);
    $is_mwbe_certified = MappingUtil::isMWBECertified($latest_minority_id);

    //if M/WBE certified, go to M/WBE (Sub Vendor) else if NOT M/WBE certified, go to Sub Vendor dashboard
    $new_dashboard = $is_mwbe_certified ? "ms" : "ss";

    $url = '/spending_landing' . CustomURLHelper::_checkbook_project_get_year_url_param_string();

    $mwbe = $is_mwbe_certified ? "/mwbe/" . MappingUtil::getTotalMinorityIds('url') : "";
    $agency = isset($agency_id) ? "/agency/{$agency_id}" : "";
    $industry = isset($industry) ? "/industry/{$industry}" : "";
    $vendor = isset($vendor_id) ? "/subvendor/{$vendor_id}" : "";
    $datasource = isset($datasource) ? "/datasource/{$datasource}" : "";
    $dashboard_param = isset($new_dashboard) ? "/dashboard/{$new_dashboard}" : "";

    /**
     * if switching between dashboard, persist only agency filter (mwbe & vendor if applicable),
     * if remaining in the same dashboard persist all filters (drill-down) except sub vendor
     */
    $url = $dashboard != $new_dashboard
      ? $url = $url . $dashboard_param . $mwbe . $industry . $agency . $vendor
      : $url . $datasource . $dashboard_param . $mwbe . $industry . $agency . $vendor;
    return $url;
  }


  /**
   * Function to build the M/WBE Category url
   * Do not hyperlink if you are looking at sub data (sub dashboard)
   * Do not hyperlink if not M/WBE certified
   * @param $minority_type_id
   * @return string
   */
  static function PrimeMwbeCategoryUrl($minority_type_id)
  {

    // Do not hyperlink if you are looking at sub data
    // Do not hyperlink if not M/WBE certified
    $is_mwbe_certified = MappingUtil::isMWBECertified($minority_type_id);
    if (Dashboard::isSubDashboard() || !$is_mwbe_certified) {
      return null;
    }

    $dashboard = RequestUtilities::get("dashboard") ?: "mp";
    return static::mwbeUrl($minority_type_id, $dashboard);
  }

  /**
   * Function to build the M/WBE Category url
   * Do not hyperlink if you are looking at prime data (prime dashboard)
   * Do not hyperlink if not M/WBE certified
   * @param $minority_type_id
   * @return string
   */
  static function SubMwbeCategoryUrl($minority_type_id)
  {

    // Do not hyperlink if you are looking at prime data
    // Do not hyperlink if not M/WBE certified
    $is_mwbe_certified = MappingUtil::isMWBECertified($minority_type_id);
    if (Dashboard::isPrimeDashboard() || !$is_mwbe_certified) {
      return null;
    }
    $dashboard = "sp";
    $url = static::mwbeUrl($minority_type_id, $dashboard);

    return isset($url) ? $url : null;
  }

  /**
   * Function to build the M/WBE Category url
   * @param $minority_type_id
   * @param $dashboard
   * @return string
   */
  static function mwbeUrl($minority_type_id, $dashboard)
  {
    $minority_type_id = $minority_type_id == 4 || $minority_type_id == 5 ? '4~5' : $minority_type_id;
    $url = '/spending_landing'
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . RequestUtilities::buildUrlFromParam('agency')
      . RequestUtilities::buildUrlFromParam('vendor')
      . RequestUtilities::buildUrlFromParam('category')
      . '/dashboard/' . $dashboard
      . '/mwbe/' . $minority_type_id;

    return $url;
  }

  /**
   * Gets the YTD Spending link in a generic way
   * @param $dynamic_parameter - custom dynamic parameters to be used in the URL
   * @param null $legacy_node_id
   * @return string
   */
  static function ytdSpendingUrl($dynamic_parameter, $legacy_node_id = null)
  {
    // Pass the correct fvendor parameter (subvendor id not vendor id) to the sub vendor ytd links
    $vendor_facet_parameter = (isset($legacy_node_id) && $legacy_node_id == 763) ? '' : static::getVendorFacetParameter();
    $legacy_node_id = isset($legacy_node_id) ? '/smnid/' . $legacy_node_id : '';
    $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';

    $url = '/spending/transactions'
      . RequestUtilities::buildUrlFromParam('vendor')
      . $vendor_facet_parameter
      . RequestUtilities::buildUrlFromParam('agency')
      . RequestUtilities::buildUrlFromParam('category')
      . RequestUtilities::buildUrlFromParam('industry')
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . $dynamic_parameter
      . $legacy_node_id;

    return $url;
  }

  /**
   * Function to build the footer url for the spending widgets
   * @param $parameters
   * @param null $legacy_node_id
   * @return string
   */
  static function getFooterUrl($parameters = null, $legacy_node_id = null)
  {
    // Donot pass fvendor parameter for subvendor details links
    $vendor_facet_parameter = (isset($legacy_node_id) && $legacy_node_id == 763) ? '' : static::getVendorFacetParameter();
    $legacy_node_id = isset($legacy_node_id) ? '/dtsmnid/' . $legacy_node_id : '';

    $url = '/spending/transactions'
      . RequestUtilities::buildUrlFromParam('vendor')
      . $vendor_facet_parameter
      . RequestUtilities::buildUrlFromParam('agency')
      . RequestUtilities::buildUrlFromParam('category')
      . RequestUtilities::buildUrlFromParam('industry')
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . $legacy_node_id;

    return $url;
  }

  /**
   * @return string
   */
  static function getMocUrlString()
  {
    return '/cevent/1/mocs/Yes';
  }

  /**
   * Returns the vendor or sub vendor id for the vendor facets
   * @return string
   */
  public static function getVendorFacetParameter()
  {
    $facet_vendor_id = Dashboard::isSubDashboard()
      ? RequestUtilities::get("subvendor")
      : RequestUtilities::get("vendor");
    return isset($facet_vendor_id) ? "/fvendor/" . $facet_vendor_id : null;
  }

}
