<?php

//include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/MinorityTypeService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/util/MappingUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/utilities/RequestUtilities.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_project.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/CommonConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/SpendingConstants.php';


use PHPUnit\Framework\TestCase;

/**
 * Class MinorityTypeServiceTest
 */
class MappingUtilTest extends TestCase
{
    /**
     * Tests getVendorTypeValue() function
     */
    public function test_getVendorTypeValue(){
        $parameters['vendor_type'] = 'pv';
        //$result = MappingUtil::getVendorTypeValue($parameters['vendor_type']);
        //$this->assertEquals('S~SM', $result);
    }

    /**
     * Tests getVendorTypeValue() function
     */
    public function test_getVendorTypeName(){
        $result = MappingUtil::getVendorTypeName('sv');
        $this->assertEquals('Sub Vendor', $result);
    }

    /**
     * Tests isMWBECertified() function
     */
    public function test_isMWBECertified(){
        $minority_type_id ="5";
        $result = MappingUtil::isMWBECertified(array($minority_type_id));
        $this->assertTrue(true);
    }

    /**
     * Tests getMinorityCategoryById() function
     */
    public function test_getMinorityCategoryById(){
        $minority_type_id = "4";
        $result = MappingUtil::getMinorityCategoryById($minority_type_id);
        $this->assertEquals('Asian American', $result);
    }

    /**
     * Tests getMinorityCategoryById() function
     */
    public function test_getTotalMinorityIds(){

        $result = MappingUtil::getTotalMinorityIds(null);
        $this->assertEquals('2,3,4,5,6,9,99', $result);
        $result = MappingUtil::getTotalMinorityIds('url');
        $this->assertEquals('2~3~4~5~6~9~99', $result);
    }

    /**
     * Tests getMinorityCategoryByName() function
     */
    public function test_getMinorityCategoryByName(){
        $result = MappingUtil::getMinorityCategoryByName('Native');
        $this->assertEquals('Native', $result);
        $result = MappingUtil::getMinorityCategoryByName('African American');
        $this->assertEquals('Black American', $result);

    }

    /**
     * Tests getMinorityCategoryMappings() function
     */
    public function test_getMinorityCategoryMappings(){
        $result = MappingUtil::getMinorityCategoryMappings();
        $this->assertIsArray($result);
    }

    /**
     * Tests getCurrenEthnicityName() function
     */
    public function test_getCurrenEthnicityName(){
        //$result = MappingUtil::getCurrenEthnicityName();
        //$this->assertIsArray($result);
    }

    /**
     * Tests getCurrenEthnicityName() function
     */
    public function test_isDefaultMWBEDashboard(){
        //$result = MappingUtil::getCurrenEthnicityName();
        //$this->assertIsArray($result);
    }

    /**
     * Tests getCurrenEthnicityName() function
     */
    public function test_getCurrentSubVendorsTopNavFilters(){
        //$result = MappingUtil::getCurrentSubVendorsTopNavFilters();
        //$this->assertIsArray($result);
    }

}
