<?php
/**
 * Created by PhpStorm.
 * User: amyanani
 * Date: 1/4/17
 * Time: 11:39 AM
 */
//require 'vendor/autoload.php';
use Calculator\Calculator;

class CalculatorTest extends PHPUnit_Framework_TestCase {
    private $calculator;

    protected function setUp()
    {
        $this->calculator = new Calculator();
    }

    protected function tearDown()
    {
        $this->calculator = NULL;
    }

    public function testAdd()
    {
        $result = $this->calculator->add(2,1);
        $this->assertEquals(3, $result);
    }
}
