<?php

/**
 * Interface INychaRevenueDataService
 */
interface INychaRevenueDataService {
  function GetNychaExpenseCategoriesByRevenue($parameters, $limit = null, $orderBy = null);
  function GetNychaResponsibilityCenterByRevenue($parameters, $limit = null, $orderBy = null);
  function GetNychaFundingSourceByRevenue($parameters, $limit = null, $orderBy = null);
  function GetNychaProgramByRevenue($parameters, $limit = null, $orderBy = null);
  function GetNychaProjectByRevenue($parameters, $limit = null, $orderBy = null);
  function GetNychaRevenueCategoriesByRevenue($parameters, $limit = null, $orderBy = null);
}
