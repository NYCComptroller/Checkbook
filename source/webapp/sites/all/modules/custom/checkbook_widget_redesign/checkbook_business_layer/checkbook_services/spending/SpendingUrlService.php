<?php
/**
 * Created by PhpStorm.
 * User: pshirodkar
 * Date: 12/7/16
 * Time: 1:36 PM
 */

class SpendingUrlService {
    
    /**
     * @param $agency_id
     * @return string
     */
    static function agencyUrl($agency_id){
        $url = '/spending_landing'
               .RequestUtilities::_getUrlParamString('vendor')
               .RequestUtilities::_getUrlParamString('category')
               .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
               ._checkbook_project_get_year_url_param_string()
               . '/agency/'. $agency_id;
        return $url;
    } 
    
      /**
     * @param $param - Widget Name to be used in the URL
     * @param $value - value of @param to be used in the URL
     * @return string
     */
    static function ytdSpendindUrl($param, $value){
        $url = '/panel_html/spending_transactions/spending/transactions'
               .RequestUtilities::_getUrlParamString('vendor')
               .RequestUtilities::_getUrlParamString('category')
               .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
               ._checkbook_project_get_year_url_param_string()
               . '/'.$param.'/'. $value;
        return $url;
    }
    
    /**
     * @param $parameters
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($parameters,$legacy_node_id = null) {
        $dtsmnid_param = isset($legacy_node_id) ? '/dtsmnid/'.$legacy_node_id : '';
        $url = '/panel_html/spending_transactions/spending/transactions'
                .RequestUtilities::_getUrlParamString('vendor')
                .RequestUtilities::_getUrlParamString('fvendor')
                .RequestUtilities::_getUrlParamString('agency')
                .RequestUtilities::_getUrlParamString('category')
                .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
               ._checkbook_project_get_year_url_param_string();
        
        return $url;
    }
} 