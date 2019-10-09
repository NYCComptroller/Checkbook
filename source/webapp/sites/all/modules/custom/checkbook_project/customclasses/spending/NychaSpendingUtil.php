<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2012, 2013 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


class NychaSpendingUtil
{
  static $widget_titles = array('wt_checks' => 'Checks', 'ytd_check' => 'Check','wt_vendors' => 'Vendors', 'ytd_vendor' => 'Vendor',
    'wt_contracts' => 'Contracts', 'ytd_contract' => 'Contract', 'wt_expense_categories' => 'Expense Categories',
    'ytd_expense_category' => 'Expense Category', 'wt_industries' => 'Industries', 'ytd_industry' => 'Industry',
    'wt_funding_sources' => 'Funding Sources', 'ytd_funding_source' => 'Funding Source', 'wt_departments' => 'Departments',
    'ytd_department' => 'Department');

  static $categories = array(3 => 'Contracts', 2 => 'Payroll', 1 => 'Section 8', 4 => 'Others', null => 'Total');

  /**
   * @return null|string -- Returns transactions title for NYCHA Spending
   */
  static public function getTransactionsTitle($url = null){
    $url = isset($url) ? $url : drupal_get_path_alias($_GET['q']);
    $widget = RequestUtil::getRequestKeyValueFromURL('widget', $url);
    $widget_titles = self::$widget_titles;

    //Transactions Page main title
    $title = isset($widget) ? $widget_titles[$widget]: "";
    $categoryName = self::getCategoryName();
    $title .= ' '. $categoryName . " Spending Transactions";

    return $title;
  }

  /**
   * @return null|string -- Returns Spending Category
   */
  static public function getCategoryName(){
    $categories = self::$categories;
    $category_id = RequestUtilities::get('category');
    return $categories[$category_id];
  }

  /**
   * @param $widget Widget Name
   * @param $bottomURL
   * @return null|string -- Returns Sub Tiltle for TYD Spending Transactions Details
   */
  static public function getTransactionsSubTitle($widget, $bottomURL){
    $widgetTitles = self::$widget_titles;
    $title = '<b>'.$widgetTitles[$widget].': </b>';

    switch($widget){
      case 'ytd_vendor':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('vendor', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("vendor_id", $reqParam);
        break;
      case 'ytd_contract':
        return null;
        break;
      case 'ytd_industry':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('industry', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("industry_type_id", $reqParam);
        break;
      case 'ytd_expense_category':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('exp_cat', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("expenditure_object_id", $reqParam);
        break;
      case 'ytd_funding_source':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('fundsrc', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("funding_source_id", $reqParam);
        break;
      case 'ytd_department':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('dept', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("department_id", $reqParam);
        break;
    }

    return $title;
  }

  /**
   * @param $widget Widget Name
   * @param $bottomURL
   * @return null|string -- widget title summary details including ytd amount and total contract amount
   */

  static public function getTransactionsTitleSummary($widget, $bottomURL){
    $results;
    switch($widget){
      case 'ytd_vendor':
        $vendor_id = RequestUtil::getRequestKeyValueFromURL('vendor', $bottomURL);
        $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
        if(isset($vendor_id)) {
          $query = "SELECT SUM(ytd_spending) AS check_amount_sum,  SUM(total_contract_spending) AS total_contract_amount_sum FROM aggregation_spending_fy
	                  WHERE (issue_date_year_id =".$year_id  ."AND vendor_id =". $vendor_id.")";
          $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);

        }
        break;
      case 'ytd_contract':
        $contractId = "'".RequestUtil::getRequestKeyValueFromURL('po_num_exact', $bottomURL)."'";
        $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
        if(isset($contractId)) {
          $query = "SELECT contract_id, contract_purpose, vendor_id, vendor_name, SUM(COALESCE(ytd_spending, 0)) AS check_amount_sum, MAX(COALESCE(total_contract_amount, 0)) AS total_contract_amount 
                    FROM aggregation_spending_contracts_fy
                    WHERE (issue_date_year_id =".$year_id ." AND contract_id=".$contractId . ")".
                    "GROUP BY contract_id, contract_purpose, vendor_name, vendor_id";
          $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
        }
        break;
      case 'ytd_industry':
        $industry_id = RequestUtil::getRequestKeyValueFromURL('industry', $bottomURL);
        $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
        if(isset($industry_id)) {
          $query = "SELECT industry_type_id AS industry_id, display_industry_type_name AS industry_name,SUM(ytd_spending) AS check_amount_sum
                    FROM aggregation_spending_fy
	                  WHERE (issue_date_year_id =".$year_id." AND industry_type_id =". $industry_id.")
	                  GROUP BY industry_type_id, display_industry_type_name";
          $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
          //return $results[0];
        }
        break;
      case 'ytd_funding_source':
        $fundsrc_id = RequestUtil::getRequestKeyValueFromURL('fundsrc', $bottomURL);
        $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
        if(isset($fundsrc_id)) {
          $query = "SELECT funding_source_id,display_funding_source_descr AS funding_source_name,SUM(ytd_spending) AS check_amount_sum
                    FROM aggregation_spending_fy
	                  WHERE (issue_date_year_id =".$year_id." AND funding_source_id =". $fundsrc_id.")
	                  GROUP BY funding_source_id, display_funding_source_descr";
          $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
        }
        break;
    }
    return $results[0];
  }

}
