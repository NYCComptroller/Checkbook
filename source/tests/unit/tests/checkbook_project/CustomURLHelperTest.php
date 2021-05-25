<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_project.inc';
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

    /**
     * Tests prepareUrl() function
    */
    public function test_prepareUrl(){
        $temp = $_GET['q'];
        $_GET['q'] = "contracts_landing/status/A/yeartype/B/year/122";
        $_SERVER['HTTP_REFERER'] = "http://checkbooknyc.com/contracts_landing/status/A/yeartype/B/year/122";
        $path = "contracts_landing";
        $params = array(
            "status" => "status",
            "vendor" => "vendor",
            "agency" => "agency", 
            "awdmethod" => "awdmethod",
            "cindustry" => "cindustry",
            "csize" => "csize"
        );
        $requestParams = array();
        $customPathParams = array(
            "doctype" => "CTA1~CT1~MA1",
            "month" => "2650",
            "amt" => 1595633629.98,
            "smnid" => 365,
            "newwindow" => ""
        );
        $applyPreviousYear = true;
        $applySpendingYear = false;

        $result = CustomURLHelper::prepareUrl($path, $params, $requestParams, $customPathParams, $applyPreviousYear, $applySpendingYear);
        $this->assertEquals("contracts_landing/yeartype/B/year/121/status/A/doctype/CTA1~CT1~MA1/month/2650/amt/1595633629.98/smnid/365/newwindow/", $result);

        $requestParams = array(
            "expandBottomCont" => "true"
        );

        $result = CustomURLHelper::prepareUrl($path, $params, $requestParams, $customPathParams, $applyPreviousYear, $applySpendingYear);
        $this->assertEquals("contracts_landing/yeartype/B/year/121/status/A/doctype/CTA1~CT1~MA1/month/2650/amt/1595633629.98/smnid/365/newwindow/?expandBottomCont=true", $result);

        $_GET['q'] = $temp;
    }

}