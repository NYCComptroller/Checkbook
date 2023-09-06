(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.secondaryNavigation = {
    attach: function (context, drupalSetting) {
      $(once("agenciesListOpen", ".agency-list-open span, .agency-list-open div b")).each(function () {
        $(this).on('click', function () {
          if ($(this).attr("id") == "other-agency-list-open") {
            $(".all-agency-list-content").slideUp(300);
          } else {
            $(".other-agency-list-content").slideUp(300);
          }
          $(this)
            .parent()
            .parent()
            .find(".agency-list-content")
            .slideToggle(300);
          $(this).toggleClass("open");
          $(this).parent().find(" div b").toggleClass("open");
        });
      });

      $(once("agenciesListClose", ".agency-list-close a")).each(function () {
        $(this).on('click', function () {
          $(".agency-list-content").slideUp(300);
          $(".agency-list-open div b").removeClass("open");
        });
      });

      if ($("#agency-list-pager1").children().length == 0)
        if ($("#allAgenciesList").length > 0) {
          $("#allAgenciesList")
            .after(
              '<div id="agency-list-pager1" class="agency-list-pager"></div>'
            )
            .cycle({
              fx: "none",
              speed: 1000,
              timeout: 0,
              pause: true,
              pauseOnPagerHover: 0,
              pager: "#agency-list-pager1",
              prev: "#prev1",
              next: "#next1",
            });
        }

      if ($("#agency-list-pager2").children().length == 0)
        if ($("#otherAgenciesList").length > 0) {
          $("#otherAgenciesList")
            .after(
              '<div id="agency-list-pager2" class="agency-list-pager"></div>'
            )
            .cycle({
              fx: "none",
              speed: 1000,
              timeout: 0,
              pause: true,
              pauseOnPagerHover: 0,
              pager: "#agency-list-pager2",
              prev: "#prev2",
              next: "#next2",
            });
        }
    },
  };
})(jQuery, Drupal, drupalSettings);
