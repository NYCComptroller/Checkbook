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

namespace Drupal\checkbook_services\Revenue;

use Drupal\checkbook_domain\Sql\SqlConfigPath;
use Drupal\checkbook_services\Common\DataService;

class RevenueDataService extends DataService implements IRevenueDataService {

    function GetAgenciesByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetFundingClassesByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetRevenueCategoriesByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetAgenciesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetRevenueCategoriesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }

    function GetRevenueFundingClassesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null) {
        return $this->configureCitywide(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    /**
     * Common function that automatically configures the Citywide Revenue sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureCitywide($dataFunction, $parameters, $limit = null, $orderBy = null) {
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::CitywideRevenue);
    }
}

