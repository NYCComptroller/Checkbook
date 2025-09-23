<?php

namespace Drupal\Tests\php\Functional;

/**
 * Tests to make sure access to the PHP filter is properly restricted.
 *
 * @group PHP
 */
class PhpAccessTest extends PhpTestBase {

  /**
   * Makes sure that the user can't use the PHP filter when not given access.
   */
  public function testNoPrivileges() {
    // Create node with PHP filter enabled.
    $permissions = [
      'access content',
      'create page content',
      'edit own page content',
    ];
    $web_user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($web_user);
    $node = $this->createNodeWithCode();

    // Make sure that the PHP code shows up as text.
    $this->drupalGet('node/' . $node->id());
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Verify the assertion: pageTextContains() for HTML responses, responseContains() for non-HTML responses.
    // The passed text should be HTML decoded, exactly as a human sees it in the browser.
    $this->assertSession()->pageTextContains('print');

    // Make sure that user doesn't have access to filter.
    $this->drupalGet('node/' . $node->id() . '/edit');
    $this->assertSession()->responseNotContains('<option value="' . $this->phpCodeFormat->id() . '">');
  }

}
