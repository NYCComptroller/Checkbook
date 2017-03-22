<?php

class RevenueDataService extends DataService implements IRevenueDataService {

    function GetAgenciesByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetFundingClassesByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetRevenueCategoriesByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    
    function GetAgenciesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    
    function GetRevenueCategoriesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    
    function GetRevenueFundingClassesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    /**
     * Common function that automatically configures the Citywide Revenue sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureCitywide($dataFunction, $parameters, $limit = null, $orderBy = null) {
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::CitywideRevenue);
    }
}

