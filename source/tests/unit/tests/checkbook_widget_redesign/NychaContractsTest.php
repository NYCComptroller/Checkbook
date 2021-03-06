<?php
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/nychaContracts/NychaContractsUrlService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/contract/NYCHAContractsUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';

use PHPUnit\Framework\TestCase;
/**
 * Class NychaContractsUrlServiceTest
 */
class NychaContractsUrlServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getFooterUrl() function
     */
    public function test_getFooterUrl()
    {
        $result = NychaContractsUrlService::getFooterUrl();
        $this->assertEquals("/panel_html/nycha_contracts_transactions_page/nycha_contracts/transactions", substr($result, 0, 74));
    }

    /**
     * Tests generateLandingPageUrl() function
     */
    public function test_generateLandingPageUrl()
    {
        $result = NychaContractsUrlService::generateLandingPageUrl('agency', '162');
        $urlParams = explode('/', $result);
        $this->assertEquals("nycha_contracts", $urlParams[1]);
        //Tests last URL params
        $this->assertEquals("agency", $urlParams[count($urlParams)-2]);
        $this->assertEquals("162", $urlParams[count($urlParams)-1]);
    }

    /**
     * Tests vendorUrl() function
     */
    public function test_vendorUrl()
    {
        $result = NychaContractsUrlService::vendorUrl( '84571');
        $urlParams = explode('/', $result);
        $this->assertEquals("nycha_contracts", $urlParams[1]);
        //Tests last URL params
        $this->assertEquals("vendor", $urlParams[count($urlParams)-2]);
        $this->assertEquals("84571", $urlParams[count($urlParams)-1]);
    }

    /**
     * Tests contractDetailsUrl() function
     */
    public function test_contractDetailsUrl()
    {
        $result = NychaContractsUrlService::contractDetailsUrl(NULL, NULL);
        $this->assertEquals("/nycha_contracts", substr($result, 0, 16));
    }

    /**
     * Tests modificationUrl() function
     */
    public function test_modificationUrl()
    {
        $result = NychaContractsUrlService::modificationUrl();
        $this->assertEquals("/modamt/0", $result);
    }

    /**
     * Tests agreementTypeUrl() function
     */

    public function test_agreementTypeUrl()
    {
        $result = NychaContractsUrlService::agreementTypeUrl('BAM');
        $this->assertEquals("/agreement_type/BAM",$result);

    }

    /**
     * Tests TypeUrl() function
     */
    public function test_TypeUrl()
    {
        $result = NychaContractsUrlService::TypeUrl('BA');
        $this->assertEquals("/tCode/BA",$result);
    }

}

/**
 * Class NychaContractUtilServiceTest
 */
class NYCHAContractsUtilTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tests getTransactionsTitle() function
     */
    public function test_getTitleByCode()
    {
        $result = NYCHAContractUtil::getTitleByCode('BAM');
        $this->assertEquals('Blanket Agreement Modifications', $result);

    }


}
