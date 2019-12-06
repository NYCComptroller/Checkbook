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
    $tcode = RequestUtil::getRequestKeyValueFromURL('tcode', $url);
    $widget_titles = self::$widget_titles;

    //Transactions Page main title
    $title = isset($widget) ? $widget_titles[$widget]: "";
    $categoryName = self::getCategoryName();
    $title .= ' '. $categoryName . " Spending Transactions";
    if (strpos($widget, 'inv_') !== false) {
      $tcode_value = NYCHAContractUtil::getTitleByCode($tcode);
      $title = $tcode_value." Spending Transactions";
    }

    return $title;
  }

  /**
   * @return null|string -- Returns Spending Category
   */
  static public function getCategoryName(){
    $categories = self::$categories;
    $category_id = RequestUtilities::get('category');
    $category_id = RequestUtilities::get('category_inv');
    return $categories[$category_id];
  }
  /**
   * @return null|string -- Returns Total Spending amount for each category
   * Required to extract payroll check amount sum
   */
  static public function getTotalSpendingAmount($categoryName,$bottomURL){
    $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
    $query =  'SELECT display_spending_category_name,SUM(check_amount) AS check_amount_sum ,SUM(invoice_net_amount) AS invoice_amount_sum from all_disbursement_transactions
              where  issue_date_year_id = '. $year_id .'group by display_spending_category_name';
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
   * @return null|string -- Returns NYCHA amount spent for each year invoiced amount link
   *
   */
  static public function getAmountSpent($bottomURL){
    $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
    $inv_contractID = "'".RequestUtil::getRequestKeyValueFromURL('po_num_inv', $bottomURL)."'";
    $inv_vendorID = RequestUtil::getRequestKeyValueFromURL('vendor_inv', $bottomURL);
    $inv_awdID = RequestUtil::getRequestKeyValueFromURL('awdmethod', $bottomURL);
    $inv_depID = RequestUtil::getRequestKeyValueFromURL('dept', $bottomURL);
    $inv_csizeID = RequestUtil::getRequestKeyValueFromURL('csize', $bottomURL);
    $inv_respID = RequestUtil::getRequestKeyValueFromURL('resp_center', $bottomURL);
    $inv_indID = RequestUtil::getRequestKeyValueFromURL('industry_inv', $bottomURL);
    if(isset($year_id)){
      $year_filter = "issue_date_year_id <=".$year_id . " AND ";
    }
    if (isset($inv_contractID) || isset($contractID)) {
      $reqParam = $inv_contractID;
      $query_param = "contract_id";
    }
    if (isset($inv_vendorID)) {
        $reqParam = $inv_vendorID;
        $query_param = "vendor_id";
    }
    if (isset($inv_awdID)) {
      $reqParam = $inv_awdID;
      $query_param = "award_method_id";
    }
    if (isset($inv_depID)) {
      $reqParam = $inv_depID;
      $query_param = "department_id";
    }
    if (isset($inv_csizeID)) {
      $reqParam = $inv_csizeID;
      $query_param = "award_size_id";
    }
    if (isset($inv_respID)) {
      $reqParam = $inv_respID;
      $query_param = "responsibility_center_id";
    }
    if (isset($inv_indID)) {
      $reqParam = $inv_indID;
      $query_param = "industry_type_id";
    }
    $year_filter = isset($year_id) ? $year_filter : '';
    $query =  'SELECT '. $query_param .' ,sum(invoice_net_amount) AS amount_spent from all_disbursement_transactions
               where '. $year_filter . $query_param.'='.$reqParam .' GROUP BY '. $query_param ;
    $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
    return $results;
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
        $reqParam = RequestUtil::getRequestKeyValueFromURL('vendor_inv', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("vendor_id", $reqParam);
        break;
      case 'ytd_contract':
        return null;
        break;
      case 'ytd_industry':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('industry_inv', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("industry_type_id", $reqParam);
        break;
      case 'ytd_expense_category':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('exp_cat_inv', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("expenditure_type_id", $reqParam);
        break;
      case 'ytd_funding_source':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('fundsrc_inv', $bottomURL);
        $title .= _checkbook_project_get_name_for_argument("funding_source_id", $reqParam);
        break;
      case 'ytd_department':
        $reqParam = RequestUtil::getRequestKeyValueFromURL('dept_inv', $bottomURL);
        $result = _checkbook_project_get_name_for_argument("department_id", $reqParam);
        $result = preg_replace("/unknown/", 'Unknown', $result);
        $title .= htmlentities($result);
        break;
    }

    return $title;
  }

  /**
   * @param $widget Widget Name
   * @param $bottomURL
   * @return null|string -- widget title summary details including ytd amount and total contract amount
   */

  static public function getTransactionsStaticSummary($widget, $bottomURL){
    $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
    $cat_id = RequestUtil::getRequestKeyValueFromURL('category_inv', $bottomURL);
    switch($widget){
      case 'ytd_vendor':
        $vendor_id = RequestUtil::getRequestKeyValueFromURL('vendor_inv', $bottomURL);
        if (isset($cat_id)){ $spend_category = "spending_category_code=".$cat_id; }
        else{$spend_category = "spending_category_code!='CONTRACT'"; }
        if(isset($vendor_id)) {
          $query = "SELECT vendor_id,vendor_name ,
                    sum (sum_ytd_spending) as check_amount_sum,
                    sum (sum_total_contract_spending) as total_contract_amount_sum
                    FROM(
                    SELECT vendor_id,vendor_name,'' as contract_id,
                    sum(ytd_spending) as sum_ytd_spending,
                    sum(0) as sum_total_contract_spending
                    FROM aggregation_spending_fy
                    WHERE (issue_date_year_id =". $year_id ." and vendor_id=".$vendor_id.") AND spending_category_code!='CONTRACT'
                    GROUP BY vendor_id,vendor_name
                    UNION
                    SELECT vendor_id,vendor_name,contract_id,
                    sum(ytd_spending) sum_ytd_spending,
                    max(Total_Contract_Amount) sum_total_contract_spending
                    FROM aggregation_spending_contracts_fy
                    WHERE (issue_date_year_id = ". $year_id ." and vendor_id=".$vendor_id.")
                    GROUP BY contract_id,vendor_id,vendor_name ) x
                    GROUP BY vendor_id,vendor_name";
          $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
        }
        break;
      case 'ytd_contract':
        $contractId = "'".RequestUtil::getRequestKeyValueFromURL('po_num', $bottomURL)."'";
        if(isset($contractId)) {
          $query = "SELECT contract_id, 
                           contract_purpose, 
                           vendor_id, 
                           vendor_name, 
                           SUM(COALESCE(ytd_spending, 0)) AS check_amount_sum, 
                           MAX(COALESCE(total_contract_amount, 0)) AS total_contract_amount_sum
                    FROM aggregation_spending_contracts_fy
                    WHERE (issue_date_year_id =".$year_id ." AND contract_id=".$contractId . ")".
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

  static public function getTransactionsStaticSummaryIssueDate($widget, $bottomURL){
    $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
    $category_id = RequestUtil::getRequestKeyValueFromURL('category_inv', $bottomURL);
    $issue_date =  RequestUtil::getRequestKeyValueFromURL('issue_date', $bottomURL);
    $date_value = explode("~",$issue_date);
    $month = date("n",strtotime($date_value[0]));
    $category_param = ' AND spending_category_id ='.$category_id;
    $category_parameter = isset($category_id) ? $category_param : '';
    $query = "SELECT issue_date_year,month_name, SUM(total_spending) AS spent_amount 
              FROM aggregation_spending_month  WHERE issue_date_year_id= ".$year_id." AND month_number =".$month.$category_parameter.
              " GROUP BY issue_date_year,month_name";
    $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
    return $results[0];
  }
  /**
   * @param $widget Widget Name
   * @param $bottomURL
   * @return null|string -- widget title summary details for invoice amount links from NYCHA contracts landing page
   */
  static public function getContractsTransactionsStaticSummary($widget, $bottomURL)
  {
      $inv_contractID = "'".RequestUtil::getRequestKeyValueFromURL('po_num_inv', $bottomURL)."'";
      $inv_vendorID = RequestUtil::getRequestKeyValueFromURL('vendor_inv', $bottomURL);
      $inv_awdID = RequestUtil::getRequestKeyValueFromURL('awdmethod', $bottomURL);
      $inv_depID = RequestUtil::getRequestKeyValueFromURL('dept', $bottomURL);
      $inv_csizeID = RequestUtil::getRequestKeyValueFromURL('csize', $bottomURL);
      $inv_respID = RequestUtil::getRequestKeyValueFromURL('resp_center', $bottomURL);
      $inv_indID = RequestUtil::getRequestKeyValueFromURL('industry_inv', $bottomURL);
      $inv_tcode = RequestUtil::getRequestKeyValueFromURL('tcode', $bottomURL);

      // Include widget level filters
      $where_filter=[];
      if (isset ($inv_vendorID)){ $where_filter[]= "vendor_id = ".$inv_vendorID ;}
      if (isset ($inv_awdID)){ $where_filter[] = "award_method_id = ".$inv_awdID ;}
      if (isset ($inv_csizeID)){ $where_filter[] = "award_size_id = ".$inv_csizeID ;}
      if (isset ($inv_indID)){ $where_filter[] = "industry_type_id = ".$inv_indID ;}
        if(count($where_filter) > 0){
          $filter = implode(' AND ' , $where_filter);
        }

        // Set aggreement id values for contract widgets transactions
      if (isset($inv_tcode) && ($inv_tcode == 'BA' || $inv_tcode == "BAM")){$agreement_type_id = 1;}
      if (isset($inv_tcode) && ($inv_tcode == 'PA' || $inv_tcode == "PAM")){$agreement_type_id = 2;}
      if (isset($inv_tcode) && $inv_tcode == 'PO'){$agreement_type_id = 3;}
      if (isset($inv_tcode) && ($inv_tcode == "BAM" || $inv_tcode == "PAM")){$sub_query = " HAVING  MAX(total_amount-original_amount)!= 0 ";}
      $sub_query = isset($sub_query) ? $sub_query : '';

      // Specific to spending transactions for contact id from contract id details page
      $year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
      if(isset($year_id)){
      $year_filter = $year_id . " BETWEEN start_year_id AND  end_year_id AND";
      }
      $year_filter = isset($year_id) ? $year_filter : '';
      if ($inv_tcode == 'BA' || $inv_tcode == 'BAM' || $inv_tcode == 'PA'|| $inv_tcode == 'PAM'|| $inv_tcode == 'PO') {
        if (isset($inv_contractID)) {
          $query = "SELECT contract_id, purpose, vendor_name, vendor_id,
                MAX(total_amount) AS total_amount,
                MAX(original_amount) AS original_amount,
                MAX(spend_to_date) AS spend_to_date,
                MAX(total_amount-original_amount) AS dollar_difference,
                ROUND( CASE COALESCE( MAX(total_amount), 0 :: NUMERIC ) WHEN 0 THEN -100 :: NUMERIC ELSE
                (MAX(total_amount-original_amount) / MAX(total_amount) )* 100 END, 2) AS percent_difference
                FROM contracts_widget_summary
                WHERE (" . $year_filter . " agreement_type_id =" . $agreement_type_id . " AND contract_id=" . $inv_contractID .
            ")GROUP BY  contract_id, purpose, vendor_name, vendor_id" . $sub_query;
          $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
        }
      }
      if ($inv_tcode == "RESC") {
        if (isset($filter)){$filter = $filter." AND ";}
        //print $inv_vendorID;
        if (isset($inv_respID)) {
          if (isset($inv_vendorID)){$filter = "vendor_id =".$inv_vendorID ." AND "; }
          $query = "SELECT responsibility_center_id, responsibility_center_code, responsibility_center_name as responsibility_center_descr,
                 count(distinct contract_id) as contract_count,
                 sum(total_amount) as total_amount,
                 sum(original_amount) as original_amount,
                 sum(spend_to_date) as spend_to_date
                 from ( select
                 responsibility_center_id, responsibility_center_code, responsibility_center_name, contract_id,
                 sum(line_total_amount) as total_amount,
                 sum(line_original_amount) as original_amount,
                 sum(line_spend_to_date) as spend_to_date
                 from release_widget_summary
                 WHERE (".$filter . " release_approved_year_id = " . $year_id . " AND responsibility_center_code IS NOT NULL AND responsibility_center_id = " . $inv_respID . " )
                 group by responsibility_center_id, responsibility_center_code, responsibility_center_name, contract_id ) a
                 group by responsibility_center_id, responsibility_center_code, responsibility_center_descr";
          $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
        }
      }
      if ($inv_tcode == 'VO' || $inv_tcode == 'IND' || $inv_tcode == 'AWD'|| $inv_tcode == 'IND'|| $inv_tcode == 'DEP' || $inv_tcode == 'SZ') {
     if ($inv_tcode == 'IND' && isset($inv_indID)){
         $query_val1 = " industry_type_id, display_industry_type_name ";
     }
     if ($inv_tcode == 'VO' && isset($inv_vendorID)) {
        $query_val1 = " vendor_id, vendor_name ";
     }
      if ($inv_tcode == 'AWD' && isset($inv_awdID)) {
        $query_val1 = " award_method_id, award_method_name ";
      }
      if ($inv_tcode == 'DEP' && isset($inv_depID)) {
        $query_val1 = "  department_id, department_name ";
        if(isset($filter)){$dep_id = "AND department_id = ".$inv_depID;}
        else {$dep_id = "department_id = ".$inv_depID;}
        $dep_id = isset($inv_depID) ? $dep_id : '';
      }
      if ($inv_tcode == 'SZ' && isset($inv_csizeID)) {
        $query_val1 = "  award_size_id, award_size_name ";
      }
      $query = "SELECT " . $query_val1 .
        " ,count(distinct contract_id) as purchase_order_count,
                  sum(total_amount) as total_amount,
                  sum(original_amount) as original_amount,
                  sum(spend_to_date) as spend_to_date
                  from (
                  SELECT " . $query_val1 .
        " , contract_id ,max(total_amount) as total_amount,
                  max(original_amount) as original_amount,
                  max(spend_to_date) as spend_to_date
                  from contracts_widget_summary WHERE (" . $year_id . " BETWEEN  start_year_id AND  end_year_id AND "
                  .$filter.$dep_id.")
                  group by " . $query_val1 . " , contract_id) a group by " . $query_val1;
      $results = _checkbook_project_execute_sql_by_data_source($query, Datasource::NYCHA);
    }
    return $results[0];
  }
}


