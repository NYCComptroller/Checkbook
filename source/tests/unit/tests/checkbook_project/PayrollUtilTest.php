<?php
/**
 * Created by PhpStorm.
 * User: snigdha.madhavaram
 * Date: 5/3/18
 * Time: 2:06 PM
 */
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/payroll/PayrollUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';

use PHPUnit\Framework\TestCase;

/**
 * Class PayrollUtilTest
 */
class PayrollUtilTest extends TestCase
{

    /**
     *
     */
    public function test_getEmploymentTypeByAmountBasisId()
    {
        $result = PayrollUtil::getEmploymentTypeByAmountBasisId(1);
        $this->assertEquals('Salaried', $result);

    }

    /**
     *
     */
    public function testGetDataByPayFrequency()
    {
        $employeedata = array(
            array("pay_frequency" => 'BI-WEEKLY'),
            array("pay_frequency" => "xyz"));
        $result = PayrollUtil::getDataByPayFrequency('BI-WEEKLY', $employeedata);
        $this->assertEquals(array("pay_frequency" => 'BI-WEEKLY'), $result);
    }

    /**
     *
     */
    public function testIsTitleLandingPage()
    {
        $_SERVER['HTTP_REFERER'] = "payroll/agency_landing/yeartype/C/year/119/title/2063/agency/18";
        $_GET['q'] = "payroll/title_landing/yeartype/C/year/119/title/2063";
        $this->assertTrue(PayrollUtil::isTitleLandingPage());
    }
}
