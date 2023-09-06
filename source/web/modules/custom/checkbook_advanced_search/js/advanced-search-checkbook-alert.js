(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.alerts_customize_submit_processor = {
    attach: function (context, settings) {

      $.fn.onScheduleAlertNextClick = function (step) {
        var next_step = '';
        var header = '';
        var instructions = '';

        disable_input([
          '#edit-next-submit',
          '#edit-back-submit'
        ]);


        switch (step) {
          case 'customize_results':
            next_step = 'schedule_alert';

            /* Update width of dialog dimension */
            $('.ui-dialog').removeClass('with-iframe');
            $('#drupal-modal').dialog('option', 'position', { my: "center top", at: "center top", of: window });

            /* Update header */
            header = "<span class='create-alert-header'><span class='inactive'>1. Select Criteria</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>2. Customize Results</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='active'>3. Schedule Alert</span></span>";
            $('.create-alert-header').replaceWith(header);

            /* Update wizard instructions */
            instructions = "<span class='create-alert-instructions'><ul><li>Checkbook alerts will notify you by email when new results matching your current search criteria are available. Use options below for alert settings.<\/li><li>Provide email address, in order to receive alerts. Emails will be sent based on the frequency selected and only after the minimum number of additional results entered has been reached since the last alert.<\/li><li>Click 'Back' to go back to Step2: Customize Results.<\/li><li>Click 'Schedule Alert' to schedule the alert.<\/li><li>The user shall receive email confirmation once the alert is scheduled.<\/li><\/ul></span>";
            $('.create-alert-instructions').replaceWith(instructions);

            /* Hide close button */
            //$('.ui-dialog-titlebar-close').hide();

            /* Buttons */
            $('#edit-next-submit').val('Schedule Alert');

            /* Hide the results page */
            $('.create-alert-customize-results').hide();

            /* Show loading icon */
            $('.create-alert-results-loading').show();

            /* Show the schedule alert page */
            $('.create-alert-schedule-form').show();

            /* Load Schedule Alert Form */
            $.fn.onScheduleAlertClick();

            /* Update hidden field for new step */
            $('input:hidden[name="step"]').val(next_step);

            /* Remove focus scedule alerts button */
            $('#edit-next-submit').blur();

            break;

          case 'schedule_alert':
            next_step = 'confirmation';

            /* Update hidden field for new step */
            $('input:hidden[name="step"]').val(next_step);

            /* Schedule Alert */
            var ajax_referral_url = $('input:hidden[name="ajax_referral_url"]').val();
            var base_url = window.location.protocol + '//' + window.location.host;
            $.fn.onScheduleAlertConfirmClick(ajax_referral_url, base_url);

            break;
        }
      };

      $.fn.onScheduleAlertBackClick = function (step) {
        var previous_step = '';
        var header = '';
        var instructions = '';

        switch (step) {
          case 'customize_results':
            previous_step = 'select_criteria';

            //enable form
            $.fn.formUnFreezeAdvancedSearch();

            /* Update width of dialog dimension */
            $('.ui-dialog').removeClass('with-iframe');
            $('#drupal-modal').dialog('option', 'position', { my: "center top", at: "center top", of: window });

            /* Update header */
            header = "<span class='create-alert-header'><span class='active'>1. Select Criteria</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>2. Customize Results</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>3. Schedule Alert</span></span>";
            $('.create-alert-header').replaceWith(header);

            /* Update wizard instructions */
            instructions = "<span class='create-alert-instructions'>Follow the three step process to schedule alert.<ul><li>Please select one of the following domains and also select the desired filters.<\/li><li>Click 'Next' button to view and customize the results.<\/li><li>Click 'Clear All' to clear out the filters applied.<\/li><\/ul><\/br></span>";
            $('.create-alert-instructions').replaceWith(instructions);

            /* Hide the results page */
            $('.create-alert-customize-results').hide();

            /* Buttons */
            $('#edit-next-submit').hide();
            $('#edit-back-submit').hide();

            /* Show the accordion and disable the input fields based on the selection criteria */
            $('#accordionAdvancedSearch').show();

            break;

          case 'schedule_alert':
            previous_step = 'customize_results';

            /* Update width of dialog dimension */
            $('.ui-dialog').addClass('with-iframe');
            $('#drupal-modal').dialog('option', 'position', { my: "center top", at: "center top", of: window });

            /* Update header */
            header = "<span class='create-alert-header'><span class='inactive'>1. Select Criteria</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='active'>2. Customize Results</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>3. Schedule Alert</span></span>";
            $('.create-alert-header').replaceWith(header);

            /* Update wizard instructions */
            instructions = "<span class='create-alert-instructions'>Further narrow down the results using the 'Narrow down your search' functionality.<ul><li>Click 'Export' button to download the results into excel.<\/li><li>Click 'Back' to go back to Step1: Select Criteria.<\/li><li>Click 'Next' button to Schedule Alert.<\/li><\/ul><\/br></span>";
            $('.create-alert-instructions').replaceWith(instructions);

            /* Hide the schedule alert page */
            $('.create-alert-schedule-form').replaceWith("<div class='create-alert-schedule-form'></div>");
            $('.create-alert-schedule-form').hide();

            /* Show the results page */
            $('.create-alert-customize-results').show();

            /* Update button text */
            $('div.create-alert-submit #edit-next-submit').val('Next');

            /* Remove focus from back */
            $('#edit-back-submit').blur();

            /* Show results buttons */
            $('.create-alert-submit').show();

            /* Buttons */
            $('#edit-next-submit').css('display', 'inline');
            $('#edit-back-submit').css('display', 'inline');

            /* Enable Next button on back to results page  */
            enable_input('#edit-next-submit');

            break;
        }

        /* Update hidden field for new step */
        $('input:hidden[name="step"]').val(previous_step);

        enable_input([
          '#edit-next-submit',
          '#edit-back-submit'
        ]);
      };

      $.fn.onScheduleAlertClick = function () {
        var scheduleAlertDiv = $(".create-alert-schedule-form");
        var scheduleAlertUrl = '/alert/transactions/advanced/search/form';

        /* Load */
        $.ajax({
          url: scheduleAlertUrl,
          success: function(data) {
            $(scheduleAlertDiv).replaceWith("<div class='create-alert-schedule-form'>" + data + "</div>");
          },
          complete: function() {
            /* Hide loading icon */
            $('.create-alert-results-loading').hide();

            enable_input([
              '#edit-next-submit',
              '#edit-back-submit'
            ]);
          }
        });
      };

      $.fn.onScheduleAlertConfirmClick = function (ajaxReferralUrl, serverName) {

        /* Add hidden field for ajax user Url */
        var ajaxUserUrl = $('#checkbook_advanced_search_result_iframe').attr('src');
        $('input:hidden[name="ajax_user_url"]').val(ajaxUserUrl);
        ajaxUserUrl = serverName + ajaxUserUrl;

        var validateEmail = function (email) {
          var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return re.test(email);
        };

        var isNumber = function (value) {
          if ((undefined === value) || (null === value)) {
            return false;
          }
          if (typeof value === 'number') {
            return true;
          }
          return !isNaN(value - 0);
        };

        var alertDiv = $('.create-alert-schedule-form');
        var alertLabel = $(alertDiv).find('input[name=alert_label]').val();
        var alertEmail = $(alertDiv).find('input[name=alert_email]').val();
        var alertMinimumResults = $(alertDiv).find('input[name=alert_minimum_results]').val();
        var alertMinimumDays = $(alertDiv).find('select[name=alert_minimum_days]').val();
        var alertEnd = $(alertDiv).find("input[name='alert_end']").val();
        var dateRegEx = '[0-9]{4,4}-[0-1][0-9]-[0-3][0-9]';

        var alertMsgs = [];
        if (alertLabel.length < 1) {
          alertMsgs.push("No Description has been set.");
        }
        if (alertEmail.length < 1) {
          alertMsgs.push("No email is entered.");
        } else if (!validateEmail(alertEmail)) {
          alertMsgs.push("Email is not valid.");
        }
        if (!isNumber(alertMinimumResults) || alertMinimumResults < 1) {
          alertMsgs.push("Minimum results is not a valid number.");
        }
        if (!isNumber(alertMinimumDays) || alertMinimumDays < 1) {
          alertMsgs.push("Alert frequency is not valid.");
        }

        if ((alertEnd.length > 1 && alertEnd.length !== 10) || (alertEnd.length > 1 && !alertEnd.match(dateRegEx))) {
          alertMsgs.push("Expiration Date is not valid.");
        }

        if (alertMsgs.length > 0) {
          /* Update hidden field for new step */
          $('input:hidden[name="step"]').val('schedule_alert');
          enable_input([
            '#edit-next-submit',
            '#edit-back-submit'
          ]);

          $(alertDiv).find('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul>' + '<li>' + alertMsgs.join('</li><li>') + '</li></ul></div>');
        } else {
          $('.ui-dialog-titlebar-close').hide();
          disable_input('#edit-next-submit');

          /* Show loading icon */
          $('.create-alert-results-loading').show();

          $(".create-alert-view").addClass('transparent');
          $(".create-alert-view").addClass('disable_me');
          $(alertDiv).find('#errorMessages').html('');

          var url = '/alert/transactions';
          var data = {
            refURL: ajaxReferralUrl,
            alert_label: alertLabel,
            alert_email: alertEmail,
            alert_minimum_results: alertMinimumResults,
            alert_minimum_days: alertMinimumDays,
            alert_end: alertEnd,
            userURL: ajaxUserUrl
          };
          $this = $(this);

          $.get(url, data, function (data) {
            if (data.data.success) {
              $('.ui-dialog-titlebar-close').show();
              $('.ui-dialog-titlebar-close').trigger('click');

              var dialog = $("#dialog_schedule_confirm");
              if (!$("#dialog_schedule_confirm").length) {
                dialog = $('<div id="dialog_schedule_confirm" style="display:none"></div>');
              }

              dialog.html(data.data.html);
              dialog.dialog({
                position: { my: "center", at: "center", of: window },
                modal: true,
                width: 550,
                height: 80,
                autoResize: true,
                resizable: false,
                dialogClass: 'noTitleDialog dialog-schedule-confirm',
                close: function () {
                  var dialog = $("#dialog_schedule_confirm");
                  $(dialog).replaceWith('<div id="dialog_schedule_confirm" style="display:none"></div>');
                }
              });
            } else {
              /* Update hidden field for new step */
              $('input:hidden[name="step"]').val('schedule_alert');
              $(alertDiv).find('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul><li>' + data.data.errors.join('<li/>') + '</ul></div>');
            }
          });
        }
      };

      $('[id^="edit-next-submit"]', context).once('createAlertNextSubmit').click(function (event) {
        $.fn.onScheduleAlertNextClick($('input:hidden[name="step"]').val());
        event.preventDefault();
      });

      $('[id^="edit-back-submit"]', context).once('createAlertBackSubmit').click(function (event) {
        $.fn.onScheduleAlertBackClick($('input:hidden[name="step"]').val());
        event.preventDefault();
      });

    }
  };

}(jQuery, Drupal, drupalSettings));
