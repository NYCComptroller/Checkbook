<?php

/**
 * Interface INychaSpendingDataService
 */
interface INychaSpendingDataService {
  function GetNychaSpendingByChecks($parameters, $limit = null, $orderBy = null);
  function  GetNychaSpendingByVendors($parameters, $limit = null, $orderBy = null);
  function  GetCountVendors($parameters);


}
