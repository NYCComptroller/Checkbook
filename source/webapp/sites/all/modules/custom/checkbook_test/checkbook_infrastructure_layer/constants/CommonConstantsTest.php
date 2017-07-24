<?php

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

class CommonConstantsTest extends PHPUnit_Framework_TestCase
{
    public function testCheckbookDomainConstants()
    {
        $domain = CheckbookDomain::SPENDING;
        $this->assertTrue($domain == "spending");
    }
}