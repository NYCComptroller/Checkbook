<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/spending/SpendingUrlService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/CommonConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/MinorityTypeService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/SpendingConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/CheckbookDateUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_project.inc';

use PHPUnit\Framework\TestCase;

/**
 * Class ContractsUrlServiceTest
 */
class SpendingUrlServiceTest extends TestCase
{


    /**
     * Tests contractIdUrl() function
     */
    public function test_contractIdUrl(){
        $result = SpendingUrlService::contractIdUrl("5868332", "MMA1");
        $this->assertEquals("/contract_details/magid/5868332/doctype/MMA1/newwindow", $result);
    }

    /**
     * Tests agencyUrl() function
     */
    public function test_agencyUrl()
    {
        $result = SpendingUrlService::agencyUrl("138");
       //$this->assertEquals("/spending_landing/yeartype/B/year/122/agency/138", $result);
        $this->assertEquals("/agency/138", substr($result, -11));
    }

    /**
     * Tests payrollAgencyUrl() function
     */
    public function test_payrollAgencyUrl()
    {
        $result = SpendingUrlService::payrollAgencyUrl("138");
        $this->assertEquals("/category/2/agency/138", substr($result, -22));
    }

    /**
     * Tests payrollAgencyUrl() function
     */
    public function test_industryUrl()
    {
        $result = SpendingUrlService::industryUrl("2");
        $this->assertEquals("/industry/2", substr($result, -11));
    }

    /**
     * Tests payrollAgencyUrl() function
     */
    public function test_getFooterUrl()
    {
        $result = SpendingUrlService::getFooterUrl('','22');
        $this->assertEquals("/panel_html/spending_transactions/spending/transactions/yeartype/B/year/-1/dtsmnid/22", $result);
    }

    /**
     * Tests ytdSpendingUrl() function
     */
    public function test_ytdSpendingUrl()
    {
        $result = SpendingUrlService::ytdSpendingUrl(null,'');
        $this->assertEquals("/panel_html/spending_transactions/spending/transactions/yeartype/B/year/-1/smnid/", $result);
    }

    /**
     * Tests mwbeUrl() function
     */
    public function test_mwbeUrl()
    {
        $result = SpendingUrlService::mwbeUrl('2~3~4~5~9','mp');
        $this->assertEquals("/spending_landing/yeartype/B/year/-1/dashboard/mp/mwbe/2~3~4~5~9?expandBottomCont=true", $result);
    }

}
