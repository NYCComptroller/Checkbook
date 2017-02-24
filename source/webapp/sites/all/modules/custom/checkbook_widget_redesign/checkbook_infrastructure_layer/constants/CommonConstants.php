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

    static public function getCurrent() {
        $urlPath = $_GET['q'];
        $ajaxPath = $_SERVER['HTTP_REFERER'];

        $domain = null;
        if(preg_match('/contracts_landing/',$urlPath) || preg_match('/contracts_landing/',$ajaxPath) ||
            preg_match('/contracts_revenue_landing/',$urlPath) || preg_match('/contracts_revenue_landing/',$ajaxPath) ||
            preg_match('/contracts_pending/',$urlPath) || preg_match('/contracts_pending/',$urlPath)) {
            $domain = self::CONTRACTS;
        }
        else if(preg_match('/spending_landing/',$urlPath) || preg_match('/spending_landing/',$ajaxPath)) {
            $domain = self::SPENDING;
        }
        else if(preg_match('/revenue/',$urlPath) || preg_match('/revenue/',$ajaxPath)) {
            $domain = self::REVENUE;
        }
        else if(preg_match('/budget/',$urlPath) || preg_match('/budget/',$ajaxPath)) {
            $domain = self::BUDGET;
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