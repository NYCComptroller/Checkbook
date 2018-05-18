<?php

include_once __DIR__ . '/../../../webapp/sites/all/modules/custom/checkbook_api/json_api/CheckBookJsonApi.php';

use PHPUnit\Framework\TestCase;
use \checkbook_json_api\CheckBookJsonApi;

function _checkbook_project_execute_sql($query)
{
    $return = [];
    $return['total'] = 777555333111;
    $return['acco_approved'] = 111;
    $return['acco_reviewing'] = 888;
    $return['acco_rejected'] = 999;
    $return['acco_cancelled'] = 222;
    $return['acco_submitted'] = 333;
    $return['total_gross_pay'] = 123456789000;
    $return['total_base_pay'] = 987654321000;
    $return['total_overtime_pay'] = 192837465000;

    return [$return];
}

/**
 * Class CheckBookJsonApiTest
 */
class CheckBookJsonApiTest extends TestCase
{
    /**
     * @var CheckBookJsonApi
     */
    private $api;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
//        $this->api = new CheckBookJsonApi();
    }

    public function test_active_expense_contracts()
    {
        $this->api = new CheckBookJsonApi(['','2017','C']);
        $result = $this->api->active_expense_contracts();
        $this->assertEquals(true, $result['success']);
        $this->assertEquals(777555333111, $result['data']);
    }

    public function test_active_subcontracts()
    {
        $this->api = new CheckBookJsonApi(['','2017','C']);
        $result = $this->api->active_subcontracts();
        $this->assertEquals(true, $result['success']);
        $this->assertEquals(777555333111, $result['data']);
    }

    public function test_subcontracts_approved()
    {
        $this->api = new CheckBookJsonApi(['','2017','C']);
        $result = $this->api->subcontracts_approved();
        $this->assertEquals(true, $result['success']);
        $this->assertEquals(111, $result['data']);
    }

    public function test_subcontracts_under_review()
    {
        $this->api = new CheckBookJsonApi(['','2017','C']);
        $result = $this->api->subcontracts_under_review();
        $this->assertEquals(true, $result['success']);
        $this->assertEquals(888, $result['data']);
    }

    public function test_subcontracts_submitted()
    {
        $this->api = new CheckBookJsonApi(['','2017','C']);
        $result = $this->api->subcontracts_submitted();
        $this->assertEquals(true, $result['success']);
        $this->assertEquals(333, $result['data']);
    }

    public function test_subcontracts_canceled()
    {
        $this->api = new CheckBookJsonApi(['','2017','C']);
        $result = $this->api->subcontracts_canceled();
        $this->assertEquals(true, $result['success']);
        $this->assertEquals(222, $result['data']);
    }

    public function test_subcontracts_rejected()
    {
        $this->api = new CheckBookJsonApi(['','2017','C']);
        $result = $this->api->subcontracts_rejected();
        $this->assertEquals(true, $result['success']);
        $this->assertEquals(999, $result['data']);
    }

    public function test_total_payroll()
    {
        $this->api = new CheckBookJsonApi(['','2017','C']);
        $result = $this->api->total_payroll();
        $this->assertEquals(true, $result['success']);
        $this->assertEquals('123457000000.00', $result['data']);
    }

    public function test_total_spending()
    {
        $this->api = new CheckBookJsonApi(['','2017','C']);
        $result = $this->api->total_spending();
        $this->assertEquals(true, $result['success']);
        $this->assertEquals('777555000000.00', $result['data']);
    }

    public function test_total_budget()
    {
        $this->api = new CheckBookJsonApi(['','2017','C']);
        $result = $this->api->total_budget();
        $this->assertEquals(true, $result['success']);
        $this->assertEquals('777555000000.00', $result['data']);
    }

    public function test_total_revenue()
    {
        $this->api = new CheckBookJsonApi(['','2017','C']);
        $result = $this->api->total_revenue();
        $this->assertEquals(true, $result['success']);
        $this->assertEquals('777555000000.00', $result['data']);
    }
}