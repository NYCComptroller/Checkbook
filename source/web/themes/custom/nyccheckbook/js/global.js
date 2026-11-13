(function ($, Drupal, drupalSettings) {

  // Add isFunction (removed in jQuery 4).
  $.fn.isFunction = function(fn) {
    return (typeof fn === 'function');
  };
  $.isFunction = function(item) {
    return (typeof item === 'function');
  };

  // Add $.expr[":"] (removed in jQuery 4).
  $.expr = jQuery.expr ?? {};
  $.expr[":"] = jQuery.expr[":"] ?? {};

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

  // Update tabindex of main menu items.
  Drupal.behaviors.menuTabIndex = {
    attach: function (context, settings) {
      // Assign tabindex="-1" to submenu containers so they can be focused via script
      $('#superfish-main .menuparent', context).attr('tabindex', '0');
    }
  };

  Drupal.behaviors.BottomNavigationHover = {
    attach: function (context, settings) {
      $('div.bottom-navigation--item, div.top-navigation--item').hover(
        function() {
          // Mouse enters: Switch role to "img"
          $(this).attr('role', 'img');
        },
        function() {
          // Mouse leaves: Switch role back to "tab"
          $(this).removeAttr('role');
        }
      );
   }
  };

  Drupal.behaviors.MainRoleAriaAttribute = {
    attach: function (context, settings) {
      $('main[role="main"]').removeAttr('role').attr('aria-label', 'Main content');
      // Update title - Optional
      if ($('h1[id="page-title"]').html() == 'New York City') {
        $('h1[id="page-title"]').attr('aria-label', 'New York City — Checkbook 2.0');
      }
    }
  };

  //Global fix for toggling aria-sort=none for datable columns
  $(document).on('init.dt draw.dt', function(e, settings) {
    // Prevent interference from non-DataTables events
    if (e.namespace !== 'dt') return;
    var api = new $.fn.dataTable.Api(settings);
    // Iterate through every column in the active table
    api.columns().every(function() {
        var header = $(this.header());
        // Check the underlying DataTables configuration for sortability
        var isSortable = this.settings()[0].aoColumns[this.index()].bSortable;
        var currentSort = header.attr('aria-sort');
        // Apply "none" only if the column is sortable and not actively sorting
        if (isSortable && currentSort !== 'ascending' && currentSort !== 'descending') {
            header.attr('aria-sort', 'none');
        }
    });
  });

  // Global fix for setting aria-label and description for interactive chart interface
  // Read all the loaded charts and update aria labels.
  
  $('div.block-widget-data-tables').each(function () {
      let container = $(this);
      let chartTitle = container.find('.chart-title').html();

      // Watch the container for new elements being added
      let observer = new MutationObserver(function(mutations, obs) {
          var objSvg = container.find('svg.highcharts-root');
          if (objSvg.length > 0) {
            // Stop watching once found
              obs.disconnect();
              var cartWrapper = objSvg.closest('div.highcharts-wrapper');
              // Set chart title aria-label
              cartWrapper.attr('aria-label', chartTitle);
              objSvg.attr('aria-label', chartTitle);
              // Create a title for svg
              objSvg.append($('<title id="' + cartWrapper.attr('id') + '-svg-title" >').text(chartTitle));
              // Set chart title as chart description
              objSvg.find('desc').html(chartTitle);
              // Set chart title as the inner H3 text
              cartWrapper.find('h3').html(chartTitle + ' - Chart');
          }
      });
      observer.observe(container[0], { childList: true, subtree: true });
  });
  

 Drupal.behaviors.fixChosenActiveDescendant = {
    attach: function (context, settings) {
      $('#year_list', context).on('chosen:showing_dropdown', function () {
      //$('#year_list', context).once('fix-activedescendant').on('chosen:showing_dropdown', function () {
        var $select = $(this);
        var $container = $select.next('.chosen-container');
        var $combobox = $container.find('[role="combobox"]');
        var $results = $container.find('.chosen-results');

        // Ensure listbox has an ID matching aria-controls
        var listId = $combobox.attr('aria-controls');
        if (!$results.attr('id')) {
          $results.attr('id', listId);
        }

        // Give each option a unique ID if it doesn't have one
        $results.find('li').each(function (index) {
          if (!this.id) {
            this.id = 'year-option-' + index;
          }
        });

        // Helper to sync NVDA's active descendant state
        function updateActiveDescendant() {
          var $active = $results.find('li.active-result.result-selected, li.active-result').first();
          if ($active.length) {
            $combobox.attr('aria-activedescendant', $active[0].id);
          }
        }

        // Update on open
        updateActiveDescendant();

        // Update when user uses arrow keys or hovers over options
        $results.on('mouseenter mousemove keydown', 'li', function () {
          $combobox.attr('aria-activedescendant', this.id);
        });
      });
    }
  };

  // Fixes for NVDA screen reader to identify the column header as single block
  Drupal.behaviors.fixDataTableSortThAriaLabel = {
    attach: function (context, settings) {
      if (/Win/i.test(navigator.userAgent)) {
        $(document).ajaxStop(function() {          
          $('table.dataTable th > div:first-child').each(
            function() {
              let parentArialabel = $(this).parent().attr('aria-label');
              if (parentArialabel) {
                $(this).attr('role', 'img');
                $(this).attr('aria-label', parentArialabel);
                $(this).parent().removeAttr('aria-label');
                $(this).parent().removeAttr('tabindex');
                if ('0' == $(this).parent().attr('tabindex')) {
                  $(this).attr('tabindex', 0);
                }
              }
            }
          );
        });
      }
    }
  };

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
