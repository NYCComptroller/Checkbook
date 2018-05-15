<?php
/**
 * Created by PhpStorm.
 * User: snigdha.madhavaram
 * Date: 5/14/18
 * Time: 9:11 PM
 */
include_once __DIR__ . '/../../../webapp/sites/all/modules/custom/checkbook_project/customclasses/util/WidgetUtil.php';

use PHPUnit\Framework\TestCase;
class WidgetUtilTest extends TestCase
{

    /**
     * @param $label
     * @param $expectedLabel
     *
     * @dataProvider Labels
     */
public function testgetLabelTest($labelParam,$expected){

    $result = WidgetUtil::getLabel($labelParam);
    $this->assertEquals($expected,$result);
}
public function Labels(){

    return array(
        array('combined_overtime_pay_ytd', 'Combined Overtime Payments YTD'),
        array('total_no_of_non_sal_employees', 'Total Number of Non-Salaried Employees'),
        array('combined_base_pay_ytd','Combined Base Pay YTD')


    );
}
}


