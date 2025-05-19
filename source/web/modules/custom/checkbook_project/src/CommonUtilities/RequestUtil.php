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

namespace Drupal\checkbook_project\CommonUtilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_log\LogHelper;
use Drupal\checkbook_project\ContractsUtilities\ContractUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\checkbook_project\SpendingUtilities\SpendingUrlHelper;
use Drupal\checkbook_services\VendorUtil\VendorService;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;

/**
 * Class RequestUtil
 */
class RequestUtil {

  const VENDOR_CUSTOM_CODE = "0000776804";
  const VENDOR_DATASET = "checkbook:vendor";

  const YEAR = "/year/";

  //Links for landing pages. This can be avoided if ajax requests can be identified uniquely.
  /**
   * @var array
   */
  public static $landing_links = array(
    "contracts_landing",
    "contracts_revenue_landing",
    "contracts_pending_rev_landing",
    "contracts_pending_exp_landing"
  );

  /**
   * @var array
   */
  public static $contracts_spending_landing_links = array(
    "spending_landing",
    "contracts_landing",
    "contracts_revenue_landing",
    "contracts_pending_rev_landing",
    "contracts_pending_exp_landing"
  );

  /**
   * @var null
   */
  public static $is_prime_mwbe_amount_zero_sub_mwbe_not_zero = null;

  /** Checks if the page bottom container is expanded */
  public static function isExpandBottomContainer()
  {
    $referer = $_SERVER['HTTP_REFERER'] ?? \Drupal::request()->query->get('q');
    foreach (self::$landing_links as $landing_link) {
      if (preg_match("/$landing_link/i", $referer)) {
        return true;
      }
    }

    return false;
  }

  /** Checks if the current URL is opened in a new window */
  public static function isNewWindow() {
    $referer = $_SERVER['HTTP_REFERER'] ?? \Drupal::request()->query->get('q');
    return preg_match('/newwindow/i', $referer);
  }

  /** Checks if the current page is Pending Expense Contratcts page
   * @param $path
   * @return bool
   */
  public static function isPendingExpenseContractPath($path) {
    return TRUE === str_contains($path, 'contracts_pending_exp_landing');
  }

  /** Checks if the current page is Pending Revenue Contratcts page
   * @param $path
   * @return bool
   */
  public static function isPendingRevenueContractPath($path)
  {
    return TRUE === str_contains($path, 'contracts_pending_rev_landing');
  }

  /** Checks if the current page is Active/Registered Expense Contratcs page
   * @param $path
   * @return bool
   */
  public static function isExpenseContractPath($path)
  {
    return TRUE === str_contains($path, 'contracts_landing');
  }

  /** Checks if the current page is Active/Registered Pending Revenue Contratcs page
   * @param $path
   * @return bool
   */
  public static function isRevenueContractPath($path)
  {
    return TRUE === str_contains($path, 'contracts_revenue_landing');
  }

  public static function isNYCHAContractPath($path)
  {
    return TRUE === str_contains($path, 'nycha_contracts');
  }

  /** Returns the request parameter value from URL
   * @param $key
   * @param $urlPath
   * @return mixed|null|string
   */
  public static function getRequestKeyValueFromURL($key, $urlPath)
  {
    $value = NULL;
    $pathParams = explode('/', $urlPath);
    $index = array_search($key, $pathParams);
    if ($index != FALSE) {
      $value = Xss::filter($pathParams[($index + 1)]);
    }
    return $value;
  }

  /**
   * @param null $dashboard
   * @return string
   */
  public static function getDashboardTitle($dashboard = null)
  {
    if ($dashboard == null) {
      $dashboard = RequestUtilities::get('dashboard');
    }
    switch ($dashboard) {
      case "mp" :
        return "M/WBE";
      case "sp" :
        return "Sub Vendors (M/WBE)";
      case "ms" :
        return "M/WBE (Sub Vendors)";
      case "ss" :
        return "Sub Vendors";
      default:
        break;
    }
    return '';
  }

  /** Returns Contracts Bottom Slider path which has data
   * @return string
   */
  public static function getContractsBottomSliderPath(){
    //Get 'Contracts Bottom Slider' Counts
    $node = widget_load(363);
    widget_config($node);
    widget_prepare($node);
    widget_invoke($node, 'widget_prepare');
    widget_data($node);
    $contracts_landing_path = NULL;
    if ($node->data[0]['total_contracts'] > 0 || $node->data[0]['current_amount_sum'] > 0) {
      $contracts_landing_path = "contracts_landing/status/A";
    } else if ($node->data[1]['total_contracts'] > 0 || $node->data[1]['current_amount_sum'] > 0) {
      $contracts_landing_path = "contracts_landing/status/R";
    } else if ($node->data[2]['total_contracts'] > 0 || $node->data[2]['current_amount_sum'] > 0) {
      $contracts_landing_path = "contracts_revenue_landing/status/A";
    } else if ($node->data[3]['total_contracts'] > 0 || $node->data[3]['current_amount_sum'] > 0) {
      $contracts_landing_path = "contracts_revenue_landing/status/R";
    } else if ($node->data[5]['total_contracts'] > 0 || $node->data[5]['current_amount_sum'] > 0) {
      $contracts_landing_path = "contracts_pending_exp_landing";
    } else if ($node->data[6]['total_contracts'] > 0 || $node->data[6]['current_amount_sum'] > 0) {
      $contracts_landing_path = "contracts_pending_rev_landing";
    }
    return $contracts_landing_path;
  }

  /** Returns top navigation URL
   * @param $domain
   * @param $contracts_url
   * @return string
   */
  public static function getTopNavURL($domain, $contracts_url = NULL) {
    $path = '';
    $fiscalYearId = CheckbookDateUtil::getFiscalYearIdForTopNavigation();

    switch ($domain) {
      case "contracts":
        $contracts_landing_path = $contracts_url ?? "/contracts_landing/status/A";
        $path = $contracts_landing_path . "/yeartype/B/year/" . $fiscalYearId . CustomURLHelper::_checkbook_append_url_params(null, array(), true);
        if (RequestUtilities::get("agency") > 0) {
          $path = $path . "/agency/" . RequestUtilities::get("agency");
        }
        else if (_checkbook_check_isEDCPage()) {
          $path = $path . "/agency/9000";
        }
        if (RequestUtilities::get("vendor") > 0) {
          $path = $path . "/vendor/" . RequestUtilities::get("vendor");
        }
        break;

      case "nycha_contracts":
        $path = "/nycha_contracts/datasource/" . Datasource::NYCHA;
        $path .= self::YEAR . $fiscalYearId;
        $path .= Datasource::getNYCHAUrl();
        if (RequestUtilities::get("vendor") > 0) {
          $path .=  "/vendor/" . RequestUtilities::get("vendor");
        }
        break;

      case "spending":
        $path = "/spending_landing/yeartype/B/year/" . $fiscalYearId . CustomURLHelper::_checkbook_append_url_params(null, array(), true);
        if (RequestUtilities::get("agency") > 0) {
          $path = $path . "/agency/" . RequestUtilities::get("agency");
        }
        else if (_checkbook_check_isEDCPage()) {
          $path = $path . "/agency/9000";
        }
        if (RequestUtilities::get("vendor") > 0) {
          $path = $path . "/vendor/" . RequestUtilities::get("vendor");
        }
        break;

      case "nycha_spending":
        $path = "/nycha_spending/datasource/".Datasource::NYCHA;
        $path .= self::YEAR . $fiscalYearId;
        $path .= Datasource::getNYCHAUrl();
        if (RequestUtilities::get("vendor") > 0) {
          $path .=  "/vendor/" . RequestUtilities::get("vendor");
        }
        break;

      case "payroll":
        // Payroll is always redirected to the respective Calendar Year irrespective of the 'yeartype' paramenter in the URL for all the other Domains
        if (!str_contains(\Drupal::request()->getRequestUri(), 'payroll')) {
          $yearType = 'C';
          $year = CheckbookDateUtil::getCalYearIdForTopNavigation();
        }
        else {
          $yearType = RequestUtilities::get("yeartype");
          $year = (RequestUtilities::get('year') ?? RequestUtilities::get('calyear')) ?? RequestUtilities::_getRequestParamValueBottomURL('calyear');
        }

        if (str_contains(Url::fromRoute("<current>")->toString(), 'agency_landing')) {
          $path = "/payroll/agency_landing/yeartype/" . $yearType . self::YEAR . $year;
          $path .= RequestUtilities::buildUrlFromParam('title');
          $path .= RequestUtilities::buildUrlFromParam('agency');
          $path .= RequestUtilities::buildUrlFromParam('datasource');
        }
        elseif (str_contains(Url::fromRoute("<current>")->toString(), 'title_landing')) {
          $path = "/payroll/title_landing/yeartype/" . $yearType . self::YEAR . $year;
          $path .= RequestUtilities::buildUrlFromParam('agency');
          $path .= RequestUtilities::buildUrlFromParam('title');
          $path .= RequestUtilities::buildUrlFromParam('datasource');
        }
        else {
          $bottomURL = RequestUtilities::getBottomContUrl();
          $bottomURL = ($bottomURL) ?: Url::fromRoute("<current>")->toString();
          $last_parameter = RequestUtil::_getLastRequestParamValue($bottomURL);
          if (isset($last_parameter['agency']) && $last_parameter['agency'] > 0 ) {
            $path = "/payroll/agency_landing/yeartype/" . $yearType . self::YEAR . $year;
            $path .= RequestUtilities::buildUrlFromParam('datasource');
            $path .= RequestUtilities::buildUrlFromParam('title');
            $path .= RequestUtilities::buildUrlFromParam('agency');
          }
          elseif (isset($last_parameter['title']) && $last_parameter['title'] > 0) {
            $path = "/payroll/title_landing/yeartype/" . $yearType . self::YEAR . $year;
            $path .= RequestUtilities::buildUrlFromParam('datasource');
            $path .= RequestUtilities::buildUrlFromParam('agency');
            $path .= RequestUtilities::buildUrlFromParam('title');
          }
          elseif (RequestUtilities::get('agency')) {
            $path = "/payroll/agency_landing/yeartype/" . $yearType . self::YEAR . $year;
            $path .= RequestUtilities::buildUrlFromParam('datasource');
            $path .= RequestUtilities::buildUrlFromParam('title');
            $path .= RequestUtilities::buildUrlFromParam('agency');
          }
          else {
            // NYCHA level.
            $datasource = RequestUtilities::get('datasource');
            if ($datasource == 'checkbook_nycha') {
              $path = "/payroll/agency_landing/yeartype/" . $yearType . self::YEAR . $year . RequestUtilities::buildUrlFromParam('datasource') . Datasource::getNYCHAUrl();
            }
            // NYC Level.
            else {
              $path = "/payroll/yeartype/" . $yearType . self::YEAR . $year . RequestUtilities::buildUrlFromParam('datasource');
            }
          }
        }
        break;
      case "budget":
        if (RequestUtilities::get("agency") > 0) {
          $path = "/budget/yeartype/B/year/" . $fiscalYearId . RequestUtilities::buildUrlFromParam('agency');
        }
        else {
          $path = "/budget/yeartype/B/year/" . $fiscalYearId;
        }
        break;
      case "nycha_budget":
        $path = "/nycha_budget/datasource/".Datasource::NYCHA;
        $path .= self::YEAR.$fiscalYearId;
        $path .= Datasource::getNYCHAUrl();
        break;
      case "revenue":
        $path = "/revenue/yeartype/B/year/" . $fiscalYearId . RequestUtilities::buildUrlFromParam('agency');
        break;
      case "nycha_revenue":
        $path = "/nycha_revenue/datasource/".Datasource::NYCHA;
        $path .= self::YEAR.$fiscalYearId;
        $path .= Datasource::getNYCHAUrl();
        break;
    }

    // Verify if the vendor is pure mwbe - meaning the vendor can only have certified minority ids
    // There are situations where the vendor id can be both certified and non-minorty ,
    // in this case the vendor is not pure mwbe certified.
    if (RequestUtilities::get("vendor") > 0 && in_array($domain, array('contracts','spending'))){
      $non_minority_type_ids = array(7, 11);
      $vendor_minority_type_ids = VendorService::getAllVendorMinorityTypesByYear($domain, RequestUtilities::get("vendor"), $fiscalYearId);
      $vendor_non_minority_type_ids = array_intersect($non_minority_type_ids ,$vendor_minority_type_ids);

      if(count($vendor_non_minority_type_ids) > 0){
        $path = preg_replace('/\/dashboard\/[^\/]*/','',$path);
        $path = preg_replace('/\/mwbe\/[^\/]*/','',$path);
      }else{
        if(!stripos(' '.$path,'/dashboard/')) {
          $path .= RequestUtilities::buildUrlFromParam('dashboard');
        }
        //using RequestUtilities::buildUrlFromParam urlencodes mwbe parameter
        if(!stripos(' '.$path,'/mwbe/') && stripos(' '.$path,'/dashboard/')) {
          $mwbe_param = RequestUtilities::get('mwbe');
          $path .= '/mwbe/';
          $path .= $mwbe_param ?? MappingUtil::getTotalMinorityIds('url');
        }
      }
    }

    return $path;
  }

  /** Checks if the current page is NYC level*/
  public static function isNYCLevelPage()
  {
    self::isEDCPage();
    $landingPages = array("contracts_landing", "contracts_revenue_landing",
      "contracts_pending_rev_landing", "contracts_pending_exp_landing",
      "spending_landing", "payroll", "budget", "revenue");

    //$url = $_GET['q'];
    //$urlPath = drupal_get_path_alias($url);
    $url = \Drupal::service('path.current')->getPath();
    $urlPath = \Drupal::service('path_alias.manager')->getAliasByPath($url);
    $pathParams = explode('/', $urlPath);
    if (in_array($pathParams[0], $landingPages)) {
      if ($pathParams[0] == "payroll" && $pathParams[1] == "search") {
        return false;
      }
      return true;

    }

    return false;
  }

  /**
   * @return bool
   */
  public static function isEDCPage()
  {
    $vendor_id = RequestUtilities::get('vendor');
    if ($vendor_id != null) {
      $vendor = _checkbook_project_querydataset(self::VENDOR_DATASET, "vendor_customer_code", array("vendor_id" => $vendor_id));

      if ($vendor[0]['vendor_customer_code'] == self::VENDOR_CUSTOM_CODE) {
        return true;
      } else {
        return false;
      }

    }
    return false;
  }

  /**
   * @return string
   */
  public static function getEDCURL()
  {
    $vendor = _checkbook_project_querydataset(self::VENDOR_DATASET, "vendor_id", array("vendor_customer_code" => self::VENDOR_CUSTOM_CODE));
    $url = "contracts_landing/status/A/yeartype/B/year/" . CheckbookDateUtil::getCurrentFiscalYearId() . "/vendor/" . $vendor[0]['vendor_id'];
    return $url;
  }

  /**
   * @return string
   */
  public static function getSpendingEDCURL()
  {
    $vendor = _checkbook_project_querydataset(self::VENDOR_DATASET, "vendor_id", array("vendor_customer_code" => self::VENDOR_CUSTOM_CODE));
    $url = "spending_landing/yeartype/B/year/" . CheckbookDateUtil::getCurrentFiscalYearId() . "/vendor/" . $vendor[0]['vendor_id'];
    return $url;
  }

  /**
   * @param null $current_state
   * @return string
   */
  public static function getNextMWBEDashboardState($current_state = null)
  {
    if ($current_state == null) {
      $current_state = RequestUtilities::get('dashboard');
    }
    return "/dashboard/" . self::getNextMWBEDashboardStateParam($current_state);

  }

  /**
   * @param null $current_state
   * @return string
   */
  public static function getNextMWBEDashboardStateParam($current_state = null)
  {
    $current_state = $current_state?: RequestUtilities::get('dashboard');

    if (!$current_state) {
      if (stripos('_' . Url::fromRoute("<current>")->toString(), 'contracts')) {
        $domain = "contracts";
      } else {
        $domain = "spending";
      }
      $applicable_filters = MappingUtil::getCurrentPrimeMWBEApplicableFilters($domain);
      if (count($applicable_filters) == 0) {
        return 'ms';
      }
    }

    switch ($current_state) {
      case "mp" :
      case "sp" :
        return "mp";
      case "ms" :
      case "ss" :
        return "ms";
      default:
        break;
    }
    return "mp";

  }

  /*
   *
   *
   *
   *  If MWBE is clicked first the flow becomes prim flow. states mp and sp
   *  If subvendor is clicked first the flow becomes subvendor flow. states ms and ms
   */

  /**
   * @param null $current_state
   * @return string
   */
  public static function getNextSubvendorDashboardState($current_state = null)
  {
    $current_state = $current_state ?: RequestUtilities::get('dashboard');

    return "/dashboard/" . self::getNextSubvendorDashboardStateParam($current_state);
  }

  /*
  *
  *
  *
  *  If MWBE is clicked first the flow becomes prim flow. states mp and sp
  *  If subvendor is clicked first the flow becomes subvendor flow. states ms and ms
  */

  /**
   * @param null $current_state
   * @return string
   */
  public static function getNextSubvendorDashboardStateParam($current_state = null)
  {
    $current_state = $current_state ?: RequestUtilities::get('dashboard');

    switch ($current_state) {
      case "mp" :
      case "sp" :
        return "sp";
      case "ms" :
      case "ss" :
        return "ss";
      default:
        break;
    }
    return "ss";
  }


  /*
   *
   *
   * If subvendor is clicked first the flow becomes subvendor flow
   *
   */

  /**
   * @param null $current_state
   * @return bool
   */
  public static function isDashboardSubvendor($current_state = null)
  {
    $current_state = $current_state ?: RequestUtilities::get('dashboard');

    switch ($current_state) {
      case "sp" :
      case "ss" :
        return true;
      default:
        break;

    }

    return false;
  }

  /**
   * @param $dashboard_filter
   * @return mixed|string
   */
  public static function getDashboardTopNavURL($dashboard_filter)
  {

    if (self::isContractsSpendingLandingPage()) {
      $url = RequestUtilities::getCurrentPageUrl();

      //Exclude parameters that should not persist in the feature dashboards for Spending Domain
      if (str_contains($url, 'contract')) {
        $url = ContractUtil::getLandingPageWidgetUrl();
      } else {
        //Default to total spending
        $override_params = array("category" => null);
        $url = SpendingUrlHelper::getLandingPageWidgetUrl($override_params);
      }

    } else {
      $url = self::getCurrentDomainURLFromParams();
    }

    switch ($dashboard_filter) {
      case "mwbe":
        if (RequestUtilities::get("dashboard") != null) {
          $url = preg_replace('/\/dashboard\/[^\/]*/', '', $url);
        }
        $url .= "/dashboard/" . self::getNextMWBEDashboardStateParam();
        if (!str_contains($url, 'mwbe')) {
          if (RequestUtilities::get("mwbe") != null) {
            $url .= "/mwbe/" . RequestUtilities::get("mwbe");
          } else {
            $url .= "/mwbe/".MappingUtil::getTotalMinorityIds('url');
          }
        }
        break;
      case "subvendor":
        if (RequestUtilities::get("dashboard") != null) {
          $url = preg_replace('/\/dashboard\/[^\/]*/', '', $url);
        }
        // tm_wbe is an exception case for total MWBE link. When prime data is not present but sub data is present for the agency vendor combination.
        if (RequestUtilities::get("dashboard") == 'ms' && RequestUtilities::get("mwbe") == MappingUtil::getTotalMinorityIds('url') && RequestUtilities::get("tm_wbe") != 'Y') {
          $url = preg_replace('/\/mwbe\/[^\/]*/', '', $url);
        }

        $url .= "/dashboard/" . self::getNextSubvendorDashboardStateParam();
        break;
      default:
        break;
    }

    //For MWBE and Sub Vendor dashboard links add status parameters if it is not there
    //If status parameter is existing, set it to 'Active' always
    //Do this only for contracts
    if (str_contains($url, 'contracts')) {
      if (!str_contains($url, 'status')) {
        $url .= "/status/A";
      } else {
        $url = preg_replace('/\/status\/[^\/]*/', '/status/A', $url);
      }
    }
    $url = str_replace(['"', "'"], '', $url);
    return $url;
  }

  /**
   * @return bool
   */
  public static function isContractsSpendingLandingPage()
  {
    $first_part = explode('/', RequestUtilities::getCurrentPageUrl());
    if (in_array($first_part[1], self::$contracts_spending_landing_links)) {
      return true;
    }
    return false;
  }

  /**
   * @return string
   */
  public static function getCurrentDomainURLFromParams()
  {
    if (stripos('_'. Url::fromRoute("<current>")->toString(), 'contract')) {

      $reqParams = MappingUtil::$contractsMWBEParamMap;
      $prefix = 'contracts_landing';
    } else {
      $reqParams = MappingUtil::$spendingMWBEParamMap;
      $prefix = 'spending_landing';
    }

    foreach ($reqParams as $key => $value) {
      $value = RequestUtilities::get($key);
      if ($key == "year") {
        $value =  CheckbookDateUtil::getFiscalYearIdForTopNavigation();
      }
      if ($key == "yeartype") {
        $value = 'B';
      }

      if ($key == "status" && $value == null) {
        $value = 'A';
      }
      $prefix .= ($value != null) ? "/$key/" . $value : "";

    }
    return $prefix;
  }

  /**
   * @param $dashboard_filter
   * @return string
   */
  public static function getDashboardTopNavTitle($dashboard_filter)
  {
    switch ($dashboard_filter) {
      case "mwbe":
        if (self::isDashboardFlowSubvendor()) {
          return "M/WBE (Sub Vendors)";
        }
        return "M/WBE";
      case "subvendor":
        // tm_wbe is an exception case for total MWBE link. When prime data is not present but sub data is present for the agency vendor combination.
        if (self::isDashboardFlowPrimevendor() || RequestUtilities::get("tm_wbe") == 'Y') {
          return "Sub Vendors (M/WBE)";
        }
        return "Sub Vendors";
      default:
        break;
    }
    return '';
  }

  /**
   * @param null $current_state
   * @return bool
   */
  public static function isDashboardFlowSubvendor($current_state = null)
  {

    if (self::$is_prime_mwbe_amount_zero_sub_mwbe_not_zero) {
      return true;
    }

    $current_state = $current_state ?: RequestUtilities::get('dashboard');

    return in_array($current_state, array('ms', 'ss'));
  }

  /**
   * @param null $current_state
   * @return bool
   */
  public static function isDashboardFlowPrimevendor($current_state = null)
  {
    $current_state = $current_state ?: RequestUtilities::get('dashboard');

    return in_array($current_state, array('mp', 'sp'));
  }

  /**
   * @param $string
   * @param $param
   * @param $value
   * @return mixed
   */
  public static function replaceParamFromString($string, $param, $value)
  {
    return preg_replace('/\/' . $param . '\/[^\/]*/', '/' . $param . '/' . $value, $string);
  }

  /**
   * @return string
   */
  public static function getTotalSubvendorsLink()
  {
    $urlParamMap = $table = $default_params = $sub_vendors_total_link = null;
    if (stripos('_'. Url::fromRoute("<current>")->toString(), 'contract')) {
      $domain = "contracts";
    } else {
      $domain = "spending";
    }

    switch ($domain) {
      case "spending":
        $table = "aggregateon_subven_spending_coa_entities";
        $urlParamMap = array("year" => "year_id", "yeartype" => "type_of_year", "agency" => "agency_id", "vendor" => "prime_vendor_id");
        $sub_vendors_total_link = RequestUtil::getLandingPageUrl("spending", RequestUtilities::get("year"), 'B');
        break;
      case "contracts":
        $table = "aggregateon_subven_contracts_cumulative_spending";
        $urlParamMap = array("year" => "fiscal_year_id", "agency" => "agency_id", "yeartype" => "type_of_year", "vendor" => "prime_vendor_id");
        $default_params = array("status_flag" => "A");
        $sub_vendors_total_link = RequestUtil::getLandingPageUrl("contracts", RequestUtilities::get("year"), 'B');
        break;
      default:
        break;
    }
    if (self::get_top_nav_records_count($urlParamMap, $default_params, $table) > 0) {
      return "/" . $sub_vendors_total_link . RequestUtilities::buildUrlFromParam('agency')
        . RequestUtilities::buildUrlFromParam('vendor') .
        "/dashboard/ss";
    }
    return '';
  }

  /**
   * @param $domain
   * @param null $year
   * @param null $yearType
   * @return null|string
   */
  public static function getLandingPageUrl($domain, $year = null, $yearType = null)
  {
    $path = null;
    $year = $year ?: CheckbookDateUtil::getCurrentFiscalYearId();
    $yearType = $yearType ?: 'B';

    switch ($domain) {
      case "contracts":
        $path = "contracts_landing/status/A/yeartype/B/year/" . $year;
        break;
      case "spending":
        $path = "spending_landing/yeartype/B/year/" . $year;
        break;
      case "payroll":
        $path = "payroll/yeartype/" . $yearType . self::YEAR . $year;
        break;
      case "budget":
        $path = "budget/yeartype/B/year/" . $year;
        break;
      case "revenue":
        $path = "revenue/yeartype/B/year/" . $year;
        break;
      default:
        break;
    }

    return $path;
  }

  /**
   * @param $urlParamMap
   * @param $default_params
   * @param $table
   * @return mixed
   */
  public static function get_top_nav_records_count($urlParamMap, $default_params, $table)
  {
    $where_filters = array();
    $where_filter = null;

    foreach ($urlParamMap as $param => $value) {
      if (RequestUtilities::get($param) != null) {
        $where_filters[] = _widget_build_sql_condition(' a1.' . $value, RequestUtilities::get($param));
      }
    }

    if (is_array($default_params)) {
      foreach ($default_params as $param => $value) {
        $where_filters[] = _widget_build_sql_condition(' a1.' . $param, $value);
      }
    }

    if (count($where_filters) > 0) {
      $where_filter = ' where ' . implode(' and ', $where_filters);
    }

    $sql = 'select count(*) count
				    from ' . $table . ' a1
				   ' . $where_filter;
    $dataSource = Datasource::getCurrent();
    $cacheKey = '_top_nav_count_'. $dataSource . '_' .md5($sql);
    $count = _checkbook_dmemcache_get($cacheKey);
    if (is_numeric($count)) {
      LogHelper::log_info("Get Cached count in RequestUtil::get_top_nav_records_count with cachekey " . $cacheKey);
      return $count;
    }
    $data = _checkbook_project_execute_sql($sql);
    $count = $data[0]['count'];
    if( is_numeric($count)) {
      LogHelper::log_info("Set Cached count in RequestUtil::get_top_nav_records_count with cachekey " . $cacheKey . " Count is $count");
      _checkbook_dmemcache_set($cacheKey, $count);
    }
    return $count;
  }

  /**
   * @return string
   */
  public static function getTotalMWBELink()
  {
    $urlParamMap = $default_params = $table_subven = $table = $urlParamMapSubven = null;

    if (stripos('_'. Url::fromRoute("<current>")->toString(), 'contract')) {
      $domain = "contracts";
    } else {
      $domain = "spending";
    }

    switch ($domain) {
      case "spending":
        $table = "aggregateon_mwbe_spending_coa_entities";
        $table_subven = "aggregateon_subven_spending_coa_entities";
        $urlParamMap = array("year" => "year_id", "agency" => "agency_id", "vendor" => "vendor_id");
        $urlParamMapSubven = array("year" => "year_id", "agency" => "agency_id", "vendor" => "prime_vendor_id");
        $default_params = array("minority_type_id" => MappingUtil::getTotalMinorityIds('url'), 'type_of_year' => 'B');
        break;
      case "contracts":
        $table = "aggregateon_mwbe_contracts_cumulative_spending";
        $table_subven = "aggregateon_subven_contracts_cumulative_spending";
        $urlParamMap = array("year" => "fiscal_year_id", "agency" => "agency_id", "vendor" => "vendor_id");
        $urlParamMapSubven = array("year" => "fiscal_year_id", "agency" => "agency_id", "vendor" => "prime_vendor_id");
        $default_params = array("status_flag" => "A", "minority_type_id" => MappingUtil::getTotalMinorityIds('url'), 'type_of_year' => 'B');
        break;
      default:
        break;
    }
    if (self::get_top_nav_records_count($urlParamMap, $default_params, $table) > 0) {
      $dashboard = "mp";
    } elseif (self::get_top_nav_records_count($urlParamMapSubven, $default_params, $table_subven) > 0) {
      // tm_wbe is an exception case for total MWBE link. When prime data is not present but sub data is present for the agency vendor combination.
      $dashboard = "ms/tm_wbe/Y";
    } else {
      return "";
    }
    return '/' . RequestUtil::getLandingPageUrl($domain, CheckbookDateUtil::getFiscalYearIdForTopNavigation(), 'B') . "/mwbe/" . MappingUtil::$total_mwbe_cats
      . "/dashboard/" . $dashboard .
      RequestUtilities::buildUrlFromParam('agency')
      . RequestUtilities::buildUrlFromParam('vendor');
  }


  /** Checks if the URL is widget link for Nycha facet disabling */
  public static function isNychaAmountLinks()
  {
    $setAutoDeselect = 0;
    $bottom_url = RequestUtilities::getBottomContUrl();

    if ($bottom_url) {
      $query_string =$bottom_url;
    } else {
      $query_string =$_SERVER['HTTP_REFERER'] ?? \Drupal::request()->query->get('q');
    }
    $widget = RequestUtil::getRequestKeyValueFromURL('widget', $query_string);
    if((strpos($widget, 'wt_') !== false) || (strpos($widget, 'ytd_') !== false) || (strpos($widget, 'comm_') !== false)|| (strpos($widget, 'rec_') !== false)){
      $setAutoDeselect = 1;
    }
    return $setAutoDeselect;
  }

  /**
   * returns last paramater value from URL($_REQUEST['q'])
   *
   * @param null $url
   * @param null $param
   * @return array
   */
  public static function _getLastRequestParamValue($url = null, $param = null){
    $value = NULL;
    $skip_next = FALSE;

    if (!isset($url)) {
      //For Gridview
      $refURL = RequestUtilities::getRefUrl();
      //For Page
      $url = $refURL ?? RequestUtilities::getCurrentPageUrl();
    }

    $urlPath = \Drupal::service('path_alias.manager')->getAliasByPath('/' . ltrim($url, '/'));
    $pathParams = explode('/', trim($urlPath, '/'));

    if (count($pathParams) < 2) {
      return [];
    }

    $replacedPathParams = array();
    $slipParams = [
      'mwbe',
      'dashboard',
      'status',
      'yeartype',
      'year',
    ];

    foreach ($pathParams as $key) {
      if (in_array($key, $slipParams)) {
        $skip_next = true;
        continue;
      }
      if ($skip_next) {
        $skip_next = false;
        continue;
      }
      $replacedPathParams[] = $key;
    }

    if ($param == null) {
      $paramName = $replacedPathParams[count($replacedPathParams) - 2];
    }
    else {
      $paramName = $param;
    }

    $index = array_search($paramName, $replacedPathParams);

    if ($index != FALSE) {
      $value = Xss::filter($replacedPathParams[($index + 1)]);
    }

    $reqParams = array($paramName => $value);
    return $reqParams;
  }

  /**
   * returns first parameter value modified url
   * @param string
   * @return string|null
   */
  public static function _getnodeid($params){
    $url_params = explode (':',$params);
    $id = array_shift($url_params);
    $reset_url = implode("/",$url_params);
    \Drupal::request()->query->set('q', '/'.$reset_url);
    return $id;
  }
}
