/**
 *  Outputs the auto suggestions for the entered text in the search textbox.
 */
(function ($) {
  $(document).ready(function () {
    $("#edit-search-box").autocomplete({
      position: {my: "right top", at: "right bottom"},
      minLength: 0,
      source: '/smart_search/autocomplete/'+$('#checkbook-smart-search-form input[name=domain]').val(),
      focus: function (event, ui) {
        $(event.target).val(ui.item.label);
        return false;
      },
      select: function (event, ui) {
        setTimeout(function () {
          $("#edit-submit").addClass('disable_button');
          $("#edit-search-box").addClass('transparent');
          $("#edit-search-box").addClass('loadinggif');
          $("#edit-search-box").attr("readonly", "readonly");
          // This is to fix the issue with chrome when trying to disable the search button
          $('input[type=submit]').attr("disabled", "disabled");
          $("#edit-submit").css('cursor', 'default');
          $("#edit-search-box").attr("disabled", "disabled");
        }, 1);
        $(event.target).val(ui.item.label);
        window.location = ui.item.url;
        return false;
      }
    })
      .data("autocomplete")._renderMenu = function (ul, items) {
      var self = this,
        currentCategory = "";
      $.each(items, function (index, item) {

        if (item.value == 'No matches found') {
          $("<li class='ui-menu-item'></li>").data("item.autocomplete", item)
            .append(item.label)
            .appendTo(ul);
        } else {
          if (item.category != currentCategory) {
            ul.append("<li class='ui-autocomplete-category'>" + item.category + "</li>");
            currentCategory = item.category;
          }
          item.url = item.url + encodeURIComponent(item.value);
          $("<li></li>").data("item.autocomplete", item)
            .append("<a href='" + item.url + "'>" + htmlEntities(item.label) + "</a>")
            .appendTo(ul);
        }

      });
    };
    $("#edit-search-box").keypress(function (e) {
      if (e.which == 13) {
        $("#edit-search-box").autocomplete("off");
        $("#edit-search-box").autocomplete("close");
      }
    });
    $("#edit-submit").click(function (e) {
      setTimeout(function () {
        $("#edit-submit").addClass('disable_button');
        $("#edit-search-box").addClass('transparent');
        $("#edit-search-box").addClass('loadinggif');
        $("#edit-search-box").focus();
        // This is to fix the issue with chrome when trying to disable the search button
        $('input[type=submit]').attr("disabled", "disabled");
        $("#edit-submit").css('cursor', 'default');
        $("#edit-search-box").attr("disabled", "disabled");
      }, 1);
    });
  });

  Drupal.behaviors.exportSmartSearchTransactions = {
    attach: function (context, settings) {
//                $('span.exportSmartSearch').unbind("click");
      $('span.exportSmartSearch').once('exportSmartSearch', function () {
        $('span.exportSmartSearch').live("click", function () {

          var dialog = $("#dialog");
          if ($("#dialog").length == 0) {
            dialog = $('<div id="dialog" style="display:none"></div>');
            //                        console.log('new dialog.init');
          } else {
            //                      console.log('$("#dialog").length = '+$("#dialog").length);
          }
          var domains = '';
          $.each($('input[name=fdomainName]:checked'), function () {
            domains = domains + "~" + this.value;
          });
          if (domains == '') {
            $.each($('input[name=fdomainName]'), function () {
              domains = domains + "~" + this.value;
            });
          }
          var solr_datasource = Drupal.settings.solr_datasource || 'citywide';
          var dialogUrl = '/exportSmartSearch/form/' + solr_datasource +
            '?search_term=' + getParameterByName("search_term") +
            '&totalRecords=' + $(this).attr("value") +
            '&resultsdomains=' + domains;

          var checked_domains = '';
          $.each($('input[name=fdomainName]:checked'), function () {
            checked_domains = checked_domains == '' ? this.value : checked_domains + "~" + this.value;
          });
          if (checked_domains == '') {
            $.each($('input[name=fdomainName]'), function () {
              checked_domains = checked_domains == '' ? this.value : checked_domains + "~" + this.value;
            });
          }
          var array_domains = checked_domains.split('~');

          //Re-order checked priority as doesn't match the facet order
          var array_checked_domains = [];
          if ($.inArray('spending', array_domains) > -1) array_checked_domains.push('spending');
          if ($.inArray('payroll', array_domains) > -1) array_checked_domains.push('payroll');
          if ($.inArray('contracts', array_domains) > -1) array_checked_domains.push('contracts');
          if ($.inArray('budget', array_domains) > -1) array_checked_domains.push('budget');
          if ($.inArray('revenue', array_domains) > -1) array_checked_domains.push('revenue');

          var dialog_html = '';
          dialog_html += '<div id="loading_gif" style="display:none"></div>';
          dialog_html += '<div id="errorMessages"></div>';
          dialog_html += '<p>Type of Data:</p>';
          dialog_html += '<table>';
          dialog_html += '<tr>';
          dialog_html += '<td><input type="radio" name="domain" value="spending"' + getCheckboxAttributes('spending', array_checked_domains) + ' />&nbsp;Spending</td>';
          dialog_html += '<td><input type="radio" name="domain" value="payroll"' + getCheckboxAttributes('payroll', array_checked_domains) + ' />&nbsp;Payroll</td>';
          dialog_html += '<td><input type="radio" name="domain" value="contracts"' + getCheckboxAttributes('contracts', array_checked_domains) + ' />&nbsp;Contracts</td>';
          dialog_html += '</tr>';
          dialog_html += '<tr>';
          dialog_html += '<td><input type="radio" name="domain" value="budget"' + getCheckboxAttributes('budget', array_checked_domains) + ' />&nbsp;Budget</td>';
          dialog_html += '<td><input type="radio" name="domain" value="revenue"' + getCheckboxAttributes('revenue', array_checked_domains) + ' />&nbsp;Revenue</td>';
          dialog_html += '</tr>';
          dialog_html += '</table>';
          dialog_html += '<span id="export-message"></span>';

          // load remote content
          dialog.load(
            dialogUrl,
            {},
            function (responseText, textStatus, XMLHttpRequest) {
              dialog.dialog({
                position: "center",
                modal: true,
                title: 'Download Search Results',
                dialogClass: "export",
                resizable: false,
                width: 700,
                open: function () {
                  $("#dialog").html(dialog_html);
                },
                buttons: {
                  "Download Data": function () {
                    var solr_datasource = Drupal.settings.solr_datasource || 'citywide';
                    var url = '/exportSmartSearch/download/'+solr_datasource;

                    url += '?search_terms='+encodeURIComponent(getParameterByName("search_term"));
                    url += '&domain='+$('input[name=domain]:checked').val();

                    // $('#dialog #export-message')
                    //   .add('.ui-dialog-titlebar')
                    //   .add('.ui-dialog-buttonpane')
                    //   .add('#dialog')
                    //   .addClass('disable_me');
                    // $('#loading_gif').show();
                    // $('#loading_gif').addClass('loading_bigger_gif');

                    // next line downloads csv!
                    document.location.href = url;
                    $(this).dialog('close');

                    // $('#dialog #export-message')
                    //   .add('.ui-dialog-titlebar')
                    //   .add('.ui-dialog-buttonpane')
                    //   .add('#dialog')
                    //   .removeClass('disable_me');
                    // $('#loading_gif').hide();
                    // $('#loading_gif').removeClass('loading_bigger_gif');

                  },
                  "Cancel": function () {
                    $(this).dialog('close');
                  }
                }
              });
              //$('.ui-dialog-buttonpane').append('<div class="exportDialogMessage">*Required Field</div>');
              onChangeDomain('spending');

              //On change of domain
              $('input:radio[name=domain]').change(function () {
                onChangeDomain($('input[name=domain]:checked').val());
              });

              function onChangeDomain(domain) {
                var totalRecords = 0;
                var selectedRecords = 0;
                var domainCounts = $('.exportSmartSearch').attr("value");
                var arrayDomainCounts = domainCounts.split('~');
                var selectedDomain = $('input[name=domain]:checked').val();
                $.each(arrayDomainCounts, function (i, val) {
                  var domainCount = val.split('|');
                  if (selectedDomain == domainCount[0])
                    selectedRecords = domainCount[1];
                  totalRecords += parseInt(domainCount[1]);
                });
                var message = '';
                if (selectedRecords <= 200000) {
                  message = addCommas(selectedRecords) + " " + selectedDomain + " records available for download. " +
                    "The report will be in Comma Delimited format. Only one domain can be selected at a time to download the data.";
                } else {
                  message = "Maximum of 200,000 records available for download from " + addCommas(selectedRecords) + " available " + selectedDomain + " records. " +
                    "The report will be in Comma Delimited format. Only one domain can be selected at a time to download the data.";
                }

                $('#export-message').html(message);
              }
            }
          );
          return false;
        });
      });

      function getCheckboxAttributes(domain, array_domains) {
        var checked_domain = array_domains[0];
        return (checked_domain == domain ? ' checked' : '') + ($.inArray(domain, array_domains) > -1 ? '' : ' disabled');
      }

    }
  };

  Drupal.behaviors.narrowDownFilters = {
    attach: function (context, settings) {
      var solr_datasource = Drupal.settings.solr_datasource || 'citywide';
      var search_term = window.location.href.toString().split(window.location.host)[1];

      //Sets up jQuery UI autocompletes and autocomplete filtering functionality for agency name facet
      $('.solr_autocomplete', context).each(function(){
        var facet_name = $(this).attr('facet');
        $(this).autocomplete({
          source: "/solr_autocomplete/"+solr_datasource+"/"+facet_name+"/" + search_term,
          focus: function (event, ui) {
            if (ui.item.label.toLowerCase() == 'no matches found') {
              return false;
            } else {
              $(event.target).val(ui.item.label);
              return false;
            }
          },
          select: function (event, ui) {
            if (ui.item.label.toLowerCase() == 'no matches found') {
              return false;
            } else {
              var url = getFacetAutocompleteUrl(facet_name, encodeURIComponent(ui.item.value));
              $(event.target).val(ui.item.label);
              window.location = url;
              return false;
            }
          }
        });
      });
    }
  };

  Drupal.behaviors.clear_search = {
    attach: function (context) {

      $('#edit-search-box', context).focus(function () {
        if (this.value == this.defaultValue) {
          $(this).val("");
        }
      });

      $('a.pagerItemDisabled').click(function (e) {
        e.preventDefault();
      });
    }
  }
// Filter Results Paginations
  Drupal.behaviors.smartSearchResults = {
    attach: function (context, settings) {
      $('.item-list ul.pager li a').live('click', function (e) {
        e.preventDefault();
        var search_string = jQuery(this).attr('href').split("?")[1];
        var search_term = search_string.split("*|*");
        var newURL = '';
        newURL = search_term[0];
        for (var i = 1; i < search_term.length; i++) {
          var search_filter = search_term[i].split("=");
          var value = encodeURIComponent(search_filter[1]);
          newURL = newURL + '*|*' + search_filter[0] + '=' + value;
        }
        var solr_datasource = Drupal.settings.solr_datasource || 'citywide';
        var curl = '/smart_search/ajax/results/'+solr_datasource+'?' + newURL;
        var progress = jQuery('.smart-search-left .loading');
        jQuery.ajax({
          url: curl,
          type: "GET",
          beforeSend: function () {
            progress.show();
          },
          success: function (data) {
            $('.smart-search-left').html(data);
          }
        });
        return false;
      });
    }
  };
}(jQuery));




/**
 *  Returns the selected filter parameters on the form
 * @param filterName
 */

/*jshint evil:true */
// function getSearchFilterCriteria(filterName) {
//   var filterId = '';
//   var oFilterIds = document.getElementsByName(filterName);
//   /*jshint evil:true */
//   if (!eval(oFilterIds)) {
//     return filterId;
//   }
//   for (var i = 0; i < oFilterIds.length; i++) {
//     if (oFilterIds[i].checked) {
//       if (filterId.length > 0) {
//         filterId = filterId + '~' + oFilterIds[i].value;
//       } else {
//         filterId = oFilterIds[i].value;
//       }
//     }
//   }
//   return filterId;
// }

/**
 *  Redirects to the search results page for the given search criteria
 *  Requires 'prepareSearchFilterUrl' function
 */
function applySearchFilters() {
  jQuery('.smart-search-right input[type=checkbox]').attr("disabled", true);

  // adding checked checkboxes to the query string
  var fq = [];
  jQuery('.smart-search-right .narrow-down-filter input:checkbox:checked').each(function(){
    var facet_name = jQuery(this).attr('facet');
    if (!(facet_name in fq)){
      fq[facet_name] = [];
    }
    fq[facet_name].push(jQuery(this).val());
  });

  var fq_string = '';
  for (var k in fq){
    fq_string += '*|*'+k+'='+ fq[k].join('~');
  }

  // adding global q string
  var searchTerm = '';
  var url = new URLSearchParams(window.location.search);
  if(url.get('search_term')) {
    searchTerm = url.get('search_term').split('*|*')[0];
  }

  var cUrl = "?search_term=" + searchTerm;

  cUrl += fq_string;

  window.location = cUrl;
}

/**
 *  Returns the query string values from the current URL
 */
function getQuerystringValues() {
  var qsParm = [];
  var query = window.location.search.substring(1);
  var parms = query.split('&');
  for (var i = 0; i < parms.length; i++) {
    var pos = parms[i].indexOf('=');
    if (pos > 0) {
      var key = parms[i].substring(0, pos);
      var val = parms[i].substring(pos + 1);
      qsParm[key] = val;
    }
  }
  return qsParm;
}

function getFacetAutocompleteUrl(category, value) {

  var searchString = new URLSearchParams(window.location.search);

  // var searchString = getQuerystringValues();
  var newUrl = '?search_term=';
  var found = 0;

  if (searchString.get('search_term')) {
    var searchTerms = searchString.get('search_term').split("*|*");
    // newUrl += searchTerms[0];

    for (var i = 1; i < searchTerms.length; i++) {
      var params = searchTerms[i].split('=');
      if (params[0] == category) {
        found++;
        var terms = params[1].split('~');
        terms.push(value);

        searchTerms[i] = params[0]+'='+ terms.join('~');
      }
    }

    newUrl += searchTerms.join('*|*');

    if (!found) {
      newUrl += "*|*" + category + '=' + value;
    }
  } else {
    newUrl += "*|*" + category + '=' + value;
  }
  return newUrl;
}


function htmlEntities(str) {
  return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
