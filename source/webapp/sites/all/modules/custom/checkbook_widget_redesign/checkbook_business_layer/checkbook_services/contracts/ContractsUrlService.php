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

    static function masterContractIdUrl($original_agreement_id,$document_code) {
        $url = "/panel_html/contract_transactions/contract_details/magid/".$original_agreement_id
            . _checkbook_project_get_url_param_string("status")
            . _checkbook_append_url_params()
            . "/doctype/".$document_code;
        return $url;
    }

    /**
     * Gets the spent to date link Url for the contract spending
     * @param $spend_type_parameter
     * @return string
     */
    static function spentToDateUrl($spend_type_parameter) {

        $url = "/contract/spending/transactions"
            . $spend_type_parameter
            . _checkbook_append_url_params()
            . _checkbook_project_get_url_param_string("status")
            . _checkbook_project_get_url_param_string("status","contstatus")
            . _checkbook_project_get_url_param_string("agency","cagency")
            . _checkbook_project_get_url_param_string("vendor","cvendor")
            . _checkbook_project_get_url_param_string("awdmethod")
            . _checkbook_project_get_url_param_string("cindustry")
            . _checkbook_project_get_url_param_string("csize")
            . _checkbook_project_get_year_url_param_string()
            . _checkbook_project_get_url_param_string("year","syear")
            . "/doctype/CT1~CTA1~MA1"
            . "/contcat/".self::_getContractCategoryParameter()
            . "/smnid/728" //todo get mapping
            . "/newwindow";
        return $url;
    }

    static function masterAgreementSpentToDateUrl($spend_type_parameter) {

        $url = "/spending/transactions"
            . $spend_type_parameter
            . _checkbook_append_url_params()
            . _checkbook_project_get_url_param_string("status","contstatus")
            . _checkbook_project_get_url_param_string("agency","cagency")
            . _checkbook_project_get_url_param_string("vendor","cvendor")
            . _checkbook_project_get_url_param_string("awdmethod")
            . _checkbook_project_get_url_param_string("cindustry")
            . _checkbook_project_get_url_param_string("csize")
            . _checkbook_project_get_year_url_param_string()
            . _checkbook_project_get_url_param_string("year","syear")
            . "/contcat/".self::_getContractCategoryParameter()
            . "/smnid/371" //todo get mapping
            . "/newwindow";
        return $url;
    }
    
    //TODO: move to a separate re-usable class (ie ContractUrlParameters) to parse the Url and return parameters
    static private  function _getCurrentPage() {
        $currentUrl = explode('/',$_SERVER['HTTP_REFERER']);
        return '/'.$currentUrl[3];
    }
    
    /**
     * Gets the Minoritype Name link for the given minority type id
     * @param $minorityTypeId
     * @return NULL or string
     */
    static function minorityTypeUrl($minorityTypeId){
        $url = NULL;
        if(MinorityTypeURLService::isMWBECertified($minorityTypeId)){
            $currentUrl = self::_getCurrentPage();
            $minorityTypeId = ($minorityTypeId == 4 || $minorityTypeId == 5) ? '4~5': $minorityTypeId;
            $dashboard = "mp";
            $url =  '/'. $currentUrl
                    . _checkbook_project_get_url_param_string("syear","year")
                    . _checkbook_project_get_url_param_string("agency")
                    . _checkbook_project_get_url_param_string("cindustry")
                    . _checkbook_project_get_url_param_string("csize")
                    . _checkbook_project_get_url_param_string("awdmethod")
                    . _checkbook_project_get_url_param_string("contstatus","status")
                    . _checkbook_project_get_url_param_string("vendor")
                    . _checkbook_project_get_url_param_string("subvendor")
                    . '/dashboard/mp'
                    . '/mwbe/'. $minorityTypeId .  '?expandBottomCont=true';
        }
        return $url;
    }

    // TODO: move to a separate re-usable class (ie ContractUrlParameters) to parse the Url and return parameters
    static private  function _getContractCategoryParameter() {
        $url = $_GET['q'];
        if(preg_match('/revenue/',$url)){
            return 'revenue';
        }
        else if(preg_match('/pending_exp/',$url)){
            return 'expense';
        }
        else if(preg_match('/pending_rev/',$url)){
            return 'revenue';
        }
        return 'expense';
    }

    static function vendorUrl($vendor_id,$agency_id,$year_id,$year_type,$minority_type_id,$is_prime_or_sub) {

        $url = _checkbook_project_get_url_param_string("agency")
            . _checkbook_project_get_url_param_string("contstatus","status")
            . _checkbook_project_get_url_param_string("cindustry")
            . _checkbook_project_get_url_param_string("csize")
            . _checkbook_project_get_url_param_string("awdmethod")
            . _checkbook_project_get_year_url_param_string();

        $latest_minority_id = MinorityTypeURLService::_getLatestMwbeCategoryByVendor($vendor_id, $agency_id, $year_id, $year_type, $is_prime_or_sub);
        $latest_minority_id = isset($latest_minority_id) ? $latest_minority_id : $minority_type_id;
        $is_mwbe_certified = MinorityTypeURLService::isMWBECertified(array($latest_minority_id));
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
        $currentUrl = self::_getCurrentPage();
        return $currentUrl . $url . "?expandBottomCont=true";
    }

    static function agencyUrl($agency_id, $original_agreement_id = null) {
        $currentUrl = self::_getCurrentPage();
        $url = $currentUrl
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

    static function awardmethodUrl($award_method_id) {
        $currentUrl = self::_getCurrentPage();
        $url = $currentUrl
            . _checkbook_append_url_params()
            ._checkbook_project_get_url_param_string('vendor')
            ._checkbook_project_get_url_param_string('cindustry')
            ._checkbook_project_get_url_param_string('csize')
            ._checkbook_project_get_url_param_string('agency')
            ._checkbook_project_get_url_param_string('status')
            ._checkbook_project_get_year_url_param_string()
            ."/awdmethod/".$award_method_id
            ."?expandBottomCont=true";
        return $url;
    }

    static function industryUrl($industry_type_id) {
        $currentUrl = self::_getCurrentPage();
        $url = $currentUrl
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

    static function contractSizeUrl($award_size_id) {
        $currentUrl = self::_getCurrentPage();
        $url = $currentUrl
            . _checkbook_append_url_params()
            ._checkbook_project_get_url_param_string('vendor')
            ._checkbook_project_get_url_param_string('agency')
            ._checkbook_project_get_url_param_string('csize')
            ._checkbook_project_get_url_param_string('awdmethod')
            ._checkbook_project_get_url_param_string('status')
            ._checkbook_project_get_year_url_param_string()
            ."/csize/".$award_size_id
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
