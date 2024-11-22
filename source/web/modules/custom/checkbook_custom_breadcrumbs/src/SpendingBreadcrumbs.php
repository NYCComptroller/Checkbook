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
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\checkbook_project\SpendingUtilities\NychaSpendingUtil;
use Drupal\checkbook_project\SpendingUtilities\SpendingUtil;
use Drupal\checkbook_project\WidgetUtilities\NodeSummaryUtil;

/**
 * Spending breadcrumb class.
 */
class SpendingBreadcrumbs {

  /**
   * Returns Spending page title and Breadcrumb.
   *
   * @return string
   *   The title.
   */
  public static function getSpendingBreadcrumbTitle(): string {
    $title = '';
    $current_path = \Drupal::service('path.current')->getPath();
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param ?? FALSE;
    if (!$bottomURL && preg_match('/^\/spending\/search\/transactions/', $current_path)) {
      $title = (Datasource::isOGE()) ? Datasource::EDC_TITLE . " " : "";
      $title .= SpendingUtil::getSpendingTransactionsTitle();
    }
    elseif ($bottomURL && preg_match('/transactions/', $bottomURL)) {
      $dtsmnid = RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL);
      $smnid = RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL);
      if ($dtsmnid > 0) {
        $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
      }
      else {
        if ($smnid > 0) {
          $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
        }
        else {
          $last_id = RequestUtil::_getLastRequestParamValue($bottomURL);
          if (isset($last_id['vendor']) > 0) {
            $title = _checkbook_project_get_name_for_argument("vendor_id", RequestUtil::getRequestKeyValueFromURL("vendor", $bottomURL));
          }
          elseif (isset($last_id["agency"]) > 0) {
            $title = _checkbook_project_get_name_for_argument("agency_id", RequestUtil::getRequestKeyValueFromURL("agency", $bottomURL));
          }
          elseif (isset($last_id["expcategory"]) > 0) {
            $title = _checkbook_project_get_name_for_argument("expenditure_object_id", RequestUtil::getRequestKeyValueFromURL("expcategory", $bottomURL));
          }
          elseif (isset($last_id["dept"]) > 0) {
            $title = _checkbook_project_get_name_for_argument("department_id", RequestUtil::getRequestKeyValueFromURL("dept", $bottomURL));
          }
          elseif (preg_match("/\/agid/", $bottomURL)) {
            $title = _checkbook_project_get_name_for_argument("agreement_id", RequestUtil::getRequestKeyValueFromURL("agid", $bottomURL));
          }
          elseif (preg_match("/\/magid/", $bottomURL)) {
            $title = _checkbook_project_get_name_for_argument("master_agreement_id", RequestUtil::getRequestKeyValueFromURL("magid", $bottomURL));
          }
          $title = $title . ' ' . RequestUtil::getDashboardTitle() . ' ' . SpendingUtil::getSpendingCategoryName();
        }
      }
      $replace_title = 'MOCS Registered COVID-19 Contract';
      if (strpos($title, $replace_title) === FALSE) {
        if (strpos($title, 'Contracts')) {
          $title = RequestUtil::getRequestKeyValueFromURL('mocs', $bottomURL) ? str_replace('Contracts', $replace_title, $title) : $title;
        }
        else {
          if (strpos($title, 'Contract')) {
            $title = RequestUtil::getRequestKeyValueFromURL('mocs', $bottomURL) ? str_replace('Contract', $replace_title, $title) : $title;
          }
        }
      }
    }
    else {
      $title = self::getSpendingPageTitle(FALSE) . ' ' . RequestUtil::getDashboardTitle() . ' ' . SpendingUtil::getSpendingCategoryName();
    }

    return html_entity_decode($title);
  }

  /**
   * Custom function to get title for spending landing pages.
   *
   * @param bool $ethinicty
   *   The ethinicty.
   *
   * @return string|null
   *   The title.
   */
  public static function getSpendingPageTitle($ethinicty = TRUE): ?string {
    if (\Drupal::request()->query->has('refURL')) {
      $lastReqParam = RequestUtil::_getLastRequestParamValue(\Drupal::request()->query->get('q'));
    }
    else {
      $lastReqParam = RequestUtil::_getLastRequestParamValue();
    }

    $title = "New York City";
    foreach ($lastReqParam as $key => $value) {
      switch ($key) {
        case 'agency':
          $title = _checkbook_project_get_name_for_argument("agency_id", $value);
          break;

        case 'vendor':
          $title = _checkbook_project_get_name_for_argument("vendor_id", $value);
          if ($ethinicty) {
            $title .= MappingUtil::getPrimeVendorEthinictyTitle($value, "spending");
          }
          break;

        case 'industry':
          $title = _checkbook_project_get_name_for_argument("industry_type_id", $value);
          break;

        case 'subvendor':
          if ($value != 'all') {
            $title = _checkbook_project_get_name_for_argument("sub_vendor_id", $value);
            if ($ethinicty) {
              $title .= MappingUtil::getSubVendorEthinictyTitle($value, "spending");
            }
          }
          break;

        default:
          break;
      }
    }
    return $title;
  }

  /**
   * Returns Spending transaction title.
   *
   * @return string
   *   The title.
   */
  public static function getSpendingTransactionTitle(): ?string {
    $title = '';
    $current_path = \Drupal::service('path.current')->getPath();
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param ?? FALSE;
    if (!$bottomURL && preg_match('/^\/spending\/search\/transactions/', $current_path)) {
      $title = (Datasource::isOGE()) ? Datasource::EDC_TITLE . " " : "";
      $title .= SpendingUtil::getSpendingTransactionsTitle();
    }
    elseif (isset($bottomURL) && $bottomURL !== FALSE && preg_match('/transactions/', $bottomURL)) {
      $dtsmnid = RequestUtilities::getTransactionsParams('dtsmnid');
      $smnid = RequestUtilities::getTransactionsParams('smnid');
      if ($dtsmnid > 0) {
        $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
      }
      else {
        if ($smnid > 0) {
          $title = NodeSummaryUtil::getInitNodeSummaryContent($smnid);
        }
        else {
          $last_id = RequestUtil::_getLastRequestParamValue($bottomURL);
          if (isset($last_id['vendor']) > 0) {
            $title = _checkbook_project_get_name_for_argument("vendor_id", RequestUtil::getRequestKeyValueFromURL("vendor", $bottomURL));
          }
          elseif (isset($last_id["agency"]) > 0) {
            $title = _checkbook_project_get_name_for_argument("agency_id", RequestUtil::getRequestKeyValueFromURL("agency", $bottomURL));
          }
          elseif (isset($last_id["expcategory"]) > 0) {
            $title = _checkbook_project_get_name_for_argument("expenditure_object_id", RequestUtil::getRequestKeyValueFromURL("expcategory", $bottomURL));
          }
          elseif (isset($last_id["dept"]) > 0) {
            $title = _checkbook_project_get_name_for_argument("department_id", RequestUtil::getRequestKeyValueFromURL("dept", $bottomURL));
          }
          elseif (preg_match("/\/agid/", $bottomURL)) {
            $title = _checkbook_project_get_name_for_argument("agreement_id", RequestUtil::getRequestKeyValueFromURL("agid", $bottomURL));
          }
          elseif (preg_match("/\/magid/", $bottomURL)) {
            $title = _checkbook_project_get_name_for_argument("master_agreement_id", RequestUtil::getRequestKeyValueFromURL("magid", $bottomURL));
          }
          $title = $title . ' ' . RequestUtil::getDashboardTitle() . ' ' . SpendingUtil::getSpendingCategoryName();
        }
      }
    }
    return $title;
  }

  /**
   * Returns NYCHA Spending page title and Breadcrumb.
   *
   * @return string
   *   The title.
   */
  public static function getNychaSpendingBreadcrumbTitle(): string {
    $bottomURL = RequestUtilities::getBottomContUrl();
    $title = "";
    if (!isset($bottomURL) && PageType::getCurrent() == PageType::ADVANCED_SEARCH_PAGE) {
      $title = 'NYCHA ' . NYCHASpendingUtil::getCategoryName() . ' Spending Transactions';
    }
    elseif (isset($bottomURL) && preg_match('/transactions/', $bottomURL)) {
      $title = NychaSpendingUtil::getTransactionsTitle($bottomURL);
    }
    else {
      $lastReqParam = RequestUtil::_getLastRequestParamValue();
      foreach ($lastReqParam as $key => $value) {
        switch ($key) {
          case 'vendor':
            $title = _checkbook_project_get_name_for_argument("vendor_id", $value);
            break;

          case 'industry':
            $title = _checkbook_project_get_name_for_argument("industry_type_id", $value);
            break;

          case 'fundsrc':
            $title = _checkbook_project_get_name_for_argument("funding_source_id", $value);
            break;

          default:
            $title = "New York City Housing Authority";
        }
        $title .= ' ' . NYCHASpendingUtil::getCategoryName() . ' Spending';
      }
    }
    return html_entity_decode($title);
  }

  /**
   * Custom function to get title for NYCHA CONTRACTS landing pages.
   *
   * @return string|null
   *   The title.
   */
  public static function getNychaSpendingPageTitle(): ?string {
    $current_path = \Drupal::service('path.current')->getPath();
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param;
    if (!$bottomURL && preg_match('/^nycha_spending\/search\/transactions/', $current_path) || preg_match('/^nycha_spending\/all\/transactions/', $current_path)) {
      $title = NULL;
    }
    else {
      $lastReqParam = RequestUtil::_getLastRequestParamValue();
      foreach ($lastReqParam as $key => $value) {
        $title = match ($key) {
          'vendor' => _checkbook_project_get_name_for_argument("vendor_id", $value),
          'industry' => _checkbook_project_get_name_for_argument("industry_type_id", $value),
          'fundsrc' => _checkbook_project_get_name_for_argument("funding_source_id", $value),
          default => "New York City Housing Authority",
        };
      }
    }
    return $title ?? NULL;
  }

}
