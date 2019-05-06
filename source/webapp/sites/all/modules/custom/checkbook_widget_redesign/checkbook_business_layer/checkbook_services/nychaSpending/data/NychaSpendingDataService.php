<?php

class NychaSpendingDataService extends DataService implements INychaSpendingDataService {

  /* Citywide Spending */
  function GetSpendingByChecks($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
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
