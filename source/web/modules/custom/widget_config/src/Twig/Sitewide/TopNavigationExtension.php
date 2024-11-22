<?php

namespace Drupal\widget_config\Twig\Sitewide;

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\ContractsUtilities\ContractURLHelper;
use Drupal\checkbook_project\ContractsUtilities\ContractUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\Component\Utility\Xss;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TopNavigationExtension extends AbstractExtension
{
  /**
   * @return TwigFunction[]
   */
  public function getFunctions()
  {
    return [
      'generateTopNavigation' => new TwigFunction('generateTopNavigation', [
        $this,
        'generateTopNavigation',
      ]),
      'isTopNavigationItemActive' => new TwigFunction('isTopNavigationItemActive', [
        $this, 'isTopNavigationItemActive'
      ])
    ];
  }

  /**
   * @param $domain
   * @return bool
   */
  public function isTopNavigationItemActive($domain): bool
  {
    $current_domain = CheckbookDomain::getCurrent();
    return $current_domain == $domain;
  }

  /**
   * @param $node
   * @return array
   */
  public function generateTopNavigation($node)
  {
    $contract_amount = $node->data[0]['current_amount_sum'];
    $spending_amount = $node->data[2]['check_amount_sum'];
    $mwbeclass ='';
    $svclass ='';

    /**
     *  Set Budget, Payroll & Revenue domains to "0" and disable them if a vendor was present in the URL.
     *  This logic should only apply to the landing & transaction pages from the details links, NOT the advanced search pages.
     */
    $currentPageUrl = RequestUtilities::getCurrentPageUrl();
    $spending_advanced_search = PageType::isSpendingAdvancedSearch();
    $contracts_advanced_search = PageType::isContractsAdvancedSearch();
    $has_vendor_parameter = RequestUtilities::get('vendor')?? RequestUtilities::get('subvendor');

    $spending_link = $budget_link = $revenue_link = $payroll_link = array(
      'link' => false,
      'dollar_amount' => FormattingUtilities::custom_number_formatter_format(0, 1, '$')
    );

    // Vendor parameter does not apply for budget, payroll and revenue domains
    // check ticket for requirements NYCCHKBK-13416
    //Budget

    if ($node->data[3]['budget_current'] > 0 && !$has_vendor_parameter && !$contracts_advanced_search && !$spending_advanced_search) {
        $budget_link = array(
          'link' => RequestUtil::getTopNavURL("budget"),
          'dollar_amount' => FormattingUtilities::custom_number_formatter_format($node->data[3]['budget_current'], 1, '$')
        );
      }

      //Revenue
      if ($node->data[4]['revenue_amount_sum'] > 0 && !$has_vendor_parameter && !$contracts_advanced_search && !$spending_advanced_search) {
        $revenue_link = array(
          'link' => RequestUtil::getTopNavURL("revenue"),
          'dollar_amount' => FormattingUtilities::custom_number_formatter_format($node->data[4]['revenue_amount_sum'], 1, '$')
        );
      }

      //Payroll
      if ($node->data[1]['total_gross_pay'] > 0 && !$has_vendor_parameter && !$contracts_advanced_search && !$spending_advanced_search) {
        $payroll_link = array(
          'link' => RequestUtil::getTopNavURL("payroll"),
          'dollar_amount' => FormattingUtilities::custom_number_formatter_format($node->data[1]['total_gross_pay'], 1, '$')
        );
      }
    //}

    //Spending
    if ($spending_amount > 0) {
      $spending_link = array(
        'link' => RequestUtil::getTopNavURL("spending"),
        'dollar_amount' => FormattingUtilities::custom_number_formatter_format($spending_amount, 1, '$')
      );
    }

    $current_dashboard = RequestUtilities::get("dashboard");

    //Contracts
    if ($contract_amount > 0) {
      $contracts_link = array(
        'link' => RequestUtil::getTopNavURL("contracts"),
        'dollar_amount' => FormattingUtilities::custom_number_formatter_format($contract_amount, 1, '$')
      );
    } else {
      //Get URL if there are any contracts exists in Contracts bottom container from 363.json
      if (RequestUtilities::isEDCPage()) {
        $contracts_url = null;
      } else {
        $contracts_url = RequestUtil::getContractsBottomSliderPath();
      }

      if (isset($contracts_url)) {
        $contracts_url = RequestUtil::getTopNavURL("contracts", $contracts_url);

        $contracts_link = array(
          'link' => RequestUtil::getTopNavURL($contracts_url),
          'dollar_amount' => FormattingUtilities::custom_number_formatter_format(0, 1, '$')
        );

      } else {
        $contracts_link = array(
          'link' => false,
          'dollar_amount' => FormattingUtilities::custom_number_formatter_format(0, 1, '$')
        );
      }
    }

    // Get mwbe and subvendor links.
    $mwbe_active_domain_link = RequestUtil::getDashboardTopNavURL("mwbe");
    $svendor_active_domain_link = RequestUtil::getDashboardTopNavURL("subvendor");
    $svendor_active_domain_link = preg_replace('/\/industry\/[^\/]*/', '', $svendor_active_domain_link);

    // calculate amount for mwbe and subvendors top nav.
    if (str_contains($currentPageUrl, 'contract')) {

      /*For M/WBE and Sub Vendors dashboard, need to consider both active & registered expense contracts for highlighting.
      This will resolve the case where there is active contracts, so user should be able to click on the dashboards. */

      /* Active Contracts */
      $active_mwbe_amount = $node->data[11]['current_amount_sum'];
      $active_mwbe_subven_amount = $node->data[13]['current_amount_sum'];
      $active_subven_amount = $node->data[12]['current_amount_sum'];

      /* Registered Contracts */
      $registered_mwbe_amount = $node->data[6]['current_amount_sum'];
      $registered_mwbe_subven_amount = $node->data[10]['current_amount_sum'];
      $registered_subven_amount = $node->data[8]['current_amount_sum'];

      /* Active & Registered Contracts */
      $active_registered_mwbe_amount = $active_mwbe_amount + $registered_mwbe_amount;
      $active_registered_mwbe_subven_amount = $active_mwbe_subven_amount + $registered_mwbe_subven_amount;
      $active_registered_subven_amount = $active_subven_amount + $registered_subven_amount;

      // for prime flow include prime + sub; for sub vendor flow include sub.
      if ($current_dashboard == "mp" || $current_dashboard == "sp" || $current_dashboard == null) {
        $mwbe_amount = $registered_mwbe_amount;
        $mwbe_subven_amount = $registered_mwbe_subven_amount;

        $mwbe_amount_active_inc = $active_registered_mwbe_amount;
        $mwbe_subven_amount_active_inc = $active_registered_mwbe_subven_amount;
      } else {
        $mwbe_amount = $registered_mwbe_subven_amount;
        $mwbe_subven_amount = 0;

        $mwbe_amount_active_inc = $active_registered_mwbe_subven_amount;
        $mwbe_subven_amount_active_inc = 0;
      }

      $svendor_amount = $registered_subven_amount;

      $mwbe_prime_amount_active_inc = $active_registered_mwbe_amount;
      $svendor_amount_active_inc = $active_registered_subven_amount;

      // if prime is zero and sub amount is not zero. change dashboard to ms
      if ($mwbe_prime_amount_active_inc == 0 && $mwbe_subven_amount_active_inc > 0) {
        $mwbe_amount += $mwbe_subven_amount;
        $mwbe_amount_active_inc += $mwbe_subven_amount_active_inc;
        RequestUtil::$is_prime_mwbe_amount_zero_sub_mwbe_not_zero = true;
        $mwbe_active_domain_link = preg_replace('/\/dashboard\/../', '/dashboard/ms', $mwbe_active_domain_link);
      }

      // call function to get mwbe drop down filters.
      $mwbe_filters = MappingUtil::getCurrentMWBETopNavFilters($mwbe_active_domain_link, "contracts");

      // call function to get sub vendors drop down filters.
      $svendor_filters = MappingUtil::getCurrentSubVendorsTopNavFilters($svendor_active_domain_link, "contracts");
    } else {
      //for prime flow include prime + sub; for sub vendor flow include sub.
      if ($current_dashboard == "mp" || $current_dashboard == "sp" || $current_dashboard == null) {
        $mwbe_amount = $node->data[5]['check_amount_sum'];
        $mwbe_subven_amount = $node->data[9]['check_amount_sum'];
      } else {
        $mwbe_amount = $node->data[9]['check_amount_sum'];
        $mwbe_subven_amount = 0;
      }

      $mwbe_prime_amount = $node->data[5]['check_amount_sum'];
      // if prime is zero and sub amount is not zero. change dashboard to ms
      if ($mwbe_prime_amount == null && $mwbe_subven_amount > 0 && $has_vendor_parameter != null) {
        $mwbe_amount += $mwbe_subven_amount;
        RequestUtil::$is_prime_mwbe_amount_zero_sub_mwbe_not_zero = true;
        $mwbe_active_domain_link = preg_replace('/\/dashboard\/../', '/dashboard/ms', $mwbe_active_domain_link);
      }

      // call function to get mwbe drop down filters.
      $mwbe_filters = MappingUtil::getCurrentMWBETopNavFilters($mwbe_active_domain_link, "spending");

      // call function to get sub vendors drop down filters.
      $svendor_filters = MappingUtil::getCurrentSubVendorsTopNavFilters($svendor_active_domain_link, "spending");
      $svendor_amount = $node->data[7]['check_amount_sum'];
    }

    // tm_wbe is an exception case for total MWBE link. When prime data is not present but sub data is present for the agency vendor combination.
    if (RequestUtilities::get("tm_wbe") == 'Y') {
      $svendor_amount = $mwbe_amount;
    }

    // make amounts zero for non mwbe and indviduals and others mwbe categories.
    if (preg_match('/mwbe\/7/', $currentPageUrl) || preg_match('/mwbe\/11/', $currentPageUrl)) {
      $mwbe_amount = 0;
    }

    // dont hightlight mwbe for advanced search pages.
    if (!str_contains($currentPageUrl, 'smnid') && (preg_match('/spending\/transactions/', $currentPageUrl) || preg_match('/contract\/all\/transactions/', $currentPageUrl)
        || preg_match('/contract\/search\/transactions/', $currentPageUrl))) {
      $mwbeclass = false;
    }

    $featured_dashboard = RequestUtilities::get("dashboard");

    if ($mwbe_amount == 0 && $mwbe_amount_active_inc == 0) {
      $mwbe_link = array(
        'link' => false,
        'dollar_amount' => FormattingUtilities::custom_number_formatter_format(0, 1, '$')
      );
    } else {
      //Contracts-M/WBE(Subvendors) should be navigated to third bottom slider only when active contracts amount is zero
      if ((!isset($mwbe_amount) || $mwbe_amount == 0) && str_contains($currentPageUrl, 'contract') && !_checkbook_check_isEDCPage() && RequestUtil::getDashboardTopNavTitle("mwbe") == 'M/WBE (Sub Vendors)') {
        $mwbe_active_domain_link = ContractURLHelper::prepareSubvendorContractsSliderFilter($mwbe_active_domain_link, 'ms', ContractURLHelper::thirdBottomSliderValue());
      }

      $mwbe_link = array(
        'link' => $mwbe_active_domain_link,
        'dollar_amount' => FormattingUtilities::custom_number_formatter_format($mwbe_amount, 1, '$')
      );
    }

    if ($svendor_amount == 0 && (isset($svendor_amount_active_inc) && $svendor_amount_active_inc == 0)) {
      if (str_contains($currentPageUrl, 'contract') && !Datasource::isOGE() && ContractUtil::checkStatusOfSubVendorByPrimeCounts()) {
        $dashboard = (isset($featured_dashboard) && $featured_dashboard == 'mp') ? 'sp' : 'ss';
        $svendor_active_domain_link = ContractURLHelper::prepareSubvendorContractsSliderFilter($svendor_active_domain_link, $dashboard, ContractURLHelper::thirdBottomSliderValue());

        $subvendors_link = array(
          'link' => $svendor_active_domain_link,
          'dollar_amount' => FormattingUtilities::custom_number_formatter_format($svendor_amount, 1, '$')
        );

      } else {
        $subvendors_link = array(
          'link' => false,
          'dollar_amount' => FormattingUtilities::custom_number_formatter_format(0, 1, '$')
        );
      }

    } else {
      $subvendors_link = array(
        'link' => $svendor_active_domain_link,
        'dollar_amount' => FormattingUtilities::custom_number_formatter_format($svendor_amount, 1, '$')
      );
    }

    // conditions for making mwbe active.
    if ($featured_dashboard == "mp" || $featured_dashboard == "ms" || ($featured_dashboard != null && ($mwbe_amount > 0 || $mwbe_amount_active_inc > 0))) {
      $mwbeclass = true;
    }
    if ($featured_dashboard == "sp" || $featured_dashboard == "ss" || ($featured_dashboard != null && ($svendor_amount > 0 || $svendor_amount_active_inc > 0))) {
      $svclass = true;
    }

    $top_navigation_render_first = [
      'budget' => array(
        'label' => 'Budget',
        'dollar_amount' => $budget_link['dollar_amount'],
        'link' => $budget_link['link'],
      ),
      'revenue' => array(
        'label' => 'Revenue',
        'dollar_amount' => $revenue_link['dollar_amount'],
        'link' => $revenue_link['link'],
      ),
      'spending' => array(
        'label' => 'Spending',
        'dollar_amount' => $spending_link['dollar_amount'],
        'link' => $spending_link['link'],
        'indicator' => !$featured_dashboard
      ),
      'contracts' => array(
        'label' => 'Contracts',
        'dollar_amount' => $contracts_link['dollar_amount'],
        'link' => $contracts_link['link'],
        'indicator' => !$featured_dashboard
      ),
      'payroll' => array(
        'label' => 'Payroll',
        'dollar_amount' => $payroll_link['dollar_amount'],
        'link' => $payroll_link['link'],
      )
    ];

    $top_navigation_render_second = [
      'mwbe' => [
        'label' => RequestUtil::getDashboardTopNavTitle("mwbe"),
        'dollar_amount' => $mwbe_link['dollar_amount'],
        'link' => str_replace(["'", '"'], ['%27', '%22'], Xss::filter('/' . ltrim($mwbe_link['link'],"/"))),
        'active' => $mwbeclass,
        'menu' => _checkbook_check_isEDCPage() ? false : $mwbe_filters,
        'indicator' => $featured_dashboard == "mp" || $featured_dashboard == "ms"
      ],
      'sub_vendors' => [
        'label' => RequestUtil::getDashboardTopNavTitle("subvendor"),
        'dollar_amount' => $subvendors_link['dollar_amount'],
        'link' => str_replace(["'", '"'], ['%27', '%22'], Xss::filter('/' . ltrim($subvendors_link['link'],"/"))),
        'active' => $svclass,
        'menu' => _checkbook_check_isEDCPage() ? false : $svendor_filters,
        'indicator' => $featured_dashboard == "sp" || $featured_dashboard == "ss"
      ]
    ];

    return [
      'page_type' => (CheckbookDomain::getCurrent() == CheckbookDomain::$CONTRACTS) ? 'Contracts' : 'Spending',
      'first' => $top_navigation_render_first,
      'second' => $top_navigation_render_second
    ];
  }
}
