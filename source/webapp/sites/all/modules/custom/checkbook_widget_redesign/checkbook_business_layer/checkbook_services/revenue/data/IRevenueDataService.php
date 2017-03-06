<?php

/**
 * Interface IRevenueDataService
 */
interface IRevenueDataService {
    /* Agencies Methods */
    function GetAgenciesByRevenue($parameters, $limit = null, $orderBy = null);
    function GetAgenciesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null);

    /* Funding Classes Methods */
    function GetFundingClassesByRevenue($parameters, $limit = null, $orderBy = null);
    function GetRevenueFundingClassesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null);
    
    /* Revenue Categories Methods */
    function GetRevenueCategoriesByRevenue($parameters, $limit = null, $orderBy = null);
    function GetRevenueCategoriesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null);
    
    
} 