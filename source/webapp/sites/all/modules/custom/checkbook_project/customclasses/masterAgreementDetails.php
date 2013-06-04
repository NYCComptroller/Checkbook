<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
 
class masterAgreementDetails {
  
  public function getData(&$node){
    
    
    $mag_id = _getRequestParamValue("magid");
    
    $query1 = "SELECT l1.contract_number,
    l2.vendor_id AS vendor_id_checkbook_vendor_history,
    l3.legal_name AS legal_name_checkbook_vendor,
    l1.description,
    l5.agency_name AS agency_name_checkbook_agency,
    l5.agency_id AS agency_id_checkbook_agency,
    l656.award_method_name AS award_method_name_checkbook_award_method,
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
    rat.agreement_type_name
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
    LEFT OUTER JOIN {ref_agreement_type} AS rat ON l1.agreement_type_id = rat.agreement_type_id
    WHERE l1.original_master_agreement_id = " . $mag_id . "
    AND l1.latest_flag = 'Y'
    ";
    $query2 = "select rfed_amount from {agreement_snapshot_expanded} where original_agreement_id = " .$mag_id . " 
     and master_agreement_yn = 'Y'  and status_flag = 'A'  order by fiscal_year  desc  limit 1"  ;
    
    $results1 = _checkbook_project_execute_sql($query1);
    $node->data = $results1;
    
    $results2 = _checkbook_project_execute_sql($query2);
    $spent_amount = 0;
    foreach ($results2 as $row) {
      $spent_amount += $row["rfed_amount"];
    }
    $node->spent_amount = $spent_amount ;
    
    $query3 = "SELECT COUNT(*) AS total_child_contracts
    FROM {history_agreement}
    WHERE master_agreement_id = " . $mag_id . "
    AND latest_flag = 'Y'";
    
    $results3 = _checkbook_project_execute_sql($query3);
    $total_child_contracts = 0;
    foreach($results3 as $row){
      $total_child_contracts +=$row["total_child_contracts"];
    }
    $node->total_child_contracts = $total_child_contracts;
    
  }
  
}
