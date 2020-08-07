<?php

class NychaSpendingDataService extends DataService implements INychaSpendingDataService {
  /* NYCHA Spending */
    function GetNychaSpendingByChecks($parameters, $limit = null, $orderBy = null) {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function  GetNychaSpendingByVendors($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function  GetNychaSpendingByContracts($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetNychaSpendingByExpenseCategories($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetNychaSpendingByResponsibilityCenters($parameters, $limit = null, $orderBy = null){
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetNychaSpendingByIndustries($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetNychaSpendingByDepartment($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetNychaSpendingByFundingSource($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetCountNychaContracts($parameters) {
        return $this->configureNycha(__FUNCTION__,$parameters);
    }
  /**
   * Common function that automatically configures the Citywide Spending sql
   * @param $dataFunction
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null) {
    return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::NychaSpending);
  }
}
