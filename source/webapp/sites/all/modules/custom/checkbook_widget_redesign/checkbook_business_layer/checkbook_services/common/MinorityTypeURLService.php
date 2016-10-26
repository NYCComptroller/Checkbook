<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class MinorityTypeURLService {
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
    
    static public function isMWBECertified($mwbe_cat){ 	
        if(in_array($mwbe_cat, self::$minority_type_category_map_multi_chart['M/WBE'])){
            return true;
    	}else{
            return false;
    	}
    } 

    /** Returns the M/WBE category name based on the minority_type_id mapping */
    static public function getMinorityCategoryById($minority_type_id) {
        return self::$minority_type_category_map[$minority_type_id];
    }

    /** Returns the M/WBE category name based on the minority_type_id mapping */
    static public function getMinorityCategoryByName($minority_type_name) {
        return self::$minority_type_category_map_by_name[$minority_type_name];
    }

    /** Returns the M/WBE category name and it's minority_type_id mapping as an array */
    static public function getMinorityCategoryMappings() {
        return self::$minority_type_category_map_multi;
    }
    
    //Returns the Latest Minority Type for the given Vendor_id
    static public function getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id = null, $year_type = null, $is_prime_or_sub = "P"){
        STATIC $contract_vendor_latest_mwbe_category;
        if($agency_id == null){
            $agency_id =  _getRequestParamValue('agency');
        }
        if($year_type == null){
            $year_type =  _getRequestParamValue('yeartype');
        }
        if($year_id == null){
            $year_id =  _getRequestParamValue('year');
        }
        if($year_id == null){
            $year_id =  _getRequestParamValue('calyear');
        }
        if($year_id == null){
            $year_type = "B";
            $year_id = _getCurrentYearID();
        }
        
        $latest_minority_type_id = null;
        $agency_query = isset($agency_id) ? "agency_id = " . $agency_id : "agency_id IS NULL";

        if(!isset($contract_vendor_latest_mwbe_category)){
            $query = "SELECT vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub
                        FROM contract_vendor_latest_mwbe_category
                        WHERE minority_type_id IN (2,3,4,5,9) AND year_id = ".$year_id
                              ." AND type_of_year = '".$year_type . "'"
                              ."  AND " . $agency_query
                              ." AND is_prime_or_sub = '" . $is_prime_or_sub . "'"
                        ." GROUP BY vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub";

            $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
            foreach($results as $row){
                if(isset($row['agency_id'])) {
                    $contract_vendor_latest_mwbe_category[$row['vendor_id']][$row['agency_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
                }
                else{
                    $contract_vendor_latest_mwbe_category[$row['vendor_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
                }
            }
        }

        $latest_minority_type_id = isset($agency_id)
        ? $contract_vendor_latest_mwbe_category[$vendor_id][$agency_id][$is_prime_or_sub]['minority_type_id']
        : $contract_vendor_latest_mwbe_category[$vendor_id][$is_prime_or_sub]['minority_type_id'];
        
        return $latest_minority_type_id;
    }



    /**
     * Get the minority type link url for a sub vendor.
     *
     * Rules:
     *
     * 1. Sub M/WBE category is only a link from Sub Dashboards
     * 2. Must be certified to be linkable
     * 3. If current dashboard is "Sub Vendors", redirect to "Sub Vendors (M/WBE)" dashboard
     *
     * @param $minority_type_id
     * @return string
     */
    static public function getSubMinorityTypeUrl($minority_type_id){

        $showLink = Dashboard::isSubDashboard() && MinorityTypeURLService::isMWBECertified($minority_type_id);
        $url = $showLink ?  ContractsUrlService::minorityTypeUrl($minority_type_id) : null;
        if(!isset($url)) return $url;

        $dashboard = DashboardParameter::getCurrent();
        $dashboard = $dashboard == DashboardParameter::SUB_VENDORS ? DashboardParameter::SUB_VENDORS_MWBE : $dashboard;

        $currentUrl = RequestUtilities::_getCurrentPage();
        $url = $currentUrl
            . _checkbook_project_get_year_url_param_string()
            . RequestUtilities::_getUrlParamString("agency")
            . RequestUtilities::_getUrlParamString("cindustry")
            . RequestUtilities::_getUrlParamString("csize")
            . RequestUtilities::_getUrlParamString("awdmethod")
            . RequestUtilities::_getUrlParamString("contstatus","status")
            . RequestUtilities::_getUrlParamString("vendor")
            . RequestUtilities::_getUrlParamString("subvendor")
            . '/dashboard/' . $dashboard
            . '/mwbe/'. $minority_type_id;

        return $url;
    }
}