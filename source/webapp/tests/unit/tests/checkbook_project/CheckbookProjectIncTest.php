<?php

use PHPUnit\Framework\TestCase;

/**
 * Class CheckbookProjectIncTest
 */
class CheckbookProjectIncTest extends TestCase
{
    /**
     * tests _checkbook_current_request_is_ajax
     */
    public function testCurrentRequestIsAjax()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
        $this->assertTrue(_checkbook_current_request_is_ajax());
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->assertFalse(_checkbook_current_request_is_ajax());
    }
}
