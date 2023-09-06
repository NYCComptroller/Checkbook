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

use Drupal\checkbook_domain\Sql\SqlConfigPath;
use Drupal\checkbook_services\Common\DataService;

class NychaBudgetDataService extends DataService implements INychaBudgetDataService {
  /* NYCHA Budget */
  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaExpenseCategoriesByBudget($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaExpenseCategoriesByCommittedExpenseBudget($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaExpenseCategoriesByPercentDifference($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaResponsibilityCentersByPercentDifference($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetResponsibilityCenters($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetResponsibilityCentersByCommittedExpense($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetFundingSources($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetFundingSourcesByCommittedExpense($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaFundingSourcesByPercentDifference($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetPrograms($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetProgramsByCommittedExpense($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetNychaProgramsByPercentDifference($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetProjects($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetProjectsByCommittedExpense($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

  /**
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  public function GetProjectsByPercentDifference($parameters, $limit = null, $orderBy = null)
  {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }

    /**
     * Common function that automatically configures the Nycha Budget sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null)
    {
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::NychaBudget);
    }
}
