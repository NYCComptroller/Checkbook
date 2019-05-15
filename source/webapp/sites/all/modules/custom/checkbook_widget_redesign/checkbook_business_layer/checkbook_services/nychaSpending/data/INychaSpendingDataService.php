<?php

/**
 * Interface INychaSpendingDataService
 */
interface INychaSpendingDataService {
  function GetNychaSpendingByChecks($parameters, $limit = null, $orderBy = null);
  function  GetNychaSpendingByVendors($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByContracts($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByExpenseCategories($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByIndustries($parameters, $limit = null, $orderBy = null);
  function  GetCountVendors($parameters);
    function GetCountContracts($parameters);


}
