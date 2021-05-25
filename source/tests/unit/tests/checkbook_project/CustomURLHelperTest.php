<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/CustomURLHelper.php';

use PHPUnit\Framework\TestCase;

/**
 * Class CustomURLHelperTest
 */
class CustomURLHelperTest extends TestCase
{
    /**
     * Tests get_url_param() function
    */
    public function test_get_url_param(){
        $pathParams = array("contracts_landing", "status", "A", "yeartype", "B", "year", "122");
        $key = "status";
        $key_alias = "status";

        $result = CustomURLHelper::get_url_param(NULL, $key);
        $this->assertEquals(NULL, $result);

        $result = CustomURLHelper::get_url_param($pathParams, $key);
        $this->assertEquals("/status/A", $result);

        $result = CustomURLHelper::get_url_param($pathParams, $key, $key_alias);
        $this->assertEquals("/status/A", $result);

        $key = "mwbe";
        $result = CustomURLHelper::get_url_param($pathParams, $key);
        $this->assertEquals(NULL, $result);
    }

    

}