<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';

use PHPUnit\Framework\TestCase;

/**
 * Class NodeSummaryUtil
 * MOCKED
 */
class NodeSummaryUtil
{
    /**
     * @param $a
     * @return string
     */
    static function getInitNodeSummaryTitle($a)
    {
        return 'getInitNodeSummaryTitle :: ' . $a;
    }

    /**
     * @param $b
     * @return string
     */
    static function getInitNodeSummaryTemplateTitle($b)
    {
        return 'getInitNodeSummaryTemplateTitle :: ' . $b;
    }
}

/**
 * Class RequestUtilTest
 */
class RequestUtilTest extends TestCase
{
//    private $requestUtil;

//    protected function setUp(): void
//    {
//        $this->requestUtil = new RequestUtil();
//    }
//
//    protected function tearDown()
//    {
//        $this->requestUtil = NULL;
//    }

    /**
     *
     */
    public function testIsPendingExpenseContractPath()
    {
        $this->assertTrue(RequestUtil::isPendingExpenseContractPath('contracts_pending_exp_landing'));
        $this->assertTrue(RequestUtil::isPendingExpenseContractPath('contracts_pending_exp_landing/contracts'));
        $this->assertFalse(RequestUtil::isPendingExpenseContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isPendingExpenseContractPath('something/contracts_pending_exp_landing'));
    }

    /**
     *
     */
    public function isPendingRevenueContractPath()
    {
        $this->assertTrue(RequestUtil::isPendingRevenueContractPath('contracts_pending_rev_landing'));
        $this->assertTrue(RequestUtil::isPendingRevenueContractPath('contracts_pending_rev_landing/contracts'));
        $this->assertFalse(RequestUtil::isPendingRevenueContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isPendingRevenueContractPath('something/contracts_pending_rev_landing'));
    }

    /**
     *
     */
    public function testIsExpenseContractPath()
    {
        $this->assertTrue(RequestUtil::isExpenseContractPath('contracts_landing'));
        $this->assertTrue(RequestUtil::isExpenseContractPath('contracts_landing/contracts'));
        $this->assertFalse(RequestUtil::isExpenseContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isExpenseContractPath('something/contracts_landing'));
    }

    /**
     *
     */
    public function testIsRevenueContractPath()
    {
        $this->assertTrue(RequestUtil::isRevenueContractPath('contracts_revenue_landing'));
        $this->assertTrue(RequestUtil::isRevenueContractPath('contracts_revenue_landing/contracts'));
        $this->assertFalse(RequestUtil::isRevenueContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isRevenueContractPath('something/contracts_revenue_landing'));
    }

    /**
     *
     */
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

    /**
     *
     */
    public function testGetBudgetBreadcrumbTitle()
    {
        global $mock_current_path;

        function _get_budget_breadcrumb_title_drilldown($a = 3)
        {
            return '5' . $a;
        }

        function filter_xss($text)
        {
            return $text;
        }

        $mock_current_path = '/panel_html/budget_agency_perecent_difference_transactions/budget/agency_details/dtsmnid/560/yeartype/B/year/119';
        $_REQUEST['expandBottomContURL'] = 'budget/yeartype/B/year/119';

        $this->assertEquals('53 Expense Budget', CustomBreadcrumbs::getBudgetBreadcrumbTitle());

        $mock_current_path = 'panel_html/budget_agency_perecent_difference_transactions/budget/agency_details/dtsmnid/560/yeartype/B/year/119';
        unset($_REQUEST['expandBottomContURL']);

        $this->assertEquals('getInitNodeSummaryTitle :: 560', CustomBreadcrumbs::getBudgetBreadcrumbTitle());

        $mock_current_path = 'budget/transactions/year/119';
        unset($_REQUEST['expandBottomContURL']);
        $this->assertEquals('Expense Budget Transactions', CustomBreadcrumbs::getBudgetBreadcrumbTitle());
    }
}
