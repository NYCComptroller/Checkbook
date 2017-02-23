<?php

class BudgetDataService extends DataService implements IBudgetDataService {

    function GetAgenciesByBudget($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetAgenciesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetAgenciesByPercentDifference($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetExpenseCategoriesByBudget($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetExpenseCategoriesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetExpenseCategoriesByPercentDifference($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetDepartmentsByBudget($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetDepartmentsByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetDepartmentsByPercentDifference($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetExpenseBudgetCategories($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetExpenseBudgetCategoriesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
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
        return $this->configure(__FUNCTION__,$parameters,$limit,$orderBy,SqlConfigPath::CitywideBudget);
    }
}
