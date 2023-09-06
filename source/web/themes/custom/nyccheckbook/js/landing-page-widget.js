(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.expandBottomCont = {
    attach: function (context, drupalSetting) {
      if (!getParameterByName("expandBottomCont") && !getParameterByName("expandBottomContURL")) {
        $('.bottomContainerToggle').click();
        $('.bottomContainer').show();
      }
    }
  }
})(jQuery, Drupal, drupalSettings);

function addExpandBottomContURL() {
  (function ($) {
    $('.bottomContainerReload')
      .not('.altered')
      .each(function(){
        //console.log($(this).html())
        // Removing the update made previously as the link s fixed in code
       //let updatedUrl = getupdateUrl($(this).attr('href'));
        //console.log(updatedUrl);
        $(this).addClass('altered');
        $(this).attr('href', window.location.pathname + "?expandBottomContURL=" + $(this).attr('href'));
      });
  }(jQuery));
}
function getDatasource() {
  let href = window.location.href.replace(/(http|https):\/\//, '');
  let n = href.indexOf('?');
  href = href.substring(0, n !== -1 ? n : href.length);
  let data_source = 'checkbook';
  if (href.indexOf('datasource/checkbook_oge') !== -1) {
    data_source = 'checkbook_oge';
  } else if (href.indexOf('datasource/checkbook_nycha') !== -1) {
    data_source = 'checkbook_nycha';
  }
  return data_source;
}


function reloadExpandCollapseWidget(context, aoData) {
  $length = null;
  if (context.fnSettings().oInit.expandto150) {
    $length = 150;
  } else if (context.fnSettings().oInit.expandto5) {
    $length = 5;
  }

  if ($length) {
    for (var i = 0; i < aoData.length; i++) {
      if (aoData[i].name == "iDisplayLength") {
        aoData[i].value = $length;
        break;
      }
    }
  }
}

function addPaddingToDataCells(table) {
  (function ($) {
    $(table).find("th").each(function (i, val) {
        if ($(this).hasClass("number")) {
          var colwidth = $(this).find("span").width();
          var maxDataWidth = 0;
          $(table).find("tr td:nth-child(" + (i + 1) + ")").each(
            function () {
              if (maxDataWidth < $(this).find("div").width()) {
                maxDataWidth = $(this).find("div").width();
              }
            }
          );
          if ((colwidth - maxDataWidth) / 2 > 1) {
            $(table).find("tr td:nth-child(" + (i + 1) + ") div").css("margin-right", Math.floor((colwidth - maxDataWidth) / 2) + "px");
          }
        }
      }
    );
    $(".DTFC_LeftHeadWrapper table").find("th").each(function (i, val) {
        if ($(this).hasClass("number")) {
          var colwidth = $(this).find("div").width();
          var maxDataWidth = 0;
          $(".DTFC_LeftBodyWrapper table").find("tr td:nth-child(" + (i + 1) + ")").each(
            function () {
              if (maxDataWidth < $(this).find("div").width()) {
                maxDataWidth = $(this).find("div").width();
              }
            }
          );
          if ((colwidth - maxDataWidth) / 2 > 1) {
            $(".DTFC_LeftBodyWrapper table").find("tr td:nth-child(" + (i + 1) + ") div").css("margin-right", Math.floor((colwidth - maxDataWidth) / 2) + "px");
          }
        }
      }
    );
    $(".dataTables_scrollHeadInner table").find("th").each(function (i, val) {
        if ($(this).hasClass("number")) {
          var colwidth = $(this).find("div").width();
          var maxDataWidth = 0;
          $(".dataTables_scrollBody table").find("tr td:nth-child(" + (i + 1) + ")").each(
            function () {
              if (maxDataWidth < $(this).find("div").width()) {
                maxDataWidth = $(this).find("div").width();
              }
            }
          );
          if ((colwidth - maxDataWidth) / 2 > 1) {
            $(".dataTables_scrollBody table").find("tr td:nth-child(" + (i + 1) + ") div").css("margin-right", Math.floor((colwidth - maxDataWidth) / 2) + "px");
          }
        }
      }
    );
  }(jQuery));
}

(function ($) {
  Drupal.behaviors.bottomContainerShowHide = {
    attach: function (context, settings) {
      $('.bottomContainerToggle', context).toggle(
        function (event) {
          event.preventDefault();
          if ($('.bottomContainer').html().length <= 10) {
            var callBackURL = '';
            var expandBottomContURL = getParameterByName("expandBottomContURL");
            if (expandBottomContURL) {
              callBackURL = expandBottomContURL + "?appendScripts=true";
            } else {
              callBackURL = this.href + window.location.pathname + "?appendScripts=true";
            }

            $('.bottomContainer').toggle();
            $('.bottomContainer').html("<img style='float:right' src='/sites/all/themes/checkbook/images/loading_large.gif' title='Loading Data...'/>");
            $('.bottomContainerToggle').toggle();
            $.ajax({
              url: callBackURL,
              success: function (data) {
                $('.bottomContainer').html(data);
                // $('.bottomContainerToggle').html("Hide Details &#171;");
                $('.bottomContainerToggle').html("");
                $('.bottomContainerToggle').toggle();
                $('.first-item').trigger('click');
              }
            });
          } else {
            $('.bottomContainer').toggle();
            // $('.bottomContainerToggle').html("Hide Details &#171;");
            $('.bottomContainerToggle').html("");
          }
        },
        function (event) {
          event.preventDefault();
          $('.bottomContainer').toggle();
          //  $('.bottomContainerToggle').html("Show Details &#187;");
          $('.bottomContainerToggle').html("");
        }
      );
      if (getParameterByName("expandBottomCont") || getParameterByName("expandBottomContURL")) {
        $('.bottomContainerToggle', context).click();
      }

    }
  };

  Drupal.behaviors.bottomContainerReload = {
    attach: function (context, settings) {
      $(document).on('ajaxStop', function() {
        if (typeof addExpandBottomContURL == 'function') {
          addExpandBottomContURL();
        }
      }).ready(function (){
        if (typeof addExpandBottomContURL == 'function') {
          addExpandBottomContURL();
        }
      })
    }
  };

  // Document is ready.
  $(function() {
    $('.contract-information li div br,' +
      '.spending-tx-subtitle div br,' +
      '#payroll-tx-static-content div br').after('<span class="spacer"></span>');
  });


}(jQuery));
