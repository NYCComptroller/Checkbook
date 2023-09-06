(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.topNavigation = {
    attach: function (context, settings) {
      // Enable Tooltip in top navigation
      $(".top-navigation--tooltip").hover(
        function () {
          $(this)
            .find(".top-navigation--tooltip-content")
            .removeClass("display-none")
            .addClass("display-block");
        },
        function () {
          $(this)
            .find(".top-navigation--tooltip-content")
            .removeClass("display-block")
            .addClass("display-none");
        }
      );

      // Enable dropdown menu for M/WBE and Sub Vendors
      $(".top-navigation--item .indicator-menu").hover(
        function () {
          $(this)
            .find(".top-navigation-item--menu")
            .removeClass("display-none")
            .removeClass("display-none")
            .addClass("display-block");
        },
        function () {
          $(this)
            .find(".top-navigation-item--menu")
            .removeClass("display-block")
            .addClass("display-none");
        }
      );
    },
  };
})(jQuery, Drupal, drupalSettings);
