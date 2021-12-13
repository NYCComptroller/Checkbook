<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/contracts/ContractsParameters.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/ContractConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/MinorityTypeService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/CommonConstants.php';

use PHPUnit\Framework\TestCase;


/**
 * Class ContractsUrlServiceTest
 */
class ContractsParametersTest extends TestCase
{
    /**
     * Tests contractIdUrl() function
     */
    public function test_getContractCategory()
    {
        $_GET['q'] = '/contracts_revenue_landing/status/A/yeartype/B/year/123';
        $result = ContractsParameters::getContractCategory();
        $this->assertEquals("revenue", $result);

        $_GET['q'] = '/contracts_pending_exp_landing/yeartype/B/year/123';
        $result = ContractsParameters::getContractCategory();
        $this->assertEquals("pending expense", $result);

        $_GET['q'] = '/contracts_pending_rev_landing/yeartype/B/year/123';
        $result = ContractsParameters::getContractCategory();
        $this->assertEquals("pending revenue", $result);

        $_GET['q'] = "/contracts_landing/status/A/yeartype/B/year/123";
        $result = ContractsParameters::getContractCategory();
        $this->assertEquals("expense", $result);
    }
}


