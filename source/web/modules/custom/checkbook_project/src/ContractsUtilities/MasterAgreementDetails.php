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

namespace Drupal\checkbook_project\ContractsUtilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

class MasterAgreementDetails {

  public function getData(&$node){
    $mag_id = RequestUtilities::_getRequestParamValueBottomURL('magid');
    $mag_id = $mag_id ?? RequestUtilities::get('magid');
    $datasource = RequestUtilities::get('datasource');
    if(!isset($datasource)){
      $contract_class_where =", rcc.contract_class_description ";
      $contract_class_join = " LEFT OUTER JOIN {ref_contract_class} AS rcc ON l1.contract_class_code = rcc.contract_class_code ";
    }

    $query1 = "SELECT l1.contract_number,
    l2.vendor_id AS vendor_id_checkbook_vendor_history,
    l3.legal_name AS legal_name_checkbook_vendor,
    l1.description,
    l5.agency_name AS agency_name_checkbook_agency,
    l5.agency_id AS agency_id_checkbook_agency,
    l656.award_method_name AS award_method_name_checkbook_award_method,
    l1.oca_number,
    l1.document_version,
    l1.tracking_number,
    l1.number_responses,
    l1.number_solicitation,
    l1.maximum_spending_limit,
    l1.board_approved_award_no,
    l1.original_contract_amount,
    l444.document_code AS document_code_checkbook_ref_document_code,
    l1040.date AS date_chckbk_dat_id_effctv_bgn_date_id_chckbk_hstr_mstr_agrmnt_0,
    l1124.date AS date_chckbk_date_id_effctv_end_dat_id_chckbk_hstr_mstr_agrmnt_1,
    l1208.date AS date_chckbk_date_id_rgstrd_date_id_chckbk_histr_master_agrmnt_2,
    rat.agreement_type_name".$contract_class_where."
    FROM {history_master_agreement} AS l1
    LEFT OUTER JOIN {vendor_history} AS l2 ON l2.vendor_history_id = l1.vendor_history_id
    LEFT OUTER JOIN {vendor} AS l3 ON l3.vendor_id = l2.vendor_id
    LEFT OUTER JOIN {ref_agency_history} AS l4 ON l4.agency_history_id = l1.agency_history_id
    LEFT OUTER JOIN {ref_agency} AS l5 ON l5.agency_id = l4.agency_id
    LEFT OUTER JOIN {ref_document_code} AS l444 ON l444.document_code_id = l1.document_code_id
    LEFT OUTER JOIN {ref_award_method} AS l656 ON l656.award_method_id = l1.award_method_id
    LEFT OUTER JOIN {ref_date} AS l1040 ON l1040.date_id = l1.effective_begin_date_id
    LEFT OUTER JOIN {ref_date} AS l1124 ON l1124.date_id = l1.effective_end_date_id
    LEFT OUTER JOIN {ref_date} AS l1208 ON l1208.date_id = l1.registered_date_id
    LEFT OUTER JOIN {ref_agreement_type} AS rat ON l1.agreement_type_id = rat.agreement_type_id".
      $contract_class_join."
    WHERE l1.original_master_agreement_id = " . $mag_id . "
    AND l1.latest_flag = 'Y'
    ";

    $results1 = _checkbook_project_execute_sql_by_data_source($query1);
    $node->data = $results1;


    if(Datasource::getCurrent() == Datasource::CITYWIDE){
    	$query2 = "select rfed_amount from {agreement_snapshot} where original_agreement_id = " .$mag_id . "
     		and master_agreement_yn = 'Y'  and latest_flag = 'Y'"  ;

	    $results2 = _checkbook_project_execute_sql_by_data_source($query2);
	    $spent_amount = 0;
	    foreach ($results2 as $row) {
	      $spent_amount += $row["rfed_amount"];
	    }
	    $node->spent_amount = $spent_amount ;
	    $node->original_contract_amount = $node->data[0]['original_contract_amount'] ;
	    $node->maximum_spending_limit = $node->data[0]['maximum_spending_limit'] ;

	    $query3 = "SELECT COUNT(*) AS total_child_contracts
	    FROM {history_agreement}
	    WHERE master_agreement_id = " . $mag_id . "
	    AND latest_flag = 'Y'";

	    $results3 = _checkbook_project_execute_sql_by_data_source($query3);
	    $total_child_contracts = 0;
	    foreach($results3 as $row){
	    	$total_child_contracts +=$row["total_child_contracts"];
	    }
	    $node->total_child_contracts = $total_child_contracts;
    }else{
    	$query2 = "select sum(original_amount) original_amount, sum(current_amount) current_amount,
    			count(distinct fms_contract_number) as num_associated_contracts, sum(check_amount) as spent_amount
				FROM {oge_contract_vendor_level} a
				JOIN (select distinct contract_number from {history_agreement} where master_agreement_id = " . $mag_id . ") b
				ON a.fms_contract_number = b.contract_number
				LEFT JOIN (SELECT sum(check_amount) as check_amount, contract_number, vendor_id FROM {disbursement_line_item_details} group by 2,3) c
				ON b.contract_number = c.contract_number AND a.vendor_id = c.vendor_id limit 1"  ;

    	$results2 = _checkbook_project_execute_sql_by_data_source($query2,Datasource::getCurrent());
    	foreach ($results2 as $row) {
    		$node->spent_amount = $row['spent_amount'] ;
    		$node->original_contract_amount = $row['original_amount'];
    		$node->maximum_spending_limit = $row['current_amount'] ;
    		$node->total_child_contracts = $row['num_associated_contracts'];
    	}
        $node->data_source_amounts_differ = ContractUtil::masterAgreementAmountsDiffer($mag_id);
    }

  }

  /**
   * return helper function to get master agreement id details
   * @param $magid
   * @return array
   */
  public static function _get_master_agreement_details($magid){
    if (!isset($magid)) {
      return NULL;
    }
    $results = get_db_results(true, 'checkbook:history_master_agreement', array("document_code@checkbook:ref_document_code", "contract_number"), array("master_agreement_id" => $magid), NULL, 0, 1);
    return $results[0] ?? NULL;
  }

  /**
   * Given the parent contract number, returns the details of the contract
   * @param $parent_contract_number
   * @return null
   */
  public static function _get_master_agreement_details_by_parent_contract_number($parent_contract_number){
    if (!isset($parent_contract_number)) {
      return NULL;
    }
    $results = get_db_results(true, 'checkbook:history_master_agreement', array("document_code@checkbook:ref_document_code", "master_agreement_id", "original_master_agreement_id"), array("contract_number" => $parent_contract_number, "latest_flag" => "Y"), NULL, 0, 1);
    return $results[0] ?? NULL;
  }

}
