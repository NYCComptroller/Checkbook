<?php

/**
 * Interface INychaSpendingDataService
 */
interface INychaSpendingDataService {
  function GetSpendingByChecks($parameters, $limit = null, $orderBy = null);
}
