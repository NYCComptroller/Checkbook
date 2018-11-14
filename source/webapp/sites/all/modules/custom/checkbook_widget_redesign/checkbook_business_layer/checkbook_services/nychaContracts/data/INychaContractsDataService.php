<?php

/**
 * Interface INychaContractsDataService
 */
interface INychaContractsDataService {
    function GetContractsByVendors($parameters, $limit = null, $orderBy = null);
    function GetCountContractsByVendors($parameters);
    function GetContractsByAwardMethods($parameters, $limit = null, $orderBy = null);
    function GetContractsByPurchaseOrders($parameters, $limit = null, $orderBy = null);
    function GetContractsByBoroughs($parameters, $limit = null, $orderBy = null);
    function GetContractsBlanketAgreements($parameters, $limit = null, $orderBy = null);
    function GetContractsBlanketAgreementModifications($parameters, $limit = null, $orderBy = null);
    function GetContractsByGrants($parameters, $limit = null, $orderBy = null);
}
