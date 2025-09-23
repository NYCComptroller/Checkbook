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

namespace Drupal\checkbook_project\RevenueUtilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

class NychaRevenueUtil{
  static array $widget_titles = array(
    'wt_expense_categories' => 'Revenue Expense Categories',
    'wt_resp_centers' => 'Responsibility Centers',
    'wt_projects' => 'Projects',
    'wt_funding_sources' => 'Funding Sources',
    'wt_program' => 'Programs',
    'wt_revcat' => 'Revenue Categories',
    'rec_expense_category' => 'Expense Category',
    'rec_respcenter' => 'Responsibility Center',
    'rec_project' => 'Project',
    'rec_funding_source' => 'Funding Source',
    'rec_program' => 'Program',
    'rec_reccat' => 'Revenue Category',
    'wt_year' => 'Year'
  );

  /**
   * @param $url
   * @return null|string -- Returns transactions title for NYCHA Revenue
   */
  static public function getTransactionsTitle(): ?string
  {
    $widget = RequestUtilities::_getRequestParamValueBottomURL('widget');
    $revenue_type = RequestUtilities::_getRequestParamValueBottomURL('revtype');
    $widget_titles = self::$widget_titles;

    //Transactions Page main title
    $title = (isset($widget) && ($widget != 'wt_year')) ? $widget_titles[$widget]: "";

    // Visualizations links for revenue category transactions title
    if (isset($revenue_type)){
      $title = '';
    }

    if (strpos($widget, 'rec_') !== false && $revenue_type == ''){
      $title .= ' '. "by Recognized Revenue Transactions";
    }
    else {
      $title .= ' ' . "Revenue Transactions";
    }

    return $title;
  }

  /**
   * @param $widget Widget Name
   * @param $bottomURL
   * @return null|string -- Returns Sub Title for Recognized Transactions Details
   */
  static public function getTransactionsSubTitle($widget, $bottomURL): ?string
  {
    $widgetTitles = self::$widget_titles;
    $title = '<b>'.$widgetTitles[$widget].': </b>';

    switch($widget){
      case 'rec_expense_category':
        $reqParam = RequestUtilities::get('expcategory', ['q'=>$bottomURL]);
        $title .= _checkbook_project_get_name_for_argument("rev_expenditure_type_id", $reqParam);
        break;
      case 'rec_respcenter':
        $reqParam = RequestUtilities::get('respcenter', ['q'=>$bottomURL]);
        $title .= _checkbook_project_get_name_for_argument("responsibility_center_id", $reqParam);
        break;
      case 'rec_funding_source':
        $reqParam = RequestUtilities::get('fundsrc', ['q'=>$bottomURL]);
        $title .= _checkbook_project_get_name_for_argument("funding_source_id", $reqParam);
        break;
      case 'rec_program':
        $reqParam =RequestUtilities::get('program', ['q'=>$bottomURL]);
        $title .= _checkbook_project_get_name_for_argument("rev_program_phase_id", $reqParam);
        break;
      case 'rec_project':
        $reqParam = RequestUtilities::get('project', ['q'=>$bottomURL]);
        $title .= _checkbook_project_get_name_for_argument("rev_gl_project_id", $reqParam);
        break;
      case 'rec_reccat':
        $reqParam = RequestUtilities::get('revcat', ['q'=>$bottomURL]);
        $title .= _checkbook_project_get_name_for_argument("revenue_category_id", $reqParam);
        break;
      case 'wt_year' :
        $reqParam = RequestUtilities::get('year', ['q'=>$bottomURL]);
        $title .= \Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil::_getYearValueFromID($reqParam);
    }

    return $title;
  }

  // NYCCHKBK - 10302
  // Requirement for Transactions Page results - budget_type and budget_name to display null as null and 'n/a' as 'n/a'
  static public function getRevenueBudgetName($revenueId)
  {
    if (isset($revenueId))
    {
      $where = "WHERE revenue_id = '" . $revenueId . "' AND budget_name IS NOT NULL";
      $query = "SELECT budget_name FROM revenue {$where} ";
      $data = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
      return $data[0]['budget_name'] ?? null;
    }
    return null;
  }

  static public function getRevenueBudgetType($revenueId)
  {
    if (isset($revenueId))
    {
      $where = "WHERE revenue_id = '" . $revenueId . "' AND budget_type IS NOT NULL";
      $query = "SELECT budget_type FROM revenue {$where} ";
      $data = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
      return $data[0]['budget_type'] ?? null;
    }
    return null;
  }

}
