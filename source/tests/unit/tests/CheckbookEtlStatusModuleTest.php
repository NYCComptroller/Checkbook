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
            'headers' => []
        ];
        checkbook_etl_status_mail_alter($msg);
        $this->assertEquals('text/html; charset=UTF-8; format=flowed; delsp=yes', $msg['headers']['Content-Type']);
        $this->assertEquals("Huge\t<strong style='color: red'>FAIL</strong>", $msg['body']);
        $this->assertFalse(empty($msg['headers']['X-Priority']));
        $this->assertFalse(empty($msg['headers']['Importance']));
        $this->assertFalse(empty($msg['headers']['X-MSMail-Priority']));
    }
}