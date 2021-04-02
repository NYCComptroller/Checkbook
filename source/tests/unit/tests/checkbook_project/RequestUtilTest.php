<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_custom_breadcrumbs/customclasses/checkbook_custom_breadcrumbs.php';

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
     * Test IsPendingExpenseContractPath function
     */
    public function testIsPendingExpenseContractPath()
    {
        $this->assertTrue(RequestUtil::isPendingExpenseContractPath('contracts_pending_exp_landing'));
        $this->assertTrue(RequestUtil::isPendingExpenseContractPath('contracts_pending_exp_landing/contracts'));
        $this->assertFalse(RequestUtil::isPendingExpenseContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isPendingExpenseContractPath('something/contracts_pending_exp_landing'));
    }

    /**
     *Test is PendingRevenueContractPath function
     */
    public function isPendingRevenueContractPath()
    {
        $this->assertTrue(RequestUtil::isPendingRevenueContractPath('contracts_pending_rev_landing'));
        $this->assertTrue(RequestUtil::isPendingRevenueContractPath('contracts_pending_rev_landing/contracts'));
        $this->assertFalse(RequestUtil::isPendingRevenueContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isPendingRevenueContractPath('something/contracts_pending_rev_landing'));
    }

    /**
     *Test isExpenseContractPath function
     */
    public function testIsExpenseContractPath()
    {
        $this->assertTrue(RequestUtil::isExpenseContractPath('contracts_landing'));
        $this->assertTrue(RequestUtil::isExpenseContractPath('contracts_landing/contracts'));
        $this->assertFalse(RequestUtil::isExpenseContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isExpenseContractPath('something/contracts_landing'));
    }

    /**
     *test IsRevenueContractPath function
     */
    public function testIsRevenueContractPath()
    {
        $this->assertTrue(RequestUtil::isRevenueContractPath('contracts_revenue_landing'));
        $this->assertTrue(RequestUtil::isRevenueContractPath('contracts_revenue_landing/contracts'));
        $this->assertFalse(RequestUtil::isRevenueContractPath('contracts_pending'));
        $this->assertFalse(RequestUtil::isRevenueContractPath('something/contracts_revenue_landing'));
    }

    /**
     *Test isNychaContractPath function
     */
    public function testisNYCHAContractPath()
    {
        $this->assertTrue(RequestUtil::isNYCHAContractPath('nycha_contracts'));
        $this->assertFalse(RequestUtil::isNYCHAContractPath('something/contracts_revenue_landing'));
    }



    /**
     *Test GetDomain function
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
     *Test getDashboardTitle function
     */
    public function testgetDashboardTitle()
    {
        $this->assertEquals('M/WBE',RequestUtil::getDashboardTitle('mp'));
        $this->assertEquals('Sub Vendors (M/WBE)',RequestUtil::getDashboardTitle('sp'));
        $this->assertEquals('M/WBE (Sub Vendors)',RequestUtil::getDashboardTitle('ms'));
        $this->assertEquals('Sub Vendors',RequestUtil::getDashboardTitle('ss'));
    }

    /**
     *Test is AdvancedSearchPage function
     */
    public function testisAdvancedSearchPage()
    {
        global $mock_current_path;
        $_REQUEST['expandBottomContURL'] = '';
        $mock_current_path = 'spending/search/transactions/something';
        $this->assertTrue(RequestUtil::isAdvancedSearchPage($mock_current_path));

        $mock_current_path = 'contract/all/transactions/something';
        $this->assertTrue(RequestUtil::isAdvancedSearchPage($mock_current_path));

        $mock_current_path = 'contract/search/transactions/something';
        $this->assertTrue(RequestUtil::isAdvancedSearchPage($mock_current_path));

        $mock_current_path = '/nycha_revenue/transactions/something';
        $this->assertTrue(RequestUtil::isAdvancedSearchPage($mock_current_path));

        $mock_current_path = '/nycha_budget/transactions/something';
        $this->assertTrue(RequestUtil::isAdvancedSearchPage($mock_current_path));

        $mock_current_path = '/nycha_payroll/search/transactions/something';
        $this->assertTrue(RequestUtil::isAdvancedSearchPage($mock_current_path));
    }

    /**
     *Test getLandingPageUrl function
     */
    public function testgetLandingPageUrl()
    {
        $this->assertEquals('spending_landing/yeartype/B/year/122',RequestUtil::getLandingPageUrl('spending','122',null));
        $this->assertEquals('contracts_landing/status/A/yeartype/B/year/122',RequestUtil::getLandingPageUrl('contracts','122',null));
        $this->assertEquals('payroll/yeartype/C/year/122',RequestUtil::getLandingPageUrl('payroll','122','C'));
        $this->assertEquals('budget/yeartype/B/year/122',RequestUtil::getLandingPageUrl('budget','122',null));
        $this->assertEquals('revenue/yeartype/B/year/122',RequestUtil::getLandingPageUrl('revenue','122',null));
    }

    /**
     *Test getTopNavURL function
     */
    public function testgetTopNavURL()
    {
        $this->assertEquals('contracts_landing/status/A/yeartype/B/year/119',RequestUtil::getTopNavURL('contracts'));
        $this->assertEquals('spending_landing/yeartype/B/year/119',RequestUtil::getTopNavURL('spending'));
        $this->assertEquals('/nycha_spending/datasource/checkbook_nycha/year/119',RequestUtil::getTopNavURL('nycha_spending'));
        $this->assertEquals('budget/yeartype/B/year/119',RequestUtil::getTopNavURL('budget'));
        $this->assertEquals('/nycha_budget/datasource/checkbook_nycha/year/119',RequestUtil::getTopNavURL('nycha_budget'));
        $this->assertEquals('revenue/yeartype/B/year/119',RequestUtil::getTopNavURL('revenue'));
        $this->assertEquals('/nycha_revenue/datasource/checkbook_nycha/year/119',RequestUtil::getTopNavURL('nycha_revenue'));
    }



    /**
     *Test getCurrentDomainURLFromParams function
     */
    public function testgetCurrentDomainURLFromParams()
    {
        $this->assertEquals('contracts_landing/status/A/yeartype/B/year/119',RequestUtil::getTopNavURL('contracts'));
        $this->assertEquals('spending_landing/yeartype/B/year/119',RequestUtil::getTopNavURL('spending'));
        $this->assertEquals('/nycha_spending/datasource/checkbook_nycha/year/119',RequestUtil::getTopNavURL('nycha_spending'));
        $this->assertEquals('budget/yeartype/B/year/119',RequestUtil::getTopNavURL('budget'));
        $this->assertEquals('/nycha_budget/datasource/checkbook_nycha/year/119',RequestUtil::getTopNavURL('nycha_budget'));
        $this->assertEquals('revenue/yeartype/B/year/119',RequestUtil::getTopNavURL('revenue'));
        $this->assertEquals('/nycha_revenue/datasource/checkbook_nycha/year/119',RequestUtil::getTopNavURL('nycha_revenue'));
    }


}
