(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.NewFeatures = {
    attach: function (context, drupalSetting) {
      //Open New Features Menu in a new window
      $('a[href="/new-features/newwindow"]').addClass('gridpopup');
      if ($('.new-features-morelink').length > 0)
        $('.new-features-morelink').click(function () {
          if ($(this).hasClass("less")) {
            $(this).removeClass("less");
            $(this).html('Show More »');
          } else {
            $(this).addClass("less");
            $(this).html('Show Less «');
          }
          $('.more').toggle();
          return false;
        });

      // jQuery Dialog
      var $dialog = $('div.status-codes').dialog({
        autoOpen: false,
        title: 'Status Codes and Messages',
        width: 800,
        modal: true
      });
      $('a.status-codes').click(function (e) {
        $dialog.dialog('open');
        // prevent the default action, e.g., following a link
        e.preventDefault();
      });
    }
  };

  function update_date_label(input) {
    let input_width = $(input).outerWidth(),
        parent = $(input).closest('.form-item'),
        date_item_label = $('.date-item-label', parent),
        description_height = $('.description', parent).height() ?? 0;

    if (!$(parent).hasClass('date-item-relative')) {
      $(parent).addClass('date-item-relative');
    }

    if (!date_item_label.length) {
      date_item_label = $('<dev class="date-item-label" />');
      $(input).after(date_item_label);
    }

    $(date_item_label)
      .css('padding-bottom', description_height - 1)
      .css('width', input_width)
      .text($(input).val().length == 0 ? '' : $(input).val());
  }

  $(function () {
    // Date.
    $('body')
      .on('change', 'input[type="date"]', function() {
        let input = this;
        update_date_label(input);
      })
      .ready(function(){
        $('input[type="date"]').each(function() {
          let input = this;
          update_date_label(input);
        })
      })
      .on('click', '.date-item-relative .date-item-label, input[type="date"]', function() {
        if ($(this).hasClass('date-item-label')) {
          $(this).closest('.date-item-relative').find('input[type="date"]')[0].showPicker();
        }
        else {
          $(this)[0].showPicker();
        }
      })
  });
})(jQuery, Drupal, drupalSettings);

function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.search);
  if (results == null)
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}

// Empty JS function from D7 to fix JS error.
function trendsCenPad() {}
