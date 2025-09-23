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

use Drupal\checkbook_domain\Sql\SqlConfigPath;
use Drupal\checkbook_services\Common\DataService;

class BudgetDataService extends DataService implements IBudgetDataService {

    function GetAgenciesByBudget($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetAgenciesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetAgenciesByPercentDifference($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetExpenseCategoriesByBudget($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetExpenseCategoriesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetExpenseCategoriesByPercentDifference($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetDepartmentsByBudget($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetDepartmentsByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetDepartmentsByPercentDifference($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetExpenseBudgetCategories($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetExpenseBudgetCategoriesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * Common function that automatically configures the Citywide Budget sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureCitywide($dataFunction, $parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::CitywideBudget);
    }
}
