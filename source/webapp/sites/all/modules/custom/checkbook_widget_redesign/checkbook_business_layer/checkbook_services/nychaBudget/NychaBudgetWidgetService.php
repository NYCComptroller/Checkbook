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
    $legacy_node_id = $this->getLegacyNodeId();
    switch ($column_name) {
      case "expense_category_name_link":
        $column = $row['expense_category'];
        $url = NychaBudgetUrlService::expenseCategoryURL($row['expenditure_type_id']);
        $value = "<a href='{$url}'>{$column}</a>";
        break;
      case "responsibility_center_name_link":
        $column = $row['responsibility_center'];
        $url = NychaBudgetUrlService::responsibilityCenterURL($row['responsibility_center_id']);
        $value = "<a href='{$url}'>{$column}</a>";
        break;
      case "funding_source_name_link":
        $column = $row['funding_source_description'];
        $url = NychaBudgetUrlService::fundingSourceURL($row['funding_source_id']);
        $value = "<a href='{$url}'>{$column}</a>";
        break;
      case "program_name_link":
        $column = $row['program_phase_description'];
        $url = NychaBudgetUrlService::programNameLink($row['program_phase_id']);
        $value = "<a href='{$url}'>{$column}</a>";
        break;
      case "project_name_link":
        $column = $row['gl_project_description'];
        $url = NychaBudgetUrlService::projectNameLink($row['gl_project_id']);
        $value = "<a href='{$url}'>{$column}</a>";
        break;
      case "expense_committed_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/expcategory/" . $row["expenditure_type_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_expense_category','remaining');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "expense_committed_by_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/expcategory/" . $row["expenditure_type_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_expense_category','committed');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "resp_committed_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/resp_center/" . $row["responsibility_center_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_resp_center','remaining');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "resp_committed_by_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/resp_center/" . $row["responsibility_center_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_resp_center','committed');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "fundsrc_committed_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/fundsrc/" . $row["funding_source_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_fundsrc','remaining');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "fundsrc_committed_by_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/fundsrc/" . $row["funding_source_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_fundsrc','committed');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "prog_committed_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/prgm/" . $row["program_phase_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_prgm','remaining');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "prog_committed_by_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/prgm/" . $row["program_phase_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_prgm','committed');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "proj_committed_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/proj/" . $row["gl_project_id"];
        $class = "bottomContainerReload";
        $url = NychaBudgetUrlService::committedBudgetUrl($dynamic_parameter, 'comm_proj','remaining');
        $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
        break;
      case "proj_committed_by_budget_link":
        $column = $row['committed'];
        $dynamic_parameter = "/proj/" . $row["gl_project_id"];
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
