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

namespace Drupal\checkbook_services\Spending;
/**
 * Interface ISpendingDataService
 */
interface ISpendingDataService {

    /* Citywide Spending */
    function GetSpendingByChecks($parameters, $limit = null, $orderBy = null);
    function GetSpendingByAgencies($parameters, $limit = null, $orderBy = null);
    function GetSpendingByContracts($parameters, $limit = null, $orderBy = null);
    function GetSpendingByExpenseCategories($parameters, $limit = null, $orderBy = null);
    function GetSpendingByDepartments($parameters, $limit = null, $orderBy = null);
    function GetSpendingByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetSpendingByIndustries($parameters, $limit = null, $orderBy = null);
    function GetSpendingByPayrollAgencies($parameters, $limit = null, $orderBy = null);
    function GetCountContracts($parameters);
    function GetCountPrimeVendors($parameters);

    /*MOCS Contracts Spending*/
    function GetSpendingByMocsContracts($parameters, $limit = null, $orderBy = null);
    function GetCountMocsContracts($parameters);

    /* Sub Contracts Spending */
    function GetSubVendorSpendingByChecks($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingByAgencies($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingBySubVendors($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingByPrimeSubVendors($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingBySubContracts($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingByContracts($parameters, $limit = null, $orderBy = null);
    function GetSubVendorSpendingByIndustries($parameters, $limit = null, $orderBy = null);
    function GetCountSubVendorPrimeVendors($parameters);
    function GetCountSubVendors($parameters);
    function GetCountSubContracts($parameters);
    function GetCountSubVendorContracts($parameters);

    /* OGE Spending */
    function GetOGESpendingByChecks($parameters, $limit = null, $orderBy = null);
    function GetOGESpendingByExpenseCategories($parameters, $limit = null, $orderBy = null);
    function GetOGESpendingByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetOGESpendingByDepartments($parameters, $limit = null, $orderBy = null);
    function GetOGESpendingByContracts($parameters, $limit = null, $orderBy = null);
    function GetCountOGEContracts($parameters);
    function GetCountOGEPrimeVendors($parameters);
}
