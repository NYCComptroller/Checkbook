<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class NychaRevenueUtil{
  static $widget_titles = array(
    'wt_expense_categories' => 'Expense Categories',
    'wt_resp_centers' => 'Responsibility Centers',
    'wt_projects' => 'Projects',
    'wt_funding_sources' => 'Funding Sources',
    'wt_program' => 'Programs',
    'wt_revcat' => 'Revenue Categories',
    'wt_year' => 'Year'
  );

  /**
   * @param $url
   * @return null|string -- Returns transactions title for NYCHA Revenue
   */
  static public function getTransactionsTitle($url = null){
    $url = isset($url) ? $url : drupal_get_path_alias($_GET['q']);
    $widget = RequestUtil::getRequestKeyValueFromURL('widget', $url);
    $widget_titles = self::$widget_titles;
    //Transactions Page main title
    $title = (isset($widget) && ($widget != 'wt_year')) ? $widget_titles[$widget]: "";
    $title .= ' ' . "Revenue Transactions";

    return $title;
  }

}
