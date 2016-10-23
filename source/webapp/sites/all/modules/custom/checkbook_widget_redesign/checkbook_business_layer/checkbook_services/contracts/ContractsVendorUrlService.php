<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 10/23/2016
 * Time: 6:16 PM
 */
class ContractsVendorUrlService {
    
    static function vendorUrl($vendor_id,$agency_id,$year_id,$year_type,$minority_type_id,$is_prime_or_sub) {

        $url = _checkbook_project_get_url_param_string("agency")
            . _checkbook_project_get_url_param_string("contstatus","status")
            . _checkbook_project_get_url_param_string("cindustry")
            . _checkbook_project_get_url_param_string("csize")
            . _checkbook_project_get_url_param_string("awdmethod")
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
            $url .= _checkbook_project_get_url_param_string("datasource")."/vendor/".$vendor_id;
        }
        $currentUrl = RequestUtilities::_getCurrentPage();
        return $currentUrl . $url . "?expandBottomCont=true";
    }

    /**
    * Returns M/WBE category for the given vendor id in the given year and year type for
    * Active/Registered Contracts Landing Pages
    * @param $row
    * @return string
    */
    static public function getContractsVendorLinkByMWBECategory($row){

        $vendor_id = $row["vendor_id"] != null ? $row["vendor_id"] : $row["vendor_id"];
        if($vendor_id == null)
            $vendor_id = $row["prime_vendor_id"];

        $year_id = RequestUtilities::getRequestParamValue("year");
        $year_type = $row["type_of_year,"];
        $is_prime_or_sub = $row["is_prime_or_sub"] != null ? $row["is_prime_or_sub"] : "P";
        $agency_id = null;

        if($row["current_prime_minority_type_id"])
            $minority_type_id = $row["current_prime_minority_type_id"];
        if($row["minority_type_id"])
            $minority_type_id = $row["minority_type_id"];
        if($row["prime_minority_type_id"])
            $minority_type_id = $row["prime_minority_type_id"];

        $smnid = RequestUtilities::getRequestParamValue("smnid");
        if($smnid == 720 || $smnid == 784) return self::getSubContractsVendorLink($vendor_id, $year_id, $year_type,$agency_id);

        $latest_minority_id = MinorityTypeURLService::getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id, $year_type, $is_prime_or_sub);
        $latest_minority_id = isset($latest_minority_id) ? $latest_minority_id : $minority_type_id;
        $is_mwbe_certified = MinorityTypeURLService::isMWBECertified($latest_minority_id);

        $status = _checkbook_project_get_url_param_string("contstatus","status");
        $status = isset($status) && $status != "" ? $status : "/status/A";
        $url = _checkbook_project_get_url_param_string("agency") . $status . _checkbook_project_get_year_url_param_string();

        if($is_mwbe_certified && RequestUtilities::getRequestParamValue('dashboard') == 'mp') {
            $url .= _checkbook_project_get_url_param_string("cindustry")
                . _checkbook_project_get_url_param_string("csize")
                . _checkbook_project_get_url_param_string("awdmethod")
                . "/dashboard/mp/mwbe/2~3~4~5~9/vendor/".$vendor_id;
        }
        else if($is_mwbe_certified && RequestUtilities::getRequestParamValue('dashboard') != 'mp') {
            $url .= "/dashboard/mp/mwbe/2~3~4~5~9/vendor/".$vendor_id;
        }
        else {
            $url .= _checkbook_project_get_url_param_string("datasource")."/vendor/".$vendor_id;
        }
        return $url;
    }
    
    static public function getSubContractsVendorLink($vendor_id, $year_id = null, $year_type = null,$agency_id = null, $mwbe_cat = null){

        $latest_minority_id = isset($mwbe_cat) ? $mwbe_cat : MinorityTypeURLService::getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id, $year_type, "S");
        $url = _checkbook_project_get_url_param_string("agency") .  _checkbook_project_get_url_param_string("contstatus","status") . _checkbook_project_get_year_url_param_string();

        $current_dashboard = RequestUtilities::getRequestParamValue("dashboard");
        $is_mwbe_certified = in_array($latest_minority_id, array(2, 3, 4, 5, 9));

        //if M/WBE certified, go to M/WBE (Sub Vendor) else if NOT M/WBE certified, go to Sub Vendor dashboard
        $new_dashboard = $is_mwbe_certified ? "ms" : "ss";
        $status = strlen(_checkbook_project_get_url_param_string("contstatus","status"))== 0 ? "/status/A" : "";

        if($current_dashboard != $new_dashboard ){
                return $url . $status . "/dashboard/" . $new_dashboard . ($is_mwbe_certified ? "/mwbe/2~3~4~5~9" : "" ) . "/subvendor/".$vendor_id;
        }else{
                $url .= $status._checkbook_project_get_url_param_string("cindustry"). _checkbook_project_get_url_param_string("csize")
                . _checkbook_project_get_url_param_string("awdmethod") ."/dashboard/" . $new_dashboard .
                ($is_mwbe_certified ? "/mwbe/2~3~4~5~9" : "" ) . "/subvendor/".$vendor_id;
                return $url;
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