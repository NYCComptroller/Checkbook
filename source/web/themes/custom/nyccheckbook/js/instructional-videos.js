(function ($) {
  $(function() {
    // Instructional Videos.
    var instructionalVideos = '.instructional-video-toggle, .instructional-video-filter-highlight';
    $('body').on('click', instructionalVideos, function (event) {
      $(this).parent().parent().find('.instructional-video-content').slideUp(300);
      if (!$(this).parent().find('.instructional-video-toggle').hasClass('open')) {
        $(this).parent().parent().find('.instructional-video-content').slideToggle(300);
      }
      $(this).parent().find('.instructional-video-toggle').toggleClass('open');
    });
  })
})(jQuery);
