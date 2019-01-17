<?php

/**
 * Interface INychaContractsDataService
 */
interface INychaContractsDataService {
    function GetContractsByVendors($parameters, $limit = null, $orderBy = null);
    function GetContractsByAwardMethods($parameters, $limit = null, $orderBy = null);
    function GetContractsByPurchaseOrders($parameters, $limit = null, $orderBy = null);
    function GetContractsByBoroughs($parameters, $limit = null, $orderBy = null);
    function GetContractsBlanketAgreements($parameters, $limit = null, $orderBy = null);
    function GetContractsBlanketAgreementModifications($parameters, $limit = null, $orderBy = null);
    function GetContractsPlannedAgreements($parameters, $limit = null, $orderBy = null);
    function GetContractsPlannedAgreementModifications($parameters, $limit = null, $orderBy = null);
    function GetContractsByDepartments($parameters, $limit = null, $orderBy = null);
    function GetContractsByIndustries($parameters, $limit = null, $orderBy = null);
    function GetContractsByRespCenters($parameters, $limit = null, $orderBy = null);
    function GetContractsBySize($parameters, $limit = null, $orderBy = null);
    function GetCountContracts($parameters);
}
