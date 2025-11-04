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

namespace Drupal\checkbook_services\NychaBudget;
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
