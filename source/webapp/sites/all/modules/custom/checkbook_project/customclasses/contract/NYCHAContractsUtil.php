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

class NYCHAContractUtil
{
    static function adjustYearParams(&$node, &$parameters) {
        if(isset($parameters['release_year_id'])){
            $year_id = $parameters['release_year_id'];
            $data_controller_instance = data_controller_get_operator_factory_instance();
            $parameters['agreement_start_year_id'] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, $year_id);
            $parameters['agreement_end_year_id'] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, $year_id);
            unset($parameters['release_year_id']);
        }
        return $parameters;
    }

    /**
     * @param $tcode String -- Widget Key
     * @return string -- Returns Widget Title for the given key
     */
    public static function getTitleByCode($tcode){
      $summaryTitle='';
      switch($tcode){
        case 'BA':
          $summaryTitle = 'Blanket Agreements';
          break;
        case 'BAM':
          $summaryTitle='Blanket Agreement Modifications';
          break;
        case 'PA':
          $summaryTitle='Planned Agreements';
          break;
        case 'PAM':
          $summaryTitle='Planned Agreement Modifications';
          break;
        case 'PO':
          $summaryTitle='Purchase Orders';
          break;
        case 'VO':
          $summaryTitle='Vendors';
          break;
        case 'AWD':
          $summaryTitle='Award Methods';
          break;
        case 'IND':
          $summaryTitle='Contracts by Industries';
          break;
        case 'RESC':
          $summaryTitle='Responsibility Centers';
          break;
        case 'DEP':
          $summaryTitle='Departments';
          break;
        case 'SZ':
          $summaryTitle='Contracts by Size';
          break;
      }
      return $summaryTitle;
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
    if (isset ($inv_vendorID)){ $where_filter[]= " vendor_id = ".$inv_vendorID ;}
    if (isset ($inv_awdID)){ $where_filter[] = " award_method_id = ".$inv_awdID ;}
    if (isset ($inv_csizeID)){ $where_filter[] = " award_size_id = ".$inv_csizeID ;}
    if (isset ($inv_indID)){ $where_filter[] = " industry_type_id = ".$inv_indID ;}
    if(count($where_filter) > 0){
      $filter = implode(' AND ' , $where_filter);
    }

    // Set agreement id values for contract widgets transactions
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
