<?php

/**
 * Interface INychaBudgetDataService
 */
interface INychaBudgetDataService {
  function GetNychaExpenseCategoriesByBudget($parameters, $limit = null, $orderBy = null);

}
