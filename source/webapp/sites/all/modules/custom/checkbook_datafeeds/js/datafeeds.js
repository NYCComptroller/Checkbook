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

            if ($('#checkbook-datafeeds-tracking-form').length) {
              $('#checkbook-datafeeds-tracking-form').submit(function(event) {
                var code = $('#edit-tracking-number').val().trim();
                var codeRegexp = /[a-z1-9]{10}/gi;

                if (code && (10 == code.length) && codeRegexp.test(code)) {
                  // tracking code is valid
                  return;
                }

                // tracking code is invalid
                tracking_invalid();
                event.preventDefault();
              });

              function tracking_invalid(){
                if ($('.datafeeds-invalid-code').length) {
                  return;
                }
                $('#edit-tracking-number').css('border', '1px solid red');
                $('#edit-go').after('<p class="datafeeds-invalid-code" style="color:red"><em>Invalid tracking code</em></p>');
              }

              function tracking_clear(event) {
                if(13 == event.keyCode) {
                  // do not remove error mgs if Enter is pressed
                  return;
                }
                $('.datafeeds-invalid-code').remove();
                $('#edit-tracking-number').css('border', '');
              }

              $('#edit-tracking-number').change(tracking_clear);
              $('#edit-tracking-number').keyup(tracking_clear);
            }
        }
    }
}(jQuery));
