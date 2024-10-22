<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Common;

abstract class CheckbookDomain {

  public static $BUDGET = 'budget';
  public static $REVENUE = 'revenue';
  public static $SPENDING = 'spending';
  public static $CONTRACTS = 'contracts';
  public static $PAYROLL = 'payroll';
  public static $NYCHA_BUDGET = 'nycha_budget';
  public static $NYCHA_REVENUE = 'nycha_revenue';
  public static $NYCHA_SPENDING = 'nycha_spending';
  public static $NYCHA_CONTRACTS = 'nycha_contracts';

  /**
   * @return string|null
   */
    public static function getCurrent(): ?string {
        //$urlPath = '//' . \Drupal::request()->query->get('q');
        //@TODO: Replace current page path and Ajax with common functions
        $urlPath = '//' . \Drupal::service('path.current')->getPath();
        if (!empty(\Drupal::request()->server->get('HTTP_X_REQUESTED_WITH')) && strtolower(\Drupal::request()->server->get('HTTP_X_REQUESTED_WITH') == 'xmlhttprequest')) {
            // that's AJAX
            $urlPath = '//' . \Drupal::request()->server->get('HTTP_REFERER');
        }
        $domain = null;
        $contracts_endpoints = array(
            '/contracts_landing/',
            '/contracts_revenue_landing/',
            '/contracts_pending/',
            '/contracts_pending_exp_landing/',
            '/contracts_pending_rev_landing/',
            '/contract/all/transactions/',
            '/contract/search/transactions/',
        );
        foreach ($contracts_endpoints as $endpoint) {
            if (stripos($urlPath, $endpoint)) {
                $domain = self::$CONTRACTS;
            }
        }

        if (!$domain && stripos($urlPath, '/nycha_contracts/')) {
          $domain = self::$NYCHA_CONTRACTS;
        }

        if (!$domain) {
            $spending_endpoints = array(
                '/spending_landing/',
                '/spending/transactions/',
                '/spending/search/transactions/',
            );
            foreach ($spending_endpoints as $endpoint) {
                if (stripos($urlPath, $endpoint) || $urlPath == '/') {
                    $domain = self::$SPENDING;
                }
            }
        }

        if (!$domain && stripos($urlPath, '/nycha_spending/')) {
          $domain = self::$NYCHA_SPENDING;
        }

        if (!$domain && stripos($urlPath, '/budget/')) {
          $domain = self::$BUDGET;
        }

        if (!$domain && stripos($urlPath, '/nycha_budget/')) {
          $domain = self::$NYCHA_BUDGET;
        }

        if (!$domain && stripos($urlPath, '/revenue/')) {
          $domain = self::$REVENUE;
        }

        if (!$domain && stripos($urlPath, '/nycha_revenue/')) {
          $domain = self::$NYCHA_REVENUE;
        }

        if (!$domain && stripos($urlPath, '/payroll/')) {
            $domain = self::$PAYROLL;
        }

        return $domain;
    }

}
