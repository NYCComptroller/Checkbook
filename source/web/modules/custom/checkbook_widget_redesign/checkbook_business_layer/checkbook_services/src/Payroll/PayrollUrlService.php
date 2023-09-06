<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_services\Payroll;

use Drupal\checkbook_infrastructure_layer\Constants\Payroll\PayrollLandingPage;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

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
            $url = '/payroll/agencywide/transactions'
                . RequestUtilities::buildUrlFromParam('yeartype')
                . RequestUtilities::buildUrlFromParam('year')
                . $data_source
                . $legacy_node_id
                .$payroll_type
                . RequestUtilities::buildUrlFromParam('agency')
                . RequestUtilities::buildUrlFromParam('title');
        } else {
            $url = '/payroll/transactions'
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
                $url = "/payroll/payroll_title/transactions";
                $filter = str_replace("/payroll/transactions", $url, $footerUrl);
                break;
            case "agency":
                $url = "/payroll/payroll_title/transactions";
                $filter = str_replace("/payroll/agencywide/transactions", $url, $footerUrl);
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


        $url = "/payroll/agencywide/transactions"
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

        $url = "/payroll/employee/transactions"
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

        $url = "/payroll/employee/transactions"
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

  static function payrollLandingPage() {
    $agency = isset($agency) ? '/agency/' . $agency : '';
    $title = isset($title) ? '/title/' . $title : '';
    if (PayrollLandingPage::getCurrent() == PayrollLandingPage::AGENCY_LEVEL) {
      $url = '/payroll/agency_landing';
    }
    elseif(PayrollLandingPage::getCurrent() == PayrollLandingPage::TITLE_LEVEL) {
      $url = '/payroll/title_landing';
    }
    else{
      $url = '/payroll';
    }

    $url .= RequestUtilities::buildUrlFromParam('yeartype')
      . RequestUtilities::buildUrlFromParam('year')
      . RequestUtilities::buildUrlFromParam('datasource')
      . $agency
      . $title;

    return $url;
  }


}
