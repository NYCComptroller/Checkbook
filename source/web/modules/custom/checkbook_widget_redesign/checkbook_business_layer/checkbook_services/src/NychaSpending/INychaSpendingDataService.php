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

namespace Drupal\checkbook_services\NychaSpending;

/**
 * Interface INychaSpendingDataService
 */
interface INychaSpendingDataService {
  function GetNychaSpendingByChecks($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByVendors($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByContracts($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByExpenseCategories($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByResponsibilityCenters($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByIndustries($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByDepartment($parameters, $limit = null, $orderBy = null);
  function GetNychaSpendingByFundingSource($parameters, $limit = null, $orderBy = null);
  function GetCountNychaContracts($parameters);
}
