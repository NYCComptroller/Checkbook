<?php
include_once __DIR__ . '/../../../webapp/sites/all/modules/custom/checkbook_etl_status/checkbook_etl_status.module';

use PHPUnit\Framework\TestCase;

/**
 * Class CheckbookEtlStatusModuleTest
 */
class CheckbookEtlStatusModuleTest extends TestCase
{

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
    }

    public function test_checkbook_etl_status_mail_alter_success()
    {
        $msg = [
            'subject' => 'Amazing Success',
            'body' => "Great\tSUCCESS",
            'module' => 'checkbook_etl_status',
            'headers' => []
        ];
        checkbook_etl_status_mail_alter($msg);
        $this->assertEquals('text/html; charset=UTF-8; format=flowed; delsp=yes', $msg['headers']['Content-Type']);
        $this->assertEquals("Great\t<strong style='color:green'>SUCCESS</strong>", $msg['body']);
        $this->assertTrue(empty($msg['headers']['X-Priority']));
        $this->assertTrue(empty($msg['headers']['Importance']));
        $this->assertTrue(empty($msg['headers']['X-MSMail-Priority']));
    }

    public function test_checkbook_etl_status_mail_alter_fail()
    {
        $msg = [
            'subject' => 'Big Fail',
            'body' => "Huge\tFAIL",
            'module' => 'checkbook_etl_status',
            'headers' => []
        ];
        checkbook_etl_status_mail_alter($msg);
        $this->assertEquals('text/html; charset=UTF-8; format=flowed; delsp=yes', $msg['headers']['Content-Type']);
        $this->assertEquals("Huge\t<strong style='color: red'>FAIL</strong>", $msg['body']);
        $this->assertFalse(empty($msg['headers']['X-Priority']));
        $this->assertFalse(empty($msg['headers']['Importance']));
        $this->assertFalse(empty($msg['headers']['X-MSMail-Priority']));
    }

//    public function test_checkbook_datafeeds_contracts_filter_data()
//    {
//        $test_form = [];
//        $test_form_state = [
//            'triggering_element' =>
//            [
//                '#value' => 'somevalue',
//                '#ajax' => ['parameters'=> ['data_source_changed'=>'yes']]
//            ]
//        ];
//        $test_data_source = [];
//        $form = checkbook_datafeeds_contracts_filter_data($test_form, $test_form_state, $test_data_source);
//        $this->assertEquals('array', gettype($form));
//    }

    public function test_checkbook_datafeeds_process_contracts_values()
    {
        $test_form = [];
        $test_form_state = [];
        $test_form_state['step_information']['contracts']['stored_values'] = [
            'df_contract_status' => true,
            'currentamtfrom'=> '',
            'category' => '',
            'currentamtto' => '',
            'currentamtfrom' => '',
            'startdateto' => '',
            'enddatefrom' => '',
            'enddateto' => '',
            'regdatefrom' => '',
            'regdateto' => '',
            'recdatefrom' => '',
            'recdateto' => '',
            'pin' => '',
            'apt_pin' => '',
            'purpose' => '',
            'agency' => 'Select One',
            'entity_contract_number' => '',
            'startdatefrom' => '',
            'commodity_line' => '',
            'budget_name' => '',
            'vendor' => '',
            'award_method' => '',
            'contractno' => '',
            'industry' => '',
            'mwbe_category' => '',
            'contract_includes_sub_vendors_id' => '',
            'sub_vendor_status_in_pip_id' => '',
            'contract_type' => 'No Contract Type Selected',
        ];
        $criteria = checkbook_datafeeds_process_contracts_values($test_form, $test_form_state, 'checkbook_oge');
        $this->assertEquals('array', gettype($criteria));
    }
}