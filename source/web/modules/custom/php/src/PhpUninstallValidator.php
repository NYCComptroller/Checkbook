<?php

namespace Drupal\php;

use Drupal\filter\FilterUninstallValidator;

/**
 * Remove filter preventing Php uninstall.
 */
class PhpUninstallValidator extends FilterUninstallValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($module) {
    $reasons = [];
    if ($module == 'php') {
      $this->removeFilterConfig();
    }
    return $reasons;
  }

  /**
   * Deletes configuration.
   */
  protected function removeFilterConfig() {
    $php_filter = \Drupal::configFactory()->getEditable('filter.format.php_code');
    $php_filter->delete();

    // Clear cache.
    drupal_flush_all_caches();
  }

}
