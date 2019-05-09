<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 9/10/14
 * Time: 2:34 PM
 */

class Domain {
    public static $SPENDING = 'spending';
    public static $CONTRACTS = 'contracts';
    public static $REVENUE = 'revenue';
    public static $BUDGET = 'budget';
    public static $PAYROLL = 'payroll';
    public static $NYCHA_CONTRACTS = 'nycha_contracts';
    public static $NYCHA_SPENDING = 'nycha_spending';
}

class VendorType {
    public static $PRIME_VENDOR = 'P';
    public static $SUB_VENDOR = 'S';
}

class PayrollType {
    public static $SALARIED = 'Salaried';
    public static $NON_SALARIED = 'Non-Salaried';
}
