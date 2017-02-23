<?php

/**
 * Interface IContractsDataService
 */
interface IBudgetDataService {

    /*
     * Department Budget Methods
     */
    function GetPercentDifferencebyDepartments($parameters, $limit = null, $orderBy = null);
    
} 