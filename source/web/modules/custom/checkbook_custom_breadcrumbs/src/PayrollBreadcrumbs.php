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

use Drupal\checkbook_infrastructure_layer\Constants\Common\Dashboard;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Constants\Payroll\PayrollLandingPage;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\PayrollUtilities\PayrollUtil;
use Drupal\checkbook_project\WidgetUtilities\NodeSummaryUtil;

/**
 * Payroll breadcrumbs class.
 */
class PayrollBreadcrumbs {

  /**
   * Returns Payroll page title and Breadcrumb.
   *
   * @return string
   *   The title.
   */
  public static function getPayrollBreadcrumbTitle(): string {
    $title = '';
    $current_path = \Drupal::service('path.current')->getPath();
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param;
    if (isset($bottomURL) && str_contains($bottomURL, 'payroll/agencywide/transactions')) {
      $smnid = RequestUtilities::getTransactionsParams("smnid");
      $dtsmnid = RequestUtilities::getTransactionsParams("dtsmnid");
      if ($dtsmnid > 0) {
        $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
      }
      else {
        if ($smnid > 0) {
          $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
        }
        else {
          $title = _checkbook_project_get_name_for_argument("agency_id", RequestUtilities::getTransactionsParams("agency")) . ' Payroll Transactions';
        }
      }
    }
    else {
      if (isset($bottomURL) && str_contains($bottomURL, 'payroll/employee/transactions')) {
        $title = "Individual Employee Payroll Transactions";
      }
      else {
        if (isset($bottomURL) && str_contains($bottomURL, 'payroll_title_transactions')) {
          $title = "Payroll Summary by Employee Title";
        }
        else {
          if (isset($bottomURL) && str_contains($bottomURL, 'payroll_nyc_transactions')) {
            $smnid = RequestUtilities::getTransactionsParams("smnid");
            $dtsmnid = RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL);
            if ($dtsmnid > 0) {
              $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
            }
            if ($smnid > 0) {
              $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
            }
          }
          else {
            if (isset($bottomURL) && str_contains($bottomURL, 'payroll_nyc_title_transactions')) {
              $smnid = RequestUtilities::getTransactionsParams("smnid");
              $payroll_type = RequestUtilities::getTransactionsParams("payroll_type");
              if (isset($payroll_type)) {
                $title = PayrollUtil::getPayrollTitlebyType($payroll_type);
              }
              else {
                if ($smnid > 0) {
                  $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
                }
              }
            }
            else {
              if (isset($bottomURL) && str_contains($bottomURL, 'payroll_by_month_nyc_transactions')) {
                $smnid = RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL);
                if ($smnid == '491' || $smnid == '492') {
                  $customTitle = "Overtime Payments by Month Transactions";
                }
                else {
                  $customTitle = "Gross Pay by Month Transactions";
                }
                $title = $customTitle;
              }
              else {
                if (isset($bottomURL) && str_contains($bottomURL, 'payroll_agency_by_month_transactions')) {
                  $smnid = RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL);
                  if ($smnid == '491') {
                    $customTitle = "Overtime Payments by Month Transactions";
                  }
                  else {
                    $customTitle = "Gross Pay by Month Transactions";
                  }
                  $title = $customTitle;
                }
                elseif (preg_match('/^\/payroll\/search\/transactions/', $current_path)) {
                  if (Datasource::isNYCHA()) {
                    $title = strtoupper(Dashboard::NYCHA) . " ";
                  }
                  $title .= "Payroll Transactions";
                }
                elseif (str_starts_with($current_path, '/payroll') && preg_match('/agency_landing/', $current_path)) {
                  $title = _checkbook_project_get_name_for_argument("agency_id", RequestUtil::getRequestKeyValueFromURL("agency", $current_path)) . ' Payroll';
                }
                elseif (str_starts_with($current_path, '/payroll') && preg_match('/title_landing/', $current_path)) {
                  $title_code = RequestUtil::getRequestKeyValueFromURL("title", $current_path);
                  $title = PayrollUtil::getTitleByCode($title_code) . ' Payroll';
                  $title = mb_convert_case($title, MB_CASE_TITLE, "UTF-8");
                }
                elseif (!isset($bottomURL) && str_starts_with($current_path, '/payroll') && !preg_match('/transactions/', $current_path)) {
                  $title = 'New York City Payroll';
                }
                else {
                  global $_checkbook_breadcrumb_title;
                  $title = $_checkbook_breadcrumb_title;
                }
              }
            }
          }
        }
      }
    }
    return html_entity_decode($title);
  }

  /**
   * Custom function to get title for Payroll landing pages.
   *
   * @return string|null
   *   The title.
   */
  public static function getPayrollPageTitle($url = NULL): ?string {
    if (isset($url)) {
      $current_path = $url;
      $bottomURL = NULL;
    }
    else {
      $current_path = RequestUtilities::getCurrentPageUrl();
      $bottomURL = RequestUtilities::getBottomContUrl();
    }
    if (!$bottomURL && preg_match('/^\/payroll\/search\/transactions/', $current_path)) {
      $title = NULL;
    }
    else {
      if (PayrollLandingPage::getCurrent() == PayrollLandingPage::AGENCY_LEVEL) {
        $value = RequestUtilities::get('agency');
        $title = _checkbook_project_get_name_for_argument("agency_id", $value);
      }
      else {
        if (PayrollLandingPage::getCurrent() == PayrollLandingPage::TITLE_LEVEL) {
          $value = RequestUtilities::get('title');
          $title = _checkbook_project_get_name_for_argument("title", $value);
        }
        elseif (stripos($current_path, '/checkbook_nycha/')) {
          $title = "New York City Housing Authority";
        }
        else {
          $title = "New York City";
        }
      }
    }
    return $title;
  }

}
