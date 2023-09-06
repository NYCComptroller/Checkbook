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

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

class TrendPageTitle
{
  /**
   * Financial Trends
   * @var array
   */
  public static array $financial_trends_map = array(
    392 => ["Changes in Net Assets"],
    393 => ["Fund Balances-Governmental Funds"],
    394 => ["Changes in Fund Balances"],
    316 => ["General Fund Revenues and Other Financing Sources"],
    347 => ["General Fund Expenditures and Other Financing Uses"],
    354 => ["Capital Projects Fund Aid Revenues"],
    395 => ["New York City Educational Construction Fund<sup class=\"title-sup\">*</sup>"],
  );

  /**
   * Revenue Capacity Trends
   * @var array
   */
  public static array $revenue_capacity_map = array(
    398 => ["Assessed Value and Estimated Actual Value of Taxable Property"],
    399 => ["Property Tax Rates"],
    351 => ["Property Tax Levies and Collections"],
    400 => ["Assessed Valuation and Tax Rate by Class"],
    360 => ["Collections, Cancellations, Abatements and Other Discounts as a Percent of Tax Levy"],
    401 => ["Uncollected Parking Violation Fines"],
    404 => ["Hudson Yards Infrastructure Corporation"],
  );

  /**
   * Debt Capacity Trends
   * @var array
   */
  public static array $debt_capacity_map = array(
    355 => ["Ratios of Outstanding Debt by Type"],
    406 => ["Ratios of City General Bonded Debt Payable"],
    407 => ["Legal Debt Margin Information"],
    420 => ["Pledged-Revenue Coverage NYC Transitional Finance Authority"],
  );

  /**
   * Demographic Trends
   * @var array
   */
  public static array $demographic_map = array(
    396 => ["Population"],
    353 => ["Personal Income", "Personal Income Details"],
    358 => ["Nonagricultural Wage Salary Employment"],
    397 => ["Persons Receiving Public Assistance"],
    359 => ["Employment Status of the Resident Population"],
  );

  /**
   * Operational Trends
   * @var array
   */
  public static array $operational_trends_map = array(
    357 => ["Number of Full Time City Employees"],
    405 => ["Capital Assets Statistics by Function/Program"],
  );

  /**
   * Featured Trend Slide IDs
   * @var array
   */
  public static array $featured_trends_slide = array(
    351 => 1,
    354 => 2,
    353 => 3,
    355 => 4,
  );

  /**
   * @return array
   */
  public static function getAllTrends(): array
  {
    return array_replace(self::$financial_trends_map, self::$revenue_capacity_map, self::$debt_capacity_map, self::$demographic_map, self::$operational_trends_map);
  }

  /**
   * @param $widgetId
   * @return array|null
   */
  public static function getBreadCrumbTitle($widgetId = null): ?array
  {
    $widgetId = !isset($widgetId) ? RequestUtilities::get('widget') : $widgetId;
    if (!$widgetId) {
      $widgetId = RequestUtilities::get('node');
    }
    if (array_key_exists($widgetId, self::$financial_trends_map)) {
      return array(
        'trend_type' => 'Financial Trends',
        'trend_name' => str_replace('*', '', strip_tags(self::$financial_trends_map[$widgetId][0])),
        'trend_title' => str_replace('*', '', strip_tags(self::$financial_trends_map[$widgetId][1] ?? self::$financial_trends_map[$widgetId][0])),
      );
    } else if (array_key_exists($widgetId, self::$revenue_capacity_map)) {
      return array(
        'trend_type' => 'Revenue Capacity Trends',
        'trend_name' => self::$revenue_capacity_map[$widgetId][0],
        'trend_title' => self::$revenue_capacity_map[$widgetId][1] ?? self::$revenue_capacity_map[$widgetId][0],
      );
    } else if (array_key_exists($widgetId, self::$debt_capacity_map)) {
      return array(
        'trend_type' => 'Debt Capacity Trends',
        'trend_name' => self::$debt_capacity_map[$widgetId][0],
        'trend_title' => self::$debt_capacity_map[$widgetId][1] ?? self::$debt_capacity_map[$widgetId][0],
      );
    } else if (array_key_exists($widgetId, self::$demographic_map)) {
      return array(
        'trend_type' => 'Demographic Trends',
        'trend_name' => self::$demographic_map[$widgetId][0],
        'trend_title' => self::$demographic_map[$widgetId][1] ?? self::$demographic_map[$widgetId][0],
      );
    } else if (array_key_exists($widgetId, self::$operational_trends_map)) {
      return array(
        'trend_type' => 'Operational Trends',
        'trend_name' => self::$operational_trends_map[$widgetId][0],
        'trend_title' => self::$operational_trends_map[$widgetId][1] ?? self::$operational_trends_map[$widgetId][0],
      );
    }
    return null;
  }

}
