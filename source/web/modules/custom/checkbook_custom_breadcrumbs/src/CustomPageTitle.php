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

namespace Drupal\checkbook_custom_breadcrumbs;

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

class CustomPageTitle {

  /**
   * The page title.
   *
   * @var string|NULL
   */
  protected static ?string $title;

  /**
   * @return string
   */
  public static function getCustomPageTitle(): ?string
  {
    if (!isset(self::$title)) {
      $domain = CheckbookDomain::getCurrent();
      $pageType = PageType::getCurrent();
      if ($pageType == PageType::ADVANCED_SEARCH_PAGE) {
        //Advanced search titles
        self::$title = match ($domain) {
          CheckbookDomain::$BUDGET => BudgetBreadcrumbs::advancedSearchTitle(),
          CheckbookDomain::$NYCHA_BUDGET => BudgetBreadcrumbs::nychaAdvancedSearchTitle(),
          CheckbookDomain::$REVENUE => RevenueBreacrumbs::advancedSearchTitle(),
          CheckbookDomain::$NYCHA_REVENUE => RevenueBreacrumbs::nychaAdvancedSearchTitle(),
          CheckbookDomain::$SPENDING => SpendingBreadcrumbs::getSpendingTransactionTitle(),
          default => "",
        };
      }else if($pageType == PageType::TRENDS_PAGE){
        $data = TrendPageTitle::getBreadCrumbTitle();
        $widgetId = RequestUtilities::get('widget');
        if (preg_match('/^\/trends-landing\/trends\/node\//', RequestUtilities::getCurrentPageUrl())) {
          $widgetId = RequestUtilities::get('node');
        }
        self::$title = $widgetId ? $data['trend_name'] ?? '' : 'Trends';
      } else {
        self::$title = match ($domain) {
          CheckbookDomain::$BUDGET => BudgetBreadcrumbs::getBudgetPageTitle(),
          CheckbookDomain::$NYCHA_BUDGET => BudgetBreadcrumbs::getNychaBudgetPageTitle(),
          CheckbookDomain::$REVENUE => RevenueBreacrumbs::getRevenuePageTitle(),
          CheckbookDomain::$NYCHA_REVENUE => RevenueBreacrumbs::getNYCHARevenuePageTitle(),
          CheckbookDomain::$PAYROLL => PayrollBreadcrumbs::getPayrollPageTitle(),
          CheckbookDomain::$SPENDING => SpendingBreadcrumbs::getSpendingPageTitle(),
          CheckbookDomain::$NYCHA_SPENDING => SpendingBreadcrumbs::getNychaSpendingPageTitle(),
          CheckbookDomain::$NYCHA_CONTRACTS => ContractsBreadcrumbs::getNychaContractsPageTitle(),
          CheckbookDomain::$CONTRACTS => ContractsBreadcrumbs::getContractsPageTitle(),
          default => "",
        };
      }

      // Ask modules for changes.
      \Drupal::moduleHandler()->invokeAll('page_title_alter', [&self::$title, $domain]);
    }
    return self::$title;
  }



}
