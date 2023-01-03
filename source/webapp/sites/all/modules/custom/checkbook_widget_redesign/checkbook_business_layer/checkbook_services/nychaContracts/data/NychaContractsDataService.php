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
    public function GetContractsByVendors($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByAwardMethods($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByPurchaseOrders($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByBoroughs($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsBlanketAgreements($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsBlanketAgreementModifications($parameters, $limit = null, $orderBy = null): DataService
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
    public function GetContractsPlannedAgreements($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsPlannedAgreementModifications($parameters, $limit = null, $orderBy = null): DataService
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
    public function GetContractsByDepartments($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByIndustries($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByRespCenters($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsBySize($parameters, $limit = null, $orderBy = null): DataService
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
     * Common function that automatically configures the NYCHA Contracts sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configure($dataFunction, SqlConfigPath::NychaContracts, $parameters,$limit,$orderBy);
    }
}
