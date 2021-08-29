<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/MappingUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';

use PHPUnit\Framework\TestCase;

/**
 * Class MappingUtilTest
 */
class MappingUtilTest extends TestCase
{
    /**
     * Tests getVendorTypeValue() function
     */
    public function test_getVendorTypeValue(){
        $vendor_type = 'pv';
        $result = MappingUtil::getVendorTypeValue(array($vendor_type));
        $this->assertEquals('P', $result[0]);
        $this->assertEquals('PM', $result[1]);
    }

    /**
     * Tests getVendorTypeName() function
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
        $this->assertEquals('Native American', $result);
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
        $result = MappingUtil::getCurrenEthnicityName(array('2'));
        $this->assertEquals('Black American', $result);
    }

    /**
     * Tests getaprv_sta_name() function
     */
    public function test_getaprv_sta_name(){
        $result = MappingUtil::getaprv_sta_name('6');
        $this->assertEquals('No Subcontract Information Submitted', $result);
    }

    /**
     * Tests getMixedVendorTypeNames() function
     */
    public function test_getMixedVendorTypeNames(){
        $result = MappingUtil::getMixedVendorTypeNames('P~PM');
        $this->assertEquals('PRIME VENDOR', $result);
    }

}
