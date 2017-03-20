<?php

class PayrollDataService extends DataService implements IPayrollDataService {

    function GetAgenciesByOvertime($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
    }

    function GetAgenciesByPayroll($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
    }

    function GetAnnualSalariesPerAgency($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
    }

    function GetAnnualSalaries($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
    }

    function GetTitlesByNumberOfEmployees($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
    }

    function GetTitlesByAgency($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__, $parameters, $limit, $orderBy);
    }

    function GetCountAgencies($parameters) {
        return $this->configureCitywide(__FUNCTION__, $parameters);
    }

    function GetCountSalariedEmployees($parameters) {
        return $this->configureCitywide(__FUNCTION__, $parameters);
    }

    /**
     * Common function that automatically configures the Citywide Budget sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureCitywide($dataFunction, $parameters, $limit = null, $orderBy = null) {
        return $this->configure($dataFunction, $parameters, $limit, $orderBy, SqlConfigPath::CitywidePayroll);
    }
}