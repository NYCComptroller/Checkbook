<?php

class ContractsDataService extends AbstractDataService {

    const sqlConfigPath = "contracts/contracts";

    function __construct() {
        parent::__construct(self::sqlConfigPath);
    }

    public function GetContracts($parameters, $limit = null, $orderBy = null) {
        return $this->getData($parameters, $limit, $orderBy, "GetContracts");
    }

    public function GetSubvendorStatusByPrimeContractID($parameters, $limit = null, $orderBy = null) {
        return $this->getData($parameters, $limit, $orderBy, "GetSubvendorStatusByPrimeContractID");
    }

    public function GetPrimeContractSubVenReporting($parameters, $limit = null, $orderBy = null) {
        return $this->getData($parameters, $limit, $orderBy, "GetPrimeContractSubVenReporting");
    }

    public function GetSubvendorContractsByPrime($parameters, $limit = null, $orderBy = null) {
        return $this->getData($parameters, $limit, $orderBy, "GetSubvendorContractsbyPrime");
    }
    public function GetSubvendorStatusByPrimeCounts($parameters, $limit = null, $orderBy = null) {
        return $this->getData($parameters, $limit, $orderBy, "GetSubvendorStatusByPrimeCounts");
    }
    public function GetSubvendorContractsbyPrimeCounts($parameters, $limit = null, $orderBy = null) {
        return $this->getData($parameters, $limit, $orderBy, "GetSubvendorContractsbyPrimeCounts");
    }
} 