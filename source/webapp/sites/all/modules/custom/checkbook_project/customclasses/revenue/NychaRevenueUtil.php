<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class NychaRevenueUtil{
  static $widget_titles = array(
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
  static public function getTransactionsTitle($url = null){
    $url = isset($url) ? $url : drupal_get_path_alias($_GET['q']);
    $widget = RequestUtil::getRequestKeyValueFromURL('widget', $url);
    $revenue_type = RequestUtil::getRequestKeyValueFromURL('revtype', $url);
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
  static public function getTransactionsSubTitle($widget, $bottomURL){
    $widgetTitles = self::$widget_titles;
    $title = '<b>'.$widgetTitles[$widget].': </b>';

    switch($widget){
      case 'rec_expense_category':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('expcategory', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("rev_expenditure_type_id", $reqParam);
        break;
      case 'rec_respcenter':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('respcenter', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("responsibility_center_id", $reqParam);
        break;
      case 'rec_funding_source':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('fundsrc', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("funding_source_id", $reqParam);
        break;
      case 'rec_program':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('program', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("rev_program_phase_id", $reqParam);
        break;
      case 'rec_project':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('project', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("rev_gl_project_id", $reqParam);
        break;
      case 'rec_reccat':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('revcat', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("revenue_category_id", $reqParam);
        break;
      case 'wt_year' :
        $reqParam = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
        $title .= _getYearValueFromID($reqParam);
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
      $data = _checkbook_project_execute_sql_by_data_source($query, 'checkbook_nycha');
      $result = isset($data[0]['budget_name']) ? $data[0]['budget_name'] : null;
      return $result;
    }
  }

  static public function getRevenueBudgetType($revenueId)
  {
    if (isset($revenueId))
    {
      $where = "WHERE revenue_id = '" . $revenueId . "' AND budget_type IS NOT NULL";
      $query = "SELECT budget_type FROM revenue {$where} ";
      $data = _checkbook_project_execute_sql_by_data_source($query, 'checkbook_nycha');
      $result = isset($data[0]['budget_type']) ? $data[0]['budget_type'] : null;
      return $result;
    }
  }

}
