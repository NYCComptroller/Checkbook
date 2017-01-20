<?php

/**
 * Interface IContractsDataService
 */
interface IContractsDataService {

    /*
     * Citywide Contracts Methods
     */
    function GetContracts($parameters, $limit = null, $orderBy = null);
    function GetContractModifications($parameters, $limit = null, $orderBy = null);
    function GetMasterAgreementContracts($parameters, $limit = null, $orderBy = null);
    function GetMasterAgreementContractModifications($parameters, $limit = null, $orderBy = null);
    function GetContractsByAgencies($parameters, $limit = null, $orderBy = null);
    function GetContractsByDepartments($parameters, $limit = null, $orderBy = null);
    function GetContractsByAwardMethods($parameters, $limit = null, $orderBy = null);
    function GetContractsByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetContractsByIndustries($parameters, $limit = null, $orderBy = null);
    function GetContractsBySize($parameters, $limit = null, $orderBy = null);
    /* Count Queries */
    function GetCountContracts($parameters);
    function GetCountContractsByPrimeVendors($parameters);
    /* Sub Vendor */
    function GetContractsSubvendorStatusByPrime($parameters, $limit = null, $orderBy = null);
    function GetContractsSubvendorReporting($parameters, $limit = null, $orderBy = null);
    function GetContractsSubvendorContractsByPrime($parameters, $limit = null, $orderBy = null);
    /* Count Queries */
    function GetCountContractsReportedWithSubVendors($parameters);
    function GetCountContractsReported($parameters);
    /* Sub Vendor Charts */
    function GetContractsSubvendorStatusByPrimeCounts($parameters, $limit = null, $orderBy = null);
    function GetContractsSubvendorStatusByPrimeCountsSubLevel($parameters, $limit = null, $orderBy = null);
    function GetContractsSubvendorReportingCounts($parameters, $limit = null, $orderBy = null);
    function GetContractsSubvendorReportingCountsSubLevel($parameters, $limit = null, $orderBy = null);

    /*
     * Oge Contracts Methods
     */
    function GetOgeContracts($parameters, $limit = null, $orderBy = null);
    function GetOgeContractModifications($parameters, $limit = null, $orderBy = null);
    function GetOgeMasterAgreementContracts($parameters, $limit = null, $orderBy = null);
    function GetOgeMasterAgreementContractModifications($parameters, $limit = null, $orderBy = null);
    function GetOgeContractsByDepartments($parameters, $limit = null, $orderBy = null);
    function GetOgeContractsByAwardMethods($parameters, $limit = null, $orderBy = null);
    function GetOgeContractsByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetOgeContractsByIndustries($parameters, $limit = null, $orderBy = null);
    function GetOgeContractsBySize($parameters, $limit = null, $orderBy = null);
    /* Count Queries */
    function GetCountOgeContracts($parameters);

    /*
     * Sub Contracts Methods
     */
    function GetSubContracts($parameters, $limit = null, $orderBy = null);
    function GetSubContractModifications($parameters, $limit = null, $orderBy = null);
    function GetSubContractsByAgencies($parameters, $limit = null, $orderBy = null);
    function GetSubContractsByDepartments($parameters, $limit = null, $orderBy = null);
    function GetSubContractsByAwardMethods($parameters, $limit = null, $orderBy = null);
    function GetSubContractsByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetSubContractsBySubVendors($parameters, $limit = null, $orderBy = null);
    function GetSubContractsByIndustries($parameters, $limit = null, $orderBy = null);
    function GetSubContractsBySize($parameters, $limit = null, $orderBy = null);
    /* Count Queries */
    function GetCountSubContracts($parameters);
    function GetCountSubContractsBySubVendors($parameters);
    function GetCountSubContractsByPrimeVendors($parameters);

} 