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

namespace Drupal\checkbook_project\BudgetUtilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

class NychaBudgetUtil
{
  static array $widget_titles = array(
    'wt_expense_categories' => 'Expense Categories',
    'wt_resp_centers' => 'Responsibility Centers',
    'wt_projects' => 'Projects',
    'wt_funding_sources' => 'Funding Sources',
    'wt_program' => 'Programs',
    'exp_details' => 'Expense Categories',
    'resp_details' => 'Responsibility Centers',
    'proj_details' => 'Projects',
    'fund_details' => 'Funding Sources',
    'prgm_details' => 'Programs',
    'comm_expense_category' => 'Expense Category',
    'comm_resp_center' => 'Responsibility Center',
    'comm_proj' => 'Project',
    'comm_fundsrc' => 'Funding Source',
    'comm_prgm' => 'Program',
    'wt_year' => 'Year'
  );

  /**
   * @param $url
   * @return null|string -- Returns transactions title for NYCHA Budget
   */
  static public function getTransactionsTitle()
  {
    $widget = RequestUtilities::_getRequestParamValueBottomURL('widget');
    $widget_titles = self::$widget_titles;
    $budgetType = RequestUtilities::_getRequestParamValueBottomURL('budgettype');
    //Transactions Page main title
    $title = (isset($widget) && ($widget != 'wt_year')) ? $widget_titles[$widget] : "";
    if ($title && $budgetType == 'committed' && $widget != 'wt_year') {
      $title .= ' ' . "by Committed " . ' ' . "Expense Budget Transactions";
    } elseif ($title && $budgetType == 'percdiff') {
      $title .= ' ' . "by Percent Difference " . ' ' . "Expense Budget Transactions";
    } else {
      $title .= ' ' . "Expense Budget Transactions";
    }
    return $title;
  }

  /**
   * @param $widget string Widget Name
   * @param $bottomURL string
   * @return null|string -- Returns Sub Title for Committed Transactions Details
   */
  static public function getTransactionsSubTitle($widget, $bottomURL)
  {
    $widgetTitles = self::$widget_titles;
    $title = '<b>' . $widgetTitles[$widget] . ': </b>';

    switch ($widget) {
      case 'comm_expense_category':
        $reqParam = RequestUtilities::get('expcategory', ['q'=>$bottomURL]);
        $title .= _checkbook_project_get_name_for_argument("expenditure_type_id", $reqParam);
        break;
      case 'comm_resp_center':
        $reqParam = RequestUtilities::get('respcenter',  ['q'=>$bottomURL]);
        $title .= _checkbook_project_get_name_for_argument("responsibility_center_id", $reqParam);
        break;
      case 'comm_fundsrc':
        $reqParam = RequestUtilities::get('fundsrc',  ['q'=>$bottomURL]);
        $title .= _checkbook_project_get_name_for_argument("funding_source_id", $reqParam);
        break;
      case 'comm_prgm':
        $reqParam = RequestUtilities::get('program',  ['q'=>$bottomURL]);
        $title .= _checkbook_project_get_name_for_argument("program_phase_id", $reqParam);
        break;
      case 'comm_proj':
        $reqParam = RequestUtilities::get('project',  ['q'=>$bottomURL]);
        $title .= _checkbook_project_get_name_for_argument("gl_project_id", $reqParam);
        break;
      case 'wt_year' :
        $reqParam = RequestUtilities::get('year',  ['q'=>$bottomURL]);
        $title .= 'FY ' . \Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil::_getYearValueFromID($reqParam);
    }
    return $title;
  }

  /**
   * @param $query string
   * @return string Altered Query
   */
  static public function alterPercentDifferenceQuery($query)
  {
    //Remove the filters at the end for count query
    if (strpos($query, 'COUNT(*) AS record_count')) {
      $filters = substr($query, strpos($query, 'WHERE b.'));
      $urlFilters = str_replace('WHERE ', '', $filters);
      $urlFilters = ' AND ' . str_replace('b.', 'a.', $urlFilters);
      $query = str_replace($filters, "", $query);
    } else {//Remove the filters at the end for Data query
      $start = "WHERE b.";
      $end = "ORDER BY";
      $filters = explode($start, $query);
      if (isset($filters[1])) {
        $filters = explode($end, $filters[1]);
        $urlFilters = str_replace('WHERE ', '', ($filters[0]));
        $urlFilters = ' AND ' . str_replace('b.', 'a.', $urlFilters);
        $query = str_replace('WHERE b.' . $filters[0], "", $query);
      }
    }

    //Append URL parameters to dataset queries
    $dataSetFilter1 = "WHERE (a.filter_type = 'H')";
    $newFilter1 = str_replace(')', $urlFilters . ')', $dataSetFilter1);
    $query = str_replace($dataSetFilter1, $newFilter1, $query);

    $dataSetFilter2 = "WHERE (a.filter_type = 'H' AND a.is_active = 1)";
    $newFilter2 = str_replace(')', $urlFilters . ')', $dataSetFilter2);
    return str_replace($dataSetFilter2, $newFilter2, $query);
  }

  // Requirement for Transactions Page results - budget_type and budget_name to display null as null and 'n/a' as 'n/a'
  static public function getBudgetName($budgetId)
  {
    if (isset($budgetId)) {
      $where = "WHERE budget_id = '" . $budgetId . "' AND budget_name IS NOT NULL";
      $query = "SELECT budget_name FROM budget {$where} ";
      $data = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
      return $data[0]['budget_name'] ?? null;
    }
    return null;
  }

  static public function getBudgetType($budgetId)
  {
    if (isset($budgetId)) {
      $where = "WHERE budget_id = '" . $budgetId . "' AND budget_type IS NOT NULL";
      $query = "SELECT budget_type FROM budget {$where} ";
      $data = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
      return $data[0]['budget_type'] ?? null;
    }
    return null;
  }
}
