<?php

class NychaRevenueDataService extends DataService implements INychaRevenueDataService {
  /* NYCHA Revenue */
  function GetNychaExpenseCategoriesByRevenue($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }
  function GetNychaResponsibilityCenterByRevenue($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }
  function GetNychaFundingSourceByRevenue($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }
  function GetNychaProgramByRevenue($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }
  function GetNychaProjectByRevenue($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }
  function GetNychaRevenueCategoriesByRevenue($parameters, $limit = null, $orderBy = null) {
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
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::NychaRevenue);
    }
}
