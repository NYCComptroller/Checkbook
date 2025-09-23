(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.advancedSearchAndAlertsNew = {
    attach: function (context, settings) {
      // Create Alert link once
      $('span.advanced-search-create-alert').click(function () {
        if (advancedSearchFormLoading) {
          return;
        }
        show_advanced_search_form(create_alert_bootstrap);
      });

      /**
       * CREATE ALERT FUNCTIONS
       */

      function create_alert_bootstrap() {
        let href = window.location.href.replace(/(http|https):\/\//, '');
        let n = href.indexOf('?');
        href = href.substring(0, n !== -1 ? n : href.length);
        let page_clicked_from = this.id ? this.id : href.split('/')[1];
        let data_source = "checkbook";
        if (href.indexOf('datasource/checkbook_oge') !== -1) {
          data_source = 'checkbook_oge';
        } else if (href.indexOf('datasource/checkbook_nycha') !== -1) {
          data_source = 'checkbook_nycha';
        }
        let active_accordion_window = initializeActiveAccordionWindow(page_clicked_from, data_source);

        let createAlertsDiv = "<span class='create-alert-instructions'>Follow the three step process to schedule alert.<ul><li>Please select one of the following domains and also select the desired filters.<\/li><li>Click 'Next' button to view and customize the results.<\/li><li>Click 'Clear All' to clear out the filters applied.<\/li><\/ul><\/br></span>";
        createAlertsDiv += "<span style='visibility: hidden;display: none;' class='create-alert-results-loading'><div id='loading-icon'><img src='/themes/custom/nyccheckbook/images/loading_large.gif'></div></span>";
        createAlertsDiv += "<div class='create-alert-customize-results' style='display: none'><br/><br/><br/></div>";
        createAlertsDiv += "<div class='create-alert-schedule-alert' style='display: none'>&nbsp;<br/><br/></div>";
        createAlertsDiv = "<div class='create-alert-view'>" + createAlertsDiv + "</div>";
        $('.create-alert-view').replaceWith(createAlertsDiv);

        //Initialize Attributes and styling
        initializeAccordionAttributes('advanced_search_create_alerts');

        $('#block-checkbookadvancedsearchformblock').dialog({
          title: "",
          position: ['center', 'center'],
          width: 800,
          modal: true,
          autoResize: true,
          resizable: false,
          dragStart: function () {
            $(".ui-autocomplete-input").autocomplete("close")
          },
          open: function () {

          },
          close: function () {
            $(".ui-autocomplete-input").autocomplete("close");
            $('.create-alert-next-btn').css('display', 'none');

            var createAlertsDiv = "<div class='create-alert-view'></div>";
            $('.create-alert-view').replaceWith(createAlertsDiv);
          }
        });
        /* Correct min-height for IE9, causes hover event to add spaces */
        $('#block-checkbookadvancedsearchformblock').css('min-height', '0%');
        let title = "<span class='create-alert-header'><span class='active'>1. Select Criteria</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>2. Customize Results</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>3. Schedule Alert</span></span>";

        $('#block-checkbookadvancedsearchformblock').dialog({autoOpen: false}).dialog('widget').find('.ui-dialog-title').html(title);

        $('.advanced-search-accordion').accordion({
          autoHeight: false,
          active: active_accordion_window
        });

        /* For EDC, Budget, Revenue & Payroll are not applicable and are disabled */
        disableAccordionSections(data_source);
        bootstrap_complete();

        return false;
      }

      function create_alert_loading(e) {
        $("#advanced-search-rotator").css('display', 'block');
        $("#advanced-search-rotator").addClass('loading_bigger_gif');
      }

      function create_alert_form_disable(e) {
        $(".ui-dialog-titlebar").addClass('transparent');
        $(".ui-dialog-titlebar").addClass('disable_me');
        $("#spending-advanced-search").addClass('transparent');
        $("#revenue-advanced-search").addClass('transparent');
        $("#budget-advanced-search").addClass('transparent');
        $("#contracts-advanced-search").addClass('transparent');
        $("#payroll-advanced-search").addClass('transparent');
        $(".advanced-search-accordion").addClass('transparent');
        $("#block-checkbookadvancedsearchformblock").addClass('disable_me');
        $('.create-alert-instructions').addClass('transparent');
      }

      function create_alert_form_enable(e) {
        $(".ui-dialog-titlebar").removeClass('transparent');
        $(".ui-dialog-titlebar").removeClass('disable_me');
        $("#spending-advanced-search").removeClass('transparent');
        $("#revenue-advanced-search").removeClass('transparent');
        $("#budget-advanced-search").removeClass('transparent');
        $("#contracts-advanced-search").removeClass('transparent');
        $("#payroll-advanced-search").removeClass('transparent');
        $(".advanced-search-accordion").removeClass('transparent');
        $("#block-checkbookadvancedsearchformblock").removeClass('disable_me');
        $('.create-alert-instructions').removeClass('transparent');
      }

      $(document).ajaxComplete(function () {
        /* Do not enable next buttons for results page here */
        var step = $('input:hidden[name="step"]').val();
        if (step === 'select_criteria') {
          disable_input([
            '#edit-next-submit',
            '#edit-back-submit'
          ]);
        } else if (step === 'schedule_alert') {
          enable_input([
            '#edit-next-submit',
            '#edit-back-submit'
          ]);
          $('a.ui-dialog-titlebar-close').show();
          $('#advanced-search-rotator').css('display', 'none');

          /* hide loading icon */
          $('.create-alert-results-loading').css('visibility', 'hidden');
          $('.create-alert-results-loading').css('display', 'none');
        } else {
          disable_input('#edit-back-submit');
        }

        $('.tableHeader').each(function (i) {
          if ($(this).find('.contCount').length > 0) {
            $(this).find('h2').append("<span class='contentCount'>" + $('span.contCount').html() + '</span>');
            $(this).find('.contCount').remove();
          }
        });

      });

      // Since we load this form via AJAX, Drupal does not bind callbacks from php #ajax form settings here,
      // so let's do that manually
      function bind_create_alert_buttons() {
        $('.create-alert-next-btn').each(function () {
          $(this).click(function (event) {
            $('a.ui-dialog-titlebar-close').hide();
            $(".ui-autocomplete-input").autocomplete("close");
            create_alert_loading();
            create_alert_form_disable();
            event.preventDefault();
          });
          $(this).addClass('ajax-processed').each(function () {
            var element_settings = {};

            // Ajax submits specified in this manner automatically submit to the
            // normal form action.
            element_settings.url = '/system/ajax';
            // Form submit button clicks need to tell the form what was clicked so
            // it gets passed in the POST request.
            element_settings.setClick = true;
            // Form buttons use the 'click' event rather than mousedown.
            element_settings.event = 'click';
            // Clicked form buttons look better with the throbber than the progress bar.
            var base = $(this).attr('id');
            Drupal.ajax[base] = new Drupal.ajax(base, this, element_settings);
          });

        });
      }
    }
  };


}(jQuery, Drupal, drupalSettings));
