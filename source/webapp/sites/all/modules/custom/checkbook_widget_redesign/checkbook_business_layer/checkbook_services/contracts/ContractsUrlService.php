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
            . RequestUtilities::_getUrlParamString("status")
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . "/doctype/".$document_code;
        return $url;
    }

    static function masterContractIdUrl($original_agreement_id,$document_code) {
        $url = "/panel_html/contract_transactions/contract_details/magid/".$original_agreement_id
            . RequestUtilities::_getUrlParamString("status")
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . "/doctype/".$document_code;
        return $url;
    }

    static function pendingMasterContractIdUrl($original_agreement_id,$doctype,$fms_contract_number,$pending_contract_number = null,$version = null, $linktype= null){
        $lower_doctype = strtolower($doctype);

        if($original_agreement_id){
            if(($lower_doctype == 'ma1') || ($lower_doctype == 'mma1') || ($lower_doctype == 'rct1')){
                $url = '/panel_html/contract_transactions/magid/'.$original_agreement_id
                    . RequestUtilities::_getUrlParamString("status")
                    . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                    . "/doctype/".$doctype;
            }else{
                $url = '/panel_html/contract_transactions/agid/'.$original_agreement_id
                    . RequestUtilities::_getUrlParamString("status")
                    . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                    . "/doctype/".$doctype;
            }
            }else{
                $url = '/minipanels/pending_contract_transactions/contract/'.$fms_contract_number
                    . RequestUtilities::_getUrlParamString("status")
                    . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                    .'/version/'.$version;
            }
        return $url;
    }
    
    function pendingContractIdLink($original_agreement_id,$doctype,$fms_contract_number,$pending_contract_number = null,$version = null, $linktype= null){
        $lower_doctype = strtolower($doctype);
        if($original_agreement_id){
            if(($lower_doctype == 'ma1') || ($lower_doctype == 'mma1') || ($lower_doctype == 'rct1')){
                $url = '/panel_html/contract_transactions/magid/'.$original_agreement_id.'/doctype/'.$doctype;
            }else{
                $url = '/panel_html/contract_transactions/agid/'.$original_agreement_id.'/doctype/'.$doctype;
            }
        }else{
            $url = '/minipanels/pending_contract_transactions/contract/'.$pending_contract_number.'/version/'.$version;
        }

        //Don't persist M/WBE parameter if there is no dashboard (this could be an advanced search parameter)
        $mwbe_parameter = _getRequestParamValue('dashboard') != null ? RequestUtilities::_getUrlParamString("mwbe") : '';
        $url .= $mwbe_parameter;

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
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . RequestUtilities::_getUrlParamString("status")
            . RequestUtilities::_getUrlParamString("status","contstatus")
            . RequestUtilities::_getUrlParamString("agency","cagency")
            . RequestUtilities::_getUrlParamString("vendor","cvendor")
            . RequestUtilities::_getUrlParamString("awdmethod")
            . RequestUtilities::_getUrlParamString("cindustry")
            . RequestUtilities::_getUrlParamString("csize")
            . _checkbook_project_get_year_url_param_string()
            . RequestUtilities::_getUrlParamString("year","syear")
            . "/doctype/CT1~CTA1~MA1"
            . "/contcat/".self::_getContractCategoryParameter()
            . "/smnid/728" //todo get mapping
            . "/newwindow";
        return $url;
    }

    static function masterAgreementSpentToDateUrl($spend_type_parameter) {

        $url = "/spending/transactions"
            . $spend_type_parameter
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . RequestUtilities::_getUrlParamString("status","contstatus")
            . RequestUtilities::_getUrlParamString("agency","cagency")
            . RequestUtilities::_getUrlParamString("vendor","cvendor")
            . RequestUtilities::_getUrlParamString("awdmethod")
            . RequestUtilities::_getUrlParamString("cindustry")
            . RequestUtilities::_getUrlParamString("csize")
            . _checkbook_project_get_year_url_param_string()
            . RequestUtilities::_getUrlParamString("year","syear")
            . "/contcat/".self::_getContractCategoryParameter()
            . "/smnid/371" //todo get mapping
            . "/newwindow";
        return $url;
    }
    
    /**
     * Gets the Minoritype Name link for the given minority type id
     * @param $minorityTypeId
     * @return NULL or string
     */
    static function minorityTypeUrl($minorityTypeId){
        $url = NULL;
        if(MinorityTypeURLService::isMWBECertified($minorityTypeId)){
            $currentUrl = RequestUtilities::_getCurrentPage();
            $minorityTypeId = ($minorityTypeId == 4 || $minorityTypeId == 5) ? '4~5': $minorityTypeId;
            $dashboard = "mp";
            $url =  '/'. $currentUrl
                    . RequestUtilities::_getUrlParamString("syear","year")
                    . RequestUtilities::_getUrlParamString("agency")
                    . RequestUtilities::_getUrlParamString("cindustry")
                    . RequestUtilities::_getUrlParamString("csize")
                    . RequestUtilities::_getUrlParamString("awdmethod")
                    . RequestUtilities::_getUrlParamString("contstatus","status")
                    . RequestUtilities::_getUrlParamString("vendor")
                    . RequestUtilities::_getUrlParamString("subvendor")
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

    static function agencyUrl($agency_id, $original_agreement_id = null) {
        $currentUrl = RequestUtilities::_getCurrentPage();
        $url = $currentUrl
            .(isset($original_agreement_id) ? ("/magid/".$original_agreement_id):'')
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            .RequestUtilities::_getUrlParamString('vendor')
            .RequestUtilities::_getUrlParamString('cindustry')
            .RequestUtilities::_getUrlParamString('csize')
            .RequestUtilities::_getUrlParamString('awdmethod')
            .RequestUtilities::_getUrlParamString('status')
            ._checkbook_project_get_year_url_param_string()
            ."/agency/".$agency_id
            ."?expandBottomCont=true";
        return $url;
    }

    static function awardmethodUrl($award_method_id) {
        $currentUrl = RequestUtilities::_getCurrentPage();
        $url = $currentUrl
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            .RequestUtilities::_getUrlParamString('vendor')
            .RequestUtilities::_getUrlParamString('cindustry')
            .RequestUtilities::_getUrlParamString('csize')
            .RequestUtilities::_getUrlParamString('agency')
            .RequestUtilities::_getUrlParamString('status')
            ._checkbook_project_get_year_url_param_string()
            ."/awdmethod/".$award_method_id
            ."?expandBottomCont=true";
        return $url;
    }

    static function industryUrl($industry_type_id) {
        $currentUrl = RequestUtilities::_getCurrentPage();
        $url = $currentUrl
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            .RequestUtilities::_getUrlParamString('vendor')
            .RequestUtilities::_getUrlParamString('agency')
            .RequestUtilities::_getUrlParamString('csize')
            .RequestUtilities::_getUrlParamString('awdmethod')
            .RequestUtilities::_getUrlParamString('status')
            ._checkbook_project_get_year_url_param_string()
            ."/cindustry/".$industry_type_id
            ."?expandBottomCont=true";
        return $url;
    }

    static function contractSizeUrl($award_size_id) {
        $currentUrl = RequestUtilities::_getCurrentPage();
        $url = $currentUrl
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            .RequestUtilities::_getUrlParamString('vendor')
            .RequestUtilities::_getUrlParamString('agency')
            .RequestUtilities::_getUrlParamString('csize')
            .RequestUtilities::_getUrlParamString('awdmethod')
            .RequestUtilities::_getUrlParamString('status')
            ._checkbook_project_get_year_url_param_string()
            ."/csize/".$award_size_id
            ."?expandBottomCont=true";
        return $url;
    }

    static function contractsFooterUrl() {
        $subvendor = RequestUtilities::getRequestParamValue('subvendor');
        $vendor = RequestUtilities::getRequestParamValue('vendor');
        $mwbe = RequestUtilities::getRequestParamValue('mwbe');
        if($subvendor) {
            $subvendor_code = ContractsVendorUrlService::getSubVendorCustomerCode($subvendor);
        }
        if($vendor) {
            $vendor_code = ContractsVendorUrlService::getVendorCustomerCode($vendor);
        }
        $subvendorURLString = (isset($subvendor) ? '/subvendor/'. $subvendor : '') .(isset($subvendor_code) ? '/vendorcode/'.$subvendor_code : '');
        $vendorURLString = (isset($vendor) ? '/vendor/'. $vendor : '') . (isset($vendor_code) ? '/vendorcode/'.$vendor_code : '');
        $mwbe_param = "";
        //pmwbe & smwbe
        if(isset($mwbe)) {
            $mwbe_param = ContractUtil::showSubVendorData() ? '/smwbe/'.$mwbe : '/pmwbe/'.$mwbe;
        }

        $url = '/panel_html/contract_details/contract/transactions/contcat/expense'
            . RequestUtilities::_getUrlParamString('status','contstatus')
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . RequestUtilities::_getUrlParamString('agency')
            . RequestUtilities::_getUrlParamString('awdmethod')
            . RequestUtilities::_getUrlParamString('csize')
            . RequestUtilities::_getUrlParamString('cindustry')
            . $mwbe_param
            . $subvendorURLString . $vendorURLString
            . _checkbook_project_get_year_url_param_string();
        return $url;
    }

    static function subContractsFooterUrl() {
        $subvendor = RequestUtilities::getRequestParamValue('subvendor');
        $vendor = RequestUtilities::getRequestParamValue('vendor');
        $mwbe = RequestUtilities::getRequestParamValue('mwbe');
        if($subvendor) {
            $subvendor_code = ContractsVendorUrlService::getSubVendorCustomerCode($subvendor);
        }
        if($vendor) {
            $vendor_code = ContractsVendorUrlService::getVendorCustomerCode($vendor);
        }
        $mwbe_param = isset($mwbe) ? '/pmwbe/'.$mwbe : '';
        $subvendorURLString = isset($subvendor_code) ? '/vendorcode/'.$subvendor_code : '';
        $vendorURLString = isset($vendor_code) ? '/vendorcode/'.$vendor_code : '';
        $url = '/panel_html/sub_contracts_transactions/subcontract/transactions/contcat/expense'
            . RequestUtilities::_getUrlParamString('status','contstatus')
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . RequestUtilities::_getUrlParamString('agency')
            . RequestUtilities::_getUrlParamString('vendor')
            . RequestUtilities::_getUrlParamString("vendor","fvendor")
            . RequestUtilities::_getUrlParamString('awdmethod')
            . RequestUtilities::_getUrlParamString('csize')
            . RequestUtilities::_getUrlParamString('cindustry')
            . $mwbe_param
            . $subvendorURLString . $vendorURLString
            . _checkbook_project_get_year_url_param_string();
        return $url;
    }   
}