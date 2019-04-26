<?php

class PayrollUrlService
{

    /**
     * @param $parameters
     * @param null $legacy_node_id
     * @param null $payroll_type
     * @return string
     */
    static function getFooterUrl($parameters, $legacy_node_id = null,$payroll_type=null)
    {
        $legacy_node_id = isset($legacy_node_id) ? '/smnid/'.$legacy_node_id : '';
        $payroll_type = isset($payroll_type) ? '/payroll_type/'.$payroll_type: '';
        $data_source = RequestUtilities::_getUrlParamString('datasource');
        $agency = RequestUtilities::get('agency');

        if ($agency) {
            $url = '/panel_html/payroll_agencytransactions/payroll/agencywide/transactions'
                . RequestUtilities::buildUrlFromParam('yeartype')
                . RequestUtilities::buildUrlFromParam('year')
                . $data_source
                . $legacy_node_id
                .$payroll_type
                . RequestUtilities::buildUrlFromParam('agency')
                . RequestUtilities::buildUrlFromParam('title');
        } else {
            $url = '/panel_html/payroll_nyc_transactions/payroll/transactions'
                . RequestUtilities::buildUrlFromParam('yeartype')
                . RequestUtilities::buildUrlFromParam('year')
                . RequestUtilities::buildUrlFromParam('title')
                . $data_source
                . $legacy_node_id
                .$payroll_type;
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
            . RequestUtilities::buildUrlFromParam('yeartype')
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('title')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('datasource')
            . "/agency/" . $agency_id;
        return $url;
    }

    static function payUrl($agency, $legacy_node_id = null)
    {
        $agency = isset($agency) ? '/agency/' . $agency : '';


        $url = "/panel_html/payroll_agencytransactions/payroll/agencywide/transactions"
            . '/smnid/' . $legacy_node_id
            . $agency
            . RequestUtilities::buildUrlFromParam('yeartype')
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('datasource')
            . RequestUtilities::buildUrlFromParam('title');
        return $url;
    }

    static function annualSalaryUrl($agency, $employee) {
        $agency = isset($agency) ? '/agency/' . $agency : '';
        $employee = isset($employee) ? "/abc/" . $employee : '';

        $url = "/panel_html/payroll_employee_transactions/payroll/employee/transactions"
            . $agency
            . RequestUtilities::buildUrlFromParam('yeartype')
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('datasource')
            . "/salamttype/1"
            . $employee;
        return $url;
    }

    static function annualSalaryPerAgencyUrl($agency, $employee) {
        $agency = isset($agency) ? '/agency/' . $agency : '';
        $employee = isset($employee) ? "/abc/" . $employee : '';

        $url = "/panel_html/payroll_employee_transactions/payroll/employee/transactions"
            . $agency
            . RequestUtilities::buildUrlFromParam('yeartype')
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('datasource')
            . $employee;
        return $url;
    }

    static function titleUrl($title) {
        $title = isset($title) ? '/title/' . $title : '';

        $url = '/payroll/title_landing'
            . RequestUtilities::buildUrlFromParam('yeartype')
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('datasource')
            . $title;
        return $url;
    }

    static function titleAgencyUrl($agency, $title) {
        $agency = isset($agency) ? '/agency/' . $agency : '';
        $title = isset($title) ? '/title/' . $title : '';

        $url = '/payroll/title_landing'
            . RequestUtilities::buildUrlFromParam('yeartype')
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('datasource')
            . $agency
            . $title;

        return $url;
    }

}
