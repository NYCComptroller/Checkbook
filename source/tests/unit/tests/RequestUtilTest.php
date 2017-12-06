<?php

//use CheckbookProject\CustomClasses\RequestUtil;

//namespace CheckbookProject\CustomClasses;

include_once '../../webapp/sites/all/modules/custom/checkbook_project/customclasses/RequestUtil.php';

use PHPUnit\Framework\TestCase;

$mock_current_path = null;

function current_path(){
    global $mock_current_path;
    return $mock_current_path;
}

class RequestUtilTest extends TestCase {
//    private $requestUtil;

//    protected function setUp()
//    {
//        $this->requestUtil = new RequestUtil();
//    }
//
//    protected function tearDown()
//    {
//        $this->requestUtil = NULL;
//    }

    public function testIsPendingExpenseContractPath()
    {
        $this->assertTrue(RequestUtil::isPendingExpenseContractPath('contracts_pending_exp_landing'));
        $this->assertTrue(RequestUtil::isPendingExpenseContractPath('contracts_pending_exp_landing/contracts'));
        $this->assertFalse(RequestUtil::isPendingExpenseContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isPendingExpenseContractPath('something/contracts_pending_exp_landing'));
    }

    public function isPendingRevenueContractPath()
    {
        $this->assertTrue(RequestUtil::isPendingRevenueContractPath('contracts_pending_rev_landing'));
        $this->assertTrue(RequestUtil::isPendingRevenueContractPath('contracts_pending_rev_landing/contracts'));
        $this->assertFalse(RequestUtil::isPendingRevenueContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isPendingRevenueContractPath('something/contracts_pending_rev_landing'));
    }

    public function testIsExpenseContractPath()
    {
        $this->assertTrue(RequestUtil::isExpenseContractPath('contracts_landing'));
        $this->assertTrue(RequestUtil::isExpenseContractPath('contracts_landing/contracts'));
        $this->assertFalse(RequestUtil::isExpenseContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isExpenseContractPath('something/contracts_landing'));
    }

    public function testIsRevenueContractPath()
    {
        $this->assertTrue(RequestUtil::isRevenueContractPath('contracts_revenue_landing'));
        $this->assertTrue(RequestUtil::isRevenueContractPath('contracts_revenue_landing/contracts'));
        $this->assertFalse(RequestUtil::isRevenueContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isRevenueContractPath('something/contracts_revenue_landing'));
    }

    public function testGetDomain()
    {
        global $mock_current_path;

        $mock_current_path = 'budget/somethin';
        $this->assertEquals('budget', RequestUtil::getDomain());

        $mock_current_path = 'revenue_somethin';
        $this->assertEquals('revenue', RequestUtil::getDomain());

        $mock_current_path = 'spending/a/b/c/d';
        $this->assertEquals('spending', RequestUtil::getDomain());

        $mock_current_path = 'payroll/budget';
        $this->assertEquals('payroll', RequestUtil::getDomain());

        $mock_current_path = 'unknown/budget';
        $this->assertNull(RequestUtil::getDomain());
    }
}
