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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

//namespace CheckbookProject\CustomClasses;

require_once(__DIR__ . '/constants/Constants.php');


/**
 * Class RequestUtil
 */
class RequestUtil
{

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
        $referer = $_SERVER['HTTP_REFERER'];

        foreach (self::$landing_links as $landing_link) {
            if (preg_match("/$landing_link/i", $referer)) {
                return true;
            }
        }

        return false;
    }

    /** Checks if the current URL is opened in a new window */
    public static function isNewWindow()
    {
        $referer = $_SERVER['HTTP_REFERER'];
        return preg_match('/newwindow/i', $referer);
    }

    /** Checks if the current page is Pending Expense Contratcts page
     * @param $path
     * @return bool
     */
    public static function isPendingExpenseContractPath($path)
    {
        return 0 === stripos($path, 'contracts_pending_exp_landing');
    }

    /** Checks if the current page is Pending Revenue Contratcts page
     * @param $path
     * @return bool
     */
    public static function isPendingRevenueContractPath($path)
    {
        return 0 === stripos($path, 'contracts_pending_rev_landing');
    }

    /** Checks if the current page is Active/Registered Expense Contratcs page
     * @param $path
     * @return bool
     */
    public static function isExpenseContractPath($path)
    {
        return 0 === stripos($path, 'contracts_landing');
    }

    /** Checks if the current page is Active/Registered Pending Revenue Contratcs page
     * @param $path
     * @return bool
     */
    public static function isRevenueContractPath($path)
    {
        return 0 === stripos($path, 'contracts_revenue_landing');
    }

    public static function isNYCHAContractPath($path)
    {
        return 0 === stripos($path, 'nycha_contracts');
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
            $value = filter_xss($pathParams[($index + 1)]);
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
        }
        return '';
    }

    /**
     * Returns chart title for a prime or sub vendor page based on 'category'/'featured dashboard'/'domain'
     * using values from current path.
     *
     * The page title above the visualization for Prime vendor Level and Sub vendor level pages:
     *
     * 1. Follow a three-line format when the M/WBE Category is equal to any of the following:
     *    Asian American, Black American, Women or Hispanic
     * 2. Follow a two-line format when the M/WBE Category is equal to any of the following:
     *    Individual & Others or Non M/WBE
     *
     * @param string $domain
     * @param string $defaultTitle
     * @return string
     */
    /*public static function getTitleByVendorType($domain, $defaultTitle = 'Total Spending')
    {
        if (_checkbook_check_is_mwbe_page()) {
            $minority_type_id = RequestUtilities::get('mwbe');
        } else {
            $lastReqParam = _getLastRequestParamValue();
            $minority_type_id = '';
            foreach ($lastReqParam as $key => $value) {
                switch ($key) {
                    case 'vendor':
                        $minority_type_id = self::getLatestMinorityTypeByVendorType($domain, $value, VendorType::$PRIME_VENDOR);
                        break;
                    case 'subvendor':
                        if ($value != 'all')
                            $minority_type_id = self::getLatestMinorityTypeByVendorType($domain, $value, VendorType::$SUB_VENDOR);
                        break;
                    default:
                }
            }
        }
        $minority_type_ids = explode('~', $minority_type_id);
        $minority_category = MappingUtil::getCurrenEthnicityName($minority_type_ids);
        $MWBE_certified = MappingUtil::isMWBECertified($minority_type_ids);
        $title = $MWBE_certified
            ? '<p class="sub-chart-title">M/WBE Category: ' . $minority_category . '</p>'
            : $minority_category . ' ';
        $title .= SpendingUtil::getSpendingCategoryName($defaultTitle);
        return html_entity_decode($title);
    }*/

    /**
     * returns the latest minority category by domain, vendor type and the selected year.
     *
     * @param $domain
     * @param $vendor_id
     * @param $vendor_type
     * @return mixed
     */
    /*public static function getLatestMinorityTypeByVendorType($domain, $vendor_id, $vendor_type)
    {
        $year_id = RequestUtilities::get('year');
        $type_of_year = 'B';

        if (empty($year_id)) $year_id = RequestUtilities::get('calyear');
        if (empty($type_of_year)) $type_of_year = 'B';
        if (empty($year_id)) $year_id = _getCurrentYearID();

        $data_set = "";
        switch ($domain) {
            case Domain::$SPENDING:
                $data_set = "checkbook:spending_vendor_latest_mwbe_category";
                break;
            case Domain::$CONTRACTS:
                $data_set = "checkbook:contract_vendor_latest_mwbe_category";
                break;
        }
        $parameters = array('vendor_id' => $vendor_id, "is_prime_or_sub" => $vendor_type, 'type_of_year' => $type_of_year, 'year_id' => $year_id);
        $minority_types = _checkbook_project_querydataset($data_set, array('minority_type_id'), $parameters);
        $minority_type_id = $minority_types[0]['minority_type_id'];

        return $minority_type_id;
    }*/


    /** Prepares Payroll bottom navigation filter
     * @param $page
     * @param $category
     * @return string
     */
    public static function prepareSpendingBottomNavFilter($page, $category)
    {
        $pathParams = explode('/', drupal_get_path_alias($_GET['q']));
        $url = $page;
        if (strlen($category) > 0) {
            $url .= "/category/" . $category;
        }
        $url .= _checkbook_append_url_params();
        $allowedFilters = array("year", "calyear", "agency", "yeartype", "vendor", "industry");
        for ($i = 1; $i < count($pathParams); $i++) {
            if (in_array($pathParams[$i], $allowedFilters)) {
                $url .= '/' . $pathParams[$i] . '/' . $pathParams[($i + 1)];
            }
            $i++;
        }
        return $url;

    }

    /**
     * @return string
     */
   /* public static function getRevenueNoRecordsMsg()
    {
        $title = '';
        $bottomURL = isset($_REQUEST['expandBottomContURL']) ? $_REQUEST['expandBottomContURL'] : FALSE;
        $find = '_' . $bottomURL . current_path();
        if (
            stripos($bottomURL, 'transactions')
            || stripos($find, 'agency_revenue_by_cross_year_collections_details')
            || stripos($find, 'revenue_category_revenue_by_cross_year_collections_details')
            || stripos($find, 'funding_class_revenue_by_cross_year_collections_details')
            || stripos('_' . current_path(), 'revenue_transactions')
        ) {
            $smnid = $bottomURL ? RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("smnid", current_path());
            $dtsmnid = $bottomURL ? RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("dtsmnid", current_path());
            if ($smnid > 0 || $dtsmnid > 0) {
                if ($dtsmnid > 0) {
                    $title = "There are no records to be displayed.";
                } else {
                    $bottomURL = $bottomURL ?: current_path();
                    $last_id = _getLastRequestParamValue($bottomURL);
                    if ($last_id["agency"] > 0) {
                        $title = _checkbook_project_get_name_for_argument("agency_id", RequestUtil::getRequestKeyValueFromURL("agency", $bottomURL));
                    } elseif ($last_id["revcat"] > 0) {
                        $title = _checkbook_project_get_name_for_argument("revenue_category_id", RequestUtil::getRequestKeyValueFromURL("revcat", $bottomURL));
                    } elseif (isset($last_id["fundsrccode"])) {
                        $title = _checkbook_project_get_name_for_argument("funding_class_code", RequestUtil::getRequestKeyValueFromURL("fundsrccode", $bottomURL));
                    }
                    $title = 'There are no records to be displayed for ' . $title . '.';
                }
            }
        } else {
            $title = "There are no revenue details.";
        }
        return html_entity_decode($title);
    }*/

    /** Returns top navigation URL
     * @param $domain
     * @return string
     */
    public static function getTopNavURL($domain)
    {
        $path = '';
        $fiscalYearId = static::getFiscalYearIdForTopNavigation();

        switch ($domain) {
            case "nycha_contracts":
                $path = "/nycha_contracts/datasource/".Datasource::NYCHA;
                $path .="/year/".$fiscalYearId;
                $path .= Datasource::getNYCHAUrl();
                if (RequestUtilities::get("vendor") > 0) {
                    $path .=  "/vendor/" . RequestUtilities::get("vendor");
                }
                break;
            case "contracts":
                //Get 'Contracts Bottom Slider' amounts
                $node = node_load(363);
                widget_config($node);
                widget_prepare($node);
                widget_invoke($node, 'widget_prepare');
                widget_data($node);
                //default value for landing path if all values are zero
                $contracts_landing_path = "contracts_landing/status/A";
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

                $path = $contracts_landing_path . "/yeartype/B/year/" . $fiscalYearId . _checkbook_append_url_params(null, array(), true);
                if (RequestUtilities::get("agency") > 0) {
                    $path = $path . "/agency/" . RequestUtilities::get("agency");
                } else if (_checkbook_check_isEDCPage()) {
                    $path = $path . "/agency/9000";
                }
                if (RequestUtilities::get("vendor") > 0) {
                    $path = $path . "/vendor/" . RequestUtilities::get("vendor");
                }
                break;
            case "spending":
                $path = "spending_landing/yeartype/B/year/" . $fiscalYearId . _checkbook_append_url_params(null, array(), true);
                if (RequestUtilities::get("agency") > 0) {
                    $path = $path . "/agency/" . RequestUtilities::get("agency");
                } else if (_checkbook_check_isEDCPage()) {
                    $path = $path . "/agency/9000";
                }
                if (RequestUtilities::get("vendor") > 0) {
                    $path = $path . "/vendor/" . RequestUtilities::get("vendor");
                }
                break;
            case "nycha_spending":
              $path = "/nycha_spending/datasource/".Datasource::NYCHA;
              $path .="/year/".$fiscalYearId;
              $path .= Datasource::getNYCHAUrl();
              if (RequestUtilities::get("vendor") > 0) {
                $path .=  "/vendor/" . RequestUtilities::get("vendor");
              }
            break;
            case "payroll":
                $year = static::getCalYearIdForTopNavigation();
                //Payroll is always redirected to the respective Calendar Year irrespective of the 'yeartpe' paramenter in the URL for all the other Domains
                if (!preg_match('/payroll/', request_uri())) {
                    $yeartype = 'C';
                } else {
                    $yeartype = RequestUtilities::get("yeartype");
                }

                if (preg_match('/agency_landing/', current_path())) {
                    $path = "payroll/agency_landing/yeartype/" . $yeartype . "/year/" . $year;
                    $path .= RequestUtilities::buildUrlFromParam('title');
                    $path .= RequestUtilities::buildUrlFromParam('agency');
                    $path .= RequestUtilities::buildUrlFromParam('datasource');
                } else if (preg_match('/title_landing/', current_path())) {
                    $path = "payroll/title_landing/yeartype/" . $yeartype . "/year/" . $year;
                    $path .= RequestUtilities::buildUrlFromParam('agency');
                    $path .= RequestUtilities::buildUrlFromParam('title');
                    $path .= RequestUtilities::buildUrlFromParam('datasource');
                } else {
                    $bottomURL = $_REQUEST['expandBottomContURL'];
                    $bottomURL = ($bottomURL) ? $bottomURL : current_path();
                    $last_parameter = _getLastRequestParamValue($bottomURL);
                    if ($last_parameter['agency'] > 0 ) {
                        $path = "payroll/agency_landing/yeartype/" . $yeartype . "/year/" . $year;
                        $path .= RequestUtilities::buildUrlFromParam('datasource');
                        $path .= RequestUtilities::buildUrlFromParam('title');
                        $path .= RequestUtilities::buildUrlFromParam('agency');
                    } else if ($last_parameter['title'] > 0) {
                        $path = "payroll/title_landing/yeartype/" . $yeartype . "/year/" . $year;
                        $path .= RequestUtilities::buildUrlFromParam('datasource');
                        $path .= RequestUtilities::buildUrlFromParam('agency');
                        $path .= RequestUtilities::buildUrlFromParam('title');
                    } else if(RequestUtilities::getRequestParamValue('agency')) {
                        $path = "payroll/agency_landing/yeartype/" . $yeartype . "/year/" . $year;
                        $path .= RequestUtilities::buildUrlFromParam('datasource');
                        $path .= RequestUtilities::buildUrlFromParam('title');
                        $path .= RequestUtilities::buildUrlFromParam('agency');
                    }
                        else{
                        //Nyc Level
                            $path = "payroll/yeartype/" . $yeartype . "/year/" . $year . RequestUtilities::buildUrlFromParam('datasource');
                    }
                }
                break;
            case "budget":
                if (RequestUtilities::get("agency") > 0) {
                    $path = "budget/yeartype/B/year/" . $fiscalYearId . RequestUtilities::buildUrlFromParam('agency');
                } else {
                    $path = "budget/yeartype/B/year/" . $fiscalYearId;
                }
                break;
            case "revenue":
                if (RequestUtilities::get("agency") > 0) {
                    $path = "revenue/yeartype/B/year/" . $fiscalYearId . RequestUtilities::buildUrlFromParam('agency');
                } else {
                    $path = "revenue/yeartype/B/year/" . $fiscalYearId;
                }
                break;
        }

        if(RequestUtilities::get("vendor") > 0 && in_array($domain, array('contracts','spending'))){
            $non_minority_type_ids = array(7, 11);
            $vendor_minority_type_ids = VendorService::getAllVendorMinorityTypesByYear($domain, RequestUtilities::get("vendor"), $fiscalYearId);
            $vendor_non_minority_type_ids = array_intersect($non_minority_type_ids ,$vendor_minority_type_ids);

            if(count($vendor_non_minority_type_ids) > 0){
                $path = preg_replace('/\/dashboard\/[^\/]*/','',$path);
                $path = preg_replace('/\/mwbe\/[^\/]*/','',$path);
            }else{
                if(!stripos(' '.$path,'/mwbe/')) {
                  $path .= RequestUtilities::buildUrlFromParam('mwbe');
                }

              if(!stripos(' '.$path,'/dashboard/')) {
                $path .= RequestUtilities::buildUrlFromParam('dashboard');
              }
            }
        }
        return $path;
    }

    /** Returns Year ID for Spending, Contracts, Budget and Revenue domains navigation URLs from Top Navigation
     * @return integer $fiscalYearId
     */
    public static function getFiscalYearIdForTopNavigation()
    {
        $year = RequestUtilities::get("year|calyear");
        if (!$year) {
            $year = _getCurrentYearID();
        }

        //For CY 2010 Payroll selection, other domains should be navigated to FY 2011
        $fiscalYearId = ($year == 111 && strtoupper(RequestUtilities::get("yeartype")) == 'C') ? 112 : $year;
        return $fiscalYearId;
    }

    /** Returns Year ID for Payroll domain navigation URLs from Top Navigation
     * @return integer $calYearId
     */
    public static function getCalYearIdForTopNavigation()
    {
        $year = null;
        if (RequestUtilities::get("year") != NULL) {
            $year = RequestUtilities::get("year");
        } else if (RequestUtilities::get("calyear") != NULL) {
            $year = RequestUtilities::get("calyear");
        }
        $currentCalYear = _getCurrentCalendarYearID();
        if (is_null($year) || $year > $currentCalYear) {
            $year = $currentCalYear;
        }
        return $year;
    }

    /** Checks if the current page is NYC level*/
    public static function isNYCLevelPage()
    {
        self::isEDCPage();
        $landingPages = array("contracts_landing", "contracts_revenue_landing",
            "contracts_pending_rev_landing", "contracts_pending_exp_landing",
            "spending_landing", "payroll", "budget", "revenue");

        $url = $_GET['q'];
        $urlPath = drupal_get_path_alias($url);
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
            $vendor = _checkbook_project_querydataset("checkbook:vendor", "vendor_customer_code", array("vendor_id" => $vendor_id));

            if ($vendor[0]['vendor_customer_code'] == "0000776804") {
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
        $vendor = _checkbook_project_querydataset("checkbook:vendor", "vendor_id", array("vendor_customer_code" => "0000776804"));
        $url = "contracts_landing/status/A/yeartype/B/year/" . _getCurrentYearID() . "/vendor/" . $vendor[0]['vendor_id'];
        return $url;
    }

    /**
     * @return string
     */
    public static function getSpendingEDCURL()
    {
        $vendor = _checkbook_project_querydataset("checkbook:vendor", "vendor_id", array("vendor_customer_code" => "0000776804"));
        $url = "spending_landing/yeartype/B/year/" . _getCurrentYearID() . "/vendor/" . $vendor[0]['vendor_id'];
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
            if (stripos('_' . current_path(), 'contracts')) {
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
                break;
            case "ms" :
            case "ss" :
                return "ms";
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
                break;
            case "ms" :
            case "ss" :
                return "ss";
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
            $url = $_GET['q'];

            //Exclude parameters that should not persist in the feature dashboards for Spending Domain
            if (preg_match('/contract/', $url)) {
                $url = ContractUtil::getLandingPageWidgetUrl();
            } else {
                //Default to total spending
                $override_params = array("category" => null);
                $url = SpendingUtil::getLandingPageWidgetUrl($override_params);
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
                if (!preg_match('/mwbe/', $url)) {
                    if (RequestUtilities::get("mwbe") != null) {
                        $url .= "/mwbe/" . RequestUtilities::get("mwbe");
                    } else {
                        $url .= "/mwbe/2~3~4~5~9";
                    }
                }
                break;
            case "subvendor":
                if (RequestUtilities::get("dashboard") != null) {
                    $url = preg_replace('/\/dashboard\/[^\/]*/', '', $url);
                }
                // tm_wbe is an exception case for total MWBE link. When prime data is not present but sub data is present for the agency vendor combination.
                if (RequestUtilities::get("dashboard") == 'ms' && RequestUtilities::get("mwbe") == '2~3~4~5~9' && RequestUtilities::get("tm_wbe") != 'Y') {
                    $url = preg_replace('/\/mwbe\/[^\/]*/', '', $url);
                }

                $url .= "/dashboard/" . self::getNextSubvendorDashboardStateParam();
                break;
        }

        //For MWBE and Sub Vendor dashboard links add status parameters if it is not there
        //If status parameter is existing, set it to 'Active' always
        //Do this only for contracts
        if (preg_match('/contracts/', $url)) {
            if (!preg_match('/status/', $url)) {
                $url .= "/status/A";
            } else {
                $url = preg_replace('/\/status\/[^\/]*/', '/status/A', $url);
            }
        }

        return $url;
    }

    /**
     * @return bool
     */
    public static function isContractsSpendingLandingPage()
    {
        $first_part = preg_replace('/\/.*/', '', $_GET['q']);
        if (in_array($first_part, self::$contracts_spending_landing_links)) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public static function getCurrentDomainURLFromParams()
    {
        if (stripos('_'.current_path(), 'contract')) {

            $reqParams = MappingUtil::$contractsMWBEParamMap;
            $prefix = 'contracts_landing';
        } else {
            $reqParams = MappingUtil::$spendingMWBEParamMap;
            $prefix = 'spending_landing';
        }

        foreach ($reqParams as $key => $value) {
            $value = RequestUtilities::get($key);
            if ($key == "year") {
                $value =  static::getFiscalYearIdForTopNavigation();
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
        if (stripos('_'.current_path(), 'contract')) {
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
        $year = $year ?: _getCurrentYearID();
        $yearType = $yearType ?: 'B';

        switch ($domain) {
            case "contracts":
                $path = "contracts_landing/status/A/yeartype/B/year/" . $year;
                break;
            case "spending":
                $path = "spending_landing/yeartype/B/year/" . $year;
                break;
            case "payroll":
                $path = "payroll/yeartype/" . $yearType . "/year/" . $year;
                break;
            case "budget":
                $path = "budget/yeartype/B/year/" . $year;
                break;
            case "revenue":
                $path = "revenue/yeartype/B/year/" . $year;
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
        $cacheKey = '_top_nav_count_'.md5($sql);
        $count = _checkbook_dmemcache_get($cacheKey);
        if (is_numeric($count)) {
          return $count;
        }
        $data = _checkbook_project_execute_sql($sql);
        $count = $data[0]['count'];
        if( is_numeric($count)) {
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

        if (stripos('_'.current_path(), 'contract')) {
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
                $default_params = array("minority_type_id" => "2~3~4~5~9", 'type_of_year' => 'B');
                break;
            case "contracts":
                $table = "aggregateon_mwbe_contracts_cumulative_spending";
                $table_subven = "aggregateon_subven_contracts_cumulative_spending";
                $urlParamMap = array("year" => "fiscal_year_id", "agency" => "agency_id", "vendor" => "vendor_id");
                $urlParamMapSubven = array("year" => "fiscal_year_id", "agency" => "agency_id", "vendor" => "prime_vendor_id");
                $default_params = array("status_flag" => "A", "minority_type_id" => "2~3~4~5~9", 'type_of_year' => 'B');
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
        return '/' . RequestUtil::getLandingPageUrl($domain, static::getFiscalYearIdForTopNavigation(), 'B') . "/mwbe/" . MappingUtil::$total_mwbe_cats
            . "/dashboard/" . $dashboard .
            RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor');
    }

    /**
     * Function will derive whether this is an advanced search transaction page based on the query string
     * @return bool|int
     */
    public static function isAdvancedSearchPage()
    {
        $bottomURL = $_REQUEST['expandBottomContURL'];
        $domain = self::getDomain();
        $currentPath = current_path();
        $isAdvancedSearch = !$bottomURL || !isset($domain);

        if ($isAdvancedSearch) {
            switch ($domain) {
                case 'budget':
                    $isAdvancedSearch = preg_match('/^budget\/transactions/', $currentPath);
                    break;
                case 'revenue':
                    $isAdvancedSearch = preg_match('/^revenue\/transactions/', $currentPath);
                    break;
                case 'spending':
                    $isAdvancedSearch = !preg_match('/smnid/', $currentPath) &&
                        (preg_match('/spending\/search\/transactions/', $currentPath));
                    break;
                case 'contract':
                    $isAdvancedSearch = !preg_match('/smnid/', $currentPath) &&
                        (preg_match('/contract\/all\/transactions/', $currentPath)
                            || preg_match('/contract\/search\/transactions/', $currentPath));
                    break;
                case 'payroll':
                    $isAdvancedSearch = preg_match('/^payroll\/search\/transactions/', $currentPath);
                    break;
            }
        }

        return $isAdvancedSearch;
    }

    /**
     * @return null|string
     */
    public static function getDomain()
    {
        $currentPath = current_path();
        switch(true){
            case (0 === stripos($currentPath, 'budget')):   return 'budget';
            case (0 === stripos($currentPath, 'revenue')):   return 'revenue';
            case (0 === stripos($currentPath, 'spending')):   return 'spending';
            case (0 === stripos($currentPath, 'contract')):   return 'contract';
            case (0 === stripos($currentPath, 'payroll')):   return 'payroll';
        }
        return null;
    }

    public static function getCurrentPageUrl()
    {
      $url = $_GET['q'];
      if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // that's AJAX
        $url = '//' . $_SERVER['HTTP_REFERER'];
      }
      return $url;
    }
}

