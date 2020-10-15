<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/nychaBudget/NychaBudgetUrlService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';

use PHPUnit\Framework\TestCase;
/**
 * Class NychaBudgetUrlServiceTest
 */
class NychaBudgetUrlServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getFooterUrl() function
     */
    public function test_getFooterUrl()
    {
        $result = NychaBudgetUrlService::getFooterUrl();
        $this->assertEquals("/panel_html/nycha_budget_transactions/nycha_budget/transactions", substr($result, 0, 63));
    }

    /**
     * Tests getPercentDiffFooterUrl() function
     */
    public function test_getPercentDiffFooterUrl()
    {
        $result = NychaBudgetUrlService::getPercentDiffFooterUrl(NychaBudgetUrlService::getFooterUrl(), 'exp_details');
        $this->assertEquals("/panel_html/nycha_budget_percent_difference_details/nycha_budget/details/budgettype/percdiff/widget/exp_details", substr($result, 0, 111));
    }

    /**
     * Tests committedBudgetUrl() function
     */
    public function test_committedBudgetUrl()
    {
        $result = NychaBudgetUrlService::committedBudgetUrl(NULL, NULL, NULL);
        $this->assertEquals("/panel_html/nycha_budget_transactions/nycha_budget/transactions", substr($result, 0, 63));
    }

    /**
     * Tests generateLandingPageUrl() function
     */
    public function test_generateLandingPageUrl()
    {
        $result = NychaBudgetUrlService::generateLandingPageUrl('agency', '162');
        $urlParams = explode('/', $result);
        $this->assertEquals("nycha_budget", $urlParams[1]);
        //Tests last URL params
        $this->assertEquals("agency", $urlParams[count($urlParams)-2]);
        $this->assertEquals("162", $urlParams[count($urlParams)-1]);
    }
}
