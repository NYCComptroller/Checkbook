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
    const SMART_SEARCH = "smart_search";
}

abstract class CheckbookDomain {

    const SPENDING = "spending";
    const CONTRACTS = "contracts";
    const REVENUE = "revenue";
    const BUDGET = "budget";
    const PAYROLL = "payroll";
    const NYCHA_CONTRACTS = "nycha_contracts";
    const NYCHA_SPENDING = "nycha_spending";
    const NYCHA_BUDGET = "nycha_budget";
    const NYCHA_REVENUE = "nycha_revenue";

  /**
   * @return string|null
   */
    public static function getCurrent(): ?string
    {
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

        if (!$domain && stripos($urlPath, '/nycha_contracts/')) {
          $domain = self::NYCHA_CONTRACTS;
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

        if (!$domain && stripos($urlPath, '/nycha_spending/')) {
          $domain = self::NYCHA_SPENDING;
        }

        if (!$domain && stripos($urlPath, '/budget/')) {
          $domain = self::BUDGET;
        }

        if (!$domain && stripos($urlPath, '/nycha_budget/')) {
          $domain = self::NYCHA_BUDGET;
        }

        if (!$domain && stripos($urlPath, '/revenue/')) {
            $domain = self::REVENUE;
        }

        if (!$domain && stripos($urlPath, '/nycha_revenue/')) {
          $domain = self::NYCHA_REVENUE;
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
    const NYCHA = "checkbook_nycha";
    const SOLR_CITYWIDE = 'citywide';
    const SOLR_EDC = 'edc';
    const SOLR_NYCHA = 'nycha';
    const EDC_TITLE = "NYCEDC";

  /**
   * @return string
   */
     public static function getCurrent(): string
     {
        $datasource = RequestUtilities::get(UrlParameter::DATASOURCE);
        switch($datasource) {
            case self::OGE: return self::OGE;
            case self::NYCHA: return self::NYCHA;
            default: return self::CITYWIDE;
        }
    }

  /**
   * @return bool
   */
    public static function isOGE(): bool
    {
        return self::getCurrent() == self::OGE;
    }

  /**
   * @return bool
   */
    public static function isNYCHA(): bool
    {
        return (self::getCurrent() == self::NYCHA || self::smartSearchDataSource() == self::NYCHA);
    }

  /**
   * @return string
   */
    public static function getNYCHAUrl(): string
    {
        $nychaId = _checkbook_project_querydataset('checkbook_nycha:agency', array('agency_id'), array('agency_short_name' => 'HOUSING AUTH'));
        $path =  (self::getCurrent() == self::NYCHA) ? '/agency/' . $nychaId[0]['agency_id'] : '';
        return $path;
    }

  /**
   * @return mixed
   */
    public static function getNYCHAId() {
        $nychaId = _checkbook_project_querydataset('checkbook_nycha:agency', array('agency_id'), array('agency_short_name' => 'HOUSING AUTH'));
        $agency_id= $nychaId[0]['agency_id'];
        return $agency_id;
    }

  /**
   * @return mixed
   */
    public static function getEDCId() {
      $edcId = _checkbook_project_querydataset('checkbook_oge:agency', array('agency_id'), array('agency_short_name' => 'NYC EDC'));
      return $edcId[0]['agency_id'];
    }

  /**
   * @return mixed
   */
    public static function getEDCCode() {
      $edcCode = _checkbook_project_querydataset('checkbook_oge:agency', array('agency_code'), array('agency_short_name' => 'NYC EDC'));
      return $edcCode[0]['agency_code'];
    }

  /**
   * @return string
   */
    public static function smartSearchDataSource(): string
    {
      $solr_datasource = _checkbook_get_datasource();
      switch($solr_datasource) {
        case self::SOLR_EDC: return self::OGE;
        case self::SOLR_NYCHA: return self::NYCHA;
        default: return self::CITYWIDE;
      }
    }
}

abstract class Dashboard {
    const CITYWIDE = "citywide";
    const OGE = "oge";
    const NYCHA = "nycha";
    const SUB_VENDORS = "sub_vendors";
    const SUB_VENDORS_MWBE = "sub_vendors_mwbe";
    const MWBE_SUB_VENDORS = "mwbe_sub_vendors";
    const MWBE = "mwbe";
    const CURRENT = "current_year";
    const PREVIOUS = "previous_year";

  /**
   * @return string
   */
     public static function getCurrent(): string
     {
        $domain = CheckbookDomain::getCurrent();
        $year = RequestUtilities::get(UrlParameter::YEAR);

        if($domain == CheckbookDomain::REVENUE){
            if($year >= RequestUtilities::getCurrentYearID()) {
              return self::CURRENT;
            }else {
              return self::PREVIOUS;
            }
        }else{
            $dashboard = DashboardParameter::getCurrent();
            switch($dashboard) {
                case DashboardParameter::SUB_VENDORS: return self::SUB_VENDORS;
                case DashboardParameter::SUB_VENDORS_MWBE: return self::SUB_VENDORS_MWBE;
                case DashboardParameter::MWBE_SUB_VENDORS: return self::MWBE_SUB_VENDORS;
                case DashboardParameter::MWBE: return self::MWBE;
                default:
                    if(Datasource::isOGE()) {
                      return self::OGE;
                    }else if(Datasource::isNYCHA()) {
                      return self::NYCHA;
                    }else {
                      return self::CITYWIDE;
                    }
            }
        }
     }

  /**
   * @return bool
   */
    public static function isOGE(): bool
    {
        return self::getCurrent() == self::OGE;
    }

  /**
   * @return bool
   */
    public static function isNYCHA(): bool
    {
        return self::getCurrent() == self::NYCHA;
    }

  /**
   * @return bool
   */
     public static function isMWBE(): bool
     {
        $dashboard = self::getCurrent();
        return $dashboard == self::MWBE || $dashboard == self::SUB_VENDORS_MWBE || $dashboard == self::MWBE_SUB_VENDORS;
     }

  /**
   * @return bool
   */
     public static function isSubDashboard(): bool
     {
        $dashboard = self::getCurrent();
        return $dashboard == self::SUB_VENDORS || $dashboard == self::SUB_VENDORS_MWBE || $dashboard == self::MWBE_SUB_VENDORS;
     }

  /**
   * @return bool
   */
      public static function isPrimeDashboard(): bool
      {
        $dashboard = self::getCurrent();
        return $dashboard == self::MWBE || $dashboard == self::CITYWIDE || $dashboard == self::OGE || $dashboard == self::NYCHA;
      }
}

abstract class DashboardParameter {

    const MWBE = "mp";
    const SUB_VENDORS = "ss";
    const SUB_VENDORS_MWBE = "sp";
    const MWBE_SUB_VENDORS = "ms";

  /**
   * @return array|string|null
   */
     public static function getCurrent() {
        return RequestUtilities::get(UrlParameter::DASHBOARD);
    }
}

abstract class PageType {

    const LANDING_PAGE = "landing_page";
    const TRANSACTION_PAGE = "transaction_page";
    const ADVANCED_SEARCH_PAGE = "advanced_search_page";
    const SMART_SEARCH_PAGE = "smart_search_page";
    const TRENDS_PAGE = "trends_page";

  /**
   * @return string|null
   */
    public static function getCurrent(): ?string
    {
        $urlPath = $_GET['q'] ?? FALSE;
        $ajaxPath = $_SERVER['HTTP_REFERER'] ?? FALSE;
        $pageType = null;
        if(preg_match("/trends/", $urlPath) || preg_match("/featured_trends/", $urlPath)){
          return self::TRENDS_PAGE;
        }
        if(preg_match("/smart_search/", $urlPath)){
          return self::SMART_SEARCH_PAGE;
        }
        switch(CheckbookDomain::getCurrent()) {
            case CheckbookDomain::SPENDING:
                /**
                 * ADVANCED_SEARCH_PAGE - spending/search/transactions
                 * TRANSACTION_PAGE - spending/transactions, contract/spending/transactions
                 * LANDING_PAGE - spending_landing
                 */
                if(preg_match('/spending\/search\/transactions/',$urlPath)) {
                    $pageType = self::ADVANCED_SEARCH_PAGE;
                } else if(preg_match('/spending\/transactions/',$urlPath) || preg_match('/spending\/transactions/',$ajaxPath) ||
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
                if(preg_match('/contract\/all\/transactions/',$urlPath) || preg_match('/contract\/search\/transactions/',$urlPath) ) {
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
              if(preg_match('/revenue\/search\/transactions/',$urlPath)) {
                $pageType = self::ADVANCED_SEARCH_PAGE;
              }
              break;
            case CheckbookDomain::BUDGET:
              if(preg_match('/budget\/search\/transactions/',$urlPath)) {
              $pageType = self::ADVANCED_SEARCH_PAGE;
              }
              break;
            case CheckbookDomain::PAYROLL:
            if(preg_match('/payroll\/search\/transactions/',$urlPath)) {
              $pageType = self::ADVANCED_SEARCH_PAGE;
            }
            break;
            case CheckbookDomain::NYCHA_SPENDING:
            if(preg_match('/nycha_spending\/search\/transactions/',$urlPath)) {
              $pageType = self::ADVANCED_SEARCH_PAGE;
            }
            break;
            case CheckbookDomain::NYCHA_CONTRACTS:
            if(preg_match('/nycha_contracts\/search\/transactions/',$urlPath) || preg_match('/nycha_contracts\/all\/transactions/',$urlPath)) {
              $pageType = self::ADVANCED_SEARCH_PAGE;
            }
            break;
            case CheckbookDomain::NYCHA_BUDGET:
              if(preg_match('/nycha_budget\/search\/transactions/',$urlPath)) {
                $pageType = self::ADVANCED_SEARCH_PAGE;
              }
              break;
            case CheckbookDomain::NYCHA_REVENUE:
              if(preg_match('/nycha_revenue\/search\/transactions/',$urlPath)) {
                $pageType = self::ADVANCED_SEARCH_PAGE;
              }
              break;
        }
        return $pageType;
    }

  /**
   * @return bool
   */
    public static function isSpendingAdvancedSearch(): bool
    {
        return static::getCurrent() == self::ADVANCED_SEARCH_PAGE && CheckbookDomain::getCurrent() == CheckbookDomain::SPENDING;
    }

  /**
   * @return bool
   */
     public static function isContractsAdvancedSearch(): bool
     {
        return static::getCurrent() == self::ADVANCED_SEARCH_PAGE && CheckbookDomain::getCurrent() == CheckbookDomain::CONTRACTS;
     }
}
