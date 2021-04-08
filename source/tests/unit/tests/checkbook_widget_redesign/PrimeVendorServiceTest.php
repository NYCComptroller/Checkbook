<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/CommonConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_project.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/CheckbookDateUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/utilities/RequestUtilities.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/MinorityTypeService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/VendorService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/PrimeVendorService.php';

use PHPUnit\Framework\TestCase;

/**
 * Class PrimeVendorServiceTest
 */
class PrimeVendorServiceTest extends TestCase
{   
    /**
     * Tests getLatestMinorityType() function
     */
    public function test_getLatestMinorityType(){
        $result = PrimeVendorService::getLatestMinorityType(129113,null,'P','spending');
        $this->assertTrue(test_validate_getLatestMinorityType($result));
    }

    /**
     * Helper function to validate result of getLatestMinorityType() function
     */
    private function test_validate_getLatestMinorityType($result){
        return $result == NULL || $result == 2;
    }

    // /**
    //  * Tests getLatestMinorityTypeByYear() function
    //  */
    // public function test_getLatestMinorityTypeByYear(){
    //     $result = PrimeVendorService::getLatestMinorityTypeByYear(5260, 161);
    //     $this->assertEquals('Black American', $result);
    // }

    // /**
    //  * Tests getVendorCode() function
    //  */
    // public function test_getVendorCode(){
    //     $result = PrimeVendorService::getVendorCode('B', 121, Domain::$SPENDING);
    //     $this->assertTrue(isset($result[5260][2]['P'][5]));
    // }
    
    // /**
    //  * Tests getVendorCode() function
    //  */
    // public function test_getVendorIdByName(){
    //     $result = PrimeVendorService::getVendorIdByName('B', 121, Domain::$SPENDING);
    //     $this->assertTrue(isset($result[5260][2]['P'][5]));
    // }  
}