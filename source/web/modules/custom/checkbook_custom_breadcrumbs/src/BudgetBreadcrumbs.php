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

use Drupal;
use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\checkbook_project\BudgetUtilities\NychaBudgetUtil;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\WidgetUtilities\NodeSummaryUtil;

class BudgetBreadcrumbs
{
  /**
   * Returns Budget page title and Breadcrumb
   * @return string
   */
  public static function getBudgetBreadcrumbTitle(): string
  {
    $current_path = \Drupal::service('path.current')->getPath();
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param ?? FALSE;
    $find = '_' . $bottomURL . $current_path;

    $title = self::getBudgetPageTitle() . ' Expense Budget';

    if (!$bottomURL && stripos('_' . $current_path, 'budget/transactions/')) {
      $title = "Expense Budget Transactions";
    } elseif (
      stripos($find, 'transactions')
      || stripos($find, 'deppartment_budget_details')
      || stripos($find, 'expense_category_budget_details')
    ) {
      $dtsmnid = $bottomURL ? RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("dtsmnid", $current_path);
      $smnid = $bottomURL ? RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("smnid", $current_path);
      if (isset($dtsmnid)) {
        $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
      } else {
        if (isset($smnid)) {
          $title = NodeSummaryUtil::getInitNodeSummaryTemplateTitle($smnid);
        }
      }
    }
    return html_entity_decode($title);
  }

  /** Custom function to get title for Budget landing pages
   * @return string|null
   */
  public static function getBudgetPageTitle(): ?string
  {
    $current_path = Drupal::service('path.current')->getPath();
    $expand_bottom_param = Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param ?? FALSE;
    if (!$bottomURL && preg_match('/^budget\/transactions/', $current_path)) {
      $title = NULL;
    } else {
      $lastReqParam = RequestUtil::_getLastRequestParamValue();
      $title = "New York City";

      foreach ($lastReqParam as $key => $value) {
        switch ($key) {
          case 'agency':
            $title = _checkbook_project_get_name_for_argument("agency_id", $value);
            break;
          case 'expcategory':
            $title = _checkbook_project_get_name_for_argument("object_class_id", $value);
            break;
          case 'dept':
            $title = _checkbook_project_get_name_for_argument("department_code", $value);
            break;
          case 'bdgcode':
            $title = _checkbook_project_get_name_for_argument("budget_code_id", $value);
          default:
        }
      }
    }
    return $title;
  }

  /**
   * @return string
   */
  public static function advancedSearchTitle() {
    return 'Expense Budget Transactions';
  }

  /**
   * @return string
   */
  public static function nychaAdvancedSearchTitle() {
    return 'NYCHA Expense Budget Transactions';
  }

  /**
   * Returns NYCHA Budget page title and Breadcrumb
   * @return string
   */
  public static function getNYCHABudgetBreadcrumbTitle(): string
  {
    $title = "";
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param ?? FALSE;
    if (PageType::getCurrent() == PageType::ADVANCED_SEARCH_PAGE && !$bottomURL) {
      $title = 'NYCHA Expense Budget Transactions';
    } else {
      if ($bottomURL) {
        $title = NychaBudgetUtil::getTransactionsTitle();
      } else {
        $lastReqParam = RequestUtil::_getLastRequestParamValue();
        foreach ($lastReqParam as $key => $value) {
          $title = match ($key) {
            'expcategory' => _checkbook_project_get_name_for_argument("expenditure_type_id", $value),
            'respcenter' => _checkbook_project_get_name_for_argument("responsibility_center_id", $value),
            'fundsrc' => _checkbook_project_get_name_for_argument("funding_source_id", $value),
            'program' => _checkbook_project_get_name_for_argument("program_phase_id", $value),
            'project' => _checkbook_project_get_name_for_argument("gl_project_id", $value),
            default => "New York City Housing Authority",
          };
          $title .= ' Expense Budget';
        }
      }
    }
    return html_entity_decode($title);
  }

  /**
   * Custom function to get title for NYCHA Budget landing pages
   * @return string|null
   */
  public static function getNychaBudgetPageTitle(): ?string
  {
    if (PageType::getCurrent() == PageType::ADVANCED_SEARCH_PAGE) {
      $title = NULL;
    } else {
      $lastReqParam = RequestUtil::_getLastRequestParamValue();
      foreach ($lastReqParam as $key => $value) {
        $title = match ($key) {
          'expcategory' => _checkbook_project_get_name_for_argument("expenditure_type_id", $value),
          'respcenter' => _checkbook_project_get_name_for_argument("responsibility_center_id", $value),
          'fundsrc' => _checkbook_project_get_name_for_argument("funding_source_id", $value),
          'program' => _checkbook_project_get_name_for_argument("program_phase_id", $value),
          'project' => _checkbook_project_get_name_for_argument("gl_project_id", $value),
          default => "New York City Housing Authority",
        };
      }
    }
    return $title;
  }
}
