<?php

namespace checkbook_advanced_search;

abstract class Column
{
    const Left = 'column-left';
    const Right = 'column-right';
    const None = 'none';
}

abstract class DataSource
{
    const CitywideAgency = 'checkbook';
    const OtherGovernmentEntities = 'checkbook_oge';
    const NYCHA = 'checkbook_nycha';
}

abstract class Domain
{
    const Budget = 'budget';
    const Revenue = 'revenue';
    const Spending = 'spending';
    const Contracts = 'contracts';
    const Payroll = 'payroll';
}

abstract class FieldDef
{
    const DataSourceFilter = 'domain_filter';
    const Status = 'status';
    const Vendor = 'vendor_name';
    const ContractType = 'type';
    const ContractId = 'contract_id';
    const PIN = 'pin';
    const CurrentContractAmount = 'current_contract_amount';
    const EndDate = 'end_date';
    const RegistrationDate = 'registration_date';
    const Category = 'category';
    const Purpose = 'purpose';
    const Agency = 'agency';
    const AptPIN = 'apt_pin';
    const AwardMethod = 'award_method';
    const StartDate = 'start_date';
    const ReceivedDate = 'received_date';
    const Year = 'year';
    const ContractSubmit = 'contracts';
    const EntityContractNumber = 'entity_contract_number';
    const CommodityLine = 'commodity_line';
    const BudgetName = 'budget_name';
    const OtherGovernmentEntities = 'other_government_entities';
    const Department = 'department';
    const ExpenseCategory = 'expense_category';
    const SpendingCategory = 'spending_category';
    const PayeeName = 'payee_name';
    const CheckAmount = 'check_amount';
    const DocumentId = 'document_id';
    const CapitalProject = 'capital_project';
    const DateFilter = 'date_filter';
    const SpendingSubmit = 'spending';
}

abstract class FieldType
{
    const DateFilter = 'date_filter';
    const DatePopup = 'date_popup';
    const DropDown = 'select';
    const HiddenField = 'hidden';
    const RadioButtons = 'radios';
    const RangeField = 'rangefield';
    const RangeDateField = 'range';
    const SubmitField = 'submit';
    const TextField = 'textfield';
}
