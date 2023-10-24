<?php

namespace Drupal\php\Tests\Condition;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests that the PHP Condition, provided by php module, is working properly.
 *
 * @group PHP
 */
class PhpConditionTest extends KernelTestBase {

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $manager;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['filter', 'system', 'php'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->manager = $this->container->get('plugin.manager.condition');
  }

  /**
   * Tests conditions.
   */
  public function testConditions() {
    // Grab the PHP condition and configure it to check against a php snippet.
    $condition = $this->manager->createInstance('php')
      ->setConfig('php', '<?php return TRUE; ?>');
    $this->assertTrue((bool) $condition->execute(), 'PHP condition passes as expected.');
    // Check for the proper summary.
    self::assertEquals($condition->summary(), 'When the given PHP evaluates as TRUE.');

    // Set the PHP snippet to return FALSE.
    $condition->setConfig('php', '<?php return FALSE; ?>');
    $this->assertFalse((bool) $condition->execute(), 'PHP condition fails as expected.');

    // Negate the condition.
    $condition->setConfig('negate', TRUE);
    // Check for the proper summary.
    self::assertEquals($condition->summary(), 'When the given PHP evaluates as FALSE.');

    // Reverse the negation.
    $condition->setConfig('negate', FALSE);
    // Set and empty snippet.
    $condition->setConfig('php', FALSE);
    // Check for the proper summary.
    self::assertEquals($condition->summary(), 'No PHP code has been provided.');
  }

}
