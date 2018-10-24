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

class pendingContractDetails {

  public function getData(&$node){


    $contract_num = RequestUtilities::get("contract");
    $version_num = RequestUtilities::get("version");

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

      $parent_contract_number = $node->data[0]['parent_contract_number'];
      if(!empty($parent_contract_number)){
          $mag_details = _get_master_agreement_details_by_parent_contract_number($parent_contract_number);
          $node->original_master_agreement_id = $mag_details['original_master_agreement_id'];
          $node->contract_number = $mag_details['contract_number'];
          $node->document_code = $mag_details['document_code@checkbook:ref_document_code'];
          $node->contract_number = $parent_contract_number;
      }

  }

}
