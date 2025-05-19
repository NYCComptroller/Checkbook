(function ($) {
  Drupal.behaviors.exportTransactions = {
    attach:function(context,settings){
      $('span.export').off().on("click", function () {
        var dialog = $("#dialog");
        if ($("dialog").length == 0) {
          dialog = $('<div id="dialog" style="display:none"></div>');
        }

        var oSettings = $('#table_' + $(this).attr('exportid')).dataTable().fnSettings();
        var iRecordsTotal = oSettings.fnRecordsTotal();
        var iRecordsDisplay = oSettings.fnRecordsDisplay();
        var iDisplayLength = oSettings._iDisplayLength;
        var iDisplayStart = oSettings._iDisplayStart;
        var maxPages = Math.ceil(iRecordsDisplay / iDisplayLength);

        var dialogUrl = '/export/transactions/form?maxPages=' + maxPages + '&iRecordsTotal=' + iRecordsTotal + '&iRecordsDisplay=' + iRecordsDisplay + ' .region--content';
        //alert(dialogUrl);
        dialog.load(
          dialogUrl,
          {},
          function (responseText, textStatus, XMLHttpRequest) {
              dialog.dialog({
                  position: { my: "center", at: "center", of: window },
                  modal: true,
                  title: 'Download Transactions Data',
                  dialogClass: "export",
                  width: 700,
                  autoResize: true,
                  resizable: false,
                  buttons: {
                      "Download Data": function () {
                          //current page
                          var startRecord = iDisplayStart;
                          var recordLimit = iDisplayLength;

                          var alertMsgs = [];
                          var dcfilter = $('input[name=dc]:checked').val();
                          if (dcfilter == null) {
                              alertMsgs.push("One of 'Data Selection' option must be selected.");
                          }


                          if (dcfilter == 'all') {
                              startRecord = 0;
                              recordLimit = iRecordsDisplay;
                          }

                          if (dcfilter == 'range') {
                              var rangefrom = $('input[name=rangefrom]').val();
                              var rangeto = $('input[name=rangeto]').val();

                              var validFrom = ((String(rangefrom).search(/^\s*(\+|-)?\d+\s*$/) != -1) && (parseFloat(rangefrom) == parseInt(rangefrom)) && parseInt(rangefrom) >= 1 && parseInt(rangefrom) <= maxPages);
                              var validTo = ((String(rangeto).search(/^\s*(\+|-)?\d+\s*$/) != -1) && (parseFloat(rangeto) == parseInt(rangeto)) && parseInt(rangeto) >= 1 && parseInt(rangeto) <= maxPages);

                              if (!validFrom && !validTo) {
                                  alertMsgs.push('If "Pages" option is selected, page numbers must be integer values between 1 and ' + maxPages);
                              } else if (rangefrom.length > 0 && !validFrom) {
                                  alertMsgs.push('From page number must be integer value between 1 and ' + maxPages);
                              } else if (rangeto.length > 0 && !validTo) {
                                  alertMsgs.push('To page number must be integer value between 1 and ' + maxPages);
                              } else {
                                  rangefrom = !validFrom ? 1 : parseInt(rangefrom);
                                  rangeto = !validTo ? maxPages : parseInt(rangeto);
                                  if (rangefrom > rangeto) {
                                      alertMsgs.push('From page number(' + rangefrom + ') must be less than or equal to ' + rangeto);
                                  } else {
                                      startRecord = (rangefrom - 1) * iDisplayLength;
                                      recordLimit = (rangeto - rangefrom + 1) * iDisplayLength;
                                      if ((startRecord + recordLimit) > iRecordsDisplay) {
                                          recordLimit = recordLimit - (startRecord + recordLimit - iRecordsDisplay);
                                      }
                                  }
                              }
                          } else {
                              $('input[name=rangefrom]').val(null);
                              $('input[name=rangeto]').val(null);
                          }

                          if (alertMsgs.length > 0) {
                              $('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul>' + '<li>' + alertMsgs.join('<li/>') + '</ul></div>');
                          } else {
                              $('#errorMessages').html('');

                              var url = '/export/transactions';
                              var inputs = "<input type='hidden' name='refURL' value='" + oSettings.sAjaxSource + "'/>"
                                  + "<input type='hidden' name='iDisplayStart' value='" + startRecord + "'/>"
                                  + "<input type='hidden' name='iDisplayLength' value='" + recordLimit + "'/>"
                              ;

                              if (oSettings.oFeatures.bSort !== false) {
                                  var iCounter = 0;

                                  aaSort = (oSettings.aaSortingFixed !== null) ?
                                      oSettings.aaSortingFixed.concat(oSettings.aaSorting) :
                                      oSettings.aaSorting.slice();

                                  for (i = 0; i < aaSort.length; i++) {
                                      aDataSort = oSettings.aoColumns[aaSort[i][0]].aDataSort;

                                      for (j = 0; j < aDataSort.length; j++) {
                                          inputs = inputs + "<input type='hidden' name='iSortCol_" + iCounter + "' value='" + aDataSort[j] + "'/>";
                                          inputs = inputs + "<input type='hidden' name='sSortDir_" + iCounter + "' value='" + aaSort[i][1] + "'/>";
                                          iCounter++;
                                      }
                                  }
                                  inputs = inputs + "<input type='hidden' name='iSortingCols' value='" + iCounter + "'/>";
                              }

                              $('<form action="' + url + '" method="get">' + inputs + '</form>').appendTo('body').submit().remove();

                              $('#dialog #export-message').addClass('disable_me');
                              $('.ui-dialog-titlebar').addClass('disable_me');
                              $('.ui-dialog-buttonset').addClass('disable_me');
                              $('#dialog #dialog').addClass('disable_me');
                              $('#loading_gif').show();
                              $('#loading_gif').addClass('loading_bigger_gif');

                              setTimeout(function () {
                                  $('#dialog #export-message').removeClass('disable_me');
                                  $('.ui-widget-header').removeClass('disable_me');
                                  $('.ui-dialog-buttonset').removeClass('disable_me');
                                  $('#dialog #dialog').removeClass('disable_me');
                                  $('#loading_gif').hide();
                                  $('#loading_gif').removeClass('loading_bigger_gif');
                              }, 3000);
                          }
                      },
                      "Cancel": function () {
                          $(this).dialog('close');
                      }
                  },
                  open: function(){
                    jQuery(this.closest('.ui-dialog')).draggable();
                    $('.export-range-input').click(function(){
                      $('#export-dc-range').attr("checked", "checked").trigger("click");
                    });
                  },
                  close: function(){
                    $(this).dialog('destroy').remove();
                  }
              });
          }
      );
        return false;

      });
    }
  };

  Drupal.behaviors.alertTransactions = {
    attach: function (context, settings) {
      // The span.alert is the object in Drupal to which you link the click button, I don�t know how it is actually named for the alert
      $('span.alerts').off().on("click", function () {
        var dialog = $("#dialog");
        if (!$("#dialog").length) {
          dialog = $('<div id="dialog" style="display:none"></div>');
        }

        // This is where you add the alerted table to which you link the output data from '/alert/transactions/form�
        var oSettings = $('#table_' + $(this).attr('alertsid')).dataTable().fnSettings();

        // This is the part where we get the data from to show in the dialogue we open, I don�t know if you process the following parameters  maxPages , record and so on but it won�t hurt if it stayed here
        //var dialogUrl = '/alert/transactions/form';
        var dialogUrl = '/alert/transactions/advanced/search/form';

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

        // load remote content
        dialog.load(
          dialogUrl,
          {},
          function (responseText, textStatus, XMLHttpRequest) {
            dialog.dialog({
              position: { my: "center", at: "center", of: window },
              modal: true,
              title: 'Alert',
              dialogClass: "alert",
              width: 700,
              buttons: {
                "Create Alert": function () {
                  var alertLabel = $('input[name=alert_label]').val();
                  var alertEmail = $('input[name=alert_email]').val();
                  var alertMinimumResults = $('input[name=alert_minimum_results]').val();
                  var alertMinimumDays = $('select[name=alert_minimum_days]').val();
                  //var alertEnd = $("input[name='alert_end[date]']").val();
                  var alertEnd = $("input[name='alert_end']").val();
                  console.log(alertEnd);
                  var dateRegEx = '[0-9]{4,4}-[0-1][0-9]-[0-3][0-9]';

                  var alertMsgs = [];
                  if (alertLabel.length < 1) {
                    alertMsgs.push("No Description has been set.");
                  }
                  if (alertEmail.length < 1 || !validateEmail(alertEmail)) {
                    alertMsgs.push("No email is entered.");
                  }
                  if (!isNumber(alertMinimumResults) || alertMinimumResults < 1) {
                    alertMsgs.push("Minimum results is not a valid number.");
                  }
                  if (!isNumber(alertMinimumDays) || alertMinimumDays < 1) {
                    alertMsgs.push("Alert frequency is not valid.");
                  }
                  var selectedDate = new Date($("input[name='alert_end']").val());
                  if ((alertEnd.length > 1 && alertEnd.length != 10) || (alertEnd.length > 1 && !alertEnd.match(dateRegEx))) {
                    alertMsgs.push("Expiration Date is not valid.");
                  } else if (selectedDate != null && selectedDate < new Date()) {
                    alertMsgs.push("Expiration date should be greater than current date.");
                  }

                  if (alertMsgs.length > 0) {
                    $('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul>' + '<li>' + alertMsgs.join('</li><li>') + '</li></ul></div>');
                  } else {
                    $('#errorMessages').html('');

                    var url = '/alert/transactions';
                    var data = {
                      refURL: oSettings.sAjaxSource,
                      alert_label: alertLabel,
                      alert_email: alertEmail,
                      alert_minimum_results: alertMinimumResults,
                      alert_minimum_days: alertMinimumDays,
                      alert_end: alertEnd,
                      userURL: window.location.href
                    };
                    $this = $(this);
                    $.get(url, data, function (data) {
                      //data = JSON.parse(data);

                      if (data.data.success) {
                        $this.dialog('close');

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
                          dialogClass: 'noTitleDialog',
                          close: function () {
                            $(this).dialog('destroy').remove();
                          }
                        });
                      } else {
                        $('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul><li>' + data.data.errors.join('<li/>') + '</ul></div>');
                      }
                    });
                  }
                },
                "Cancel": function () {
                  $(this).dialog('close');
                }
              },
              close: function () {
                $(this).dialog('destroy').remove();
              }
            });
          }
        );
        return false;
      });
    }
  };
}(jQuery));
