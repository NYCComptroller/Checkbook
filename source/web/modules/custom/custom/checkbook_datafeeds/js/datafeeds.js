(function ($) {

  //Generates Solr URL for auto-completes
  $.fn.autoCompleteSourceUrl = function(solr_datasource, facet, filters) {
    let url = '/advanced_autocomplete/';
    let fq = '';

    $.fn.extractId = function(param) {
      if (param && (param.indexOf('id=>') > -1)){
        return param.split('~')[0].split('=>')[1];
      }
      return param;
    }

    Object.keys(filters).forEach(function (key) {
      let val = $.fn.extractId(String(filters[key]));
      if (val && ("0" !== val)){
        // remove trailing space from search terms
        fq += '*!*'+key+'='+val.trim();
      }
    });
    let search_term = '/?search_term=' + fq;
    return url + solr_datasource + '/' + facet + search_term;
  }

  //Formats Data-source filters section
  $.fn.formatDatafeedsDatasourceRadio = function(select_id_name) {
    if ($(".oge-datasource-fieldset").length <= 0) {
      let select_id = '#' + select_id_name;
      let oge_datasources = $(select_id + " .js-form-item:not(:first-child)");
      let oge_fieldset = $('<fieldset />').addClass('oge-datasource-fieldset');
      let oge_fieldset_legend = $('<legend />').text('Other Government Entities:');
      oge_fieldset.append(oge_fieldset_legend);
      oge_datasources.detach();
      oge_fieldset.append(oge_datasources);
      $('#div_data_source ' + select_id).append(oge_fieldset);
      $('#div_data_source').append($('<div />').addClass('clear2'));

      $("#checkbook-datafeeds-form").addClass('datafeeds-form-loaded');
    }
  };

  /**
   * Prevents selection of specified Item for autocomplete field
   * @param event
   * @param ui
   * @param selection_to_prevent
   */
  $.fn.preventSelectionDefault = function(event, ui, selection_to_prevent = "No Matches Found") {
    var label = ui.item.label;
    if (label === selection_to_prevent) {
      // prevent `selection_to_prevent` item from being selected
      event.preventDefault();
      $(event.target).val('');
    }
  };

  Drupal.behaviors.commonDataFeeds = {
    attach: function (context, settings) {
      // When the hyperlink is clicked...
      $(once('commonDataFeeds', '.link-download-datafeeds-zip')).click(function(event){
          var url = $(this).attr("href");
          // Ajax call to perform backend update on download link.
          var token = getParameterByName('code');
          $.ajax({
              url: 'data-feeds/download/'+token,
              dataType: 'json',
              success: function () {
                  window.location.assign(url);
              },
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

          // Tracking code is invalid.
          if (code != '') {
            tracking_invalid();
          }
          event.preventDefault();
        });

        function tracking_invalid(){
          if ($('.datafeeds-invalid-code').length) {
            return;
          }
          $('#edit-tracking-number').css('border', '1px solid red');
          $('#checkbook-datafeeds-tracking-form .form-actions').after('<p class="datafeeds-invalid-code" style="color:red;padding-top:5px;clear:both;"><em>Invalid tracking code</em></p>');
        }

        function tracking_clear(event) {
          if (13 == event.keyCode) {
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
