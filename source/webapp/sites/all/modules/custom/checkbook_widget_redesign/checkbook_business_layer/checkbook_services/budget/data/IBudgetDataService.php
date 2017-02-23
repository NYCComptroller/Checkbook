<?php

/**
 * Interface IContractsDataService
 */
interface IBudgetDataService {

    /* Agencies Methods */
    function GetAgenciesByBudget($parameters, $limit = null, $orderBy = null);
    function GetAgenciesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null);
    function GetAgenciesByPercentDifference($parameters, $limit = null, $orderBy = null);

    /* Expense Categories Methods */
    function GetExpenseCategoriesByBudget($parameters, $limit = null, $orderBy = null);
    function GetExpenseCategoriesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null);
    function GetExpenseCategoriesByPercentDifference($parameters, $limit = null, $orderBy = null);

    /* Department Methods */
    function GetDepartmentsByBudget($parameters, $limit = null, $orderBy = null);
    function GetDepartmentsByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null);
    function GetDepartmentsByPercentDifference($parameters, $limit = null, $orderBy = null);

    /* Expense Budget Categories Methods */
    function GetExpenseBudgetCategories($parameters, $limit = null, $orderBy = null);
    function GetExpenseBudgetCategoriesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null);
    
} 