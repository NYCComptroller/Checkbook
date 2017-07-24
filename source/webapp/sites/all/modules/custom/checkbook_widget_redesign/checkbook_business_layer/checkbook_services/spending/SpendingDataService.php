<?php

class SpendingDataService extends DataService implements ISpendingDataService {

    /* Citywide Spending */
    function GetSpendingByChecks($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSpendingByAgencies($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSpendingByContracts($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSpendingByExpenseCategories($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSpendingByDepartments($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSpendingByPrimeVendors($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSpendingByIndustries($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSpendingByPayrollAgencies($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetCountContracts($parameters) {
        return $this->configureCitywide(__FUNCTION__,$parameters);
    }

    function GetCountPrimeVendors($parameters) {
        return $this->configureCitywide(__FUNCTION__,$parameters);
    }

    /* Sub Vendors Spending */
    function GetSubVendorSpendingByChecks($parameters, $limit = null, $orderBy = null) {
        return $this->configureSubVendor(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSubVendorSpendingByAgencies($parameters, $limit = null, $orderBy = null) {
        return $this->configureSubVendor(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSubVendorSpendingByPrimeVendors($parameters, $limit = null, $orderBy = null) {
        return $this->configureSubVendor(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSubVendorSpendingBySubVendors($parameters, $limit = null, $orderBy = null) {
        return $this->configureSubVendor(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSubVendorSpendingByPrimeSubVendors($parameters, $limit = null, $orderBy = null) {
        return $this->configureSubVendor(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSubVendorSpendingBySubContracts($parameters, $limit = null, $orderBy = null) {
        return $this->configureSubVendor(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSubVendorSpendingByContracts($parameters, $limit = null, $orderBy = null) {
        return $this->configureSubVendor(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetSubVendorSpendingByIndustries($parameters, $limit = null, $orderBy = null) {
        return $this->configureSubVendor(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetCountSubVendorPrimeVendors($parameters) {
        return $this->configureSubVendor(__FUNCTION__,$parameters);
    }

    function GetCountSubVendors($parameters) {
        return $this->configureSubVendor(__FUNCTION__,$parameters);
    }

    function GetCountSubContracts($parameters) {
        return $this->configureSubVendor(__FUNCTION__,$parameters);
    }

    function GetCountSubVendorContracts($parameters) {
        return $this->configureSubVendor(__FUNCTION__,$parameters);
    }

    /* OGE Spending */
    function GetOGESpendingByChecks($parameters, $limit = null, $orderBy = null) {
        return $this->configureOge(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetOGESpendingByExpenseCategories($parameters, $limit = null, $orderBy = null) {
        return $this->configureOge(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetOGESpendingByPrimeVendors($parameters, $limit = null, $orderBy = null) {
        return $this->configureOge(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetOGESpendingByDepartments($parameters, $limit = null, $orderBy = null) {
        return $this->configureOge(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetOGESpendingByContracts($parameters, $limit = null, $orderBy = null) {
        return $this->configureOge(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetCountOGEContracts($parameters) {
        return $this->configureOge(__FUNCTION__,$parameters);
    }

    function GetCountOGEPrimeVendors($parameters) {
        return $this->configureOge(__FUNCTION__,$parameters);
    }

    /**
     * Common function that automatically configures the Citywide Spending sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureCitywide($dataFunction, $parameters, $limit = null, $orderBy = null) {
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::CitywideSpending);
    }

    /**
     * Common function that automatically configures the Citywide Spending sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureOge($dataFunction, $parameters, $limit = null, $orderBy = null) {
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::OgeSpending);
    }

    /**
     * Common function that automatically configures the Sub Vendor Spending sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureSubVendor($dataFunction, $parameters, $limit = null, $orderBy = null) {
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::SubVendorsSpending);
    }
}
