<?php

/**
 * Interface IPayrollDataService
 */
interface IPayrollDataService {

    /* Agencies Methods */
    function GetAgenciesByOvertime($parameters, $limit = null, $orderBy = null);

    function GetAgenciesByPayroll($parameters, $limit = null, $orderBy = null);

    /* Salaries Methods */
    function GetAnnualSalariesPerAgency($parameters, $limit = null, $orderBy = null);

    function GetAnnualSalaries($parameters, $limit = null, $orderBy = null);
     function GetNonSalariedRates($parameters, $limit = null, $orderBy = null);

    /* Titles Method(s) */
    function GetTitlesByNumberOfEmployees($parameters, $limit = null, $orderBy = null);
    function GetTitlesByNonSalariedEmployees($parameters, $limit = null, $orderBy = null);


    /* Count Methods */
    function GetCountAgencies($parameters);
    function GetCountSalariedEmployees($parameters);
    function GetCountNonSalariedEmployees($parameters);
}