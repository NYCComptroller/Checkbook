<?php
/**
 * Created by PhpStorm.
 * User: pshirodkar
 * Date: 12/7/16
 * Time: 1:36 PM
 */

class SpendingUrlService {
    static function agencyUrl($agency_id){
        $url = '/spending_landing'
               .RequestUtilities::_getUrlParamString('vendor')
               .RequestUtilities::_getUrlParamString('category')
               .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
               ._checkbook_project_get_year_url_param_string()
               . '/agency/'. $agency_id;
        return $url;
    }          
} 