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

use Drupal\checkbook_domain\Sql\SqlConfigPath;
use Drupal\checkbook_services\Common\DataService;

class NychaSpendingDataService extends DataService implements INychaSpendingDataService {
  /* NYCHA Spending */
  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByChecks($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function  GetNychaSpendingByVendors($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function  GetNychaSpendingByContracts($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByExpenseCategories($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByResponsibilityCenters($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByIndustries($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByDepartment($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
    public function GetNychaSpendingByFundingSource($parameters, $limit = null, $orderBy = null): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

  /**
   * @param $parameters
   * @return DataService
   */
    public function GetCountNychaContracts($parameters): DataService
    {
      return $this->configureNycha(__FUNCTION__,$parameters);
    }

  /**
   * Common function that automatically configures the Citywide Spending sql
   * @param $dataFunction
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null): DataService
  {
    return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::NychaSpending);
  }
}
