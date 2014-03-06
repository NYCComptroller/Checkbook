<?php

namespace checkbook_datafeeds;

abstract class Domain
{
    const Budget = 'budget';
    const Revenue = 'revenue';
    const Spending = 'spending';
    const Contracts = 'contracts';
    const Payroll = 'payroll';
}

abstract class DataSource
{
    const CitywideAgency = 'checkbook';
    const OtherGovernmentEntities = 'checkbook_oge';
}
