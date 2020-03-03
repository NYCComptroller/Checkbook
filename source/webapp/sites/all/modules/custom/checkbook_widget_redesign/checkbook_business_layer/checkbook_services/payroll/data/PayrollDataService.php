<?php

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