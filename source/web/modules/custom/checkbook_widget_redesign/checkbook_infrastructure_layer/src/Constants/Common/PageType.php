<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Common;

use Drupal\checkbook_custom_breadcrumbs\TrendPageTitle;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

abstract class PageType {

    const LANDING_PAGE = "landing_page";
    const TRANSACTION_PAGE = "transaction_page";
    const ADVANCED_SEARCH_PAGE = "advanced_search_page";
    const SMART_SEARCH_PAGE = "smart_search_page";
    const TRENDS_PAGE = "trends_page";
    const FEATURED_TRENDS_PAGE = "featured_trends_page";

  /**
   * @return string|null
   */
    public static function getCurrent(): ?string
    {
        //$urlPath = \Drupal::request()->query->get('q') ?? FALSE;
        //$ajaxPath = \Drupal::request()->server->get('HTTP_REFERER') ?? FALSE;
        $urlPath = RequestUtilities::getCurrentPageUrl();
        $ajaxPath = RequestUtilities::getAjaxPath();
        $bottomContURL = RequestUtilities::getBottomContUrl();
        $widgetId = RequestUtilities::get('widget');
        $trends = TrendPageTitle::getAllTrends();

        $pageType = null;
        if(str_contains($urlPath, "trends") || array_key_exists($widgetId, $trends)){
          return self::TRENDS_PAGE;
        }

        if(str_contains($urlPath, "featured_trends")) {
          return self::FEATURED_TRENDS_PAGE;
        }
        if(str_contains($urlPath, "smart_search")){
          return self::SMART_SEARCH_PAGE;
        }
        switch(CheckbookDomain::getCurrent()) {
            case CheckbookDomain::$SPENDING:
                /**
                 * ADVANCED_SEARCH_PAGE - spending/search/transactions
                 * TRANSACTION_PAGE - spending/transactions, contract/spending/transactions
                 * LANDING_PAGE - spending_landing
                 */
                if(preg_match('/spending\/search\/transactions/',$urlPath) && is_null($bottomContURL)) {
                    $pageType = self::ADVANCED_SEARCH_PAGE;
                } else if(preg_match('/spending\/transactions/',$urlPath) || preg_match('/spending\/transactions/',$ajaxPath) ||
                    preg_match('/contract\/spending\/transactions/',$urlPath) || preg_match('/contract\/spending\/transactions/',$ajaxPath)) {
                    $pageType = self::TRANSACTION_PAGE;
                }
                else if(str_contains($urlPath, 'spending_landing') || str_contains($ajaxPath, 'spending_landing')) {
                    $pageType = self::LANDING_PAGE;
                }
                break;

            case CheckbookDomain::$CONTRACTS:
                /**
                 * ADVANCED_SEARCH_PAGE - contract/all/transactions, contract/search/transactions
                 * TRANSACTION_PAGE - contract/transactions
                 * LANDING_PAGE - contracts_landing, contracts_revenue_landing, contracts_pending_landing, contracts_pending_exp_landing, contracts_pending_rev_landing
                 */
                if((preg_match('/contract\/all\/transactions/',$urlPath) || preg_match('/contract\/search\/transactions/',$urlPath)) && is_null($bottomContURL)) {
                    $pageType = self::ADVANCED_SEARCH_PAGE;
                }
                else if(preg_match('/contract\/transactions/',$urlPath) || preg_match('/contract\/transactions/',$ajaxPath)) {
                    $pageType = self::TRANSACTION_PAGE;
                }
                else if(str_contains($urlPath, 'contracts_landing') || str_contains($ajaxPath, 'contracts_landing') ||
                  str_contains($urlPath, 'contracts_revenue_landing') || str_contains($ajaxPath, 'contracts_revenue_landing') ||
                  str_contains($urlPath, 'contracts_pending') || str_contains($urlPath, 'contracts_pending')) {
                    $pageType = self::LANDING_PAGE;
                }
                break;

            case CheckbookDomain::$REVENUE:
              if(preg_match('/revenue\/transactions/',$urlPath) && is_null($bottomContURL)) {
                $pageType = self::ADVANCED_SEARCH_PAGE;
              }
              break;
            case CheckbookDomain::$BUDGET:
              if(preg_match('/budget\/transactions/',$urlPath) && is_null($bottomContURL)) {
              $pageType = self::ADVANCED_SEARCH_PAGE;
              }
              break;
            case CheckbookDomain::$PAYROLL:
            if(preg_match('/payroll\/search\/transactions/',$urlPath) && is_null($bottomContURL)) {
              $pageType = self::ADVANCED_SEARCH_PAGE;
            }
            break;
            case CheckbookDomain::$NYCHA_SPENDING:
            if(preg_match('/nycha_spending\/search\/transactions/',$urlPath) && is_null($bottomContURL)) {
              $pageType = self::ADVANCED_SEARCH_PAGE;
            }
            break;
            case CheckbookDomain::$NYCHA_CONTRACTS:
            if(is_null($bottomContURL) && (preg_match('/nycha_contracts\/search\/transactions/',$urlPath) || preg_match('/nycha_contracts\/all\/transactions/',$urlPath))) {
              $pageType = self::ADVANCED_SEARCH_PAGE;
            }
            elseif(str_contains($urlPath, 'nycha_contract_details') || str_contains($ajaxPath, '/nycha_contract_details/')) {
                $pageType = self::TRANSACTION_PAGE;
              }
            elseif(preg_match('/nycha_contracts\/transactions',$urlPath) || str_contains($ajaxPath,'/nycha_contracts\/transactions/')){
              $pageType = self::TRANSACTION_PAGE;
            }
            else {
              $pageType = self::LANDING_PAGE;
            }
            break;
            case CheckbookDomain::$NYCHA_BUDGET:
              if(preg_match('/nycha_budget\/search\/transactions/',$urlPath) && is_null($bottomContURL)) {
                $pageType = self::ADVANCED_SEARCH_PAGE;
              }
              break;
            case CheckbookDomain::$NYCHA_REVENUE:
              if(preg_match('/nycha_revenue\/search\/transactions/',$urlPath) && is_null($bottomContURL)) {
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
        return static::getCurrent() == self::ADVANCED_SEARCH_PAGE && CheckbookDomain::getCurrent() == CheckbookDomain::$SPENDING;
    }

  /**
   * @return bool
   */
   public static function isContractsAdvancedSearch(): bool
   {
      return static::getCurrent() == self::ADVANCED_SEARCH_PAGE && CheckbookDomain::getCurrent() == CheckbookDomain::$CONTRACTS;
   }

  /**
   * @return bool
   */
   public static function isFrontPage(): bool
   {
     $url = request_uri();
     $url_parts = parse_url($url);
     $urlPath = $url_parts['path'];
     //Get Raw URL instead of alias
     if ((\Drupal::service('path.matcher')->isFrontPage() || $urlPath == '/')) {
       return true;
     }
     return false;
   }

   /**
    * @return bool
    */
  public static function isGirdviewPopup(): bool
  {
    return (RequestUtilities::get("gridview") == 'popup');
  }
}
