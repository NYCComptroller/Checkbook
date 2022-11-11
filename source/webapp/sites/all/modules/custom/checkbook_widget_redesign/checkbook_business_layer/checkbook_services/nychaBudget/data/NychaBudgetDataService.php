<?php

class NychaBudgetDataService extends DataService implements INychaBudgetDataService {
  /* NYCHA Budget */
  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaExpenseCategoriesByBudget($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaExpenseCategoriesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaExpenseCategoriesByPercentDifference($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaResponsibilityCentersByPercentDifference($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetResponsibilityCenters($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetResponsibilityCentersByCommittedExpense($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetFundingSources($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetFundingSourcesByCommittedExpense($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaFundingSourcesByPercentDifference($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetPrograms($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetProgramsByCommittedExpense($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaProgramsByPercentDifference($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetProjects($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetProjectsByCommittedExpense($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetProjectsByPercentDifference($parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

    /**
     * Common function that automatically configures the Nycha Budget sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configure($dataFunction, SqlConfigPath::NychaBudget, $parameters,$limit,$orderBy);
    }
}
