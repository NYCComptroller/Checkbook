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
    if ($budgetType == 'committed' && $widget != 'wt_year'){
      $title .= ' '."By Committed ".' '. "Expense Budget Transactions";
    }
    elseif ($budgetType == 'percdiff'){
      $title .= ' '."By Percent Difference ".' '. "Expense Budget Transactions";
    }
    else {
      $title .= ' ' . "Expense Budget Transactions";
    }
    return $title;
  }

  /**
   * @param $widget Widget Name
   * @param $bottomURL
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
        $reqParam = RequestUtil::getRequestKeyValueFromURL('resp_center', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("responsibility_center_id", $reqParam);
        break;
      case 'comm_fundsrc':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('fundsrc', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("funding_source_id", $reqParam);
        break;
      case 'comm_prgm':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('prgm', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("program_phase_id", $reqParam);
        break;
      case 'comm_proj':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('proj', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("gl_project_id", $reqParam);
        break;
      case 'wt_year' :
        $reqParam = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
        $title .= _getYearValueFromID($reqParam);
    }

    return $title;
  }

}
