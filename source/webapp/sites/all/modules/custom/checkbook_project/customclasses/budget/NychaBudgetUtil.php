<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class NychaBudgetUtil{

  static $widget_titles = array(
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
  static public function getTransactionsTitle($url = null){
    $url = isset($url) ? $url : drupal_get_path_alias($_GET['q']);
    $widget = RequestUtil::getRequestKeyValueFromURL('widget', $url);
    $widget_titles = self::$widget_titles;
    $budgetType = RequestUtil::getRequestKeyValueFromURL('budgettype', $url);
    //Transactions Page main title
    $title = (isset($widget) && ($widget != 'wt_year')) ? $widget_titles[$widget]: "";
    if ($title && $budgetType == 'committed' && $widget != 'wt_year'){
      $title .= ' '."by Committed ".' '. "Expense Budget Transactions";
    }
    elseif ($title && $budgetType == 'percdiff'){
      $title .= ' '."by Percent Difference ".' '. "Expense Budget Transactions";
    }
    else {
      $title .= ' ' . "Expense Budget Transactions";
    }
    return $title;
  }

  /**
   * @param $widget string Widget Name
   * @param $bottomURL string
   * @return null|string -- Returns Sub Title for Committed Transactions Details
   */
  static public function getTransactionsSubTitle($widget, $bottomURL){
    $widgetTitles = self::$widget_titles;
    $title = '<b>'.$widgetTitles[$widget].': </b>';

    switch($widget){
      case 'comm_expense_category':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('expcategory', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("expenditure_type_id", $reqParam);
        break;
      case 'comm_resp_center':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('respcenter', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("responsibility_center_id", $reqParam);
        break;
      case 'comm_fundsrc':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('fundsrc', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("funding_source_id", $reqParam);
        break;
      case 'comm_prgm':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('program', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("program_phase_id", $reqParam);
        break;
      case 'comm_proj':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('project', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("gl_project_id", $reqParam);
        break;
      case 'wt_year' :
        $reqParam = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
        $title .= 'FY ' . _getYearValueFromID($reqParam);
    }

    return $title;
  }

  /**
   * @param $query string
   * @return string Altered Query
   */
  static public function alterPercentDifferenceQuery($query){
    //Remove the filters at the end for count query
    if (strpos($query, 'COUNT(*) AS record_count')) {
      $filters = substr($query, strpos($query, 'WHERE b.'));
      $urlFilters = str_replace('WHERE ', '', $filters);
      $urlFilters = ' AND ' . str_replace('b.', 'a.', $urlFilters);
      $query = str_replace($filters, "", $query);
    }else {//Remove the filters at the end for Data query
      $start = "WHERE b.";
      $end = "ORDER BY";
      $filters = explode($start, $query);
      if (isset($filters[1])) {
        $filters = explode($end, $filters[1]);
        $urlFilters = str_replace('WHERE ', '', ($filters[0]));
        $urlFilters = ' AND ' . str_replace('b.', 'a.', $urlFilters);
        $query = str_replace('WHERE b.'.$filters[0], "", $query);
      }
    }

    //Append URL parameters to dataset queries
    $dataSetFilter1 = "WHERE (a.filter_type = 'H')";
    $newFilter1 = str_replace(')', $urlFilters . ')', $dataSetFilter1);
    $query = str_replace($dataSetFilter1, $newFilter1, $query);

    $dataSetFilter2 = "WHERE (a.filter_type = 'H' AND a.is_active = 1)";
    $newFilter2 = str_replace(')', $urlFilters . ')', $dataSetFilter2);
    $query = str_replace($dataSetFilter2, $newFilter2, $query);

    return $query;
  }

  // NYCCHKBK - 10215
  // Requirement for Transactions Page results - budget_type and budget_name to display null as null and 'n/a' as 'n/a'
  static public function getBudgetName($budgetId)
  {
    $where = "WHERE budget_id = '" . $budgetId . "' AND budget_name IS NOT NULL";
    $query = "SELECT budget_name FROM budget {$where} ";
    $data = _checkbook_project_execute_sql_by_data_source($query, 'checkbook_nycha');
    $result = isset($data[0]['budget_name'])? $data[0]['budget_name'] : null;
    return $result;
  }
  static public function getBudgetType($budgetId)
  {
    $where = "WHERE budget_id = '" . $budgetId . "' AND budget_type IS NOT NULL";
    $query = "SELECT budget_type FROM budget {$where} ";
    $data = _checkbook_project_execute_sql_by_data_source($query, 'checkbook_nycha');
    $result = isset($data[0]['budget_type'])? $data[0]['budget_type'] : null;
    return $result;
  }
}
