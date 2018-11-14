<?php

class NychaContractsDataService extends DataService implements INychaContractsDataService {
    function GetContractsByVendors($parameters, $limit = null, $orderBy = null) {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetCountContractsByVendors($parameters) {
        return $this->configureNycha(__FUNCTION__,$parameters);
    }
    function GetContractsByAwardMethods($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetContractsByPurchaseOrders($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetContractsByBoroughs($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetContractsBlanketAgreements($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetContractsBlanketAgreementModifications($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetContractsByGrants($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * Common function that automatically configures the NYCHA Contracts sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null) {
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::NychaContracts);
    }
}
