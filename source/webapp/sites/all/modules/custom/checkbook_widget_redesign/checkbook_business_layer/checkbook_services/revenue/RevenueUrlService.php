<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 01/10/2017
 * Time: 1:16 PM
 */
class RevenueUrlService {
    /**
     * @param $parameters
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($parameters,$legacy_node_id = null) {
        $url = '/panel_html/revenue_transactions/budget/transactions'.'/dtsmnid/' . $legacy_node_id;
        $url .= RequestUtilities::_getUrlParamString('agency');
        $url .= RequestUtilities::_getUrlParamString('revcat');
        $url .= RequestUtilities::_getUrlParamString('fundsrccode');
        $url .= _checkbook_project_get_year_url_param_string();
        return $url;
    }
    
    static function getRecognizedAmountUrl($param, $value,$legacy_node_id = null, $crorss_year = null) {
        $url = '/panel_html/revenue_transactions/budget/transactions'.'/smnid/' . $legacy_node_id;
        $url .= RequestUtilities::_getUrlParamString('agency');
        $url .= RequestUtilities::_getUrlParamString('revcat');
        $url .= RequestUtilities::_getUrlParamString('fundsrccode');
        $url .= _checkbook_project_get_year_url_param_string();
        $url .= isset($crorss_year) ? '/fiscal_year/'.(RequestUtilities::getRequestParamValue('year')+$crorss_year) : "";
        $url .= '/'.$param.'/'.$value;
        return $url;
    }
}