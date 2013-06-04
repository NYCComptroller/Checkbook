(function ($) {

/**
 * Attach prettify admin page settings behavior.
 */
Drupal.behaviors.prettifyAdmin = {
  attach: function (context) {  
    var preInput = $('#edit-prettify-auto-markup-pre');
    var precodeInput = $('#edit-prettify-auto-markup-precode');
    
    var processPreInput = function(){
      if (preInput.attr('checked')) {
        precodeInput.attr('checked', 'checked').attr('disabled', 'disabled');
      } else {
        precodeInput.removeAttr('disabled');
      }
    };
  
    preInput.click(function() {
      processPreInput();
    });
  
    processPreInput();
  }
};

})(jQuery);
