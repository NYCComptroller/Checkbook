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

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Custom breadcrumb class.
 */
class CustomBreadcrumbs {

  const API_URL = '/data-feeds/api';
  const API_TITLE = 'API';
  const NONE_URL = '<none>';
  const ACFR_TITLE = 'ACFR Trends';
  const ALL_TITLE = 'All Trends';

  /**
   * Breadcrumb links.
   *
   * @var array|null
   */
  protected static $links;

  /**
   * Get links.
   *
   * @return array
   *   The array of links.
   */
  public static function getLinks() {
    if (!isset(self::$links)) {
      $path = self::getCurrentPath();
      $widgetId = RequestUtilities::get('widget');
      if (PageType::getCurrent() == PageType::TRENDS_PAGE) {
        if (preg_match('/^\/trends-landing\/trends\/node\//', $path)) {
          $widgetId = RequestUtilities::get('node');
        }

        if (preg_match('/^\/featuredtrends\/node\//', $path)) {
          $widgetFeaturedId = RequestUtilities::get('node');
        }
      }

      $trends = TrendPageTitle::getAllTrends();

      if (preg_match('/(contract-api)/', $path)) {
        self::$links[] = Link::fromTextAndUrl(self::API_TITLE, Url::fromUserInput(self::API_URL));
        self::$links[] = Link::createFromRoute('Contracts API', self::NONE_URL);
      }
      elseif (preg_match('/(payroll-api)/', $path)) {
        self::$links[] = Link::fromTextAndUrl(self::API_TITLE, Url::fromUserInput(self::API_URL));
        self::$links[] = Link::createFromRoute('Payroll API', self::NONE_URL);
      }
      elseif (preg_match('/(spending-api)/', $path)) {
        self::$links[] = Link::fromTextAndUrl(self::API_TITLE, Url::fromUserInput(self::API_URL));
        self::$links[] = Link::createFromRoute('Spending API', self::NONE_URL);
      }
      elseif (preg_match('/(budget-api)/', $path)) {
        self::$links[] = Link::fromTextAndUrl(self::API_TITLE, Url::fromUserInput(self::API_URL));
        self::$links[] = Link::createFromRoute('Budget API', self::NONE_URL);
      }
      elseif (preg_match('/(revenue-api)/', $path)) {
        self::$links[] = Link::fromTextAndUrl(self::API_TITLE, Url::fromUserInput(self::API_URL));
        self::$links[] = Link::createFromRoute('Revenue API', self::NONE_URL);
      }
      elseif (str_contains($path, 'featured-trends')) {
        // From generalFundExpendOtherFinSourcesTop().
        self::$links[] = Link::createFromRoute(self::ACFR_TITLE, self::NONE_URL);
        self::$links[] = Link::fromTextAndUrl('Featured Trends', Url::fromUserInput('/featured-trends'));
        self::$links[] = Link::createFromRoute('General Fund Revenues and General Fund Expenditures', self::NONE_URL);
      }
      elseif (str_contains($path, 'api')) {
        self::$links[] = Link::createFromRoute(self::API_TITLE, self::NONE_URL);
      }
      elseif (str_contains($path, 'smart_search')) {
        $datasource = Datasource::getCurrentSolrDatasource();
        $tail = match ($datasource) {
          'nycha' => ' NYCHA',
          'edc' => ' EDC',
          default => '',
        };
        self::$links[] = Link::createFromRoute('Search Results' . $tail, self::NONE_URL);
      }
      elseif (str_contains($path, 'all-trends')) {
        self::$links[] = Link::createFromRoute(self::ACFR_TITLE, self::NONE_URL);
        self::$links[] = Link::fromTextAndUrl(self::ALL_TITLE, Url::fromUserInput('/all-trends'));
      }
      elseif (array_key_exists($widgetId, $trends)) {
        $trendBreadCrumb = TrendPageTitle::getBreadCrumbTitle($widgetId);
        self::$links[] = Link::createFromRoute(self::ACFR_TITLE, self::NONE_URL);
        self::$links[] = Link::fromTextAndUrl(self::ALL_TITLE, Url::fromUserInput('/all-trends'));
        self::$links[] = Link::createFromRoute($trendBreadCrumb['trend_type'], self::NONE_URL);
        self::$links[] = Link::createFromRoute($trendBreadCrumb['trend_name'], self::NONE_URL);
      }
      elseif (!empty($widgetFeaturedId) && array_key_exists($widgetFeaturedId, $trends)) {
        $trendBreadCrumb = TrendPageTitle::getBreadCrumbTitle($widgetFeaturedId);
        $slide = TrendPageTitle::$featuredTrendsSlide[$widgetFeaturedId] ?? NULL;
        $options = [];
        if ($slide) {
          $options['query']['slide'] = $slide;
        }
        self::$links[] = Link::fromTextAndUrl('Trends', Url::fromUserInput('/featured-trends'));
        self::$links[] = Link::fromTextAndUrl($trendBreadCrumb['trend_name'], Url::fromUri('internal:/featured-trends', $options));
        self::$links[] = Link::createFromRoute($trendBreadCrumb['trend_title'], self::NONE_URL);
      }

      // Add "Home" link.
      if (!empty(self::$links)) {
        self::$links = array_merge([Link::createFromRoute('Home', '<front>')], self::$links);
      }
    }

    return self::$links;
  }

  /**
   * Check if the current page is hierarchical.
   *
   * @return bool
   *   Is hierarchical.
   */
  public static function isHierarchical() {
    $path = self::getCurrentPath();

    if (preg_match('/(contract-api)|(payroll-api)|(spending-api)|(budget-api)|(revenue-api)/', $path)) {
      return TRUE;
    }
    elseif (preg_match('/(contract)|(payroll)|(spending)|(budget)|(revenue)|(newwindow)|(gridview)/', $path)) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Get the current path.
   *
   * @return string
   *   The current path.
   */
  public static function getCurrentPath($alias = TRUE) {
    $current_path = \Drupal::service('path.current')->getPath();
    return $alias ? \Drupal::service('path_alias.manager')->getAliasByPath($current_path) : $current_path;
  }

}
