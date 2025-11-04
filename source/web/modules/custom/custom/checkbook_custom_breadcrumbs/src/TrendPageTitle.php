<?php

namespace Drupal\checkbook_custom_breadcrumbs;

/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City.
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

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

/**
 * Trend Page title class.
 */
class TrendPageTitle {

  /**
   * Financial Trends.
   *
   * @var array
   *   The arrays of trends.
   */
  public static array $financialTrendsMmap = [
    392 => ["Changes in Net Position"],
    393 => ["Fund Balances-Governmental Funds"],
    394 => ["Changes in Fund Balances-Governmental Funds"],
    316 => ["General Fund Revenues and Other Financing Sources"],
    347 => ["General Fund Expenditures and Other Financing Uses"],
    354 => ["Capital Projects Fund Aid Revenues by Agency"],
    395 => ["New York City Educational Construction Fund<sup class=\"title-sup\">*</sup>"],
  ];

  /**
   * Revenue Capacity Trends.
   *
   * @var array
   *   The arrays of trends.
   */
  public static array $revenueCapacityMap = [
    398 => ["Assessed Value and Estimated Actual Value of Taxable Property"],
    399 => ["Property Tax Rates"],
    351 => ["Property Tax Levies and Collections"],
    400 => ["Assessed Valuation and Tax Rate by Class"],
    360 => ["Collections, Cancellations, Abatements and Other Discounts as a Percent of Tax Levy"],
    401 => ["Uncollected Parking Violation Fines"],
    404 => ["Hudson Yards Infrastructure Corporation"],
  ];

  /**
   * Debt Capacity Trends.
   *
   * @var array
   *   The arrays of trends.
   */
  public static array $debtCapacityMap = [
    355 => ["Ratios of Outstanding Debt by Type"],
    406 => ["Ratios of City General Bonded Debt Payable"],
    407 => ["Legal Debt Margin Information"],
    420 => ["Pledged-Revenue Coverage NYC Transitional Finance Authority"],
  ];

  /**
   * Demographic Trends.
   *
   * @var array
   *   The arrays of trends.
   */
  public static array $demographicMap = [
    396 => ["Population"],
    353 => ["Personal Income", "Personal Income Details"],
    358 => ["Nonagricultural Wage Salary Employment"],
    397 => ["Persons Receiving Public Assistance"],
    359 => ["Employment Status of the Resident Population"],
  ];

  /**
   * Operational Trends.
   *
   * @var array
   *   The arrays of trends.
   */
  public static array $operationalTrendsMap = [
    357 => ["Number of Full Time City Employees"],
    405 => ["Capital Assets Statistics by Function/Program"],
  ];

  /**
   * Featured Trend Slide IDs.
   *
   * @var array
   *    The arrays of trends.
   */
  public static array $featuredTrendsSlide = [
    351 => 1,
    354 => 2,
    353 => 3,
    355 => 4,
  ];

  /**
   * Get all Trends.
   *
   * @return array
   *   The list of trends.
   */
  public static function getAllTrends(): array {
    return array_replace(self::$financialTrendsMmap, self::$revenueCapacityMap, self::$debtCapacityMap, self::$demographicMap, self::$operationalTrendsMap);
  }

  /**
   * Get Breadcrumb title.
   *
   * @param int $widgetId
   *   The widgetId.
   *
   * @return array|null
   *   The title.
   */
  public static function getBreadCrumbTitle($widgetId = NULL): ?array {
    $widgetId = !isset($widgetId) ? RequestUtilities::get('widget') : $widgetId;
    if (!$widgetId) {
      $widgetId = RequestUtilities::get('node');
    }
    if (array_key_exists($widgetId, self::$financialTrendsMmap)) {
      return [
        'trend_type' => 'Financial Trends',
        'trend_name' => str_replace('*', '', strip_tags(self::$financialTrendsMmap[$widgetId][0])),
        'trend_title' => str_replace('*', '', strip_tags(self::$financialTrendsMmap[$widgetId][1] ?? self::$financialTrendsMmap[$widgetId][0])),
      ];
    }
    elseif (array_key_exists($widgetId, self::$revenueCapacityMap)) {
      return [
        'trend_type' => 'Revenue Capacity Trends',
        'trend_name' => self::$revenueCapacityMap[$widgetId][0],
        'trend_title' => self::$revenueCapacityMap[$widgetId][1] ?? self::$revenueCapacityMap[$widgetId][0],
      ];
    }
    elseif (array_key_exists($widgetId, self::$debtCapacityMap)) {
      return [
        'trend_type' => 'Debt Capacity Trends',
        'trend_name' => self::$debtCapacityMap[$widgetId][0],
        'trend_title' => self::$debtCapacityMap[$widgetId][1] ?? self::$debtCapacityMap[$widgetId][0],
      ];
    }
    elseif (array_key_exists($widgetId, self::$demographicMap)) {
      return [
        'trend_type' => 'Demographic Trends',
        'trend_name' => self::$demographicMap[$widgetId][0],
        'trend_title' => self::$demographicMap[$widgetId][1] ?? self::$demographicMap[$widgetId][0],
      ];
    }
    elseif (array_key_exists($widgetId, self::$operationalTrendsMap)) {
      return [
        'trend_type' => 'Operational Trends',
        'trend_name' => self::$operationalTrendsMap[$widgetId][0],
        'trend_title' => self::$operationalTrendsMap[$widgetId][1] ?? self::$operationalTrendsMap[$widgetId][0],
      ];
    }
    return NULL;
  }

}
