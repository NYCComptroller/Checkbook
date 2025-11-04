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

namespace Drupal\checkbook_services\NychaRevenue;

use Drupal\checkbook_services\Widget\IWidgetService;
use Drupal\checkbook_services\Widget\WidgetDataService;

class NychaRevenueWidgetService extends WidgetDataService implements IWidgetService {

  /**
   * Function to allow the client to initialize the data service
   * @return mixed
   */
  public function initializeDataService() {
    return new NychaRevenueDataService();
  }

    public function implementDerivedColumn($column_name,$row) {
      //$url_param = \Drupal::service('path.current')->getPath();
      $value = null;
      switch($column_name) {
        case "expense_category_name_link":
          $column = $row['expense_category'];
          $url = NychaRevenueUrlService::generateLandingPageUrl('expcategory',$row['expenditure_type_id']);
          $value = "<a href='{$url}'>{$column}</a>";
          break;
        case "responsibility_center_name_link":
          $column = $row['responsibility_center'];
          $url = NychaRevenueUrlService::generateLandingPageUrl('respcenter',$row['responsibility_center_id']);
          $value = "<a href='{$url}'>{$column}</a>";
          break;
        case "funding_source_name_link":
          $column = $row['funding_source'];
          $url = NychaRevenueUrlService::generateLandingPageUrl('fundsrc',$row['funding_source_id']);
          $value = "<a href='{$url}'>{$column}</a>";
          break;
        case "program_name_link":
          $column = $row['program_phase_description'];
          $url = NychaRevenueUrlService::generateLandingPageUrl('program',$row['program_phase_id']);
          $value = "<a href='{$url}'>{$column}</a>";
          break;
        case "project_name_link":
          $column = $row['gl_project_description'];
          $url = NychaRevenueUrlService::generateLandingPageUrl('project',$row['gl_project_id']);
          $value = "<a href='{$url}'>{$column}</a>";
          break;
        /* Recognized Revenue links */
        case "expcat_rev_link":
          $column = $row['recognized_amount'];
          $class = "bottomContainerReload";
          $dynamic_parameter = "/expcategory/" . $row["expenditure_type_id"];
          $url = NychaRevenueUrlService::recRevenueUrl($dynamic_parameter, 'rec_expense_category');
          $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
          break;
        case "fundsrc_rev_link":
          $column = $row['recognized_amount'];
          $class = "bottomContainerReload";
          $dynamic_parameter = "/fundsrc/" . $row["funding_source_id"];
          $url = NychaRevenueUrlService::recRevenueUrl($dynamic_parameter, 'rec_funding_source');
          $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
          break;
        case "project_rev_link":
          $column = $row['recognized_amount'];
          $class = "bottomContainerReload";
          $dynamic_parameter = "/project/" . $row["gl_project_id"];
          $url = NychaRevenueUrlService::recRevenueUrl($dynamic_parameter, 'rec_project');
          $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
          break;
        case "program_rev_link":
          $column = $row['recognized_amount'];
          $class = "bottomContainerReload";
          $dynamic_parameter = "/program/" . $row["program_phase_id"];
          $url = NychaRevenueUrlService::recRevenueUrl($dynamic_parameter, 'rec_program');
          $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
          break;
        case "reccat_rev_link":
          $column = $row['recognized_amount'];
          $class = "bottomContainerReload";
          $dynamic_parameter = "/revcat/" . $row["revenue_category_id"];
          $url = NychaRevenueUrlService::recRevenueUrl($dynamic_parameter, 'rec_reccat');
          $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
          break;
        case "respcenter_rev_link":
          $column = $row['recognized_amount'];
          $class = "bottomContainerReload";
          $dynamic_parameter = "/respcenter/" . $row["responsibility_center_id"];
          $url = NychaRevenueUrlService::recRevenueUrl($dynamic_parameter, 'rec_respcenter');
          $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
          break;
      }

      return $value;
    }

  /**
   * @param $parameters
   * @param $urlPath
   * @return mixed
   */
  public function adjustParameters($parameters, $urlPath) {
    return $parameters;
  }

  /**
   * @param $parameters
   * @return string
   */
  public function getWidgetFooterUrl($parameters) {
    return NychaRevenueUrlService::getFooterUrl($parameters);
  }
}
