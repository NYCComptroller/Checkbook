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

/**
 * Class NychaContractDetails
 */
class NychaContractDetails {

    /**
     * @param $node
     */
    public function getData(&$node){

      $contract_id = RequestUtilities::getRequestParamValue('contract');

        if (!ctype_alnum($contract_id)) {
            return;
        }

      $query = <<<SQL
            SELECT
              contract_id,
              agency_name,
              start_date,
              end_date,
              approved_date,
              approved_year,
              total_amount,
              original_amount,
              spend_to_date,
              amount_difference,
              award_size_id,
              award_size_name,
              revision_count,
              purpose,
              revision_number,
              revision_total_amount,
              revision_approved_date,
              agency_id,
              agency_code,
              agency_name,
              contract_type_id,
              contract_type_code,
              contract_type_descr,
              vendor_id,
              vendor_number,
              vendor_name,
              vendor_site_id,
              address_line1,
              address_line2,
              city,
              STATE,
              zip,
              industry_type_id,
              industry_type_code,
              industry_type_name,
              department_id,
              department_code,
              department_name,
              award_method_id,
              award_method_code,
              award_method_name,
              contracting_agency,
              commodity_category_id,
              commodity_category_code,
              commodity_category_descr,
              number_of_solicitations,
              response_to_solicitation
            FROM
              all_agreements
            WHERE
              (purchase_order_number = '{$contract_id}' OR contract_id='{$contract_id}')
SQL;
        $query2 =
            "SELECT
            contract_id, count(release_number) as associated_releases from all_agreement_transactions  where (contract_id = '{$contract_id}' ) group by contract_id";


  $latest_contract_sql = $query . " AND latest_flag = 'Y' LIMIT 1";

  $node->data = _checkbook_project_execute_sql_by_data_source($latest_contract_sql,'checkbook_nycha');

  $contract_history_query = $query . " ORDER BY revision_number DESC ";

  $node->contract_history = _checkbook_project_execute_sql_by_data_source($contract_history_query,'checkbook_nycha');
        $results3 = _checkbook_project_execute_sql_by_data_source($query2,'checkbook_nycha');
        $total_associated_releases= 0;
        foreach($results3 as $row){
            $total_associated_releases +=$row["associated_releases"];
        }
        $node->total_associated_releases = $total_associated_releases;


  }

}
