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

namespace Drupal\checkbook_services\Budget;
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
