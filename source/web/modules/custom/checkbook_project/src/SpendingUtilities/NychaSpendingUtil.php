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

namespace Drupal\checkbook_project\SpendingUtilities;

use Drupal;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\NychaContractUtilities\NYCHAContractUtil;

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
  static $widget_titles = array('wt_checks' => 'Checks', 'ytd_check' => 'Check', 'wt_vendors' => 'Vendors', 'ytd_vendor' => 'Vendor',
    'wt_contracts' => 'Contracts', 'ytd_contract' => 'Contract', 'wt_expense_categories' => 'Expense Categories',
    'wt_resp_centers' => 'Responsibility Centers', 'ytd_expense_category' => 'Expense Category', 'wt_industries' => 'Industries', 'ytd_industry' => 'Industry',
    'wt_funding_sources' => 'Funding Sources', 'ytd_funding_source' => 'Funding Source', 'wt_departments' => 'Departments',
    'ytd_department' => 'Department', 'ytd_resp_center' => 'Responsibility Center');

  static $categories = array(3 => 'Contract', 2 => 'Payroll', 1 => 'Section 8', 4 => 'Other', null => 'Total');

  /**
   * @param $url
   * @return null|string -- Returns transactions title for NYCHA Spending
   */
  static public function getTransactionsTitle($url = null)
  {
    //$url = isset($url) ? $url : drupal_get_path_alias($_GET['q']);
    if (!isset($url)) {
      $current_path = Drupal::service('path.current')->getPath();
      $url = Drupal::service('path_alias.manager')->getAliasByPath($current_path);
    }
    $widget = RequestUtil::getRequestKeyValueFromURL('widget', $url);
    $tcode = RequestUtil::getRequestKeyValueFromURL('tcode', $url);
    $widget_titles = self::$widget_titles;

    //Transactions Page main title
    $title = isset($widget) ? $widget_titles[$widget] : "";
    $categoryName = self::getCategoryName();

    // Exception to remove Contracts displayed twice in the title
    if ((($widget == "wt_contracts") || ($widget == "ytd_contract")) && ($categoryName == "Contract")) {
      $categoryName = "";
    }
    $title .= ' ' . $categoryName . " Spending Transactions";
    if (strpos($widget, 'inv_') !== false) {
      $tcode_value = NYCHAContractUtil::getTitleByCode($tcode);
      $title = $tcode_value . " Spending Transactions";
    }

    return $title;
  }

  /**
   * @return null|string -- Returns Spending Category
   */
  static public function getCategoryName()
  {
    $categories = self::$categories;
    $category_id = RequestUtilities::getTransactionsParams('category');
    $category_id_inv = RequestUtilities::getTransactionsParams('category_inv');
    $category_id = isset($category_id) ? $category_id : $category_id_inv;
    return $categories[$category_id];
  }

  /**
   * @param $categoryName
   * @param $bottomURL
   * @return null|string -- Returns Total Spending amount for each category
   * Required to extract payroll check amount sum
   */
  static public function getTotalSpendingAmount($categoryName, $bottomURL)
  {
    $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
    $vendor_id = RequestUtil::getRequestKeyValueFromURL('vendor', $bottomURL);
    $vendor = isset($vendor_id) ? $vendor = " AND vendor_id = " . $vendor_id : "";
    $total_spending = 0;

    $query = 'SELECT display_spending_category_name,SUM(check_amount) AS check_amount_sum,
                SUM(adj_distribution_line_amount) AS invoice_amount_sum from all_disbursement_transactions
              WHERE issue_date_year_id = ' . $year_id . $vendor . 'group by display_spending_category_name';
    $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
    foreach ($results as $key => $row) {
      if ($row['display_spending_category_name'] == 'Payroll') {
        $row['invoice_amount_sum'] = $row['check_amount_sum'];
      }
      $total_spending += $row['invoice_amount_sum'];
    }
    //if ($categoryName == 'Payroll'){ $total_spending = $results['Payroll']['check_amount_sum'];}
    return $total_spending;
  }

  /**
   * @param $bottomURL bottom container URL
   * @return null|string -- Returns NYCHA amount spent for each year invoiced amount link
   *
   */
  static public function getAmountSpent($bottomURL)
  {
    $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
    $inv_contractID = RequestUtil::getRequestKeyValueFromURL('po_num_inv', $bottomURL);
    $inv_vendorID = RequestUtil::getRequestKeyValueFromURL('vendor_inv', $bottomURL);
    $inv_awdID = RequestUtil::getRequestKeyValueFromURL('awdmethod', $bottomURL);
    $inv_depID = RequestUtil::getRequestKeyValueFromURL('dept', $bottomURL);
    $inv_csizeID = RequestUtil::getRequestKeyValueFromURL('csize', $bottomURL);
    $inv_respID = RequestUtil::getRequestKeyValueFromURL('resp_center', $bottomURL);
    $inv_indID = RequestUtil::getRequestKeyValueFromURL('industry_inv', $bottomURL);
    // Include widget level filters
    $where_filter = [];
    $where_filter[] = isset($year_id) ? "issue_date_year_id <=" . $year_id : "";
    $where_filter[] = isset($inv_vendorID) ? " vendor_id = " . $inv_vendorID : "";
    $where_filter[] = isset($inv_awdID) ? " award_method_id = " . $inv_awdID : "";
    $where_filter[] = isset($inv_csizeID) ? " award_size_id = " . $inv_csizeID : "";
    $where_filter[] = isset($inv_indID) ? " industry_type_id = " . $inv_indID : "";
    $where_filter[] = isset($inv_contractID) ? " contract_id = '" . $inv_contractID . "'" : "";
    $where_filter[] = isset($inv_depID) ? " department_id = " . $inv_depID : "";
    $where_filter[] = isset($inv_respID) ? " responsibility_center_id = " . $inv_respID : "";

    $filter = (count($where_filter) > 0) ? implode(' AND ', array_filter($where_filter)) : "";
    $query = 'SELECT SUM(adj_distribution_line_amount) AS amount_spent FROM all_disbursement_transactions
               WHERE ' . $filter;
    $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
    return $results;
  }

  /**
   * @param $widget Widget Name
   * @param $bottomURL
   * @return null|string -- Returns Sub Title for TYD Spending Transactions Details
   */
  static public function getTransactionsSubTitle($widget, $bottomURL)
  {
    $widgetTitles = self::$widget_titles;
    $title = '<b>' . $widgetTitles[$widget] . '</b>: ';

    switch ($widget) {
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
        $reqParam = RequestUtil::getRequestKeyValueFromURL('expcategorycode', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("expenditure_type_code", $reqParam);
        break;
      case 'ytd_funding_source':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('fundsrc', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("funding_source_id", $reqParam);
        break;
      case 'ytd_department':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('dept_code', $bottomURL);
        $result = _checkbook_project_get_name_for_argument("department_code", $reqParam);
        $result = preg_replace("/unknown/", 'Unknown', $result);
        $title .= htmlentities($result);
        break;
      case 'ytd_resp_center':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('resp_center', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("responsibility_center_id", $reqParam);
        break;
    }

    return $title;
  }

  /**
   * @param $widget Widget Name
   * @param $bottomURL
   * @return null|string -- widget title summary details including ytd amount and total contract amount
   */

  static public function getTransactionsStaticSummary($widget, $bottomURL)
  {
    $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
    $cat_id = RequestUtil::getRequestKeyValueFromURL('category', $bottomURL);
    switch ($widget) {
      case 'ytd_vendor':
        $vendor_id = RequestUtil::getRequestKeyValueFromURL('vendor', $bottomURL);
        if (isset($cat_id)) {
          $spend_category = "spending_category_code=" . $cat_id;
        } else {
          $spend_category = "spending_category_code!='CONTRACT'";
        }
        if (isset($vendor_id)) {
          $query = "SELECT vendor_id,vendor_name ,
                    sum (sum_ytd_spending) as check_amount_sum,
                    sum (sum_total_contract_spending) as total_contract_amount_sum
                    FROM(
                    SELECT vendor_id,vendor_name,'' as contract_id,
                    sum(ytd_spending) as sum_ytd_spending,
                    sum(0) as sum_total_contract_spending
                    FROM aggregation_spending_fy
                    WHERE (issue_date_year_id =" . $year_id . " and vendor_id=" . $vendor_id . ") AND spending_category_code!='CONTRACT'
                    GROUP BY vendor_id,vendor_name
                    UNION
                    SELECT vendor_id,vendor_name,contract_id,
                    sum(ytd_spending) sum_ytd_spending,
                    max(Total_Contract_Amount) sum_total_contract_spending
                    FROM aggregation_spending_contracts_fy
                    WHERE (issue_date_year_id = " . $year_id . " and vendor_id=" . $vendor_id . ")
                    GROUP BY contract_id,vendor_id,vendor_name ) x
                    GROUP BY vendor_id,vendor_name";
          $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
        }
        break;
      case 'ytd_contract':
        $contractId = "'" . RequestUtil::getRequestKeyValueFromURL('po_num_exact', $bottomURL) . "'";
        if (isset($contractId)) {
          $query = "SELECT contract_id,
                           contract_purpose,
                           vendor_id,
                           vendor_name,
                           SUM(COALESCE(ytd_spending, 0)) AS check_amount_sum,
                           MAX(COALESCE(total_contract_amount, 0)) AS total_contract_amount_sum
                    FROM aggregation_spending_contracts_fy
                    WHERE (issue_date_year_id =" . $year_id . " AND contract_id=" . $contractId . ")" .
            "GROUP BY contract_id, contract_purpose, vendor_name, vendor_id";
          $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
        }
        break;

    }
    return $results[0];
  }

  /**
   * @param $widget Widget Name,issuedate
   * @param $bottomURL
   * @return null|string -- widget title summary details for issue date
   */

  static public function getTransactionsStaticSummaryIssueDate($widget, $bottomURL)
  {
    $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
    $category_id = RequestUtil::getRequestKeyValueFromURL('category_inv', $bottomURL);
    $issue_date = RequestUtil::getRequestKeyValueFromURL('issue_date', $bottomURL);
    $date_value = explode("~", $issue_date);
    $month = date("n", strtotime($date_value[0]));
    $category_param = ' AND spending_category_id =' . $category_id;
    $category_parameter = isset($category_id) ? $category_param : '';
    $query = "SELECT issue_date_year,month_name, SUM(total_spending) AS spent_amount
              FROM aggregation_spending_month  WHERE issue_date_year_id= " . $year_id . " AND month_number =" . $month . $category_parameter .
      " GROUP BY issue_date_year,month_name";
    $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
    return $results[0];
  }

}
