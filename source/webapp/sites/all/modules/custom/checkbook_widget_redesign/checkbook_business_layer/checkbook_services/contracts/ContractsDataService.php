<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 6/15/16
 * Time: 2:38 PM
 */

class ContractsDataService extends AbstractDataService {

    const sqlConfigPath = "contracts/contracts";

    public function GetContracts($parameters, $limit, $orderBy) {
        return $this->getData($parameters, $limit, $orderBy, "GetContracts");
    }

    public function GetSubvendorStatusByPrimeContractID($parameters, $limit, $orderBy) {
        return $this->getData($parameters, $limit, $orderBy, "GetSubvendorStatusByPrimeContractID");
    }

    public function GetPrimeContractSubVenReporting($parameters, $limit, $orderBy) {
        return $this->getData($parameters, $limit, $orderBy, "GetPrimeContractSubVenReporting");
    }

    public function GetSubvendorContractsByPrime($parameters, $limit, $orderBy) {
        return $this->getData($parameters, $limit, $orderBy, "GetSubvendorContractsbyPrime");
    }
} 