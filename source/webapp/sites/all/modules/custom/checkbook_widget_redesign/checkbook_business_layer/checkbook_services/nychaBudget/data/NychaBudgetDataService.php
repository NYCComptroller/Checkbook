<?php

class NychaBudgetDataService extends DataService implements INychaBudgetDataService {
  /* NYCHA Budget */
  function GetNychaExpenseCategoriesByBudget($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  function GetNychaExpenseCategoriesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  function GetResponsibilityCenters($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  function GetResponsibilityCentersByCommittedExpense($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  function GetFundingSources($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  function GetFundingSourcesByCommittedExpense($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  function GetPrograms($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  function GetProgramsByCommittedExpense($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  function GetProjects($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  function GetProjectsByCommittedExpense($parameters, $limit = null, $orderBy = null) {
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
    private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null) {
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::NychaBudget);
    }
}
