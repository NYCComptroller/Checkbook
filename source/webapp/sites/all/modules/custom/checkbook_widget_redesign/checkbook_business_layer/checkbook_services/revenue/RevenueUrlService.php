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
    static function getFooterUrl($parameters=null,$legacy_node_id = null) {
        $parameters = isset($parameters) ? $parameters : null;
        $legacy_node_id = isset($legacy_node_id) ? $legacy_node_id : null;
        $url = '/panel_html/revenue_transactions/budget/transactions'.'/dtsmnid/' . $legacy_node_id;
        $url .= RequestUtilities::buildUrlFromParam('agency');
        $url .= RequestUtilities::buildUrlFromParam('revcat');
        $url .= RequestUtilities::buildUrlFromParam('fundsrccode');
        $url .= _checkbook_project_get_year_url_param_string();
        return $url;
    }

    /**
     * @param $footerUrl
     * @param $crossYearFooterUrl
     * @return string
     */
    static function getCrossYearFooterUrl($footerUrl=null,$crossYearFooterUrl=null) {
        $footerUrl = isset($footerUrl) ? $footerUrl : null;
        $crossYearFooterUrl = isset($crossYearFooterUrl) ? $crossYearFooterUrl : null ;
        $url = str_replace('/revenue_transactions/budget/transactions/',$crossYearFooterUrl,$footerUrl);
        return $url;
    }

    /**
     * @param $agencyId
     * @param null $legacy_node_id
     * @return string
     */
    static function getAgencyUrl($agencyId,$legacy_node_id = null) {
        $legacy_node_id = isset($legacy_node_id) ? $legacy_node_id : null;
        $url = '/revenue'.RequestUtilities::buildUrlFromParam('year')
                .RequestUtilities::buildUrlFromParam('yeartype')
                .'/agency/'.$agencyId;
        return $url;
    }

    /**
     * @param $param Parameter Name
     * @param $value pParameter Value
     * @param $legacy_node_id Legacy Node Id
     * @param $crorss_year Prevoius Year
     * @return string
     */
    static function getRecognizedAmountUrl($param, $value,$legacy_node_id = null, $crorss_year = null) {
        $legacy_node_id = isset($legacy_node_id)?$legacy_node_id : null;
        $url = '/panel_html/revenue_transactions/budget/transactions'.'/smnid/' . $legacy_node_id;
        $url .= RequestUtilities::buildUrlFromParam('agency');
        $url .= RequestUtilities::buildUrlFromParam('revcat');
        $url .= RequestUtilities::buildUrlFromParam('fundsrccode');
        $url .= _checkbook_project_get_year_url_param_string();
        $url .= isset($crorss_year) ? '/fiscal_year/'.(RequestUtilities::get('year')+$crorss_year) : "";
        $url .= '/'.$param.'/'.$value;
        return $url;
    }
}
