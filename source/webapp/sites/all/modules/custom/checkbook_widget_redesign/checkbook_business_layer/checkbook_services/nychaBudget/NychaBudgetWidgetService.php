<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 03/30/2020
 * Time: 2:05 PM
 */

class NychaBudgetWidgetService extends WidgetDataService implements IWidgetService {

  /**
   * Function to allow the client to initialize the data service
   * @return mixed
   */
  public function initializeDataService() {
    return new NychaBudgetDataService();
  }

  public function implementDerivedColumn($column_name, $row) {
    $value = null;
    $url = isset($url) ? $url : drupal_get_path_alias($_GET['q']);
    $exp = RequestUtil::getRequestKeyValueFromURL('expcategory', $url);
    $resp = RequestUtil::getRequestKeyValueFromURL('respcenter', $url);
    $fund = RequestUtil::getRequestKeyValueFromURL('fundsrc', $url);
    $prog = RequestUtil::getRequestKeyValueFromURL('program', $url);
    $proj = RequestUtil::getRequestKeyValueFromURL('project', $url);
    switch ($column_name) {
      case "expense_category_name_link":
        $column = $row['expense_category'];
        $url = NychaBudgetUrlService::generateLandingPageUrl('expcategory', $row['expenditure_type_id']);
        $value = (isset($proj) && isset($prog) && isset($fund) && isset($resp)) ? $column : "<a href='{$url}'>{$column}</a>";
        break;
      case "responsibility_center_name_link":
        $column = $row['responsibility_center'];
        $url = NychaBudgetUrlService::generateLandingPageUrl('respcenter', $row['responsibility_center_id']);
        $value = (isset($proj) && isset($prog) && isset($fund) && isset($exp)) ? $column : "<a href='{$url}'>{$column}</a>";
        break;
      case "funding_source_name_link":
        $column = $row['funding_source_description'];
        $url = NychaBudgetUrlService::generateLandingPageUrl('fundsrc', $row['funding_source_id']);
        $value = (isset($proj) && isset($prog) && isset($exp) && isset($resp)) ? $column : "<a href='{$url}'>{$column}</a>";
        break;
      case "program_name_link":
        $column = $row['program_phase_description'];
        $url = NychaBudgetUrlService::generateLandingPageUrl('program', $row['program_phase_id']);
        $value = (isset($proj) && isset($exp) && isset($fund) && isset($resp)) ? $column : "<a href='{$url}'>{$column}</a>";
        break;
      case "project_name_link":
        $column = $row['gl_project_description'];
        $url = NychaBudgetUrlService::generateLandingPageUrl('project', $row['gl_project_id']);
        $value = (isset($exp) && isset($prog) && isset($fund) && isset($resp)) ? $column : "<a href='{$url}'>{$column}</a>";
        break;
      case "expense_committed_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/expcategory/" . $row["expenditure_type_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_expense_category','committed');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "resp_committed_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/respcenter/" . $row["responsibility_center_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_resp_center','committed');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "fundsrc_committed_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/fundsrc/" . $row["funding_source_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_fundsrc','committed');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "prog_committed_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/program/" . $row["program_phase_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_prgm','committed');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "proj_committed_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/project/" . $row["gl_project_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_proj','committed');
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
    return NychaBudgetUrlService::getFooterUrl($parameters);
  }
}
