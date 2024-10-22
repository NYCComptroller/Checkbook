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

namespace Drupal\checkbook_services\Contracts;

require_once(\Drupal::service('extension.list.module')->getPath('checkbook_project') . "/includes/checkbook_project.inc");

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Dashboard;
use Drupal\checkbook_infrastructure_layer\Constants\Common\DashboardParameter;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\checkbook_infrastructure_layer\Constants\Contract\ContractCategory;
use Drupal\checkbook_infrastructure_layer\Constants\Contract\ContractStatus;
use Drupal\checkbook_infrastructure_layer\Constants\Contract\ContractType;
use Drupal\checkbook_infrastructure_layer\Constants\Contract\subVendorContractsByPrimeVendor;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_log\LogHelper;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;
use Drupal\checkbook_project\ContractsUtilities\ContractUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\checkbook_services\VendorUtil\PrimeVendorService;
use Drupal\checkbook_services\VendorUtil\SubVendorService;
use Drupal\checkbook_services\VendorUtil\VendorService;

class ContractsUrlService {

  const CONTRACT_AGID = '/contract_details/agid/';
  const MAGID = '/magid/';
  const CONTRACT_MAGID = '/contract_details/magid/';
  const DOCTYPE = '/doctype/';
  const CONTCAT = '/contcat/';
  const SMNID = '/smnid/';
  const NEWWINDOW = '/newwindow';
  const DASHBOARD = '/dashboard/';
  const MWBE = '/mwbe/';
  const VENDOR = '/vendor/';
  const SUBVENDOR = '/subvendor/';

  /**
   * To Do
   * Need to verify and update
   * RequestUtilities,DashboardParameter,
   * _checkbook_project_get_year_url_param_string,
   * Contract Category path variables
   */

  /**
   * @param $original_agreement_id
   * @param $document_code
   * @return string
   */
  public static function contractIdUrl($original_agreement_id, $document_code): string {
    return self::CONTRACT_AGID . $original_agreement_id
      . RequestUtilities::buildUrlFromParam([
        'status',
        'bottom_slider',
      ])
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . self::DOCTYPE . $document_code;
  }

  /**
   * @param $original_agreement_id
   * @param $document_code
   * @return string
   */
  public static function masterContractIdUrl($original_agreement_id, $document_code): string {
    return self::CONTRACT_MAGID . $original_agreement_id
      . RequestUtilities::buildUrlFromParam([
        'status',
        'bottom_slider',
      ])
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . self::DOCTYPE . $document_code;
  }

  /**
   * @param $original_agreement_id
   * @param $doctype
   * @param $fms_contract_number
   * @param null $pending_contract_number
   * @param null $version
   * @param null $linktype
   * @return string
   */
  public static function pendingMasterContractIdUrl($original_agreement_id, $doctype, $fms_contract_number, $pending_contract_number = null, $version = null, $linktype = null): string {
    $lower_doctype = strtolower($doctype);
    if ($original_agreement_id) {
      if (($lower_doctype == 'ma1') || ($lower_doctype == 'mma1') || ($lower_doctype == 'rct1')) {
        $url = self::CONTRACT_MAGID . $original_agreement_id
          . RequestUtilities::buildUrlFromParam('status')
          . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
          . self::DOCTYPE . $doctype;
      }
      else {
        $url = self::CONTRACT_AGID . $original_agreement_id
          . RequestUtilities::buildUrlFromParam('status')
          . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
          . self::DOCTYPE . $doctype;
      }
    }
    else {
      $url = '/pending_contract_transactions/contract/' . $fms_contract_number
        . RequestUtilities::buildUrlFromParam('status')
        . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
        . '/version/' . $version;
    }
    return $url;
  }

  /**
   * @param $original_agreement_id
   * @param $doctype
   * @param $fms_contract_number
   * @param null $pending_contract_number
   * @param null $version
   * @param null $linktype
   * @return string
   */
  public static function pendingContractIdLink($original_agreement_id, $doctype, $fms_contract_number, $pending_contract_number = null, $version = null, $linktype = null): string {
    $lower_doctype = strtolower($doctype);
    if ($original_agreement_id) {
      if (($lower_doctype == 'ma1') || ($lower_doctype == 'mma1') || ($lower_doctype == 'rct1')) {
        $url = self::CONTRACT_MAGID . $original_agreement_id . self::DOCTYPE . $doctype;
      }
      else {
        $url = self::CONTRACT_AGID . $original_agreement_id . self::DOCTYPE . $doctype;
      }
    }
    else {
      $url = '/pending_contract_transactions/contract/' . $pending_contract_number . '/version/' . $version;
    }

    //Don't persist M/WBE parameter if there is no dashboard (this could be an advanced search parameter)
    $mwbe_parameter = RequestUtilities::get('dashboard') != null ? RequestUtilities::buildUrlFromParam('mwbe') : '';
    $url .= $mwbe_parameter;

    return $url;
  }

  /**
   * Gets the spent to date link Url for the contract spending
   * @param $spend_type_parameter
   * @param null $legacy_node_id
   * @return string
   */
  public static function spentToDateUrl($spend_type_parameter, $legacy_node_id = null): string {
    return "/contract/spending/transactions"
      . $spend_type_parameter
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . RequestUtilities::buildUrlFromParam([
        'status',
        'status|contstatus',
        'agency|cagency',
        'vendor|cvendor',
        'awdmethod',
        'cindustry',
        'csize',
      ])
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . RequestUtilities::buildUrlFromParam('year|syear')
      . self::DOCTYPE . "CT1~CTA1~MA1"
      . self::CONTCAT . ContractCategory::getCurrent()
      . (isset($legacy_node_id) ? self::SMNID . $legacy_node_id . self::NEWWINDOW : self::NEWWINDOW);
  }

  public static function masterAgreementSpentToDateUrl($spend_type_parameter, $legacy_node_id = null) {
    return "/contract/spending/transactions"
      . $spend_type_parameter
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . RequestUtilities::buildUrlFromParam('status|contstatus')
      . (!Datasource::isOGE() ? RequestUtilities::buildUrlFromParam('agency|sagency') : "")
      . (!Datasource::isOGE() ? RequestUtilities::buildUrlFromParam('vendor|svendor') : RequestUtilities::buildUrlFromParam('vendor'))
      . RequestUtilities::buildUrlFromParam('cindustry|sindustry')
      . RequestUtilities::buildUrlFromParam([
        'awdmethod',
        'csize',
      ])
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . RequestUtilities::buildUrlFromParam('year|syear')
      . self::CONTCAT . ContractCategory::getCurrent()
      . (isset($legacy_node_id) ? self::SMNID . $legacy_node_id . self::NEWWINDOW : self::NEWWINDOW);
  }

  /**
   * Gets the Minority Type Name link for the given minority type id
   * @param $minority_type_id
   * @return NULL or string
   */
  public static function primeMinorityTypeUrl($minority_type_id): ?string {
    $showLink = Dashboard::isPrimeDashboard() && MappingUtil::isMWBECertified($minority_type_id);
    $dashboard = DashboardParameter::MWBE;
    return $showLink ? self::minorityTypeUrl($minority_type_id, $dashboard) : null;
  }

  /**
   * Get the minority type link url for a sub vendor.
   *
   * Rules:
   *
   * 1. Sub M/WBE category is only a link from Sub Dashboards
   * 2. Must be certified to be linkable
   * 3. If current dashboard is "Sub Vendors", redirect to "Sub Vendors (M/WBE)" dashboard
   *
   * @param $minority_type_id
   * @return string
   */
  public static function subMinorityTypeUrl($minority_type_id): ?string {
    $showLink = Dashboard::isSubDashboard() && MappingUtil::isMWBECertified($minority_type_id);
    $dashboard = DashboardParameter::getCurrent();
    $dashboard = $dashboard == DashboardParameter::SUB_VENDORS ? DashboardParameter::SUB_VENDORS_MWBE : $dashboard;
    return $showLink ? self::minorityTypeUrl($minority_type_id, $dashboard) : null;
  }

  /**
   * Gets the Minority Type Name link for the given minority type id
   * @param $minority_type_id
   * @param $dashboard
   * @return NULL or string
   */
  public static function minorityTypeUrl($minority_type_id, $dashboard): ?string {
    $url = NULL;
    if (MappingUtil::isMWBECertified($minority_type_id)) {
      $currentUrl = ContractType::getCurrentContractsLandingPage();
      $minority_type_id = ($minority_type_id == 4 || $minority_type_id == 5) ? '4~5' : $minority_type_id;
      $url = '/' . $currentUrl
        . RequestUtilities::buildUrlFromParam('syear|year')
        . CustomURLHelper::_checkbook_project_get_year_url_param_string()
        . RequestUtilities::buildUrlFromParam([
          'agency',
          'cindustry',
          'csize',
          'awdmethod',
          'contstatus|status',
          'vendor',
          'subvendor',
        ])
        . self::DASHBOARD . $dashboard
        . self::MWBE . $minority_type_id;
    }
    return $url;
  }

  /**
   * @param $agency_id
   * @param null $original_agreement_id
   * @return string
   */
  public static function agencyUrl($agency_id, $original_agreement_id = null): string {
    $currentUrl = ContractType::getCurrentContractsLandingPage();
    return '/' . $currentUrl
      . (isset($original_agreement_id) ? (self::MAGID . $original_agreement_id) : '')
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . RequestUtilities::buildUrlFromParam([
        'vendor',
        'cindustry',
        'csize',
        'awdmethod',
        'status',
        'bottom_slider',
      ])
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . "/agency/" . $agency_id;
  }

  /**
   * @param $award_method_code
   * @return string
   */
  public static function awardmethodUrl($award_method_code): string {
    $currentUrl = ContractType::getCurrentContractsLandingPage();
    return '/'. $currentUrl
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . RequestUtilities::buildUrlFromParam([
        'vendor',
        'cindustry',
        'csize',
        'agency',
        'status',
      ])
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . "/awdmethod/" . $award_method_code;
  }

  /**
   * @param $industry_type_id
   * @return string
   */
  public static function industryUrl($industry_type_id): string {
    $currentUrl = ContractType::getCurrentContractsLandingPage();
    return '/' . $currentUrl
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . RequestUtilities::buildUrlFromParam([
        'vendor',
        'agency',
        'csize',
        'awdmethod',
        'status',
        'bottom_slider',
      ])
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . "/cindustry/" . $industry_type_id;
  }

  /**
   * @param $award_size_id
   * @return string
   */
  public static function contractSizeUrl($award_size_id): string {
    $currentUrl = ContractType::getCurrentContractsLandingPage();
    //$currentPath = \Drupal::service('path.current')->getPath();
    //var_dump($currentPath);
    return '/' . $currentUrl
      . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
      . RequestUtilities::buildUrlFromParam([
        'vendor',
        'subvendor',
        'agency',
        'csize',
        'awdmethod',
        'status',
      ])
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . "/csize/" . $award_size_id;
  }

  /**
   * @param $parameters
   * @param null $legacy_node_id
   * @return string
   */
  public static function getFooterUrl($parameters, $legacy_node_id = null): string {
    list($subvendor, $vendor, $mwbe, $industry) = RequestUtilities::get(['subvendor', 'vendor', 'mwbe', 'cindustry']);
    $category = ContractCategory::getCurrent();

    $subvendor_code = $subvendor ? SubVendorService::getVendorCode($subvendor) : null;
    $vendor_code = $vendor ? PrimeVendorService::getVendorCode($vendor) : null;

    $subvendor_param = isset($subvendor_code) ? '/vendorcode/' . $subvendor_code : '';
    $vendor_param = isset($vendor_code) ? '/vendorcode/' . $vendor_code : '';
    $mwbe_param = isset($mwbe) ? (Dashboard::isSubDashboard() || $legacy_node_id == 720 ? '/smwbe/' . $mwbe : '/pmwbe/' . $mwbe) : '';


    if (Datasource::isOGE()) {
      $industry_param = isset($industry) ? '/cindustry/' . $industry : '';
    }
    else {
      $industry_param = isset($industry) ? (Dashboard::isSubDashboard() || $legacy_node_id == 720 ? '/scindustry/' . $industry : '/pcindustry/' . $industry) : '';
    }
    //Handle 3rd bottom navigation
    $bottom_slider = RequestUtilities::get('bottom_slider');
    if ($bottom_slider == "sub_vendor") {
      $mwbe_param = isset($mwbe) ? '/pmwbe/' . $mwbe : "";
    }

    $category_param = self::CONTCAT . (isset($category) ? $category : ContractCategory::EXPENSE);
    $smnid_param = isset($legacy_node_id) ? self::SMNID . $legacy_node_id : '';
    $contract_status = RequestUtilities::buildUrlFromParam('status|contstatus');
    $contract_status = isset($contract_status) && $contract_status != '' ? $contract_status : "/contstatus/P";
    // Add mwbe url parameter for pending contracts facet and transactions filtering
    if ($contract_status == '/contstatus/P' || $category == ContractCategory::REVENUE) {
      $mwbe_param = isset($mwbe) ? self::MWBE . $mwbe : '';
    }

    $contract_type = ContractType::getCurrent();
    if ($contract_type == 'registered_revenue' || $contract_type == 'pending_expense' || $contract_type == 'active_revenue') {
      $mwbe_param = isset($mwbe) ? self::MWBE . $mwbe : "";
    }
    $bottom_slider = RequestUtilities::get('bottom_slider');
    $path = isset($bottom_slider) && Dashboard::isSubDashboard() && subVendorContractsByPrimeVendor::getCurrent() == ContractCategory::EXPENSE
      ? '/subcontract/transactions'
      : '/contract/transactions';

    return $path . $category_param
      . $contract_status
      . CustomURLHelper::_checkbook_append_url_params()
      . RequestUtilities::buildUrlFromParam([
        'agency',
        'vendor',
        'subvendor',
        'vendor|fvendor',
        'awdmethod',
        'csize',
        'bottom_slider',
      ])
      . RequestUtilities::buildUrlFromParam('dashboard')
      . $mwbe_param . $subvendor_param . $vendor_param . $industry_param
      . CustomURLHelper::_checkbook_project_get_year_url_param_string()
      . self::getDocumentCodeUrlString($parameters)
      . $smnid_param;
  }

  /**
   * @param $parameters
   * @return string
   */
  public static function getDocumentCodeUrlString($parameters): string {
    $doc_type = $parameters['doctype']??null;
    if (isset($doc_type)) {
      $doc_type = explode(",", $doc_type);
      $doc_type = implode("~", str_replace("'", "", $doc_type));
      $doc_type = str_replace("(", "", str_replace(")", "", $doc_type));
    }
    else {
      //contract category or doc type is derived from the page path
      $status = ContractStatus::getCurrent();
      $category = ContractCategory::getCurrent();
      switch ($status) {
        case ContractStatus::PENDING:
          switch ($category) {
            case ContractCategory::REVENUE:
              $doc_type = "RCT1";
              break;
            default:
              if (isset($parameters['contract_type']) && $parameters['contract_type'] == 'master_agreement') {
                $doc_type = "MMA1~MA1~MAR";
              } else if (isset($parameters['contract_type']) && $parameters['contract_type']== 'child_contract') {
                $doc_type = "CT1~CTA1~CTR";
              } else {
                $doc_type = "MMA1~MA1~MAR~CT1~CTA1~CTR";
              }
              break;
          }
          break;
        default:
          switch ($category) {
            case ContractCategory::REVENUE:
              $doc_type = "RCT1";
              break;
            default:
              $doc_type = "MA1~CTA1~CT1";
              break;
          }
          break;
      }
    }

    return isset($doc_type) ? self::DOCTYPE . $doc_type : '';
  }

  /**
   * @param $blnIsMasterAgreement
   * @param $primeOrSub - Used to set prime or sub dollar difference parameter for
   *        Active/Registered Expense Contracts Modifications details links
   * @return string
   */
  public static function getAmtModificationUrlString($blnIsMasterAgreement = false, $primeOrSub = NULL): string {
    // Set modification parameter for Active/Registered Expense Contracts Modifications details links
    if ($primeOrSub == 'P') {
      return '/modamt/0/pmodamt/0';
    }
    elseif ($primeOrSub == 'S') {
      return '/modamt/0/smodamt/0';
    }

    if ($blnIsMasterAgreement) {
      $url = "/modamt/0" . (ContractUtil::showSubVendorData() ? '/smodamt/0' : '/pmodamt/0');
    }
    else {
      $url = "/modamt/0/pmodamt/0/smodamt/0";
    }

    return $url;
  }

  /**
   * @return string
   */
  public static function getMocUrlString(): string {
    $url = '/mocs/Yes';
    return $url;
  }

  /**
   * Returns Contracts Prime Vendor Landing page URL for the given prime vendor id, year and year type
   * @param $vendor_id
   * @param $year_id
   * @param $effective_end_year_id
   * @param bool $current
   * @return string
   */
  public static function primeVendorUrl($vendor_id, $year_id = null, $effective_end_year_id = null, bool $current = true): string {
    $url = RequestUtilities::buildUrlFromParam([
        'agency',
        'contstatus|status',
        'csize',
      ])
      . CustomURLHelper::_checkbook_project_get_year_url_param_string();

    list($year_type, $agency_id) = RequestUtilities::get(['yeartype', 'agency']);

    // For advanced search, if the year value is not set, get the latest minority type category for current Fiscal Year
    if (PageType::getCurrent() == PageType::ADVANCED_SEARCH_PAGE) {
      $year_url = self::applyYearParameter($effective_end_year_id);
      $year_id = $effective_end_year_id;
      $url = preg_replace("/\/year\/\d+/", $year_url, $url);
    }

    $latest_minority_id = $year_id
      ? PrimeVendorService::getLatestMinorityTypeByYear($vendor_id, $year_id, $year_type,'P',CheckbookDomain::$CONTRACTS)
      : PrimeVendorService::getLatestMinorityType($vendor_id, $agency_id ?? null,'P',CheckbookDomain::$CONTRACTS);

    $is_mwbe_certified = MappingUtil::isMWBECertified($latest_minority_id);
    $mwbe_amount = VendorService::getMwbeAmount($vendor_id, $year_id);
    $subven_amount = VendorService::getSubVendorAmount($vendor_id, $year_id);

    $urlPath = RequestUtilities::getCurrentPageUrl();
    if (!str_contains($urlPath, 'pending')) {
      if (!RequestUtilities::get('status')) {
        $url .= "/status/A";
      }
    }

    $minority_id = MappingUtil::getTotalMinorityIds('url');
    if ($is_mwbe_certified && isset($mwbe_amount)) {
      $url .= self::DASHBOARD . "mp" . self::MWBE . $minority_id . self::VENDOR . $vendor_id;
    }
    elseif ($mwbe_amount == 0 && $subven_amount > 0 || !isset($mwbe_amount) && $subven_amount > 0) {
      // if prime is zero and sub amount is not zero. change dashboard to ms
      $url .= self::DASHBOARD . "ms" . self::MWBE . $minority_id . self::VENDOR . $vendor_id;
    }
    elseif ($is_mwbe_certified) {
      $url .= self::DASHBOARD . "mp" . self::MWBE . $minority_id . self::VENDOR . $vendor_id;
    }
    else {
      $url .= RequestUtilities::buildUrlFromParam('datasource') . self::VENDOR . $vendor_id;
    }

    $currentUrl = '/'. ContractType::getCurrentContractsLandingPage();
    return ($current) ? $currentUrl . $url : $url;
  }

  /**
   * Returns Sub Vendor Landing page URL for the given sub vendor id in the given year and year type for
   * Active/Registered Contracts Landing Pages
   * @param $vendor_id
   * @param $year_id
   * @return string
   */
  public static function subVendorUrl($vendor_id, $year_id = null): string {
    list($year_type, $agency_id) = RequestUtilities::get(['yeartype', 'agency']);
    $currentUrl = '/'. ContractType::getCurrentContractsLandingPage();

    $latest_minority_id = !(isset($year_id))
      ? SubVendorService::getLatestMinorityType($vendor_id, $agency_id,'S',CheckbookDomain::$CONTRACTS)
      : SubVendorService::getLatestMinorityTypeByYear($vendor_id, $year_id, $year_type,'S',CheckbookDomain::$CONTRACTS);

    $url = RequestUtilities::buildUrlFromParam(['agency', 'contstatus|status'])
      . CustomURLHelper::_checkbook_project_get_year_url_param_string();

    $current_dashboard = RequestUtilities::get("dashboard");
    $is_mwbe_certified = MappingUtil::isMWBECertified(array($latest_minority_id));

    //if M/WBE certified, go to M/WBE (Sub Vendor) else if NOT M/WBE certified, go to Sub Vendor dashboard
    $new_dashboard = $is_mwbe_certified ? "ms" : "ss";
    $status = strlen(RequestUtilities::buildUrlFromParam('contstatus|status')) == 0 ? "/status/A" : "";

    if ($current_dashboard != $new_dashboard) {
      return $currentUrl . $url . $status . self::DASHBOARD . $new_dashboard . ($is_mwbe_certified ? self::MWBE . MappingUtil::getTotalMinorityIds('url') : "") . self::SUBVENDOR . $vendor_id;
    }
    else {
      $url .= $status
        . RequestUtilities::buildUrlFromParam([
          'cindustry',
          'csize',
          'awdmethod',
        ])
        . self::DASHBOARD . $new_dashboard
        . ($is_mwbe_certified ? self::MWBE . MappingUtil::getTotalMinorityIds('url') : "")
        . self::SUBVENDOR . $vendor_id;
      return $currentUrl . $url;
    }
  }

  /**
   * @param $docType
   * @return string
   */
  public static function applyLandingParameter($docType): string {
    if ($docType == "RCT1") {
      $page = "/contracts_revenue_landing";
    }
    else {
      $page = "/contracts_landing";
    }
    return $page;
  }

  /**
   * @param $effective_end_year_id
   * @return string
   */
  public static function applyYearParameter($effective_end_year_id = null): string {
    $year_id = RequestUtilities::get("year");
    if (PageType::getCurrent() == PageType::ADVANCED_SEARCH_PAGE && !isset($year_id)) {
      if ($effective_end_year_id != '' && $effective_end_year_id < CheckbookDateUtil::getCurrentFiscalYearId()) {
        $year_id = $effective_end_year_id;
      }
      else {
        $year_id = CheckbookDateUtil::getCurrentFiscalYearId();
      }
    }

    //adding for ticket NYCCHKBK-12884, yearid was not getting added after /year/
    if (!isset($year_id)) {
      $year_id = CheckbookDateUtil::getCurrentFiscalYearId();
    }
    $url = '/year/' . $year_id;
    return $url;
  }

  /**
   * @param $effective_end_year_id
   * @return string
   */
  public static function adjustYeartypeParameter($effective_end_year_id = null): string {
    $url = CustomURLHelper::_checkbook_project_get_year_url_param_string();
    $year = self::applyYearParameter($effective_end_year_id);
    return preg_replace("/\/year\/\d+/", $year, $url);
  }
}
