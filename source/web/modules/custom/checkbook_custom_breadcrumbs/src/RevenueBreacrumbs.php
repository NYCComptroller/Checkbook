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

use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\RevenueUtilities\NychaRevenueUtil;
use Drupal\checkbook_project\WidgetUtilities\NodeSummaryUtil;

/**
 * Revenue breadcrumbs class.
 */
class RevenueBreacrumbs {

  /**
   * Returns Revenue page title and Breadcrumb.
   *
   * @return string
   *   The title.
   */
  public static function getRevenueBreadcrumbTitle(): string {
    $current_path = \Drupal::service('path.current')->getPath();
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param ?? FALSE;
    $find = '_' . $bottomURL . $current_path;
    if (
      stripos($bottomURL, 'transactions')
      || stripos($find, 'agency_revenue_by_cross_year_collections_details')
      || stripos($find, 'revenue_category_revenue_by_cross_year_collections_details')
      || stripos($find, 'funding_class_revenue_by_cross_year_collections_details')
      || stripos('_' . $current_path, 'revenue_transactions')
    ) {
      $dtsmnid = $bottomURL ? RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("dtsmnid", $current_path);
      $smnid = $bottomURL ? RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("smnid", $current_path);
      if (isset($dtsmnid)) {
        $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
      }
      else {
        if (isset($smnid)) {
          $title = NodeSummaryUtil::getInitNodeSummaryTemplateTitle($smnid);
        }
        else {
          $title = BudgetBreadcrumbs::getBudgetPageTitle() . ' Revenue';
        }
      }
    }
    else {
      if (!$bottomURL && stripos('_' . $current_path, 'revenue/transactions/')) {
        $title = "Revenue Transactions";
      }
      else {
        $title = BudgetBreadcrumbs:: getBudgetPageTitle() . ' Revenue';
      }
    }
    return html_entity_decode($title);
  }

  /**
   * Custom function to get title for Revenue landing pages.
   *
   * @return string|null
   *   The title.
   */
  public static function getRevenuePageTitle(): ?string {
    $current_path = \Drupal::service('path.current')->getPath();
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param;
    if (!$bottomURL && preg_match('/^revenue\/transactions/', $current_path)) {
      $title = NULL;
    }
    else {
      $lastReqParam = RequestUtil::_getLastRequestParamValue();
      $title = "New York City";
      foreach ($lastReqParam as $key => $value) {
        switch ($key) {
          case 'agency':
            $title = _checkbook_project_get_name_for_argument("agency_id", $value);
            break;

          case 'revcat':
            $title = _checkbook_project_get_name_for_argument("revenue_category_id", $value);
            break;

          case 'fundsrccode':
            $title = _checkbook_project_get_name_for_argument("funding_class_code", $value);
            break;

          default:
            break;
        }
      }
    }
    return $title;
  }

  /**
   * The advanced search title.
   *
   * @return string
   *   The title.
   */
  public static function advancedSearchTitle() {
    return 'Revenue Transactions';
  }

  /**
   * The NYCHA advanced search title.
   *
   * @return string
   *   The tile.
   */
  public static function nychaAdvancedSearchTitle() {
    return 'NYCHA Revenue Transactions';
  }

  /**
   * Returns NYCHA Revenue page title and Breadcrumb.
   *
   * @return string
   *   The title.
   */
  public static function getNychaRevenueBreadcrumbTitle(): string {
    $title = "";
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param ?? FALSE;
    if (PageType::getCurrent() == PageType::ADVANCED_SEARCH_PAGE && !$bottomURL) {
      $title = 'NYCHA RevenueTransactions';
    }
    else {
      if ($bottomURL) {
        $title = NychaRevenueUtil::getTransactionsTitle();
      }
      else {
        $lastReqParam = RequestUtil::_getLastRequestParamValue();
        foreach ($lastReqParam as $key => $value) {
          $title = match ($key) {
            'expcategory' => _checkbook_project_get_name_for_argument("rev_expenditure_type_id", $value),
            'respcenter' => _checkbook_project_get_name_for_argument("responsibility_center_id", $value),
            'fundsrc' => _checkbook_project_get_name_for_argument("funding_source_id", $value),
            'program' => _checkbook_project_get_name_for_argument("rev_program_phase_id", $value),
            'project' => _checkbook_project_get_name_for_argument("rev_gl_project_id", $value),
            default => "New York City Housing Authority",
          };
          $title .= ' Revenue';
        }
      }
    }
    return html_entity_decode($title);
  }

  /**
   * Custom function to get title for NYCHA Revenu landing pages.
   *
   * @return string|null
   *   The title.
   */
  public static function getNychaRevenuePageTitle(): ?string {
    if (PageType::getCurrent() == PageType::ADVANCED_SEARCH_PAGE) {
      $title = NULL;
    }
    else {
      $lastReqParam = RequestUtil::_getLastRequestParamValue();
      foreach ($lastReqParam as $key => $value) {
        $title = match ($key) {
          'expcategory' => _checkbook_project_get_name_for_argument("rev_expenditure_type_id", $value),
          'respcenter' => _checkbook_project_get_name_for_argument("responsibility_center_id", $value),
          'fundsrc' => _checkbook_project_get_name_for_argument("funding_source_id", $value),
          'program' => _checkbook_project_get_name_for_argument("rev_program_phase_id", $value),
          'project' => _checkbook_project_get_name_for_argument("rev_gl_project_id", $value),
          default => "New York City Housing Authority",
        };
      }
    }
    return $title ?? NULL;
  }

}
