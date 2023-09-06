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

class CustomBreadcrumbTitle {

  /**
   * The breadcrumb title.
   *
   * @var string
   */
  protected static $title;

  /**
   * @return string|null
   */
  public static function getCustomBreadcrumbTitle(): ?string {
    if (!isset(self::$title)) {
      $domain = CheckbookDomain::getCurrent();
      self::$title = match ($domain) {
        CheckbookDomain::$BUDGET => BudgetBreadcrumbs::getBudgetBreadcrumbTitle(),
        CheckbookDomain::$NYCHA_BUDGET => BudgetBreadcrumbs::getNychaBudgetBreadcrumbTitle(),
        CheckbookDomain::$REVENUE => RevenueBreacrumbs::getRevenueBreadcrumbTitle(),
        CheckbookDomain::$NYCHA_REVENUE => RevenueBreacrumbs::getNYCHARevenueBreadcrumbTitle(),
        CheckbookDomain::$PAYROLL => PayrollBreadcrumbs::getPayrollBreadcrumbTitle(),
        CheckbookDomain::$SPENDING => SpendingBreadcrumbs::getSpendingBreadcrumbTitle(),
        CheckbookDomain::$NYCHA_SPENDING => SpendingBreadcrumbs::getNychaSpendingBreadcrumbTitle(),
        CheckbookDomain::$NYCHA_CONTRACTS => ContractsBreadcrumbs::getNychaContractsBreadcrumbTitle(),
        CheckbookDomain::$CONTRACTS => ContractsBreadcrumbs::getContractsBreadcrumbTitle(),
        default => "",
      };

      // Ask modules for changes.
      \Drupal::moduleHandler()->invokeAll('breadcrumb_title_alter', [&self::$title, $domain]);
    }
    return self::$title;
  }
}
