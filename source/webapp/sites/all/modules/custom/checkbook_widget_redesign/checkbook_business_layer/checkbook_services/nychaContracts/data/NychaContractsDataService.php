<?php

class NychaContractsDataService extends DataService implements INychaContractsDataService {
    function GetContractsByVendors($parameters, $limit = null, $orderBy = null) {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetCountContractsByVendors($parameters) {
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
    private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null) {log_error(SqlConfigPath::NychaContracts);
        var_dump(SqlConfigPath::NychaContracts);
        return $this->configureNycha($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::NychaContracts);
    }
}
