<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/25/16
 * Time: 10:26 AM
 */

class ContractsUrlService {

    static function contractIdUrl($original_agreement_id,$document_code) {
        $url = "/panel_html/contract_transactions/agid/".$original_agreement_id
            . _checkbook_project_get_url_param_string("status")
            . _checkbook_append_url_params()
            . "/doctype/".$document_code;
        return $url;
    }

    static function spentToDateUrl($original_agreement_id,$vendor_id,$contract_number) {

        $year_type = _getRequestParamValue("yeartype");
        $year = _getRequestParamValue("year");
        $url = "/contract/spending/transactions"
            . (_checkbook_check_isEDCPage() ? ("/agid/".$original_agreement_id."/cvendor/".$vendor_id) :"/contnum/" .$contract_number )
            . _checkbook_project_get_url_param_string("status")
            . _checkbook_append_url_params()
            . _checkbook_project_get_year_url_param_string()
            . ($year_type == "B" ? "/syear/".$year : "/scalyear/".$year)
            . ContractUtil::getSpentToDateParams()
            . "/smnid/368/newwindow";
        return $url;
    }

    static function vendorUrl($vendor_id,$agency_id,$year_id,$year_type,$minority_type_id,$is_prime_or_sub) {

        $url = _checkbook_project_get_url_param_string("agency")
            . _checkbook_project_get_url_param_string("contstatus","status")
            . _checkbook_project_get_url_param_string("cindustry")
            . _checkbook_project_get_url_param_string("csize")
            . _checkbook_project_get_url_param_string("awdmethod")
            . _checkbook_project_get_year_url_param_string();

        $latest_minority_id = ContractUtil::getLatestMwbeCategoryByVendor($vendor_id, $agency_id, $year_id, $year_type, $is_prime_or_sub);
        $latest_minority_id = isset($latest_minority_id) ? $latest_minority_id : $minority_type_id;
        $is_mwbe_certified = MappingUtil::isMWBECertified(array($latest_minority_id));
        $dashboard= _getRequestParamValue("dashboard");


        if(!_getRequestParamValue('status')){
            $url .= "/status/A";
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
        return "/contracts_landing" . $url . "?expandBottomCont=true";
    }

    static function agencyUrl($agency_id, $original_agreement_id = null) {
        $url = "/contracts_landing"
            .(isset($original_agreement_id) ? ("/magid/".$original_agreement_id):'')
            . _checkbook_append_url_params()
            ._checkbook_project_get_url_param_string('vendor')
            ._checkbook_project_get_url_param_string('cindustry')
            ._checkbook_project_get_url_param_string('csize')
            ._checkbook_project_get_url_param_string('awdmethod')
            ._checkbook_project_get_url_param_string('status')
            ._checkbook_project_get_year_url_param_string()
            ."/agency/".$agency_id
            ."?expandBottomCont=true";
        return $url;
    }

    static function industryUrl($industry_type_id) {
        $url = "/contracts_landing"
            . _checkbook_append_url_params()
            ._checkbook_project_get_url_param_string('vendor')
            ._checkbook_project_get_url_param_string('agency')
            ._checkbook_project_get_url_param_string('csize')
            ._checkbook_project_get_url_param_string('awdmethod')
            ._checkbook_project_get_url_param_string('status')
            ._checkbook_project_get_year_url_param_string()
            ."/cindustry/".$industry_type_id
            ."?expandBottomCont=true";
        return $url;
    }

    static function contractsFooterUrl() {
        $subvendor = _getRequestParamValue('subvendor');
        $vendor = _getRequestParamValue('vendor');
        $mwbe = _getRequestParamValue('mwbe');
        if($subvendor) {
            $subvendor_code = self::getSubVendorCustomerCode($subvendor);
        }
        if($vendor) {
            $vendor_code = self::getVendorCustomerCode($vendor);
        }
        $subvendorURLString = (isset($subvendor) ? '/subvendor/'. $subvendor : '') .(isset($subvendor_code) ? '/vendorcode/'.$subvendor_code : '');
        $vendorURLString = (isset($vendor) ? '/vendor/'. $vendor : '') . (isset($vendor_code) ? '/vendorcode/'.$vendor_code : '');
        $mwbe_param = "";
        //pmwbe & smwbe
        if(isset($mwbe)) {
            $mwbe_param = ContractUtil::showSubVendorData() ? '/smwbe/'.$mwbe : '/pmwbe/'.$mwbe;
        }

        $url = '/panel_html/contract_details/contract/transactions/contcat/expense'
            . _checkbook_project_get_url_param_string('status','contstatus')
            . _checkbook_append_url_params()
            . _checkbook_project_get_url_param_string('agency')
            . _checkbook_project_get_url_param_string('awdmethod')
            . _checkbook_project_get_url_param_string('csize')
            . _checkbook_project_get_url_param_string('cindustry')
            . $mwbe_param
            . $subvendorURLString . $vendorURLString
            . _checkbook_project_get_year_url_param_string();
        return $url;
    }

    static function subContractsFooterUrl() {
        $subvendor = _getRequestParamValue('subvendor');
        $vendor = _getRequestParamValue('vendor');
        $mwbe = _getRequestParamValue('mwbe');
        if($subvendor) {
            $subvendor_code = self::getSubVendorCustomerCode($subvendor);
        }
        if($vendor) {
            $vendor_code = self::getVendorCustomerCode($vendor);
        }
        $mwbe_param = isset($mwbe) ? '/pmwbe/'.$mwbe : '';
        $subvendorURLString = isset($subvendor_code) ? '/vendorcode/'.$subvendor_code : '';
        $vendorURLString = isset($vendor_code) ? '/vendorcode/'.$vendor_code : '';
        $url = '/panel_html/sub_contracts_transactions/subcontract/transactions/contcat/expense'
            . _checkbook_project_get_url_param_string('status','contstatus')
            . _checkbook_append_url_params()
            . _checkbook_project_get_url_param_string('agency')
            . _checkbook_project_get_url_param_string('vendor')
            . _checkbook_project_get_url_param_string("vendor","fvendor")
            . _checkbook_project_get_url_param_string('awdmethod')
            . _checkbook_project_get_url_param_string('csize')
            . _checkbook_project_get_url_param_string('cindustry')
            . $mwbe_param
            . $subvendorURLString . $vendorURLString
            . _checkbook_project_get_year_url_param_string();
        return $url;
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