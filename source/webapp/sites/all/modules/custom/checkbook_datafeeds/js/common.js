(function ($) {
    Drupal.behaviors.commonDataFeeds = {
        attach: function (context, settings) {
            // When the hyperlink is clicked...
            $('.link-download-datafeeds-zip').click(function(event){
                var url = $(this).attr("href");
                // Ajax call to perform backend update on download link.
                var token = getParameterByName('code');
                $.ajax({
                    url: 'data-feeds/download/'+token,
                    dataType: 'json',
                    success: function () {
                        window.location.assign(url);
                    }
                });
                return false;
            });
        }
    }
}(jQuery));