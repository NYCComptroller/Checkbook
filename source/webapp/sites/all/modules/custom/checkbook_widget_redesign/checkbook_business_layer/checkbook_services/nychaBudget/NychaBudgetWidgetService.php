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
        $url = NychaBudgetUrlService::expenseCategoryURL($row['expenditure_type_code']);
        $value = "<a href='{$url}'>{$column}</a>";
        break;
      case "responsibility_center_name_link":
        $column = $row['responsibility_center'];
        $url = NychaBudgetUrlService::responsibilityCenterURL($row['responsibility_center_code']);
        $value = "<a href='{$url}'>{$column}</a>";
        break;
      case "funding_source_name_link":
        $column = $row['funding_source_description'];
        $url = NychaBudgetUrlService::fundingSourceURL($row['funding_source_code']);
        $value = "<a href='{$url}'>{$column}</a>";
        break;
      case "program_name_link":
        $column = $row['program_phase_description'];
        $url = NychaBudgetUrlService::programNameLink($row['program_phase_code']);
        $value = "<a href='{$url}'>{$column}</a>";
        break;
      case "project_name_link":
        $column = $row['gl_project_description'];
        $url = NychaBudgetUrlService::projectNameLink($row['gl_project_code']);
        $value = "<a href='{$url}'>{$column}</a>";
        break;
      case "committed_budget_link":
        $column = $row['committed'];
        //$url = BudgetUrlService::departmentUrl($row['department_code']);
        $value = "<a href='{}'>{$column}</a>";
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
    return NychaBudgetUrlService::getFooterUrl($parameters,$this->getLegacyNodeId());
  }
}
