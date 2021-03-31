<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/MinorityTypeService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';

use PHPUnit\Framework\TestCase;

/**
 * Class MinorityTypeServiceTest
 */
class MinorityTypeServiceTest extends TestCase
{   
    /**
     * Tests isMWBECertified() function
     */
    public function test_isMWBECertified(){
        $result = MinorityTypeService::isMWBECertified("Asian American");
        $this->assertEquals(true, $result);
    }

    /**
     * Tests getMinorityCategoryById() function
     */
    public function test_getMinorityCategoryById(){
        $result = MinorityTypeService::getMinorityCategoryById(2);
        $this->assertEquals('Black American', $result);
    }

    /**
     * Tests getAllVendorMinorityTypes() function
     */
    public function test_getAllVendorMinorityTypes(){
        $result = MinorityTypeService::getAllVendorMinorityTypes('B', 121, Domain::$SPENDING);
        $this->assertTrue(isset($result[5260][2]['P'][5]));
    }    
}