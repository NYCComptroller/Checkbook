<?php
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/nychaRevenue/NychaRevenueUrlService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/revenue/NychaRevenueUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';

use PHPUnit\Framework\TestCase;
/**
 * Class NychaBudgetUrlServiceTest
 */
class NychaRevenueUrlServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getFooterUrl() function
     */
    public function test_getFooterUrl()
    {
        $result = NychaRevenueUrlService::getFooterUrl();
        $this->assertEquals("/panel_html/nycha_revenue_transactions/nycha_revenue/transactions", substr($result, 0, 65));
    }

    /**
     * Tests recognizedRevenueUrl() function
     */
    public function test_recRevenueUrl()
    {
        $result = NychaRevenueUrlService::recRevenueUrl(NULL, NULL, NULL);
        $this->assertEquals("/panel_html/nycha_revenue_transactions/nycha_revenue/transactions", substr($result, 0, 65));
    }

    /**
     * Tests generateLandingPageUrl() function
     */
    public function test_generateLandingPageUrl()
    {
        $result = NychaRevenueUrlService::generateLandingPageUrl('agency', '162');
        $urlParams = explode('/', $result);
        $this->assertEquals("nycha_revenue", $urlParams[1]);
        //Tests last URL params
        $this->assertEquals("agency", $urlParams[count($urlParams)-2]);
        $this->assertEquals("162", $urlParams[count($urlParams)-1]);
    }


}

/**
 * Class NychaBudgetUtilTest
 */
class NychaRevenueUtilTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getTransactionsTitle() function
     */
    public function test_getTransactionsTitle()
    {
        $result = NychaRevenueUtil::getTransactionsTitle('panel_html/nycha_revenue_transactions/nycha_revenue/transactions/year/121/datasource/checkbook_nycha/widget/wt_funding_sources');
        $this->assertEquals('Funding Sources Revenue Transactions', $result);
    }

    /**
     * Tests getTransactionsSubTitle() function
     */
    public function test_getTransactionsSubTitle()
    {
        $result = NychaRevenueUtil::getTransactionsSubTitle('rec_funding_source', '/panel_html/nycha_revenue_transactions/nycha_revenue/transactions/year/121/datasource/checkbook_nycha/widget/rec_funding_source/fundsrc/527');
        log_error($result);
        $this->assertEquals("<b>Funding Source: </b>", $result);
    }

}
