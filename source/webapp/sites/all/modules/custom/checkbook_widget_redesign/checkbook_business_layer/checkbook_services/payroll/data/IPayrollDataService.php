<?php

/**
 * Interface IPayrollDataService
 */
interface IPayrollDataService {

    /*Agencies Methods*/
    function GetAgenciesByOvertime($parameters, $limit = null, $orderBy = null);

    function GetAgenciesByPayroll($parameters, $limit = null, $orderBy = null);

    function GetCountAgencies($parameters);

    /*Salaries Methods*/
    function GetAnnualSalariesPerAgency($parameters, $limit = null, $orderBy = null);

    function GetAnnualSalaries($parameters, $limit = null, $orderBy = null);

    /*Titles Method(s)*/
    function GetTitlesByNumberOfEmployees($parameters, $limit = null, $orderBy = null);

}