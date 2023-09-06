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

namespace Drupal\checkbook_services\NychaContracts;

use Drupal\checkbook_domain\Sql\SqlConfigPath;
use Drupal\checkbook_services\Common\DataService;

/**
 * Class NychaContractsDataService
 */
class NychaContractsDataService extends DataService implements INychaContractsDataService {
    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByVendors($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByAwardMethods($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByPurchaseOrders($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByBoroughs($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsBlanketAgreements($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsBlanketAgreementModifications($parameters, $limit = null, $orderBy = null): DataService
    {
        $parameters["is_modification"] = true;
        return $this->configureNycha('GetContractsBlanketAgreements',$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsPlannedAgreements($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsPlannedAgreementModifications($parameters, $limit = null, $orderBy = null): DataService
    {
        $parameters["is_modification"] = true;
        return $this->configureNycha('GetContractsPlannedAgreements',$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByDepartments($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByIndustries($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsByRespCenters($parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    /**
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    public function GetContractsBySize($parameters, $limit = null, $orderBy = null): DataService
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
     * Common function that automatically configures the NYCHA Contracts sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null): DataService
    {
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::NychaContracts);
    }
}
