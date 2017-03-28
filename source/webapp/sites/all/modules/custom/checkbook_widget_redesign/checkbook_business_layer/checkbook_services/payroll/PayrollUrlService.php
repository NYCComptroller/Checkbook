<?php

class PayrollUrlService
{

    /**
     * @param $parameters
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($parameters, $legacy_node_id = null)
    {

        $legacy_node_id = isset($legacy_node_id) ? '/dtsmnid/'.$legacy_node_id : '';
        $url = '/panel_html/payroll_nyc_transactions/payroll/transactions'
            . RequestUtilities::_getUrlParamString('yeartype')
            . RequestUtilities::_getUrlParamString('year')
            . $legacy_node_id;
        return $url;
    }

    static function getTitleFooterUrl($footerUrl, $widget){
        $url = null;
        $old = array("/panel_html/payroll_nyc_transactions/payroll/transactions", "dtsmnid");
        switch($widget){
            case "landing":
                $url = array("/panel_html/payroll_nyc_title_transactions/payroll/payroll_title/transactions", "smnid");
                break;
            case "agency":
                $url = array("/panel_html/payroll_nyc_title_transactions/payroll/payroll_title/transactions"
                    . RequestUtilities::_getUrlParamString("agency"),
                     "smnid");
                break;
        }
        if(isset($url)){
            return str_replace($old, $url, $footerUrl);
        }else{
            return $footerUrl;
        }
    }

    static function agencyNameUrl($agency_id)
    {
        $url = "/payroll/agency_landing"
            . RequestUtilities::_getUrlParamString("yeartype")
            . RequestUtilities::_getUrlParamString("year")
            . RequestUtilities::_getUrlParamString("title")
            . RequestUtilities::_getUrlParamString("agency")
            . "/agency/" . $agency_id;
        return $url;
    }

    static function payUrl($agency)
    {
        $agency = isset($agency) ? '/agency/' . $agency : '';

        $url = "/panel_html/payroll_agencytransactions/payroll/agencywide/transactions"
            . $agency
            . RequestUtilities::_getUrlParamString("yeartype")
            . RequestUtilities::_getUrlParamString("year")
            . RequestUtilities::_getUrlParamString("title");
        return $url;
    }

    static function annualSalaryUrl($agency, $employee) {
        $agency = isset($agency) ? '/agency/' . $agency : '';
        $employee = isset($employee) ? "/abc/" . $employee : '';

        $url = "/panel_html/payroll_employee_transactions/payroll/employee/transactions"
            . $agency
            . RequestUtilities::_getUrlParamString("yeartype")
            . RequestUtilities::_getUrlParamString("year")
            . "/salamttype/1"
            . $employee;
        return $url;
    }

    static function annualSalaryPerAgencyUrl($agency, $employee) {
        $agency = isset($agency) ? '/agency/' . $agency : '';
        $employee = isset($employee) ? "/abc/" . $employee : '';

        $url = "/panel_html/payroll_employee_transactions/payroll/employee/transactions"
            . $agency
            . RequestUtilities::_getUrlParamString("yeartype")
            . RequestUtilities::_getUrlParamString("year")
            . $employee;
        return $url;
    }

    static function titleUrl($title) {
        $title = isset($title) ? '/title/' . $title : '';

        $url = '/payroll/title_landing'
            . RequestUtilities::_getUrlParamString("yeartype")
            . RequestUtilities::_getUrlParamString("year")
            . $title;
        return $url;
    }

    static function titleAgencyUrl($agency, $title) {
        $agency = isset($agency) ? '/agency/' . $agency : '';
        $title = isset($title) ? '/title/' . $title : '';

        $url = '/payroll/title_landing/yeartype/B/year/118'
            .$agency
            .$title;
        return $url;
    }

}
