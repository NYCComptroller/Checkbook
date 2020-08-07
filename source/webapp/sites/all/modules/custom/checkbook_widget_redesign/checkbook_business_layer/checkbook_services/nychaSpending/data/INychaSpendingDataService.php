<?php

/**
 * Interface INychaSpendingDataService
 */
interface INychaSpendingDataService {
  function GetNychaSpendingByChecks($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByVendors($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByContracts($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByExpenseCategories($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByResponsibilityCenters($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByIndustries($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByDepartment($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByFundingSource($parameters, $limit = null, $orderBy = null);
  function GetCountNychaContracts($parameters);
}
