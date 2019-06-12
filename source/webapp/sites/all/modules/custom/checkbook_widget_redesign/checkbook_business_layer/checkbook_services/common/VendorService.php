<?php

abstract class VendorService {

    /**
     * Returns the Latest Minority Type for the given Vendor and the current selected year
     * @param $vendor_id
     * @param null $agency_id
     * @param string $vendor_type
     * @param string $domain
     * @return mixed
     */
    static protected function getLatestMinorityType($vendor_id, $agency_id = null, $vendor_type, $domain = null) {

        $latest_minority_types = null;
        $type_of_year = 'B';
        $year_id = RequestUtilities::get('year') ?:  _getCurrentYearID();
        $agency_id = $agency_id ?: RequestUtilities::get('agency');
        $domain = $domain ?: CheckbookDomain::getCurrent();

        $latest_minority_types = MinorityTypeService::getAllVendorMinorityTypes($type_of_year, $year_id, $domain);
        $latest_minority_type_id = isset($agency_id)
            ? $latest_minority_types[$vendor_id][(int)$agency_id][$vendor_type]['minority_type_id']
            : $latest_minority_types[$vendor_id][$vendor_type]['minority_type_id'];

        return $latest_minority_type_id;
    }

    /**
     *  Returns the Latest Minority Type for the given Vendor and the current provided transaction year
     * @param $vendor_id
     * @param $year_id
     * @param $type_of_year
     * @param string $vendor_type
     * @param string $domain
     * @return bool
     */
    static protected function getLatestMinorityTypeByYear($vendor_id, $year_id, $type_of_year, $vendor_type, $domain = null) {
        $vendor_id_param = (isset($vendor_id)) ? " AND vendor_id = ".$vendor_id ." " : "";
        switch ($domain){
            case Domain::$SPENDING :
                $query = "SELECT minority_type_id
                            FROM spending_vendor_latest_mwbe_category
                            WHERE minority_type_id IN (2,3,4,5,9)"
                            . $vendor_id_param .
                            "AND year_id = ".$year_id."
                            AND type_of_year = '".$type_of_year."'
                            AND is_prime_or_sub = '".$vendor_type."' LIMIT 1";

                $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
                break;
            default :
                $query = "SELECT DISTINCT minority_type_id, latest_minority_flag, latest_mwbe_flag
                            FROM contract_vendor_latest_mwbe_category
                            WHERE minority_type_id IN (2,3,4,5,9)"
                            . $vendor_id_param .
                            "AND year_id = ".$year_id."
                            AND type_of_year = 'B'
                            AND latest_minority_flag = 'Y'
                            AND is_prime_or_sub = '".$vendor_type."'";
                $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        }
        $minority_type_id = $results[0]['minority_type_id'];
        return $minority_type_id != '' ? $minority_type_id : false;
    }

    /**
     * Given the vendor name, returns an array of sub and prime vendor ids
     * @param null $vendor_name
     * @return array|null
     */
    public static function getVendorIdByName($vendor_name) {
        $vendors = array();
        if($vendor_name != NULL) {
            $vendors[] = PrimeVendorService::getVendorIdByName($vendor_name);
            $vendors[] = SubVendorService::getVendorIdByName($vendor_name);
            $vendors = array_filter($vendors);
        }
        return !empty($vendors) ? $vendors : null;
    }

    /**
     * Given the vendor name, returns an array of sub and prime vendor ids
     * @param $domain
     * @param $vendor_id
     * @param $year_id
     * @param string $status
     * @return array $minority_types
     */
    public static function getAllVendorMinorityTypesByYear($domain, $vendor_id, $year_id, $status = 'A') {
        $minority_type_ids  = array();
        switch($domain){
            case Domain::$SPENDING:
                        $query = "SELECT DISTINCT minority_type_id
                                    FROM aggregateon_mwbe_spending_coa_entities
                                    WHERE vendor_id = ".$vendor_id."
                                    AND year_id = ". $year_id ."
                                    AND type_of_year = 'B'";
                break;
            case Domain::$CONTRACTS:
                        $query = "SELECT DISTINCT minority_type_id
                                    FROM aggregateon_mwbe_contracts_cumulative_spending
                                    WHERE vendor_id = ".$vendor_id."
                                    AND fiscal_year_id = ". $year_id ."
                                    AND status_flag = '" . $status . "' 
                                    AND type_of_year = 'B'";
                break;
        }
        $minority_types = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        foreach($minority_types as $minority_type_id){
            $minority_type_ids[] = $minority_type_id['minority_type_id'];
        }
        return $minority_type_ids;
    }
    public static function getMwbeAmount($vendor_id =null,$year_id=null,$status='A')
    {


        $contstatus = RequestUtilities::get('contstatus')?:RequestUtilities::get('status');
        $status =  ($contstatus == null) ? $status : $contstatus;
        $year_id = ($year_id == null) ? _getCurrentYearID() : $year_id;
        $vendor_id_param = (isset($vendor_id)) ? " AND s0.vendor_id = ".$vendor_id ." " : "";

      $query = "SELECT SUM(COALESCE(maximum_contract_amount,0)) AS current_amount_sum
                  FROM aggregateon_mwbe_contracts_cumulative_spending s0
                  LEFT OUTER JOIN ref_document_code s15 ON s15.document_code_id = s0.document_code_id
                  WHERE s0.minority_type_id IN (2,3,4,5,9)"
                  .$vendor_id_param.
                  "AND s0.fiscal_year_id= ".$year_id."
                   AND s0.type_of_year = 'B'
                   AND s0.status_flag = '" . $status . "' 
                   AND s15.document_code IN ('CT1', 'CTA1', 'RCT1', 'MA1') ";
        $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        return $results[0]['current_amount_sum'];
    }
    public static function getSubVendorAmount($vendor_id =null,$year_id=null,$status='A')
    {
        $contstatus = RequestUtilities::get('contstatus')?:RequestUtilities::get('status');
        $status =  ($contstatus == null) ? $status : $contstatus;
        $year_id = ($year_id == null) ? _getCurrentYearID() : $year_id;
        $prime_vendor_id_param = (isset($vendor_id)) ? " AND s0.prime_vendor_id = ".$vendor_id ." " : "";
        $query = "SELECT SUM(COALESCE(maximum_contract_amount,0)) AS current_amount_sum
                  FROM aggregateon_subven_contracts_cumulative_spending s0
                  LEFT OUTER JOIN ref_document_code s15 ON s15.document_code_id = s0.document_code_id
                  WHERE s0.minority_type_id IN (2,3,4,5,9)"
                        .$prime_vendor_id_param.
                        "AND s0.fiscal_year_id= ".$year_id."
                         AND s0.type_of_year = 'B'
                         AND s0.status_flag = '" . $status . "' 
                         AND s15.document_code IN ('CT1', 'CTA1', 'RCT1', 'MA1')
                          ";
        $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        return $results[0]['current_amount_sum'];

    }
}
