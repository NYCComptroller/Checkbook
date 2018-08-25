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

        $agency = RequestUtilities::buildUrlFromParam('agency');
        $title = RequestUtilities::buildUrlFromParam('title');
        $legacy_node_id = isset($legacy_node_id) ? '/smnid/'.$legacy_node_id : '';

        if ($agency != '') {
            $url = '/panel_html/payroll_agencytransactions/payroll/agencywide/transactions'
                . RequestUtilities::buildUrlFromParam('yeartype')
                . RequestUtilities::buildUrlFromParam('year')
                . $legacy_node_id
                . $agency.$title;
        }
        else {
            $url = '/panel_html/payroll_nyc_transactions/payroll/transactions'
                . RequestUtilities::buildUrlFromParam('yeartype')
                . RequestUtilities::buildUrlFromParam('year')
                . RequestUtilities::buildUrlFromParam('title')
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
            . RequestUtilities::buildUrlFromParam('yeartype')
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('title')
            . RequestUtilities::buildUrlFromParam('agency')
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
            . $employee;
        return $url;
    }

    static function titleUrl($title) {
        $title = isset($title) ? '/title/' . $title : '';

        $url = '/payroll/title_landing'
            . RequestUtilities::buildUrlFromParam('yeartype')
            . RequestUtilities::buildUrlFromParam('year')
            . $title;
        return $url;
    }

    static function titleAgencyUrl($agency, $title) {
        $agency = isset($agency) ? '/agency/' . $agency : '';
        $title = isset($title) ? '/title/' . $title : '';
        $year=RequestUtilities::get('year');
        $yearType = RequestUtilities::get('yeartype');
        if($yearType=='C'){
            $url = '/payroll/title_landing/yeartype/C/year/'.$year
                .$agency
                .$title;
        }
        else {
            $url = '/payroll/title_landing/yeartype/B/year/118'
                . $agency
                . $title;
        }
        return $url;
    }

}
