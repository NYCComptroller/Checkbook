(function ($) {

Drupal.prettify = Drupal.prettify || {};
  
/**
 * Attach prettify loader behavior.
 */
Drupal.behaviors.prettify = {
  attach: function (context) {  
    if (Drupal.settings.prettify.match) {
      context = Drupal.settings.prettify.match;
    }
  
    if (Drupal.settings.prettify.markup['code']) {
      // Selector for <code>...</code>
      $("code:not(.prettyprint)", context).not($("pre > code", context)).each(function () {
        Drupal.prettify.prettifyBlock($(this));
      });
    }
    if (Drupal.settings.prettify.markup['pre']) {
      // Selector for <pre>...</pre>
      $("pre:not(.prettyprint)", context).each(function () {
        Drupal.prettify.prettifyBlock($(this));
      });
    }
    else if (Drupal.settings.prettify.markup['precode']) {
      // Selector for <pre><code>...</code></pre>
      $("pre:not(.prettyprint) > code", context).parent().each(function () {
        Drupal.prettify.prettifyBlock($(this));
      });
    }
    
    // Process custom markup selectors
    for (var i = 0; i < Drupal.settings.prettify.custom.length; i++) {
      var selector = Drupal.settings.prettify.custom[i];
      if (selector) {
        $(selector, context).each(function () {
          if (!$(this).hasClass('prettyprint')) {
            codeBlock = $(this).parent().is('pre') ? $(this).parent() : $(this);
            Drupal.prettify.prettifyBlock(codeBlock);
          }
        });
      }      
    }
  
    if ($(".prettyprint").length > 0) {
      prettyPrint();
    }
  }
};

Drupal.prettify.prettifyBlock = function(codeBlock) {
  if (!codeBlock.hasClass(Drupal.settings.prettify.nocode)) {
    codeBlock.addClass("prettyprint");
    if (Drupal.settings.prettify.linenums && codeBlock.is('pre')) {
      codeBlock.addClass("linenums");
    }
  }
}

})(jQuery);
