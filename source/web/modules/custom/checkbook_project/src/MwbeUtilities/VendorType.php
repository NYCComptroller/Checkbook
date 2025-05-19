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

namespace Drupal\checkbook_project\MwbeUtilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;

class VendorType
{
  public static $PRIME_VENDOR = 'P';
  public static $SUB_VENDOR = 'S';
  /**
   * @var array
   */
  private static $vendor_type_value_map = array(
    'pv' => 'P~PM',
    'sv' => 'S~SM',
    'mv' => 'SM~PM',
  );

  /**
   * @var array
   */
  private static $vendor_type_name_map = array(
    'sv' => 'Sub Vendor',
    'pv' => 'Prime Vendor',
    'mv' => 'M/WBE Vendor',
  );


  /**
   * @param $vendor_type_name_id
   * @return string|null
   */
  public static function getMixedVendorTypeNames($vendor_type_name_id): ?string
  {
    switch ($vendor_type_name_id) {
      case 'P~PM':
        return "PRIME VENDOR";
      case 'S~SM':
        return "SUB VENDOR";
      case 'PM~SM':
        return "M/WBE VENDOR";
      default:
        return null;
    }
  }

  /** Returns the vendor type value based on the vendor_type mapping
   * @param $vendor_types
   * @return array
   */
  public static function getVendorTypeValue($vendor_types): array
  {
    $param = "";
    foreach ($vendor_types as $key => $value) {
      $param .= self::$vendor_type_value_map[$value] . '~';
    }
    return explode('~', substr($param, 0, -1));
  }

  /** Returns the vendor type name based on the vendor_type mapping
   * @param $vendor_type
   * @return mixed
   */
  public static function getVendorTypeName($vendor_type)
  {
    return self::$vendor_type_name_map[$vendor_type];
  }

  /**
   * @param $nodeData
   * @param $param
   * @return array
   */
  public static function getVendorTypes($nodeData, $param): array
  {
    $unchecked = array();
    $checked = array();
    $params = explode('~', $param);
    $vendor_counts = array();
    if (is_array($nodeData)) {
      foreach ($nodeData as $row) {
        if (in_array($row[0], array('P', 'PM'))) {
          $vendor_counts['pv'] = $vendor_counts['pv'] + $row[2];
        }
        if (in_array($row[0], array('S', 'SM'))) {
          $vendor_counts['sv'] = $vendor_counts['sv'] + $row[2];
        }
        if (in_array($row[0], array('PM', 'SM'))) {
          $vendor_counts['mv'] = $vendor_counts['mv'] + $row[2];
        }
      }
    }
    foreach ($vendor_counts as $key => $value) {
      if (in_array($key, $params)) {
        $checked[] = array($key, self::getVendorTypeName($key), $value);
      } else {
        $unchecked[] = array($key, self::getVendorTypeName($key), $value);
      }
    }
    return array('unchecked' => $unchecked, "checked" => $checked);
  }

  /**
   * Populates static variables with the latest minority category by vendor for specified domain
   * @param $type_of_year
   * @param $year_id
   * @param $domain
   * @return array
   */
  public static function getAllVendorMinorityTypes($type_of_year, $year_id, $domain) {
    STATIC $spending_vendor_latest_mwbe_category;
    STATIC $contract_vendor_latest_mwbe_category;
    STATIC $contract_pending_vendor_latest_mwbe_category;

    switch($domain) {
      case CheckbookDomain::$SPENDING:

        if(!isset($spending_vendor_latest_mwbe_category)) {

          $query = "SELECT minority_type_id,vendor_id,agency_id,is_prime_or_sub
                              FROM spending_vendor_latest_mwbe_category
                              WHERE minority_type_id IN (".MappingUtil::getTotalMinorityIds().")
                              AND year_id = ".$year_id."
                              AND type_of_year = '".$type_of_year."'";

          $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');

          foreach($results as $row){
            if(isset($row['agency_id'])) {
              $spending_vendor_latest_mwbe_category[$row['vendor_id']][$row['agency_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
            }
            $spending_vendor_latest_mwbe_category[$row['vendor_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
          }
        }
        return $spending_vendor_latest_mwbe_category;

      case CheckbookDomain::$CONTRACTS:

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
          // that's AJAX
          $ajaxReferer = '//' . $_SERVER['HTTP_REFERER'];
          if (stripos($ajaxReferer, 'contracts_pending_exp_landing')
            || stripos($ajaxReferer, 'contracts_pending_rev_landing')) {
            $query = "SELECT
                                minority_type_id,
                                vendor_id,
                                document_agency_id as agency_id,
                                is_prime_or_sub
                              FROM pending_contracts
                              WHERE minority_type_id IN (".MappingUtil::getTotalMinorityIds().")
                              AND is_prime_or_sub = 'P'";

            $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
            foreach($results as $row){
              if(isset($row['agency_id'])) {
                $contract_pending_vendor_latest_mwbe_category[$row['vendor_id']][$row['agency_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
              }
              $contract_pending_vendor_latest_mwbe_category[$row['vendor_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
            }
            return $contract_pending_vendor_latest_mwbe_category;
          }
        }

        if(!isset($contract_vendor_latest_mwbe_category)) {

          $query = "SELECT minority_type_id,vendor_id,agency_id,is_prime_or_sub
                              FROM contract_vendor_latest_mwbe_category
                              WHERE minority_type_id IN (".MappingUtil::getTotalMinorityIds().")
                              AND year_id = ".$year_id."
                              AND type_of_year = '".$type_of_year."'";

          $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');

          foreach($results as $row){
            if(isset($row['agency_id'])) {
              $contract_vendor_latest_mwbe_category[$row['vendor_id']][$row['agency_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
            }
            $contract_vendor_latest_mwbe_category[$row['vendor_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
          }
        }
        return $contract_vendor_latest_mwbe_category;
    }
    return array();
  }

  /**
   * @param $agreement_id
   *
   * @return bool
   */
  public static function _is_mwbe_vendor($agreement_id){
    if (!($agreement_id = intval($agreement_id))) {
      return false;
    }
    $query1 = "SELECT (CASE WHEN fa.minority_type_id IN (".MappingUtil::getTotalMinorityIds().")  THEN 'Yes' ELSE 'NO' END) AS mwbe_vendor,
	                 (CASE WHEN fa.minority_type_id in (4,5,10) then 'Asian American' ELSE fa.minority_type_name END)AS ethnicity
              FROM {agreement_snapshot} fa
	          WHERE fa.latest_flag = 'Y' and fa.original_agreement_id = " . $agreement_id . " limit 1";
    $results1 = _checkbook_project_execute_sql_by_data_source($query1);
    $res = $results1;
    if ($res[0]['mwbe_vendor'] == 'Yes') {
      return true;
    }
    return false;
  }
}
