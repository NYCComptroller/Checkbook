<?php

class ContractsDataService extends DataService implements IContractsDataService {

    function GetContracts($parameters, $limit = null, $orderBy = null) {
        $sqlConfigPath = $parameters['contract_status'] == ContractStatus::PENDING
            ? SqlConfigPath::PendingContracts
            : SqlConfigPath::CitywideContracts;

        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath($sqlConfigPath)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractModifications($parameters, $limit = null, $orderBy = null) {
        $parameters["is_modification"] = true;
        return $this->GetContracts($parameters,$limit,$orderBy);
    }

    function GetMasterAgreementContracts($parameters, $limit = null, $orderBy = null) {
        return $this->GetContracts($parameters,$limit,$orderBy);
    }

    function GetMasterAgreementContractModifications($parameters, $limit = null, $orderBy = null) {
        $parameters["is_modification"] = true;
        return $this->GetContracts($parameters,$limit,$orderBy);
    }

    function GetContractsByAgencies($parameters, $limit = null, $orderBy = null) {
        $sqlConfigPath = $parameters['contract_status'] == ContractStatus::PENDING
            ? SqlConfigPath::PendingContracts
            : SqlConfigPath::CitywideContracts;

        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath($sqlConfigPath)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractsByDepartments($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractsByAwardMethods($parameters, $limit = null, $orderBy = null) {
        $sqlConfigPath = $parameters['contract_status'] == ContractStatus::PENDING
            ? SqlConfigPath::PendingContracts
            : SqlConfigPath::CitywideContracts;

        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath($sqlConfigPath)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractsByPrimeVendors($parameters, $limit = null, $orderBy = null) {
        $sqlConfigPath = $parameters['contract_status'] == ContractStatus::PENDING
            ? SqlConfigPath::PendingContracts
            : SqlConfigPath::CitywideContracts;

        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath($sqlConfigPath)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractsByIndustries($parameters, $limit = null, $orderBy = null) {
        $sqlConfigPath = $parameters['contract_status'] == ContractStatus::PENDING
            ? SqlConfigPath::PendingContracts
            : SqlConfigPath::CitywideContracts;

        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath($sqlConfigPath)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractsBySize($parameters, $limit = null, $orderBy = null) {
        $sqlConfigPath = $parameters['contract_status'] == ContractStatus::PENDING
            ? SqlConfigPath::PendingContracts
            : SqlConfigPath::CitywideContracts;

        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath($sqlConfigPath)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetCountContracts($parameters) {
        $sqlConfigPath = $parameters['contract_status'] == ContractStatus::PENDING
            ? SqlConfigPath::PendingContracts
            : SqlConfigPath::CitywideContracts;

        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath($sqlConfigPath)
            ->setParameters($parameters);
    }

    function GetCountContractsByPrimeVendors($parameters) {
        $sqlConfigPath = $parameters['contract_status'] == ContractStatus::PENDING
            ? SqlConfigPath::PendingContracts
            : SqlConfigPath::CitywideContracts;

        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath($sqlConfigPath)
            ->setParameters($parameters);
    }

    function GetContractsSubvendorStatusByPrime($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractsSubvendorReporting($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }
    function GetPrimeContractSubVenReportingSubLevel($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractsSubvendorContractsByPrime($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractsSubvendorContractsByAgency($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetCountContractsReportedWithSubVendors($parameters) {
        $dataFunction = isset($parameters['subvendor']) && $parameters['subvendor'] != null
            ? "GetCountContractsReportedWithSubVendorsSubLevel"
            : "GetCountContractsReportedWithSubVendors";
        return $this->setDataFunction($dataFunction)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters);
    }

    function GetCountContractsReported($parameters) {
        $dataFunction = isset($parameters['subvendor']) && $parameters['subvendor'] != null
            ? "GetCountContractsReportedSubLevel"
            : "GetCountContractsReported";
        return $this->setDataFunction($dataFunction)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters);
    }

    function GetContractsSubvendorStatusByPrimeCounts($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractsSubvendorStatusByPrimeCountsSubLevel($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractsSubvendorReportingCounts($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetContractsSubvendorReportingCountsSubLevel($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::CitywideContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetOgeContracts($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::OgeContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetOgeContractModifications($parameters, $limit = null, $orderBy = null) {
        $parameters["is_modification"] = true;
        return $this->GetOgeContracts($parameters,$limit,$orderBy);
    }

    function GetOgeMasterAgreementContracts($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::OgeContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetOgeMasterAgreementContractModifications($parameters, $limit = null, $orderBy = null) {
        $parameters["is_modification"] = true;
        return $this->GetOgeContracts($parameters,$limit,$orderBy);
    }

    function GetOgeContractsByDepartments($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::OgeContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetOgeContractsByAwardMethods($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::OgeContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetOgeContractsByPrimeVendors($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::OgeContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetOgeContractsByIndustries($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::OgeContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetOgeContractsBySize($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::OgeContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetCountOgeContracts($parameters) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::OgeContracts)
            ->setParameters($parameters);
    }

    function GetCountOgeContractsByPrimeVendors($parameters) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::OgeContracts)
            ->setParameters($parameters);
    }

    function GetSubContracts($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::SubContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetSubContractModifications($parameters, $limit = null, $orderBy = null) {
        $parameters["is_modification"] = true;
        return $this->GetSubContracts($parameters,$limit,$orderBy);
    }

    function GetSubContractsByAgencies($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::SubContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetSubContractsByDepartments($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::SubContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetSubContractsByAwardMethods($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::SubContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetSubContractsByPrimeVendors($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::SubContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetSubContractsBySubVendors($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::SubContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetSubContractsByIndustries($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::SubContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetSubContractsBySize($parameters, $limit = null, $orderBy = null) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::SubContracts)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    function GetCountSubContracts($parameters) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::SubContracts)
            ->setParameters($parameters);
    }

    function GetCountSubContractsBySubVendors($parameters) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::SubContracts)
            ->setParameters($parameters);
    }

    function GetCountSubContractsByPrimeVendors($parameters) {
        return $this->setDataFunction(__FUNCTION__)
            ->setSqlConfigPath(SqlConfigPath::SubContracts)
            ->setParameters($parameters);
    }
}