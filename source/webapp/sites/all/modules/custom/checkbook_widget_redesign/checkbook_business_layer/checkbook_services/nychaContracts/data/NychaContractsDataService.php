<?php

/**
 * Class NychaContractsDataService
 */
class NychaContractsDataService extends DataService implements INychaContractsDataService {
    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsByVendors($parameters, $limit = null, $orderBy = null)
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsByAwardMethods($parameters, $limit = null, $orderBy = null)
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsByPurchaseOrders($parameters, $limit = null, $orderBy = null)
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsByBoroughs($parameters, $limit = null, $orderBy = null)
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsBlanketAgreements($parameters, $limit = null, $orderBy = null)
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsBlanketAgreementModifications($parameters, $limit = null, $orderBy = null)
    {
        $parameters["is_modification"] = true;
        return $this->configureNycha('GetContractsBlanketAgreements',$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsPlannedAgreements($parameters, $limit = null, $orderBy = null)
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsPlannedAgreementModifications($parameters, $limit = null, $orderBy = null)
    {
        $parameters["is_modification"] = true;
        return $this->configureNycha('GetContractsPlannedAgreements',$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsByDepartments($parameters, $limit = null, $orderBy = null)
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsByIndustries($parameters, $limit = null, $orderBy = null)
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsByRespCenters($parameters, $limit = null, $orderBy = null)
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    function GetContractsBySize($parameters, $limit = null, $orderBy = null)
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @return DataService
     */
    function GetCountContracts($parameters){
        return $this->configureNycha(__FUNCTION__,$parameters);
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
