(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.chart = {
    attach: function (context, drupalSetting) {
      if ($(".cycle-slideshow").filter(":first").length > 0) {
        $(once("chart-slide", ".cycle-slideshow")).filter(":first").cycle({
          slideExpr: ".slider-pane",
          fx: "fade",
          timeout: 45000,
          height: "373px",
          width: "100%",
          fit: 1,
          pause: true,
          pager: ".slider-pager",
        });
      }
    },
  };

  /* Griview Pop up */
  Drupal.behaviors.gridViewAllPopup = {
    attach: function (context, settings) {
      newWindow('a.gridpopup');
      newWindow('a.new_window');

      function newWindow(selector) {
        $(once(selector, 'body', context)).each(function () {
          $(this).on('click', selector, function (e) {
            e.preventDefault();
            var source = $(this).attr('href');
            var newWindow = window.open(source, '_blank', 'menubar=no,toolbar=no,location=no,resizable=yes,scrollbars=yes,personalbar=no,chrome=yes,height=700,width=980');
            return false;
          });
        });
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
