/**
 * @file
 * PHP block behaviors.
 */

(function ($) {

  "use strict";

  /**
   * Provide the summary information for the block settings vertical tabs.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the behavior for the block settings summaries.
   */
  Drupal.behaviors.phpSettingsSummary = {
    attach: function () {
      // The drupalSetSummary method required for this behavior is not available
      // on the Blocks administration page, so we need to make sure this
      // behavior is processed only if drupalSetSummary is defined.
      if (typeof jQuery.fn.drupalSetSummary === 'undefined') {
        return;
      }

      $('[data-drupal-selector="edit-visibility-php"]').drupalSetSummary(function (context) {
        var $code = $(context).find('textarea[name="visibility[php][php]"]');
        if ($code.val() === '<?php return TRUE; ?>') {
          return Drupal.t('Not restricted');
        }
        else {
          return Drupal.t('Restricted to certain pages');
        }
      });
    }
  };

})(jQuery);
