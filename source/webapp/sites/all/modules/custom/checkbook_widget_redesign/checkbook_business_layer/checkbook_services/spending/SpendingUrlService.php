<?php

class SpendingUrlService {

    /**
     * Function to build the contract id url
     * @param $agreement_id
     * @param $document_code
     * @return string
     */
    static function contractIdUrl($agreement_id, $document_code) {

        $contractUrl = DocumentCode::isMasterAgreement($document_code)
            ? '/magid/' . $agreement_id . '/doctype/' . $document_code
            : '/agid/' . $agreement_id . '/doctype/' . $document_code;

        $url = '/contract_details'
            . $contractUrl
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . '/newwindow' ;

        return $url;
    }

    /**
     * Function to build the agency url
     * @param $agency_id
     * @return string
     */
    static function agencyUrl($agency_id) {
        $url = '/spending_landing'
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('subvendor')
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . _checkbook_project_get_year_url_param_string()
            . '/agency/'. $agency_id;

        return $url;
    }

    /**
     * Payroll Agencies widget include spending category url parameter for payroll
     * @param $agency_id
     * @return string
     */
    static function payrollAgencyUrl($agency_id) {
        $url = '/spending_landing'
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . _checkbook_project_get_year_url_param_string()
            . '/category/2/agency/'. $agency_id;

        return $url;
    }

    /**
     * Function to build the industry url
     * @param $industry_type_id
     * @return string
     */
    static function industryUrl($industry_type_id) {

        $url = '/spending_landing'
            ._checkbook_project_get_year_url_param_string()
            .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            .RequestUtilities::buildUrlFromParam('vendor')
            .RequestUtilities::buildUrlFromParam('subvendor')
            .RequestUtilities::buildUrlFromParam('category')
            .RequestUtilities::buildUrlFromParam('agency')
            . '/industry/'. $industry_type_id;

        return $url;
    }

    /**
     * Returns Prime Vendor Landing page URL for the given prime vendor, year and year type
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
     * @param $year_id
     * @return string
     */
    static function primeVendorUrl($vendor_id, $year_id = null) {

        if(!isset($vendor_id) || $vendor_id == "") {
            return null;
        }

        $year_type = RequestUtilities::get("yeartype");
        $industry = RequestUtilities::get("industry");
        $agency_id = RequestUtilities::get("agency");
        $category = RequestUtilities::get("category");
        $dashboard = RequestUtilities::get("dashboard");
        $datasource = RequestUtilities::get("datasource");

        $latest_minority_id = !isset($year_id)
            ? PrimeVendorService::getLatestMinorityType($vendor_id, $agency_id)
            : PrimeVendorService::getLatestMinorityTypeByYear($vendor_id, $year_id, $year_type);
        $is_mwbe_certified = MinorityTypeService::isMWBECertified($latest_minority_id);

        //if M/WBE certified, go to M/WBE dashboard else if NOT M/WBE certified, go to citywide
        $new_dashboard = $is_mwbe_certified ? "mp" : null;

        $url = RequestUtilities::_getCurrentPage() . _checkbook_project_get_year_url_param_string();

        $mwbe = $is_mwbe_certified ? "/mwbe/2~3~4~5~9" : "";
        $industry = isset($industry) ? "/industry/{$industry}" : "";
        $agency = isset($agency_id) ? "/agency/{$agency_id}" : "";
        $category = isset($category) ? "/category/{$category}" : "";
        $vendor = isset($vendor_id) ? "/vendor/{$vendor_id}" : "";
        $datasource = isset($datasource) ? "/datasource/{$datasource}" : "";
        $dashboard_param = isset($new_dashboard) ? "/dashboard/{$new_dashboard}" : "";

        /**
         * if switching between dashboard, persist only agency filter (mwbe & vendor if applicable),
         * if remaining in the same dashboard persist all filters (drill-down) except sub vendor
         */
        $url = $dashboard != $new_dashboard
            ? $url = $url . $dashboard_param . $mwbe . $industry . $category . $agency . $vendor
            : $url . $datasource . $dashboard_param . $mwbe . $industry . $category . $agency . $vendor;
        return $url;
    }

    /**
     * Returns Sub Vendor Landing page URL for the given sub vendor, year and year type
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
     * @param $year_id
     * @return string
     */
    static function subVendorUrl($vendor_id, $year_id = null) {

        $year_type = RequestUtilities::get("yeartype");
        $agency_id = RequestUtilities::get("agency");
        $industry = RequestUtilities::get("industry");
        $dashboard = RequestUtilities::get("dashboard");
        $datasource = RequestUtilities::get("datasource");

        $latest_minority_id = !isset($year_id)
            ? SubVendorService::getLatestMinorityType($vendor_id, $agency_id)
            : SubVendorService::getLatestMinorityTypeByYear($vendor_id, $year_id, $year_type);
        $is_mwbe_certified = MinorityTypeService::isMWBECertified($latest_minority_id);

        //if M/WBE certified, go to M/WBE (Sub Vendor) else if NOT M/WBE certified, go to Sub Vendor dashboard
        $new_dashboard = $is_mwbe_certified ? "ms" : "ss";

        $url = RequestUtilities::_getCurrentPage() . _checkbook_project_get_year_url_param_string();

        $mwbe = $is_mwbe_certified ? "/mwbe/2~3~4~5~9" : "";
        $agency = isset($agency_id) ? "/agency/{$agency_id}" : "";
        $industry = isset($industry) ? "/industry/{$industry}" : "";
        $vendor = isset($vendor_id) ? "/subvendor/{$vendor_id}" : "";
        $datasource = isset($datasource) ? "/datasource/{$datasource}" : "";
        $dashboard_param = isset($new_dashboard) ? "/dashboard/{$new_dashboard}" : "";

        /**
         * if switching between dashboard, persist only agency filter (mwbe & vendor if applicable),
         * if remaining in the same dashboard persist all filters (drill-down) except sub vendor
         */
        $url = $dashboard != $new_dashboard
            ? $url = $url . $dashboard_param . $mwbe . $industry . $agency . $vendor
            : $url . $datasource . $dashboard_param . $mwbe . $industry . $agency . $vendor;
        return $url;
    }


    /**
     * Function to build the M/WBE Category url
     * Do not hyperlink if you are looking at sub data (sub dashboard)
     * Do not hyperlink if not M/WBE certified
     * @param $minority_type_id
     * @return string
     */
    static function PrimeMwbeCategoryUrl($minority_type_id){

        // Do not hyperlink if you are looking at sub data
        // Do not hyperlink if not M/WBE certified
        $is_mwbe_certified = MinorityTypeService::isMWBECertified($minority_type_id);
        if(Dashboard::isSubDashboard() || !$is_mwbe_certified) {
            return null;
        }

        $dashboard = RequestUtilities::get("dashboard") ?: "mp";
        $url = static::mwbeUrl($minority_type_id,$dashboard);

        return $url;
    }

    /**
     * Function to build the M/WBE Category url
     * Do not hyperlink if you are looking at prime data (prime dashboard)
     * Do not hyperlink if not M/WBE certified
     * @param $minority_type_id
     * @return string
     */
    static function SubMwbeCategoryUrl($minority_type_id) {

        // Do not hyperlink if you are looking at prime data
        // Do not hyperlink if not M/WBE certified
        $is_mwbe_certified = MinorityTypeService::isMWBECertified($minority_type_id);
        if(Dashboard::isPrimeDashboard() || !$is_mwbe_certified) {
            return null;
        }
        $dashboard = "sp";
        $url = static::mwbeUrl($minority_type_id,$dashboard);

        return $url;
    }

    /**
     * Function to build the M/WBE Category url
     * @param $minority_type_id
     * @param $dashboard
     * @return string
     */
    static function mwbeUrl($minority_type_id, $dashboard) {
        $minority_type_id = $minority_type_id == 4 || $minority_type_id == 5 ? '4~5' : $minority_type_id;
        $url = '/spending_landing'
            . _checkbook_project_get_year_url_param_string()
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('category')
            . '/dashboard/'. $dashboard
            . '/mwbe/'. $minority_type_id
            . '?expandBottomCont=true';

        return $url;
    }

    /**
     * Gets the YTD Spending link in a generic way
     * @param $dynamic_parameter - custom dynamic parameters to be used in the URL
     * @param null $legacy_node_id
     * @return string
     */
    static function ytdSpendingUrl($dynamic_parameter, $legacy_node_id = null) {

        $legacy_node_id = isset($legacy_node_id) ? '/smnid/'.$legacy_node_id : '';
        $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';

        $url = '/panel_html/spending_transactions/spending/transactions'
            . RequestUtilities::buildUrlFromParam('vendor')
            . static::getVendorFacetParameter()
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . _checkbook_project_get_year_url_param_string()
            . $dynamic_parameter
            . $legacy_node_id;

        return $url;
    }

    /**
     * Function to build the footer url for the spending widgets
     * @param $parameters
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($parameters,$legacy_node_id = null) {
        $legacy_node_id = isset($legacy_node_id) ? '/dtsmnid/'.$legacy_node_id : '';
        $url = '/panel_html/spending_transactions/spending/transactions'
            . RequestUtilities::buildUrlFromParam('vendor')
            . static::getVendorFacetParameter()
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
            . _checkbook_project_get_year_url_param_string()
            . $legacy_node_id;
        
        return $url;
    }

    /**
     * Returns the vendor or sub vendor id for the vendor facets
     * @return string
     */
    static function getVendorFacetParameter() {

        $facet_vendor_id = Dashboard::isSubDashboard()
            ? RequestUtilities::get("subvendor")
            : RequestUtilities::get("vendor");

        $facet_vendor_id = isset($facet_vendor_id) ? "/fvendor/" . $facet_vendor_id : '';

        return $facet_vendor_id;
    }

} 
