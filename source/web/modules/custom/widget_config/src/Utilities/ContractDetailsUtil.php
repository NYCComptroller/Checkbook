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

namespace Drupal\widget_config\Utilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;

class ContractDetailsUtil
{
  public static function getSubVendorStatus($contract_number) {
    $querySubVendorStatus = "SELECT ref_status.scntrc_status_name  AS contract_subvendor_status
                            FROM all_agreement_transactions l1
                            JOIN ref_subcontract_status ref_status on ref_status.scntrc_status = l1.scntrc_status
                            WHERE contract_number = '". $contract_number . "' AND latest_flag = 'Y' LIMIT 1";
    $results = _checkbook_project_execute_sql_by_data_source($querySubVendorStatus,Datasource::getCurrent());
    return $results[0]['contract_subvendor_status'];
  }
  public static function getSubVendorInfoQueryResult($contract_number) {
    $querySubVendorinfo = "SELECT SUM(maximum_contract_amount) AS total_current_amt, SUM(original_contract_amount) AS total_original_amt, SUM(rfed_amount) AS total_spent_todate
                            FROM {subcontract_details}
                            WHERE contract_number = '". $contract_number . "'
                            AND latest_flag = 'Y'
                            LIMIT 1";
    return _checkbook_project_execute_sql_by_data_source($querySubVendorinfo,Datasource::getCurrent());
  }
  public static function getSubVendorCount($contract_number) {
    $querySubVendorCount = "SELECT  COUNT(DISTINCT vendor_id) AS sub_vendor_count  FROM sub_agreement_snapshot
                            WHERE contract_number = '". $contract_number . "'
                            AND latest_flag = 'Y'
                            LIMIT 1";
    $result = _checkbook_project_execute_sql_by_data_source($querySubVendorCount,Datasource::getCurrent());
    return $result[0]['sub_vendor_count'];
  }

  public static function getVendorDetailsQueryResults($ag_id) {
    $minority_type_ids = implode(',', MappingUtil::$minority_type_category_map_multi_chart['M/WBE']);
    if(Datasource::getCurrent() != Datasource::OGE){
      $queryVendorDetails = "SELECT cvlmc.minority_type_id, fa.contract_number, rb.business_type_code, fa.agreement_id,fa.original_agreement_id,
                                fa.vendor_id, va.address_id, ve.legal_name AS vendor_name, a.address_line_1, a.address_line_2, a.city, a.state, a.zip, a.country,
                                (CASE WHEN cvlmc.minority_type_id IN (". $minority_type_ids .") THEN 'Yes' ELSE 'NO' END) AS mwbe_vendor,
                                (CASE WHEN cvlmc.minority_type_id IN (4,5,10) then 'Asian American' ELSE rm.minority_type_name END) AS ethnicity
	                        FROM agreement_snapshot fa
	                            LEFT JOIN vendor_history vh ON fa.vendor_history_id = vh.vendor_history_id
	                            LEFT JOIN vendor as ve ON ve.vendor_id = vh.vendor_id
	                            LEFT JOIN vendor_address va ON vh.vendor_history_id = va.vendor_history_id
	                            LEFT JOIN address a ON va.address_id = a.address_id
	                            LEFT JOIN ref_address_type ra ON va.address_type_id = ra.address_type_id
	                            LEFT JOIN vendor_business_type vb ON vh.vendor_history_id = vb.vendor_history_id
	                            LEFT JOIN ref_business_type rb ON vb.business_type_id = rb.business_type_id
	                            LEFT JOIN contract_vendor_latest_mwbe_category cvlmc ON cvlmc.vendor_id = fa.vendor_id
                                LEFT JOIN ref_minority_type rm ON cvlmc.minority_type_id = rm.minority_type_id
	                        WHERE ra.address_type_code = 'PR' AND fa.latest_flag = 'Y' AND cvlmc.latest_minority_flag ='Y' AND fa.original_agreement_id = " . $ag_id. "ORDER BY cvlmc.year_id DESC LIMIT 1";
    }else{
      $queryVendorDetails = "SELECT  fa.contract_number, rb.business_type_code, fa.agreement_id,fa.original_agreement_id,
                                  fa.vendor_id, va.address_id, ve.legal_name AS vendor_name, a.address_line_1, a.address_line_2, a.city, a.state, a.zip, a.country
	                       FROM agreement_snapshot fa
	                            LEFT JOIN vendor_history vh ON fa.vendor_history_id = vh.vendor_history_id
	                            LEFT JOIN vendor as ve ON ve.vendor_id = vh.vendor_id
	                            LEFT JOIN vendor_address va ON vh.vendor_history_id = va.vendor_history_id
	                            LEFT JOIN address a ON va.address_id = a.address_id
	                            LEFT JOIN ref_address_type ra ON va.address_type_id = ra.address_type_id
	                            LEFT JOIN vendor_business_type vb ON vh.vendor_history_id = vb.vendor_history_id
	                            LEFT JOIN ref_business_type rb ON vb.business_type_id = rb.business_type_id
	                            LEFT JOIN ref_minority_type rm ON vb.minority_type_id = rm.minority_type_id
	                       WHERE ra.address_type_code = 'PR' AND fa.latest_flag = 'Y' AND fa.original_agreement_id = " . $ag_id. "LIMIT 1";
    }
    return _checkbook_project_execute_sql_by_data_source($queryVendorDetails,Datasource::getCurrent());
  }

  public static function getVendorCountQueryResult($ag_id) {
    $queryVendorCount = " select count(*) total_contracts_sum from {agreement_snapshot} where vendor_id =
                        (select vendor_id from {agreement_snapshot} where original_agreement_id =". $ag_id . "limit 1)
                           and latest_flag = 'Y'";
    return _checkbook_project_execute_sql_by_data_source($queryVendorCount,Datasource::getCurrent());
  }
}
