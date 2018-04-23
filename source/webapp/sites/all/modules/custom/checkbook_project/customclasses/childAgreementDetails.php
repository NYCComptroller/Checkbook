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

require_once(realpath(drupal_get_path('module', 'checkbook_project')) .'/customclasses/contract/ContractUtil.php');

class childAgreementDetails {

  public function getData(&$node){

    $ag_id = RequestUtilities::getRequestParamValue("agid");

    $query1 = "SELECT l1.contract_number, a.master_contract_number,
           l2.vendor_id AS vendor_id_checkbook_vendor_history,
           l529.legal_name AS legal_name_checkbook_vendor,
           l1.description,
           l531.agency_name AS agency_name_checkbook_agency,
           l531.agency_id AS agency_id_checkbook_agency,
           l1071.award_method_name AS award_method_name_checkbook_award_method,
           l1.document_version,
           l1.tracking_number,
           l1.number_responses,
           l1.number_solicitation,
           l1.maximum_contract_amount,
           l1.brd_awd_no,
           l1.original_contract_amount,
           l903.document_code AS document_code_checkbook_ref_document_code,
           l1237.date AS date_chckbk_date_id_effctv_begin_date_id_chckbk_histor_agrmnt_0,
           l1318.date AS date_checkbk_date_id_effctv_end_date_id_chckbk_history_agrmnt_1,
           l1399.date AS date_chckbk_date_id_rgstrd_date_id_checkbook_history_agreemnt_2,
           rat.agreement_type_name
      FROM history_agreement AS l1
           LEFT OUTER JOIN agreement_snapshot AS a ON l1.master_agreement_id = a.master_agreement_id
           LEFT OUTER JOIN vendor_history AS l2 ON l2.vendor_history_id = l1.vendor_history_id
           LEFT OUTER JOIN vendor AS l529 ON l529.vendor_id = l2.vendor_id
           LEFT OUTER JOIN ref_agency_history AS l530 ON l530.agency_history_id = l1.agency_history_id
           LEFT OUTER JOIN ref_agency AS l531 ON l531.agency_id = l530.agency_id
           LEFT OUTER JOIN ref_document_code AS l903 ON l903.document_code_id = l1.document_code_id
           LEFT OUTER JOIN ref_award_method AS l1071 ON l1071.award_method_id = l1.award_method_id
           LEFT OUTER JOIN ref_date AS l1237 ON l1237.date_id = l1.effective_begin_date_id
           LEFT OUTER JOIN ref_date AS l1318 ON l1318.date_id = l1.effective_end_date_id
           LEFT OUTER JOIN ref_date AS l1399 ON l1399.date_id = l1.registered_date_id
           LEFT OUTER JOIN {ref_agreement_type} AS rat ON l1.agreement_type_id = rat.agreement_type_id
     WHERE l1.original_agreement_id = " . $ag_id . "
       AND l1.latest_flag = 'Y'
    ";
    $query2 = "select rfed_amount from history_agreement where original_agreement_id = " .$ag_id . " and latest_flag = 'Y' limit 1";



    $results1 = _checkbook_project_execute_sql_by_data_source($query1,_get_current_datasource());
    $node->data = $results1;
    $magid = _get_master_agreement_id();
    if(!empty($magid)){
    	$magdetails = _get_master_agreement_details($magid);
    	$node->magid = $magid;
    	$node->document_code = $magdetails['document_code@checkbook:ref_document_code'];
    	$node->contract_number = $magdetails['contract_number'];
    }
    if(_get_current_datasource() ==_get_default_datasource() ){
	    $results2 = _checkbook_project_execute_sql_by_data_source($query2,_get_current_datasource());
	    $spent_amount = 0;
	    foreach($results2 as $row){
	      $spent_amount +=$row["rfed_amount"];
	    }
	    $node->spent_amount = $spent_amount ;


	    $query3 = "SELECT COUNT(*) AS total_child_contracts
	    FROM {history_agreement}
	   WHERE master_agreement_id = " . $magid . "
	     AND latest_flag = 'Y'";

	    $results3 = _checkbook_project_execute_sql($query3);
	    $total_child_contracts = 0;
	    foreach($results3 as $row){
	      $total_child_contracts +=$row["total_child_contracts"];
	    }
	    $node->total_child_contracts = $total_child_contracts;
    }else{
    	$query2 = "select sum(original_amount) original_amount, sum(current_amount) current_amount,
    			 sum(check_amount) as spent_amount
				FROM {oge_contract_vendor_level} a
				JOIN (select distinct contract_number from {history_agreement} where agreement_id = " . $ag_id . ") b
				ON a.fms_contract_number = b.contract_number
				LEFT JOIN (SELECT sum(check_amount) as check_amount, contract_number, vendor_id FROM {disbursement_line_item_details} group by 2,3) c
				ON b.contract_number = c.contract_number AND a.vendor_id = c.vendor_id limit 1"  ;

    	$results2 = _checkbook_project_execute_sql_by_data_source($query2,_get_current_datasource());
    	foreach ($results2 as $row) {
    		$node->spent_amount = $row['spent_amount'] ;
    		$node->original_contract_amount = $row['original_amount'];
    		$node->maximum_contract_amount = $row['current_amount'] ;
    		$node->total_child_contracts = $row['num_associated_contracts'];
    	}

        $node->data_source_amounts_differ = ContractUtil::childAgreementAmountsDiffer($ag_id);
    }
  }

}
