<?php

/**
 * Interface INychaContractsDataService
 */
interface INychaContractsDataService {
    function GetContractsByVendors($parameters, $limit = null, $orderBy = null);
    function GetCountContractsByVendors($parameters);
    function GetContractsByAwardMethods($parameters, $limit = null, $orderBy = null);
}
