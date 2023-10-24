<?php

namespace Drupal\Tests\php\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\user\RoleInterface;

/**
 * Test if PHP filter works in general.
 *
 * @group PHP
 */
abstract class PhpTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['node', 'php'];

  protected $phpCodeFormat;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create Basic page node type.
    $this->drupalCreateContentType(['type' => 'page', 'name' => t('Basic page')]);

    // Create and login admin user.
    $admin_user = $this->drupalCreateUser(['administer filters']);
    $this->drupalLogin($admin_user);

    // Verify that the PHP code text format was inserted.
    $php_format_id = 'php_code';
    $this->phpCodeFormat = \Drupal::entityTypeManager()->getStorage('filter_format')->load($php_format_id);

    $this->assertEquals($this->phpCodeFormat->label(), 'PHP code', 'PHP code text format was created.');

    // Verify that the format has the PHP code filter enabled.
    $filters = $this->phpCodeFormat->filters();
    $this->assertTrue($filters->get('php_code')->status, 'PHP code filter is enabled.');

    // Verify that the format exists on the administration page.
    $this->drupalGet('admin/config/content/formats');
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Verify the assertion: pageTextContains() for HTML responses, responseContains() for non-HTML responses.
    // The passed text should be HTML decoded, exactly as a human sees it in the browser.
    $this->assertSession()->pageTextContains('PHP code');

    // Verify that anonymous and authenticated user roles do not have access.
    $this->drupalGet('admin/config/content/formats/manage/' . $php_format_id);
    $this->assertSession()->fieldValueEquals('roles[' . RoleInterface::ANONYMOUS_ID . ']', FALSE);
    $this->assertSession()->fieldValueEquals('roles[' . RoleInterface::AUTHENTICATED_ID . ']', FALSE);
  }

  /**
   * Creates a test node with PHP code in the body.
   *
   * @return \Drupal\node\NodeInterface
   *   Node object.
   */
  public function createNodeWithCode() {
    return $this->drupalCreateNode(['body' => [['value' => '<?php print "SimpleTest PHP was executed!";print "Current state is " . Drupal::state()->get("php_state_test", "empty"); ?>']]]);
  }

}
