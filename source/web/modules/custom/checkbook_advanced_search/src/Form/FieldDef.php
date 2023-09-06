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

namespace Drupal\checkbook_advanced_search\Form;

abstract class FieldDef
{
    const DATA_SOURCE_FILTER = 'domain_filter';
    const STATUS = 'status';
    const VENDOR = 'vendor_name';
    const CONTRACT_TYPE = 'type';
    const CONTRACT_ID = 'contract_id';
    const PIN = 'pin';
    const CURRENT_CONTRACT_AMOUNT = 'current_contract_amount';
    const END_DATE = 'end_date';
    const REGISTRATION_DATE = 'registration_date';
    const CATEGORY = 'category';
    const PURPOSE = 'purpose';
    const AGENCY = 'agency';
    const APT_PIN = 'apt_pin';
    const AWARD_METHOD = 'award_method';
    const START_DATE = 'start_date';
    const RECEIVED_DATE = 'received_date';
    const YEAR = 'year';
    const CONTRACT_SUBMIT = 'contracts';
    const ENTITY_CONTRACT_NUMBER = 'entity_contract_number';
    const COMMODITY_LINE = 'commodity_line';
    const BUDGET_NAME = 'budget_name';
    const OTHER_GOVERNMENT_ENTITIES = 'other_government_entities';
    const DEPARTMENT = 'department';
    const EXPENSE_CATEGORY = 'expense_category';
    const SPENDING_CATEGORY = 'spending_category';
    const PAYEE_NAME = 'payee_name';
    const CHECK_AMOUNT = 'check_amount';
    const DOCUMENT_ID = 'document_id';
    const CAPITAL_PROJECT = 'capital_project';
    const DATE_FILTER = 'date_filter';
    const SPENDING_SUBMIT = 'spending';
}
