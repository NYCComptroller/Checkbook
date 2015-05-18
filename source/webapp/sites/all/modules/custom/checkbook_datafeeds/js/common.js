(function ($) {
    Drupal.behaviors.commonDataFeeds = {
        attach: function (context, settings) {
            // When the hyperlink is clicked...
            $('.link-download-datafeeds-zip').click(function(event){
                // Ajax call to perform backend update on download link.
                var token = getParameterByName('code');
                $.ajax({url: 'data-feeds/download/'+token});
            });
        }
    }
}(jQuery));