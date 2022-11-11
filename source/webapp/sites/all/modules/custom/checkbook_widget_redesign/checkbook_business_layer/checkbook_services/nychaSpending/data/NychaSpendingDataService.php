<?php

class NychaSpendingDataService extends DataService implements INychaSpendingDataService {
  /* NYCHA Spending */
  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByChecks($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function  GetNychaSpendingByVendors($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function  GetNychaSpendingByContracts($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByExpenseCategories($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByResponsibilityCenters($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByIndustries($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByDepartment($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByFundingSource($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @return DataService
   */
    public function GetCountNychaContracts($parameters): DataService
    {
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
  private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configure($dataFunction, SqlConfigPath::NychaSpending, $parameters,$limit,$orderBy);
  }
}
