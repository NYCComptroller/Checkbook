<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 10/19/16
 * Time: 11:00 AM
 */

abstract class UrlParameter {

    const DATASOURCE = "datasource";
    const DASHBOARD = "dashboard";
    const YEAR = "year";
    const YEAR_TYPE = "yeartype";
    const MWBE = "mwbe";
    const PRIME_MWBE = "pmwbe";
    const SUB_MWBE = "mwbe";
    const AGENCY = "agency";
    const VENDOR = "vendor";
    const SUB_VENDOR = "subvendor";
    const AWARD_METHOD = "awdmethod";
    const CONTRACT_SIZE = "csize";
    const CONTRACT_INDUSTRY = "cindustry";
    const CONTRACT_STATUS = "status";
    const SPENDING_CATEGORY = "category";
}

abstract class CheckbookDomain {

    const SPENDING = "spending";
    const CONTRACTS = "contracts";
    const REVENUE = "revenue";
    const BUDGET = "budget";
    const PAYROLL = "payroll";

    static public function getCurrent() {

        $urlPath = '//' . $_GET['q'];

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // that's AJAX
            $urlPath = '//' . $_SERVER['HTTP_REFERER'];
        }

        $domain = null;
        $contracts_endpoints = array(
            '/contracts_landing/',
            '/contracts_revenue_landing/',
            '/contracts_pending/',
            '/contracts_pending_exp_landing/',
            '/contracts_pending_rev_landing/',
            '/contract\/all\/transactions/',
            '/contract\/search\/transactions/',
        );
        foreach ($contracts_endpoints as $endpoint) {
            if (stripos($urlPath, $endpoint)) {
                $domain = self::CONTRACTS;
            }
        }

        if (!$domain) {
            $spending_endpoints = array(
                '/spending_landing/',
                '/spending\/transactions/',
            );
            foreach ($spending_endpoints as $endpoint) {
                if (stripos($urlPath, $endpoint)) {
                    $domain = self::SPENDING;
                }
            }
        }

        if (!$domain && stripos($urlPath, '/revenue/')) {
            $domain = self::REVENUE;
        }

        if (!$domain && stripos($urlPath, '/budget/')) {
            $domain = self::BUDGET;
        }

        if (!$domain && stripos($urlPath, '/payroll/')) {
            $domain = self::PAYROLL;
        }

        return $domain;
    }
}

abstract class Datasource {

    const CITYWIDE = "checkbook";
    const OGE = "checkbook_oge";

    static public function getCurrent() {
        $datasource = RequestUtilities::getRequestParamValue(UrlParameter::DATASOURCE);
        switch($datasource) {
            case self::OGE: return self::OGE;
            default: return self::CITYWIDE;
        }
    }

    static public function isOGE() {
        return self::getCurrent() == Datasource::OGE;
    }
}

abstract class Dashboard {

    const CITYWIDE = "citywide";
    const OGE = "oge";
    const SUB_VENDORS = "sub_vendors";
    const SUB_VENDORS_MWBE = "sub_vendors_mwbe";
    const MWBE_SUB_VENDORS = "mwbe_sub_vendors";
    const MWBE = "mwbe";
    const CURRENT = "current_year";
    const PREVIOUS = "previous_year";

    static public function getCurrent() {
        $domain = CheckbookDomain::getCurrent();
        $year = RequestUtilities::getRequestParamValue(UrlParameter::YEAR);

        if($domain == CheckbookDomain::REVENUE){
            if($year >= RequestUtilities::getCurrentYearID())
                return self::CURRENT;
            else
                return self::PREVIOUS;
        }else{
            $dashboard = DashboardParameter::getCurrent();
            switch($dashboard) {
                case DashboardParameter::SUB_VENDORS: return self::SUB_VENDORS;
                case DashboardParameter::SUB_VENDORS_MWBE: return self::SUB_VENDORS_MWBE;
                case DashboardParameter::MWBE_SUB_VENDORS: return self::MWBE_SUB_VENDORS;
                case DashboardParameter::MWBE: return self::MWBE;
                default: return Datasource::isOGE() ? self::OGE : self::CITYWIDE;
            }
        }
    }

    static public function isOGE() {
        return self::getCurrent() == self::OGE;
    }

    static public function isMWBE() {
        $dashboard = self::getCurrent();
        return $dashboard == self::MWBE || $dashboard == self::SUB_VENDORS_MWBE || $dashboard == self::MWBE_SUB_VENDORS;
    }

    static public function isSubDashboard() {
        $dashboard = self::getCurrent();
        return $dashboard == self::SUB_VENDORS || $dashboard == self::SUB_VENDORS_MWBE || $dashboard == self::MWBE_SUB_VENDORS;
    }

    static public function isPrimeDashboard() {
        $dashboard = self::getCurrent();
        return $dashboard == self::MWBE || $dashboard == self::CITYWIDE || $dashboard == self::OGE;
    }
}

abstract class DashboardParameter {

    const MWBE = "mp";
    const SUB_VENDORS = "ss";
    const SUB_VENDORS_MWBE = "sp";
    const MWBE_SUB_VENDORS = "ms";

    static public function getCurrent() {
        return RequestUtilities::getRequestParamValue(UrlParameter::DASHBOARD);
    }
}

abstract class PageType {

    const LANDING_PAGE = "landing_page";
    const TRANSACTION_PAGE = "transaction_page";
    const ADVANCED_SEARCH_PAGE = "advanced_search_page";

    static public function getCurrent() {
        $urlPath = $_GET['q'];
        $ajaxPath = $_SERVER['HTTP_REFERER'];

        $pageType = null;
        switch(CheckbookDomain::getCurrent()) {

            case CheckbookDomain::SPENDING:
                /**
                 * ADVANCED_SEARCH_PAGE - spending/search/transactions
                 * TRANSACTION_PAGE - spending/transactions, contract/spending/transactions
                 * LANDING_PAGE - spending_landing
                 */
                if(preg_match('/spending\/search\/transactions/',$urlPath) || preg_match('/spending\/search\/transactions/',$ajaxPath)) {
                    $pageType = self::ADVANCED_SEARCH_PAGE;
                }
                else if(preg_match('/spending\/transactions/',$urlPath) || preg_match('/spending\/transactions/',$ajaxPath) ||
                    preg_match('/contract\/spending\/transactions/',$urlPath) || preg_match('/contract\/spending\/transactions/',$ajaxPath)) {
                    $pageType = self::TRANSACTION_PAGE;
                }
                else if(preg_match('/spending_landing/',$urlPath) || preg_match('/spending_landing/',$ajaxPath)) {
                    $pageType = self::LANDING_PAGE;
                }
                break;

            case CheckbookDomain::CONTRACTS:
                /**
                 * ADVANCED_SEARCH_PAGE - contract/all/transactions, contract/search/transactions
                 * TRANSACTION_PAGE - contract/transactions
                 * LANDING_PAGE - contracts_landing, contracts_revenue_landing, contracts_pending_landing, contracts_pending_exp_landing, contracts_pending_rev_landing
                 */
                if(preg_match('/contract\/all\/transactions/',$urlPath) || preg_match('/contract\/all\/transactions/',$ajaxPath) ||
                    preg_match('/contract\/search\/transactions/',$urlPath) || preg_match('/contract\/search\/transactions/',$ajaxPath)) {
                    $pageType = self::ADVANCED_SEARCH_PAGE;
                }
                else if(preg_match('/contract\/transactions/',$urlPath) || preg_match('/contract\/transactions/',$ajaxPath)) {
                    $pageType = self::TRANSACTION_PAGE;
                }
                else if(preg_match('/contracts_landing/',$urlPath) || preg_match('/contracts_landing/',$ajaxPath) ||
                    preg_match('/contracts_revenue_landing/',$urlPath) || preg_match('/contracts_revenue_landing/',$ajaxPath) ||
                    preg_match('/contracts_pending/',$urlPath) || preg_match('/contracts_pending/',$urlPath)) {
                    $pageType = self::LANDING_PAGE;
                }
                break;

            case CheckbookDomain::REVENUE:
            case CheckbookDomain::BUDGET:
            case CheckbookDomain::PAYROLL:
                break;
        }
        return $pageType;
    }

    static public function isSpendingAdvancedSearch() {
        return self::getCurrent() == self::ADVANCED_SEARCH_PAGE && CheckbookDomain::getCurrent() == CheckbookDomain::SPENDING;
    }

    static public function isContractsAdvancedSearch() {
        return self::getCurrent() == self::ADVANCED_SEARCH_PAGE && CheckbookDomain::getCurrent() == CheckbookDomain::CONTRACTS;
    }
}