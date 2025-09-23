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

use Drupal\checkbook_domain\Sql\SqlConfigPath;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_services\Common\DataService;


class PayrollDataService extends DataService implements IPayrollDataService {
    function GetAgenciesByOvertime($parameters, $limit = null, $orderBy = null) {
        if(Datasource::isNYCHA()){
            return $this->configureNYCHA(__FUNCTION__, $parameters, $limit, $orderBy);
        }else {
            return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
        }
    }

    function GetAgenciesByPayroll($parameters, $limit = null, $orderBy = null) {
        if(Datasource::isNYCHA()){
            return $this->configureNYCHA(__FUNCTION__, $parameters, $limit, $orderBy);
        }else {
            return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
        }
    }

    function GetAnnualSalariesPerAgency($parameters, $limit = null, $orderBy = null) {
        if(Datasource::isNYCHA()){
            return $this->configureNYCHA(__FUNCTION__, $parameters, $limit, $orderBy);
        }else {
            return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
        }
    }

    function GetAnnualSalaries($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
    }
    function GetNonSalariedRates($parameters, $limit = null, $orderBy = null)
    {
        if (Datasource::isNYCHA()) {
            return $this->configureNYCHA(__FUNCTION__, $parameters, $limit, $orderBy);
        }
    }

    function GetTitlesByNumberOfEmployees($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
    }

    function GetTitlesByAgency($parameters, $limit = null, $orderBy = null) {
        if(Datasource::isNYCHA()){
            return $this->configureNYCHA(__FUNCTION__, $parameters, $limit, $orderBy);
        }else {
            return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
        }
    }
    function GetTitlesByNonSalariedEmployees($parameters, $limit = null, $orderBy = null) {
        if(Datasource::isNYCHA()){
            return $this->configureNYCHA(__FUNCTION__, $parameters, $limit, $orderBy);
        }
    }

    function GetCountAgencies($parameters) {
        if(Datasource::isNYCHA()){
            return $this->configureNYCHA(__FUNCTION__, $parameters);
        }else {
            return $this->configureCitywide(__FUNCTION__, $parameters);
        }
    }

    function GetCountSalariedEmployees($parameters) {
        if(Datasource::isNYCHA()){
            return $this->configureNYCHA(__FUNCTION__, $parameters);
        }else {
            return $this->configureCitywide(__FUNCTION__, $parameters);
        }
    }

    function GetCountNonSalariedEmployees($parameters) {
        if(Datasource::isNYCHA()) {
            return $this->configureNYCHA(__FUNCTION__, $parameters);
        }
    }
    /**
     * Common function that automatically configures the Citywide Payroll sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureCitywide($dataFunction, $parameters, $limit = null, $orderBy = null) {
        return $this->configure($dataFunction, $parameters, $limit, $orderBy, SqlConfigPath::CitywidePayroll);
    }

    /**
     * Common function that configures the NYCHA sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureNYCHA($dataFunction, $parameters, $limit = null, $orderBy = null) {
        return $this->configure($dataFunction, $parameters, $limit, $orderBy, SqlConfigPath::NYCHAPayroll);
    }
}
