<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 04/03/2020
 * Time: 2:05 PM
 */

class NychaRevenueWidgetService extends WidgetDataService implements IWidgetService {

  /**
   * Function to allow the client to initialize the data service
   * @return mixed
   */
  public function initializeDataService() {
    return new NychaRevenueDataService();
  }

    public function implementDerivedColumn($column_name,$row) {
      $url_param = drupal_get_path_alias($_GET['q']);
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

      if(isset($value)) {
      return $value;
    }
    return $value;
  }

  public function adjustParameters($parameters, $urlPath) {
    return $parameters;
  }
  public function getWidgetFooterUrl($parameters) {
    return NychaRevenueUrlService::getFooterUrl($parameters);
  }
}
