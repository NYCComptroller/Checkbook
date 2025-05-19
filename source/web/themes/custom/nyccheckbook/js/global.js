(function ($, Drupal, drupalSettings) {

  // Add isFunction.
  $.fn.isFunction = function(fn) {
    return (typeof fn === 'function');
  };
  $.isFunction = function(item) {
    return (typeof item === 'function');
  };

  // Show loader.
  Drupal.checkbook_advanced_search = Drupal.checkbook_advanced_search ?? {};
  Drupal.checkbook_advanced_search.show_loading_spinner = function() {
    if (!$('.as-loading').length) {
      $('a.advanced-search').before(
        "<span class='as-loading'>" +
        "<img style='float:right' src='/themes/custom/nyccheckbook/images/loading_large.gif' title='Loading Data...' />" +
        "</span>"
      );
    }
  }

  // Hide loader.
  Drupal.checkbook_advanced_search.hide_loading_spinner = function() {
    $('.as-loading').remove();
  }

  Drupal.behaviors.advancedSearchLoader = {
    attach: function(context, settings) {
      $(once("alert_loading", 'body')).each(function () {
        $(this).on('mousedown', 'a.advanced-search, a.advanced-search-alerts', function(e) {
          if (e.which == 1) {
            Drupal.checkbook_advanced_search.show_loading_spinner();
          }
        });
      });

      $(window)
        .on('dialog:beforecreate', function(dialog, $element, settings) {
          Drupal.checkbook_advanced_search.hide_loading_spinner();
        });
    }
  };

  Drupal.behaviors.disableClicks = {
    attach: function (context, settings) {
      if ($('body').hasClass('gridview') || ($('body').hasClass('newwindow') && !($('body').hasClass('page-new-features')))) {
        $('body').delegate('a', 'click', function () {
          return !!($(this).hasClass('subContractViewAll') || $(this).hasClass('showHide') || $(this).attr('rel') == 'home' || $(this).hasClass('enable-link'));
        });
      }
    }
  };

  /*
   * Function to tell if the current window is inside an iFrame
   * Returns true if the window is in an iFrame, else false
   */
  function inIframe() {
    try {
      return window.self !== window.top;
    } catch (e) {
      return true;
    }
  }

  if (inIframe() && document.URL.indexOf("/createalert") >= 0) {
    function updateIframe() {
      if (window.oTable && window.oTable.dataTable() && window.oTable.dataTable().fnSettings()) {
        let refUrl = window.oTable.dataTable().fnSettings().sAjaxSource;
        $('input:hidden[name="ajax_referral_url"]', window.parent.document).val(refUrl);
      }

      // Parent iframe cannot be bigger than content.
      if ($('#no-records').length) {
        $(window.frameElement).css('max-height', $(window).height());
      }
    }

    // Loaded createalert iframe.
    $(function() {
      $('body.createalert').on('click', '.dataTable tbody a', function() {
        return false;
      });

      updateIframe();

      if (!$('.create-alert-customize-results', window.parent.document).is(":visible")) {
        return;
      }

      $('#checkbook_advanced_search_result_iframe', window.parent.document).css('height', 600);
      $('#checkbook_advanced_search_result_iframe', window.parent.document).attr('scrolling', 'yes');
      $('#checkbook_advanced_search_result_iframe', window.parent.document).attr('scroll', 'yes');
      $('#checkbook_advanced_search_result_iframe', window.parent.document).css('overflow-x', 'hidden');
      $('#checkbook_advanced_search_result_iframe', window.parent.document).css('overflow-y', 'scroll');
      $('#checkbook_advanced_search_result_iframe', window.parent.document).css('padding-left', '0px');
      $('.ui-dialog', window.parent.document).css('height', 835);

      $('.create-alert-results-loading', window.parent.document).hide();
      $('#checkbook_advanced_search_result_iframe', window.parent.document).show();
      $('.ui-dialog-titlebar-close', window.parent.document).show();

      /* On parent back button click, need to re-stick the header */
      // $('[id^="edit-back-submit"', window.parent.document).click(function (event) {
      //   var step = $('input:hidden[name="step"]').val();
      //   if (step === 'schedule_alert' || 'customize_results') {
      //     setTimeout(function () {
      //       fnCustomInitCompleteReload();
      //     }, 250);
      //   }
      // });

      /* Enable button for results page after ajax loads */
      $('[id^="edit-back-submit"]', window.parent.document).removeAttr('disabled');
      if (!$('#no-records').length) {
        $('[id^="edit-next-submit"]', window.parent.document).removeAttr('disabled');
      }
    });

    // Filter selection event.
    $(document).ajaxComplete(function() {
      updateIframe();
    });
  }

  $(function() {
    var intervalTime;
    intervalTime = setInterval(function() {
      var selector = '.DTFC_ScrollWrapper .sticky-wrapper',
          maxHeight = 0;
      $(selector).each(function () {
        maxHeight = Math.max(maxHeight, $(this).height())
      });
      if (maxHeight) {
        $(selector).css('height', maxHeight);
        clearInterval(intervalTime);
      }
    }, 100);
  })
})(jQuery, Drupal, drupalSettings);


if (typeof jQuery != 'undefined' && !jQuery.browser) {
  jQuery.uaMatch = function (ua) {
    ua = ua.toLowerCase()

    var match =
        /(chrome)[ \/]([\w.]+)/.exec(ua) ||
        /(webkit)[ \/]([\w.]+)/.exec(ua) ||
        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
        /(msie) ([\w.]+)/.exec(ua) ||
        (ua.indexOf('compatible') < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua)) ||
        []

    return {
      browser: match[1] || '',
      version: match[2] || '0'
    }
  }

  matched = jQuery.uaMatch(navigator.userAgent)
  browser = {}

  if (matched.browser) {
    browser[matched.browser] = true
    browser.version = matched.version
  }

  // Chrome is Webkit, but Webkit is also Safari.
  if (browser.chrome) {
    browser.webkit = true
  } else if (browser.webkit) {
    browser.safari = true
  }

  jQuery.browser = browser
}
