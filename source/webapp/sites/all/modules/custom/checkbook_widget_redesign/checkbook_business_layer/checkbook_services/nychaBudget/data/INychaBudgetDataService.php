<?php

/**
 * Interface INychaBudgetDataService
 */
interface INychaBudgetDataService {

  function GetNychaExpenseCategoriesByBudget($parameters, $limit = null, $orderBy = null);

  function GetNychaExpenseCategoriesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null);

  function GetNychaExpenseCategoriesByPercentDifference($parameters, $limit = null, $orderBy = null);

  function GetNychaResponsibilityCentersByPercentDifference($parameters, $limit = null, $orderBy = null);

  function GetResponsibilityCenters($parameters, $limit = null, $orderBy = null);

  function GetResponsibilityCentersByCommittedExpense($parameters, $limit = null, $orderBy = null);

  function GetFundingSources($parameters, $limit = null, $orderBy = null);

  function GetFundingSourcesByCommittedExpense($parameters, $limit = null, $orderBy = null);

  function GetNychaFundingSourcesByPercentDifference($parameters, $limit = null, $orderBy = null);

  function GetPrograms($parameters, $limit = null, $orderBy = null);

  function GetProgramsByCommittedExpense($parameters, $limit = null, $orderBy = null);

  function GetNychaProgramsByPercentDifference($parameters, $limit = null, $orderBy = null);

  function GetProjects($parameters, $limit = null, $orderBy = null);

  function GetProjectsByCommittedExpense($parameters, $limit = null, $orderBy = null);

  function GetProjectsByPercentDifference($parameters, $limit = null, $orderBy = null);

}
