<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class MinorityTypeService {

    public static $minority_type_category_map = array(
        2 => 'Black American',
        3 => 'Hispanic American',
        4 => 'Asian American',
        5 => 'Asian American',
        7 => 'Non-M/WBE',
        9 => 'Women',
        11 => 'Individuals and Others',
    );
    
    public static $minority_type_category_map_multi_chart = array(
        'Black American' => array(2),
        'Hispanic American' => array(3),
        'Asian American' => array(4,5),
        'Non-M/WBE' => array(7),
        'Women' => array(9),
        'Individuals and Others' => array(11),
        'M/WBE' => array(2,3,4,5,9),
    );
    
    static $mwbe_prefix = "M/WBE" ;
    
       public static  function isMWBECertified($mwbe_cat){
        if(in_array($mwbe_cat, static::$minority_type_category_map_multi_chart['M/WBE'])){
            return true;
    	}else{
            return false;
    	}
    }

    /**
     * Returns the M/WBE category name based on the minority_type_id mapping
     * @param $minority_type_id
     * @return mixed
     */
      public static  function getMinorityCategoryById($minority_type_id) {
        return static::$minority_type_category_map[$minority_type_id];
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
            case Domain::$SPENDING:

                if(!isset($spending_vendor_latest_mwbe_category)) {

                    $query = "SELECT minority_type_id,vendor_id,agency_id,is_prime_or_sub
                              FROM spending_vendor_latest_mwbe_category
                              WHERE minority_type_id IN (2,3,4,5,9)
                              AND year_id = ".$year_id."
                              AND type_of_year = '".$type_of_year."'";

                    $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');

                    foreach($results as $row){
                        if(isset($row['agency_id'])) {
                            $spending_vendor_latest_mwbe_category[$row['vendor_id']][$row['agency_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
                        }
                        else {
                            $spending_vendor_latest_mwbe_category[$row['vendor_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
                        }
                    }
                }
                return $spending_vendor_latest_mwbe_category;

            case Domain::$CONTRACTS:

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
                              WHERE minority_type_id IN (2,3,4,5,9)
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
                              WHERE minority_type_id IN (2,3,4,5,9)
                              AND year_id = ".$year_id."
                              AND type_of_year = '".$type_of_year."'";

                    $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');

                    foreach($results as $row){
                        if(isset($row['agency_id'])) {
                            $contract_vendor_latest_mwbe_category[$row['vendor_id']][$row['agency_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
                        }
                        else {
                            $contract_vendor_latest_mwbe_category[$row['vendor_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
                        }
                    }
                }
                return $contract_vendor_latest_mwbe_category;
        }
        return array();
    }
}