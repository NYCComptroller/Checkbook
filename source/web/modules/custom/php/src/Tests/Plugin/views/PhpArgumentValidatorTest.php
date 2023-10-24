<?php

namespace Drupal\php\Tests\Plugin\views;

use Drupal\Tests\views\Kernel\ViewsKernelTestBase;
use Drupal\views\Tests\ViewTestData;
use Drupal\views\Views;

/**
 * Tests Views PHP argument validators.
 *
 * @group PHP
 *
 * @see \Drupal\php\Plugin\views\argument_validator\Php
 */
class PhpArgumentValidatorTest extends ViewsKernelTestBase {

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = ['test_view_argument_validate_php'];

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['filter', 'php', 'php_views_test_config'];

  /**
   * {@inheritdoc}
   */
  protected function setUp($import_test_views = TRUE): void {
    parent::setUp();
    if ($import_test_views) {
      ViewTestData::createTestViews(get_class($this), ['php_views_test_config']);
    }
  }

  /**
   * Tests the validateArgument question.
   */
  public function testArgumentValidatePhp() {
    $string = $this->randomMachineName();
    $view = Views::getView('test_view_argument_validate_php');
    $view->setDisplay();
    $view->displayHandlers->get('default')->options['arguments']['null']['validate_options']['code'] = 'return $argument == \'' . $string . '\';';

    $view->initHandlers();
    $this->assertTrue($view->argument['null']->validateArgument($string));
    // Reset saved argument validation.
    $view->argument['null']->argument_validated = NULL;
    $this->assertFalse($view->argument['null']->validateArgument($this->randomMachineName()));
  }

}
