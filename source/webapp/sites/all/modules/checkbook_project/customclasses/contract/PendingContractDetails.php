<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/

class pendingContractDetails {

  //Returns pending contratcs data for the given node
  public function getData(&$node){


    $contract_num = _getRequestParamValue("contract");
    $version_num = _getRequestParamValue("version");

    $query1 = "SELECT
                vh.vendor_id,
                l1.fms_parent_contract_number AS parent_contract_number,
                l1.vendor_customer_code,
                l1.contract_number,
                l1.vendor_legal_name AS legal_name_checkbook_vendor,
                l1.vendor_id vendor_vendor,
                l1.description,
                l1.document_agency_name AS agency_name_checkbook_agency,
                l1.document_agency_id AS agency_id_checkbook_agency,
                l1.award_method_name AS award_method_name_checkbook_award_method,
                l1.document_version,
                l1.tracking_number,
                l1.board_award_number AS board_approved_award_no,
                l1.original_maximum_amount AS original_contract_amount,
                l1.revised_maximum_amount AS maximum_spending_limit,
                l444.document_code AS document_code_checkbook_ref_document_code,
                l1.start_date AS date_chckbk_dat_id_effctv_bgn_date_id_chckbk_hstr_mstr_agrmnt_0,
                l1.end_date AS date_chckbk_date_id_effctv_end_dat_id_chckbk_hstr_mstr_agrmnt_1
        FROM pending_contracts AS l1
        LEFT OUTER JOIN ref_document_code AS l444 ON l444.document_code_id = l1.document_code_id
        LEFT JOIN {vendor} v ON l1.vendor_id = v.vendor_id
        LEFT JOIN (SELECT vendor_id, MAX(vendor_history_id) AS vendor_history_id
                FROM {vendor_history} WHERE miscellaneous_vendor_flag::BIT = 0 ::BIT  GROUP BY 1) vh ON v.vendor_id = vh.vendor_id
        WHERE l1.contract_number = '" . $contract_num ."' AND document_version = ".$version_num;
    $results1 = _checkbook_project_execute_sql($query1);
    $node->data = $results1;

  }

}
