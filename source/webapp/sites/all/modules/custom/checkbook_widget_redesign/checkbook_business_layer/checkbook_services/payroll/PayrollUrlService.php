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
        $agency = RequestUtilities::_getUrlParamString('agency');
        $title = RequestUtilities::_getUrlParamString('title');
        $legacy_node_id = isset($legacy_node_id) ? '/smnid/'.$legacy_node_id : '';
        $data_source = RequestUtilities::_getUrlParamString('datasource');

        if ($agency != '') {
            $url = '/panel_html/payroll_agencytransactions/payroll/agencywide/transactions'
                . RequestUtilities::_getUrlParamString('yeartype')
                . RequestUtilities::_getUrlParamString('year')
                . $data_source
                . $legacy_node_id
                . $agency.$title;
        }
        else {
            $url = '/panel_html/payroll_nyc_transactions/payroll/transactions'
                . RequestUtilities::_getUrlParamString('yeartype')
                . RequestUtilities::_getUrlParamString('year')
                . RequestUtilities::_getUrlParamString('title')
                . $data_source
                . $legacy_node_id;
        }
        return $url;
    }

    static function getTitleFooterUrl($footerUrl, $widget){
        $url = null;
        switch($widget){
            case "landing":
                $url = "/panel_html/payroll_nyc_title_transactions/payroll/payroll_title/transactions";
                $filter = str_replace("/panel_html/payroll_nyc_transactions/payroll/transactions", $url, $footerUrl);
                break;
            case "agency":
                $url = "/panel_html/payroll_nyc_title_transactions/payroll/payroll_title/transactions";
                $filter = str_replace("/panel_html/payroll_agencytransactions/payroll/agencywide/transactions", $url, $footerUrl);
                break;
        }

        return $filter;
    }

    static function agencyNameUrl($agency_id)
    {
        $url = "/payroll/agency_landing"
            . RequestUtilities::_getUrlParamString("yeartype")
            . RequestUtilities::_getUrlParamString("year")
            . RequestUtilities::_getUrlParamString("title")
            . RequestUtilities::_getUrlParamString("datasource")
            . "/agency/" . $agency_id;
        return $url;
    }

    static function payUrl($agency, $legacy_node_id = null)
    {
        $agency = isset($agency) ? '/agency/' . $agency : '';


        $url = "/panel_html/payroll_agencytransactions/payroll/agencywide/transactions"
            . '/smnid/' . $legacy_node_id
            . $agency
            . RequestUtilities::_getUrlParamString("yeartype")
            . RequestUtilities::_getUrlParamString("year")
            . RequestUtilities::_getUrlParamString("datasource")
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
            . RequestUtilities::_getUrlParamString("datasource")
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
            . RequestUtilities::_getUrlParamString("datasource")
            . $employee;
        return $url;
    }

    static function titleUrl($title) {
        $title = isset($title) ? '/title/' . $title : '';

        $url = '/payroll/title_landing'
            . RequestUtilities::_getUrlParamString("yeartype")
            . RequestUtilities::_getUrlParamString("year")
            . RequestUtilities::_getUrlParamString("datasource")
            . $title;
        return $url;
    }

    static function titleAgencyUrl($agency, $title) {
        $agency = isset($agency) ? '/agency/' . $agency : '';
        $title = isset($title) ? '/title/' . $title : '';
        $year=RequestUtilities::getRequestParamValue('year');
        $yearType = RequestUtilities::getRequestParamValue('yeartype');
        $dataSource = RequestUtilities::_getUrlParamString("datasource");
        if($yearType=='C'){
            $url = '/payroll/title_landing/yeartype/C/year/'.$year
                . $agency
                . $dataSource
                . $title;
        }
        else {
            $url = '/payroll/title_landing/yeartype/B/year/118'
                . $agency
                . $dataSource
                . $title;
        }
        return $url;
    }

}
