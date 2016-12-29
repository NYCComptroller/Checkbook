<?php
/**
 * Created by PhpStorm.
 * User: pshirodkar
 * Date: 12/7/16
 * Time: 1:39 PM
 */

class SpendingVendorUrlService {
    
    static $landingPageParams = array("category"=>"category","industry"=>"industry","mwbe"=>"mwbe","dashboard"=>"dashboard","agency"=>"agency","vendor"=>"vendor","subvendor"=>"subvendor");

    static function vendorUrl($row){
        if(RequestUtilities::isEDCPage()){
            $url = '/spending_landing'
               .RequestUtilities::_getUrlParamString('agency')
               .RequestUtilities::_getUrlParamString('category')
               .RequestUtilities::_getUrlParamString('industry')
               .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
               ._checkbook_project_get_year_url_param_string()
               . '/vendor/'. $row['vendor_id'];
            return $url;
        }else{
            return self::citywideVendorUrl($row);
        }
    }
    
    /**
     * Returns Prime Vendor Name Link Url based on values from current path & data row
     * @param $node
     * @param $row
     * @return string
     */
    static function citywideVendorUrl($row){
        $vendor_id = isset($row["prime_vendor_id"]) ? $row["prime_vendor_id"] : $row["vendor_id"];
        if(!isset($vendor_id)) {
            $vendor_id = isset($row["vendor_id"]) ? $row["vendor_id"] : $row["vendor"];
        }
        $year_id = RequestUtilities::getRequestParamValue("year");
        $year_type = RequestUtilities::getRequestParamValue("yeartype");
        $agency_id = RequestUtilities::getRequestParamValue("agency");
        $dashboard = RequestUtilities::getRequestParamValue("dashboard");
        $datasource = RequestUtilities::getRequestParamValue("datasource");

        return self::getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard, '', $datasource);
    }

    /**
     * @param $prime_vendor_id
     * @return string
     */
    static function getOGEPrimeVendorNameLinkUrl($vendor_id){
        $url = '/spending_landing'
            .RequestUtilities::_getUrlParamString('agency')
            .RequestUtilities::_getUrlParamString('category')
            ._checkbook_project_get_year_url_param_string()
            . '/vendor/'. $vendor_id;
        return $url;
    }

    /**
     * Returns Prime Vendor Name Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getPrimeVendorNameLinkUrl($row){

        $vendor_id = isset($row["prime_vendor_id"]) ? $row["prime_vendor_id"] : $row["prime_vendor_prime_vendor"];
        if(!isset($vendor_id)) {
            $vendor_id = isset($row["vendor_id"]) ? $row["vendor_id"] : $row["vendor_vendor"];
        }
        $year_id = RequestUtilities::getRequestParamValue("year");
        $year_type = RequestUtilities::getRequestParamValue("yeartype");
        $agency_id = RequestUtilities::getRequestParamValue("agency");
        $dashboard = RequestUtilities::getRequestParamValue("dashboard");
        $datasource = RequestUtilities::getRequestParamValue("datasource");

        return self::getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard, '', $datasource);
    }


    /**
     * Returns Prime Vendor Name Link Url based on values from current path & data row
     *
     * if vendor is M/WBE certified - go to M/WBE dashboard
     * if vendor is NOT M/WBE certified - go to citywide (default) dashboard
     *
     * if switching from citywide->M/WBE OR M/WBE->citywide,
     * then persist only agency filter (mwbe & vendor if applicable)
     *
     * if remaining in the same dashboard persist all filters (drill-down) except sub vendor
     *
     * @param $vendor_id
     * @param $agency_id
     * @param $year_id
     * @param $year_type
     * @param $current_dashboard
     * @param $payee_name
     * @return string
     */
    static function getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $current_dashboard, $payee_name = false, $datasource = false){

        $override_params = null;
        $latest_certified_minority_type_id = self::getLatestMwbeCategoryByVendor($vendor_id, $agency_id, $year_id, $year_type, "P");
        $is_mwbe_certified = isset($latest_certified_minority_type_id);

        //if M/WBE certified, go to M/WBE dashboard else if NOT M/WBE certified, go to citywide
        $new_dashboard = $is_mwbe_certified ? "mp" : null;

        //if switching between dashboard, persist only agency filter (mwbe & vendor if applicable)
        if($current_dashboard != $new_dashboard) {
            $override_params = array(
                "dashboard"=>$new_dashboard,
                "mwbe"=>$is_mwbe_certified ? "2~3~4~5~9" : null,
                "agency"=>$agency_id,
                "vendor"=>$vendor_id,
                "subvendor"=>null,
                "category"=>null,
                "industry"=>null
            );
        }
        //if remaining in the same dashboard persist all filters (drill-down) except sub vendor
        else {
            $override_params = array(
                "dashboard"=>$new_dashboard,
                "subvendor"=>null,
                "agency"=>$agency_id,
                "datasource"=>$datasource,
                "vendor"=>$vendor_id
            );
            //payee name will never have a drill down, this is to avoid ajax issues on drill down
            if($payee_name) {
                $override_params["mwbe"] = $is_mwbe_certified ? "2~3~4~5~9" : null;
            }
        }
        return '/' . self::getLandingPageWidgetUrl($override_params);
    }


    /**
     * Returns Sub Vendor Name Link Url based on values from current path & data row,
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getSubVendorNameLinkUrl($row){
        $override_params = null;
        $vendor_id = isset($row["sub_vendor_sub_vendor"]) ? $row["sub_vendor_sub_vendor"] : $row["vendor_id"];
        $year_id = RequestUtilities::getRequestParamValue("year");
        $year_type = RequestUtilities::getRequestParamValue("yeartype");
        $agency_id = RequestUtilities::getRequestParamValue("agency");
        $dashboard = RequestUtilities::getRequestParamValue("dashboard");

        return self::getSubVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard);
    }

    /**
     * Returns Sub Vendor Name Link Url based on values from current path & data row
     *
     * if sub vendor is M/WBE certified - go to M/WBE (Sub Vendor) dashboard
     * if sub vendor is NOT M/WBE certified - go to Sub Vendor dashboard
     *
     * if switching from citywide->M/WBE OR M/WBE->citywide,
     * then persist only agency filter (mwbe & vendor if applicable)
     *
     * if remaining in the same dashboard persist all filters (drill-down) except sub vendor
     *
     * @param $vendor_id
     * @param $agency_id
     * @param $year_id
     * @param $year_type
     * @param $current_dashboard
     * @param $payee_name
     * @return string
     */
    static function getSubVendorLink($vendor_id, $agency_id, $year_id, $year_type, $current_dashboard, $payee_name = false){

        $override_params = null;
        $latest_certified_minority_type_id = self::getLatestMwbeCategoryByVendor($vendor_id, $agency_id, $year_id, $year_type, "S");
        $is_mwbe_certified = isset($latest_certified_minority_type_id);

        //if M/WBE certified, go to M/WBE (Sub Vendor) else if NOT M/WBE certified, go to Sub Vendor dashboard
        $new_dashboard = $is_mwbe_certified ? "ms" : "ss";

        //if switching between dashboard, persist only agency filter (mwbe & subvendor if applicable)
        if($current_dashboard != $new_dashboard) {
            $override_params = array(
                "dashboard"=>$new_dashboard,
                "mwbe"=>$is_mwbe_certified ? "2~3~4~5~9" : null,
                "agency"=>$agency_id,
                "subvendor"=>$vendor_id,
                "vendor"=>null,
                "category"=>null,
                "industry"=>null
            );
        }
        //if remaining in the same dashboard persist all filters (drill-down) except vendor
        else {
            $override_params = array(
                "dashboard"=>$new_dashboard,
                "subvendor"=>$vendor_id,
                "vendor"=>null
            );
            //payee name will never have a drill down, this is to avoid ajax issues on drill down
            if($payee_name) {
                $override_params["mwbe"] = $is_mwbe_certified ? "2~3~4~5~9" : null;
            }
        }
        return '/' . self::getLandingPageWidgetUrl($override_params);
    }

    /**
     * Returns M/WBE category for the given vendor id in the given year and year type
     *
     * @param $vendor_id
     * @param $agency_id
     * @param $year_id
     * @param $year_type
     * @param string $is_prime_or_sub
     * @return null
     */
    static public function getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id = null, $year_type = null, $is_prime_or_sub = "P"){
        STATIC $spending_vendor_latest_mwbe_category;

        if($agency_id == null){
        	$agency_id =  RequestUtilities::getRequestParamValue('agency');
        }
        
        if($year_id == null){
        	$year_id =  RequestUtilities::getRequestParamValue('year');
        }

        if($year_id == null){
            $year_id =  RequestUtilities::getRequestParamValue('calyear');
        }

        if($year_type == null){
        	$year_type =  RequestUtilities::getRequestParamValue('yeartype');
        }
        
        $latest_minority_type_id = null;
        if(!isset($spending_vendor_latest_mwbe_category)){
            $query = "SELECT vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub
                      FROM spending_vendor_latest_mwbe_category
                      WHERE minority_type_id IN (2,3,4,5,9) AND year_id = '".$year_id."' AND type_of_year = '".$year_type."'
                      GROUP BY vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub";

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

        $latest_minority_type_id = isset($agency_id)
            ? $spending_vendor_latest_mwbe_category[$vendor_id][$agency_id][$is_prime_or_sub]['minority_type_id']
            : $spending_vendor_latest_mwbe_category[$vendor_id][$is_prime_or_sub]['minority_type_id'];
        return $latest_minority_type_id;
    }

    /**
     *  Returns a spending landing page Url with custom parameters appended but instead of persisted
     *
     * @param array $override_params
     * @return string
     */
    static function getLandingPageWidgetUrl($override_params = array()) {
        $url = self::getSpendingUrl('spending_landing',$override_params);
        return str_replace("calyear","year",$url);
    }
    
    /**
     * Function build the url using the path and the current Spending URL parameters.
     * The Url parameters can be overridden by the override parameter array.
     *
     * @param $path
     * @param array $override_params
     * @return string
     */
    static function getSpendingUrl($path, $override_params = array()) {

        $url =  $path . _checkbook_project_get_year_url_param_string();

        $pathParams = explode('/',drupal_get_path_alias($_GET['q']));
        $url_params = self::$landingPageParams;
        $exclude_params = array_keys($override_params);
        if(is_array($url_params)){
            foreach($url_params as $key => $value){
                if(!in_array($key,$exclude_params)){
                    $url .=  RequestUtilities::get_url_param($pathParams,$key,$value);
                }
            }
        }

        if(is_array($override_params)){
            foreach($override_params as $key => $value){
                if(isset($value)){
                    if($key == 'yeartype' && $value == 'C'){
                        $value = 'B';
                    }
                    $url .= "/$key";
                    $url .= "/$value";
                }
            }
        }

        return $url;
    }
} 
