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

/**
 * Interface IRevenueDataService
 */
interface IRevenueDataService {
    /* Agencies Methods */
    function GetAgenciesByRevenue($parameters, $limit = null, $orderBy = null);
    function GetAgenciesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null);

    /* Funding Classes Methods */
    function GetFundingClassesByRevenue($parameters, $limit = null, $orderBy = null);
    function GetRevenueFundingClassesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null);

    /* Revenue Categories Methods */
    function GetRevenueCategoriesByRevenue($parameters, $limit = null, $orderBy = null);
    function GetRevenueCategoriesCrossYearCollectionsByRevenue($parameters, $limit = null, $orderBy = null);


}
