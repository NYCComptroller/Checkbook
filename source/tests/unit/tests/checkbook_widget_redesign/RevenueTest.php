<?php
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/revenue/RevenueUrlService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/utilities/RequestUtilities.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/CheckbookDateUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_project.inc';

use PHPUnit\Framework\TestCase;
/**
 * Class RevenueUrlServiceTest
 */
class RevenueUrlServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getAgencyUrl function
     */
    public function test_getAgencyUrl()
    {
        $result = RevenueUrlService::getAgencyUrl(138, NULL);
        $this->assertEquals("/revenue/agency/138",$result);
    }

    /**
     * Tests getRecognizedAmountUrl() function
     */
    public function test_getRecognizedAmountUrl()
    {
        $result = RevenueUrlService::getRecognizedAmountUrl('revcat', '15','580','1');
        $this->assertEquals("/panel_html/revenue_transactions/budget/transactions/smnid/580", substr($result, 0, 62));
        //Tests last URL params
        $urlParams = explode('/', $result);
        $this->assertEquals("revcat", $urlParams[count($urlParams)-2]);
        $this->assertEquals("15", $urlParams[count($urlParams)-1]);
    }
}
