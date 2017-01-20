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

    static function pendingContractIdLink($original_agreement_id,$doctype,$fms_contract_number,$pending_contract_number = null,$version = null, $linktype= null){
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
     * @param null $legacy_node_id
     * @return string
     */
    static function spentToDateUrl($spend_type_parameter, $legacy_node_id = null) {

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
            . "/contcat/".ContractCategory::getCurrent()
            . (isset($legacy_node_id) ? "/smnid/".$legacy_node_id."/newwindow" : "/newwindow");
        return $url;
    }

    static function masterAgreementSpentToDateUrl($spend_type_parameter, $legacy_node_id = null) {

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
            . "/contcat/".ContractCategory::getCurrent()
            . (isset($legacy_node_id) ? "/smnid/".$legacy_node_id."/newwindow" : "/newwindow");
        return $url;
    }

    /**
     * Gets the Minority Type Name link for the given minority type id
     * @param $minority_type_id
     * @return NULL or string
     */
    static function primeMinorityTypeUrl($minority_type_id){

        $showLink = Dashboard::isPrimeDashboard() && MinorityTypeService::isMWBECertified($minority_type_id);
        $dashboard = DashboardParameter::MWBE;
        $url = $showLink ?  self::minorityTypeUrl($minority_type_id, $dashboard) : null;

        return $url;
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
    static public function subMinorityTypeUrl($minority_type_id){

        $showLink = Dashboard::isSubDashboard() && MinorityTypeService::isMWBECertified($minority_type_id);
        $dashboard = DashboardParameter::getCurrent();
        $dashboard = $dashboard == DashboardParameter::SUB_VENDORS ? DashboardParameter::SUB_VENDORS_MWBE : $dashboard;
        $url = $showLink ?  self::minorityTypeUrl($minority_type_id, $dashboard) : null;

        return $url;
    }

    /**
     * Gets the Minority Type Name link for the given minority type id
     * @param $minority_type_id
     * @param $dashboard
     * @return NULL or string
     */
    static function minorityTypeUrl($minority_type_id, $dashboard){
        $url = NULL;
        if(MinorityTypeService::isMWBECertified($minority_type_id)){
            $currentUrl = RequestUtilities::_getCurrentPage();
            $minority_type_id = ($minority_type_id == 4 || $minority_type_id == 5) ? '4~5': $minority_type_id;
            $url = $currentUrl
                . RequestUtilities::_getUrlParamString("syear","year")
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
        }
        return $url;
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
            .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
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

    /**
     * @param $parameters
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($parameters,$legacy_node_id = null) {

        $subvendor = RequestUtilities::getRequestParamValue('subvendor');
        $vendor = RequestUtilities::getRequestParamValue('vendor');
        $mwbe = RequestUtilities::getRequestParamValue('mwbe');
        $industry = RequestUtilities::getRequestParamValue('cindustry');
        $category = ContractCategory::getCurrent();

        $subvendor_code = $subvendor ? SubVendorService::getVendorCode($subvendor) : null;
        $vendor_code = $vendor ? PrimeVendorService::getVendorCode($vendor) : null;

        $subvendor_param = isset($subvendor_code) ? '/vendorcode/'.$subvendor_code : '';
        $vendor_param = isset($vendor_code) ? '/vendorcode/'.$vendor_code : '';
        $mwbe_param = isset($mwbe) ? (Dashboard::isSubDashboard() ||  $legacy_node_id == 720 ? '/smwbe/'.$mwbe : '/pmwbe/'.$mwbe) : '';
        if(Datasource::isOGE()) {
            $industry_param = isset($industry) ? '/cindustry/'.$industry : '';
        }
        else {
            $industry_param = isset($industry) ? (Dashboard::isSubDashboard() ||  $legacy_node_id == 720 ? '/scindustry/'.$industry : '/pcindustry/'.$industry) : '';
        }
        $category_param = '/contcat/'.(isset($category) ? $category : ContractCategory::EXPENSE);
        $smnid_param = isset($legacy_node_id) ? '/smnid/'.$legacy_node_id : '';
        $contract_status = _checkbook_project_get_url_param_string('status','contstatus');
        $contract_status = isset($contract_status) && $contract_status != '' ? $contract_status : "/contstatus/P";

        $path = Dashboard::isSubDashboard() && ContractStatus::getCurrent() == ContractStatus::PENDING
            ? '/panel_html/sub_contracts_transactions/subcontract/transactions'
            : '/panel_html/contract_details/contract/transactions';

        $url = $path . $category_param
            . $contract_status
            . _checkbook_append_url_params()
            . _checkbook_project_get_url_param_string('agency')
            . _checkbook_project_get_url_param_string('vendor')
            . _checkbook_project_get_url_param_string('subvendor')
            . _checkbook_project_get_url_param_string("vendor","fvendor")
            . _checkbook_project_get_url_param_string('awdmethod')
            . _checkbook_project_get_url_param_string('csize')
            . $mwbe_param . $subvendor_param . $vendor_param . $industry_param
            . _checkbook_project_get_year_url_param_string()
            . self::getDocumentCodeUrlString($parameters)
            . $smnid_param;
        return $url;
    }

    static function getDocumentCodeUrlString($parameters) {

        $doc_type = $parameters['doctype'];

        if(isset($doc_type)) {
            $doc_type = explode(",",$doc_type);
            $doc_type =  implode("~",str_replace("'", "", $doc_type));
            $doc_type =  str_replace("(", "", str_replace(")", "", $doc_type));
        }
        else {
            //contract category or doc type is derived from the page path
            $status = ContractStatus::getCurrent();
            $category = ContractCategory::getCurrent();

            switch($status){
                case ContractStatus::PENDING:
                    switch($category) {
                        case ContractCategory::REVENUE:
                            $doc_type = "RCT1";
                            break;
                        default:
                            $doc_type = "MMA1~MA1~MAR~CT1~CTA1~CTR";
                            break;
                    }
                    break;

                default:
                    switch($category) {
                        case ContractCategory::REVENUE:
                            $doc_type = "RCT1";
                            break;
                        default:
                            $doc_type = "MA1~CTA1~CT1";
                            break;
                    }
                    break;
            }
        }

        return isset($doc_type) ? '/doctype/'. $doc_type : '';
    }

    /**
     * @param $blnIsMasterAgreement
     * @return string
     */
    static function getAmtModificationUrlString($blnIsMasterAgreement = false) {
        if($blnIsMasterAgreement)
            $url = "/modamt/0".(ContractUtil::showSubVendorData() ? '/smodamt/0' : '/pmodamt/0');
        else
            $url = "/modamt/0";
        return $url;
    }

    /**
     * Returns Contracts Prime Vendor Landing page URL for the given prime vendor id, year and year type
     * @param $vendor_id
     * @param $year_id
     * @return string
     */
    static function primeVendorUrl($vendor_id, $year_id = null) {

        $url = RequestUtilities::_getUrlParamString("agency")
            . RequestUtilities::_getUrlParamString("contstatus","status")
            . RequestUtilities::_getUrlParamString("cindustry")
            . RequestUtilities::_getUrlParamString("csize")
            . RequestUtilities::_getUrlParamString("awdmethod")
            . _checkbook_project_get_year_url_param_string();

        $year_type = _getRequestParamValue("yeartype");
        $agency_id = _getRequestParamValue("agency");
        $dashboard = _getRequestParamValue("dashboard");

        $latest_minority_id = !isset($year_id)
            ? PrimeVendorService::getLatestMinorityType($vendor_id, $agency_id)
            : PrimeVendorService::getLatestMinorityTypeByYear($vendor_id, $year_id, $year_type);
        $is_mwbe_certified = MinorityTypeService::isMWBECertified($latest_minority_id);

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
     * @return string
     */
    static function subVendorUrl($vendor_id, $year_id = null) {

        $year_type = _getRequestParamValue("yeartype");
        $agency_id = _getRequestParamValue("agency");
        $currentUrl = RequestUtilities::_getCurrentPage();

        $latest_minority_id = !(isset($year_id))
            ? SubVendorService::getLatestMinorityType($vendor_id, $agency_id)
            : SubVendorService::getLatestMinorityTypeByYear($vendor_id, $year_id, $year_type);

        $url = RequestUtilities::_getUrlParamString("agency") .  RequestUtilities::_getUrlParamString("contstatus","status") . _checkbook_project_get_year_url_param_string();

        $current_dashboard = RequestUtilities::getRequestParamValue("dashboard");
        $is_mwbe_certified = in_array($latest_minority_id, array(2, 3, 4, 5, 9));

        //if M/WBE certified, go to M/WBE (Sub Vendor) else if NOT M/WBE certified, go to Sub Vendor dashboard
        $new_dashboard = $is_mwbe_certified ? "ms" : "ss";
        $status = strlen(RequestUtilities::_getUrlParamString("contstatus","status"))== 0 ? "/status/A" : "";

        if($current_dashboard != $new_dashboard ) {
            return $currentUrl.$url . $status . "/dashboard/" . $new_dashboard . ($is_mwbe_certified ? "/mwbe/2~3~4~5~9" : "" ) . "/subvendor/".$vendor_id;
        }
        else {
            $url .= $status.RequestUtilities::_getUrlParamString("cindustry"). RequestUtilities::_getUrlParamString("csize")
                . RequestUtilities::_getUrlParamString("awdmethod") ."/dashboard/" . $new_dashboard .
                ($is_mwbe_certified ? "/mwbe/2~3~4~5~9" : "" ) . "/subvendor/".$vendor_id;
            return $currentUrl.$url;
        }
    }
}