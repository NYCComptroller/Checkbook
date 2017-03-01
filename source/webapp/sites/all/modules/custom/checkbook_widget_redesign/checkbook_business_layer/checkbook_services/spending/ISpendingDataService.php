<?php

/**
 * Interface ISpendingDataService
 */
interface ISpendingDataService {

    /* Citywide Spending */
    function GetSpendingByChecks($parameters, $limit = null, $orderBy = null);
    function GetSpendingByAgencies($parameters, $limit = null, $orderBy = null);
    function GetSpendingByContracts($parameters, $limit = null, $orderBy = null);
    function GetSpendingByExpenseCategories($parameters, $limit = null, $orderBy = null);
    function GetSpendingByDepartments($parameters, $limit = null, $orderBy = null);
    function GetSpendingByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetSpendingByIndustries($parameters, $limit = null, $orderBy = null);
    function GetSpendingByPayrollAgencies($parameters, $limit = null, $orderBy = null);
    function GetCountContracts($parameters);
    function GetCountPrimeVendors($parameters);

    /* Sub Contracts Spending */
    function GetSubVendorSpendingByChecks($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingByAgencies($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingBySubVendors($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingByPrimeSubVendors($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingBySubContracts($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingByContracts($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingByIndustries($parameters, $limit = null, $orderBy = null);
    function GetCountSubVendorPrimeVendors($parameters);
    function GetCountSubVendors($parameters);
    function GetCountSubContracts($parameters);
    function GetCountSubVendorContracts($parameters);

    /* OGE Spending */
    function GetOGESpendingByChecks($parameters, $limit = null, $orderBy = null);
    function GetOGESpendingByExpenseCategories($parameters, $limit = null, $orderBy = null);
    function GetOGESpendingByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetOGESpendingByDepartments($parameters, $limit = null, $orderBy = null);
    function GetOGESpendingByContracts($parameters, $limit = null, $orderBy = null);
    function GetCountOGEContracts($parameters);
    function GetCountOGEPrimeVendors($parameters);
} 