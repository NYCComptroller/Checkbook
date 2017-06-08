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
                        var downloadLink = document.createElement("a");
                        downloadLink.href = url;
                        downloadLink.download = "nyc-data-feed";

                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                    }
                });
                return false;
            });
        }
    }
}(jQuery));