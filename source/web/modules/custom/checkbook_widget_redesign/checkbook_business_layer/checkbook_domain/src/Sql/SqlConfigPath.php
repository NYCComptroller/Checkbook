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

namespace Drupal\checkbook_domain\Sql;

abstract class SqlConfigPath {
  /* Spending */
    const CitywideSpending = "spending/spending";
    const OgeSpending = "spending/oge_spending";
    const SubVendorsSpending = "spending/sub_vendors_spending";
    /* NYCHA Spending */
    const NychaSpending = "spending/nycha_spending";
    /* Contracts */
    const CitywideContracts = "contracts/contracts";
    const PendingContracts = "contracts/pending_contracts";
    const OgeContracts = "contracts/oge_contracts";
    const SubContracts = "contracts/sub_contracts";
    /* NYCHA Contracts */
    const NychaContracts = "contracts/nycha_contracts";
    /* Budget */
    const CitywideBudget = "budget/budget";
    const NychaBudget = "budget/nycha_budget";
    /*Payroll*/
    const CitywidePayroll = "payroll/payroll";
    const NYCHAPayroll = "payroll/nycha_payroll";
     /* Revenue */
    const CitywideRevenue = "revenue/revenue";
    const NychaRevenue = "revenue/nycha_revenue";
}
