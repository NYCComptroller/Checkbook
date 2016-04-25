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

    static function vendorUrl($vendor_id,$minority_type_id,$is_prime_or_sub) {

        $url = _checkbook_project_get_url_param_string("agency")
            . _checkbook_project_get_url_param_string("contstatus","status")
            . _checkbook_project_get_url_param_string("cindustry")
            . _checkbook_project_get_url_param_string("csize")
            . _checkbook_project_get_url_param_string("awdmethod")
            . _checkbook_project_get_year_url_param_string();

        $latest_minority_id = ContractUtil::getLatestMwbeCategoryByVendor($vendor_id, $is_prime_or_sub);
        $latest_minority_id = isset($latest_minority_id) ? $latest_minority_id : $minority_type_id;
        $is_mwbe_certified = MappingUtil::isMWBECertified(array($latest_minority_id));
        $dashboard= _getRequestParamValue("dashboard");

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

    static function agencyUrl($original_agreement_id, $agency_id) {
        $url = "/contracts_landing"
            ."/magid/".$original_agreement_id
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

    static function contractsFooterUrl() {
        $url = '/panel_html/contract_details/contract/transactions/contcat/expense'
            . _checkbook_project_get_url_param_string('status','contstatus')
            . _checkbook_append_url_params()
            . _checkbook_project_get_url_param_string('agency')
            . _checkbook_project_get_url_param_string('vendor')
            . _checkbook_project_get_url_param_string("vendor","fvendor")
            . _checkbook_project_get_url_param_string('awdmethod')
            . _checkbook_project_get_url_param_string('csize')
            . _checkbook_project_get_url_param_string('cindustry')
            . _checkbook_project_get_year_url_param_string();
        return $url;
    }
} 