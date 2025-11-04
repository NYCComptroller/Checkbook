<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_services\Payroll;
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
