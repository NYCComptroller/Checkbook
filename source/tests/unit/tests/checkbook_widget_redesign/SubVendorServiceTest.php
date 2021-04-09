<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/CommonConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_project.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/CheckbookDateUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/utilities/RequestUtilities.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/MinorityTypeService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/VendorService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/SubVendorService.php';

use PHPUnit\Framework\TestCase;

/**
 * Class SubVendorServiceTest
 */
class SubVendorServiceTest extends TestCase
{   
    /**
     * Tests getLatestMinorityType() function
     */
    public function test_getLatestMinorityType(){
        $result = SubVendorService::getLatestMinorityType(129113, null, 'P', 'spending');
        $this->assertEquals(9, $result);
    }

    /**
     * Tests getLatestMinorityTypeByYear() function
     */
    public function test_getLatestMinorityTypeByYear(){
        $result = SubVendorService::getLatestMinorityTypeByYear(129113,121,'B','P','spending');
        $this->assertEquals(2,$result);
    }

    /**
     * Tests getVendorCode() function
     */
    public function test_getVendorCode(){
        $result = SubVendorService::getVendorCode(129113);
        $this->assertEquals("VC00153345", $result);
    }
    
    /**
     * Tests getVendorCode() function
     */
    public function test_getVendorIdByName(){
        $result = SubVendorService::getVendorIdByName("WHERE TO GET IT SERVICES LLC");
        $this->assertEquals(129113, $result);
    }    
}