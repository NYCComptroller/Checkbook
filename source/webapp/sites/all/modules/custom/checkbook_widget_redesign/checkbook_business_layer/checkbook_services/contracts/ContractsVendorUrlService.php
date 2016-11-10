<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 10/23/2016
 * Time: 6:16 PM
 */
class ContractsVendorUrlService {
    /**
    * Returns Prime Vendor Landing page URL for the given vendor id in the given year and year type for
     Active/Registered Contracts Landing Pages
    * @param $vendor_id
    * @param $agency_id
    * @param $year_id
    * @param $year_type
    * @param $minority_type_id
    * @param $is_prime_or_sub
    * @return string
    */
    static function vendorUrl($vendor_id,$agency_id,$year_id,$year_type,$minority_type_id,$is_prime_or_sub) {

        $url = RequestUtilities::_getUrlParamString("agency")
            . RequestUtilities::_getUrlParamString("contstatus","status")
            . RequestUtilities::_getUrlParamString("cindustry")
            . RequestUtilities::_getUrlParamString("csize")
            . RequestUtilities::_getUrlParamString("awdmethod")
            . _checkbook_project_get_year_url_param_string();

        $latest_minority_id = MinorityTypeURLService::getLatestMwbeCategoryByVendor($vendor_id, $agency_id, $year_id, $year_type, $is_prime_or_sub);
        $latest_minority_id = isset($latest_minority_id) ? $latest_minority_id : $minority_type_id;
        $is_mwbe_certified = MinorityTypeURLService::isMWBECertified(array($latest_minority_id));
        $dashboard= RequestUtilities::getRequestParamValue("dashboard");

        $urlPath = drupal_get_path_alias($_GET['q']);
        if(!preg_match('/pending/',$urlPath)){
            if(!RequestUtilities::getRequestParamValue('status')){
                $url .= "/status/A";
            }
        }

        if($is_mwbe_certified && $dashboard == 'mp') {
            $url .= "/dashboard/mp/mwbe/2~3~4~5~9/vendor/".$vendor_id;
        }
        else if($is_mwbe_certified && $dashboard != 'mp') {
            $url .= "/dashboard/mp/mwbe/2~3~4~5~9/vendor/".$vendor_id;
        }
        else {
            $url .= RequestUtilities::_getUrlParamString("datasource")."/vendor/".$vendor_id;
        }
        $currentUrl = RequestUtilities::_getCurrentPage();
        return $currentUrl . $url . "?expandBottomCont=true";
    }
    
    /**
    * Returns Sub Vendor Landing page URL for the given sub vendor id in the given year and year type for
    * Active/Registered Contracts Landing Pages
    * @param $vendor_id
    * @param $year_id
    * @param $year_type
    * @param $agency_id
    * @param $mwbe_cat
    * @return string
    */
    static public function getSubContractsVendorLink($vendor_id, $year_id = null, $year_type = null,$agency_id = null, $mwbe_cat = null){
        $currentUrl = RequestUtilities::_getCurrentPage();
        $latest_minority_id = isset($mwbe_cat) ? $mwbe_cat : MinorityTypeURLService::getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id, $year_type, "S");
        $url = RequestUtilities::_getUrlParamString("agency") .  RequestUtilities::_getUrlParamString("contstatus","status") . _checkbook_project_get_year_url_param_string();

        $current_dashboard = RequestUtilities::getRequestParamValue("dashboard");
        $is_mwbe_certified = in_array($latest_minority_id, array(2, 3, 4, 5, 9));

        //if M/WBE certified, go to M/WBE (Sub Vendor) else if NOT M/WBE certified, go to Sub Vendor dashboard
        $new_dashboard = $is_mwbe_certified ? "ms" : "ss";
        $status = strlen(RequestUtilities::_getUrlParamString("contstatus","status"))== 0 ? "/status/A" : "";

        if($current_dashboard != $new_dashboard ){
                return $currentUrl.$url . $status . "/dashboard/" . $new_dashboard . ($is_mwbe_certified ? "/mwbe/2~3~4~5~9" : "" ) . "/subvendor/".$vendor_id;
        }else{
                $url .= $status.RequestUtilities::_getUrlParamString("cindustry"). RequestUtilities::_getUrlParamString("csize")
                . RequestUtilities::_getUrlParamString("awdmethod") ."/dashboard/" . $new_dashboard .
                ($is_mwbe_certified ? "/mwbe/2~3~4~5~9" : "" ) . "/subvendor/".$vendor_id;
                return $currentUrl.$url;
        }

        return '';
    }
  
    static function getSubVendorCustomerCode($subVendorId){
        $result = NULL;
        $query = "SELECT v.vendor_customer_code 
                FROM subvendor v
                JOIN (SELECT vendor_id, MAX(vendor_history_id) AS vendor_history_id FROM subvendor_history GROUP BY 1) vh 
                  ON v.vendor_id = vh.vendor_id
                WHERE v.vendor_id = ".$subVendorId;

        $subVendorCustomerCode = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        if($subVendorCustomerCode) {
            return $subVendorCustomerCode[0]['vendor_customer_code'];
        }
        else {
            return null;
        }
    }

    static function getVendorCustomerCode($vendorId){
        $vendor = _checkbook_project_querydataset("checkbook:vendor","vendor_customer_code",array("vendor_id"=>$vendorId));
        if($vendor[0]) {
            return $vendor[0]['vendor_customer_code'];
        }
        else {
            return null;
        }
    }
}