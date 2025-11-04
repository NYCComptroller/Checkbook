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
/**
 * Interface INychaContractsDataService
 */
interface INychaContractsDataService {
    function GetContractsByVendors($parameters, $limit = null, $orderBy = null);
    function GetContractsByAwardMethods($parameters, $limit = null, $orderBy = null);
    function GetContractsByPurchaseOrders($parameters, $limit = null, $orderBy = null);
    function GetContractsByBoroughs($parameters, $limit = null, $orderBy = null);
    function GetContractsBlanketAgreements($parameters, $limit = null, $orderBy = null);
    function GetContractsBlanketAgreementModifications($parameters, $limit = null, $orderBy = null);
    function GetContractsPlannedAgreements($parameters, $limit = null, $orderBy = null);
    function GetContractsPlannedAgreementModifications($parameters, $limit = null, $orderBy = null);
    function GetContractsByDepartments($parameters, $limit = null, $orderBy = null);
    function GetContractsByIndustries($parameters, $limit = null, $orderBy = null);
    function GetContractsByRespCenters($parameters, $limit = null, $orderBy = null);
    function GetContractsBySize($parameters, $limit = null, $orderBy = null);
    function GetCountNychaContracts($parameters);
}
