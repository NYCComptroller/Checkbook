<?php

class PayrollUrlService
{

    /**
     * @param $parameters
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($legacy_node_id = null)
    {
        $legacy_node_id = isset($legacy_node_id) ? '/smnid/' . $legacy_node_id : '';

        $url = '/panel_html/payroll_nyc_transactions/payroll/transactions'
            . RequestUtilities::_getUrlParamString('yeartype')
            . RequestUtilities::_getUrlParamString('year')
            . $legacy_node_id;
        return $url;
    }

    static function agencyNameUrl($agency_id)
    {
        $url = "/payroll/agency_landing"
            . RequestUtilities::_getUrlParamString("yeartype")
            . RequestUtilities::_getUrlParamString("year")
            . RequestUtilities::_getUrlParamString("agency")
            . "/agency/" . $agency_id;
        return $url;
    }

    static function payUrl($dynamic_parameter, $legacy_node_id = null)
    {
        $legacy_node_id = isset($legacy_node_id) ? '/smnid/' . $legacy_node_id : '';
        $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';

        $url = "/panel_html/payroll_agencytransactions/payroll/agencywide/transactions"
            . RequestUtilities::_getUrlParamString("yeartype")
            . RequestUtilities::_getUrlParamString("year")
            . $dynamic_parameter
            . $legacy_node_id;
        return $url;
    }

}
