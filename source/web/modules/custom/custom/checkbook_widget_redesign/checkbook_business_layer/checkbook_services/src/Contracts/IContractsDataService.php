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

namespace Drupal\checkbook_services\Contracts;

/**
 * Interface IContractsDataService
 */
interface IContractsDataService {

    /*
     * Citywide Contracts Methods
     */
    function GetContracts($parameters, $limit = null, $orderBy = null);
    function GetMocsContracts($parameters, $limit = null, $orderBy = null);
    function GetContractModifications($parameters, $limit = null, $orderBy = null);
    function GetMasterAgreementContracts($parameters, $limit = null, $orderBy = null);
    function GetMasterAgreementContractModifications($parameters, $limit = null, $orderBy = null);
    function GetContractsByAgencies($parameters, $limit = null, $orderBy = null);
    function GetContractsByDepartments($parameters, $limit = null, $orderBy = null);
    function GetContractsByAwardMethods($parameters, $limit = null, $orderBy = null);
    function GetContractsByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetContractsByIndustries($parameters, $limit = null, $orderBy = null);
    function GetContractsBySize($parameters, $limit = null, $orderBy = null);
    /* Count Queries */
    function GetCountContracts($parameters);
    function GetCountMocsContracts($parameters);
    function GetCountContractsByPrimeVendors($parameters);
    /* Sub Vendor */
    function GetContractsSubvendorStatusByPrime($parameters, $limit = null, $orderBy = null);
    function GetContractsSubvendorReporting($parameters, $limit = null, $orderBy = null);
    function GetPrimeContractSubVenReportingSubLevel($parameters, $limit = null, $orderBy = null);
    function GetContractsSubvendorContractsByPrime($parameters, $limit = null, $orderBy = null);
    function GetContractsSubvendorContractsByAgency($parameters, $limit = null, $orderBy = null);
    /* Count Queries */
    function GetCountContractsReportedWithSubVendors($parameters);
    function GetCountContractsReported($parameters);
    /* Sub Vendor Charts */
    function GetContractsSubvendorStatusByPrimeCounts($parameters, $limit = null, $orderBy = null);
    function GetContractsSubvendorStatusByPrimeCountsSubLevel($parameters, $limit = null, $orderBy = null);
    function GetContractsSubvendorReportingCounts($parameters, $limit = null, $orderBy = null);
    function GetContractsSubvendorReportingCountsSubLevel($parameters, $limit = null, $orderBy = null);

    /*
     * Oge Contracts Methods
     */
    function GetOgeContracts($parameters, $limit = null, $orderBy = null);
    function GetOgeContractModifications($parameters, $limit = null, $orderBy = null);
    function GetOgeMasterAgreementContracts($parameters, $limit = null, $orderBy = null);
    function GetOgeMasterAgreementContractModifications($parameters, $limit = null, $orderBy = null);
    function GetOgeContractsByDepartments($parameters, $limit = null, $orderBy = null);
    function GetOgeContractsByAwardMethods($parameters, $limit = null, $orderBy = null);
    function GetOgeContractsByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetOgeContractsByIndustries($parameters, $limit = null, $orderBy = null);
    function GetOgeContractsBySize($parameters, $limit = null, $orderBy = null);
    /* Count Queries */
    function GetCountOgeContracts($parameters);
    function GetCountOgeContractsByPrimeVendors($parameters);

    /*
     * Sub Contracts Methods
     */
    function GetSubContracts($parameters, $limit = null, $orderBy = null);
    function GetSubContractModifications($parameters, $limit = null, $orderBy = null);
    function GetSubContractsByAgencies($parameters, $limit = null, $orderBy = null);
    function GetSubContractsByDepartments($parameters, $limit = null, $orderBy = null);
    function GetSubContractsByAwardMethods($parameters, $limit = null, $orderBy = null);
    function GetSubContractsByPrimeVendors($parameters, $limit = null, $orderBy = null);
    function GetSubContractsBySubVendors($parameters, $limit = null, $orderBy = null);
    function GetSubContractsByIndustries($parameters, $limit = null, $orderBy = null);
    function GetSubContractsBySize($parameters, $limit = null, $orderBy = null);
    /* Count Queries */
    function GetCountSubContracts($parameters);
    function GetCountSubContractsBySubVendors($parameters);
    function GetCountSubContractsByPrimeVendors($parameters);

}
