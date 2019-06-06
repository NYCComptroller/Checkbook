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
          var solr_datasource = Drupal.settings.checkbook_smart_search.solr_datasource || 'citywide';
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
                    var inputs = "<input type='hidden' name='search_term' value='" + getParameterByName("search_term") + "'/>"
                      + "<input type='hidden' name='domain' value='" + $('input[name=domain]:checked').val() + "'/>";
                    var solr_datasource = Drupal.settings.checkbook_smart_search.solr_datasource || 'citywide';
                    var url = '/exportSmartSearch/download/'+solr_datasource;
                    $('<form id="downloadForm" action="' + url + '" method="get">' + inputs + '</form>')
                      .appendTo('body')
                      .submit()
                      .remove();

                    $('#dialog #export-message').addClass('disable_me');
                    $('.ui-dialog-titlebar').addClass('disable_me');
                    $('.ui-dialog-buttonpane').addClass('disable_me');
                    $('#dialog').addClass('disable_me');
                    $('#loading_gif').show();
                    $('#loading_gif').addClass('loading_bigger_gif');

                    $.ajax({
                      url: $('#downloadForm').attr('action'),
                      data: {
                        search_term: getParameterByName("search_term"),
                        domain: $('input[name=domain]:checked').val()
                      },
                      success: function () {
                        $('#dialog #export-message').removeClass('disable_me');
                        $('.ui-dialog-titlebar').removeClass('disable_me');
                        $('.ui-dialog-buttonpane').removeClass('disable_me');
                        $('#dialog').removeClass('disable_me');
                        $('#loading_gif').hide();
                        $('#loading_gif').removeClass('loading_bigger_gif');
                      },
                      error: function () {
                        $('#dialog #export-message').removeClass('disable_me');
                        $('.ui-dialog-titlebar').removeClass('disable_me');
                        $('.ui-dialog-buttonpane').removeClass('disable_me');
                        $('#dialog').removeClass('disable_me');
                        $('#loading_gif').hide();
                        $('#loading_gif').removeClass('loading_bigger_gif');
                      }
                    });
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
      var search_term = "";
      search_term = window.location.href.toString().split(window.location.host)[1];
      //Sets up jQuery UI autocompletes and autocomplete filtering functionality for agency name facet
      $('#autocomplete_fagencyName', context).autocomplete({
        source: "/smart_search/autocomplete/agency/" + search_term,
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
            var url = getFacetAutocompleteUrl("agency_names", encodeURIComponent(ui.item.value));
            $(event.target).val(ui.item.label);
            window.location = url;
            return false;
          }
        }
      })

      $('#autocomplete_fogeName', context).autocomplete({
        source: "/smart_search/autocomplete/oge/" + search_term,
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
            var url = getFacetAutocompleteUrl("oge_agency_names", encodeURIComponent(ui.item.value));
            $(event.target).val(ui.item.label);
            window.location = url;
            return false;
          }
        }
      })

      $('#autocomplete_fvendorName', context).autocomplete({
        source: "/smart_search/autocomplete/vendor" + search_term,
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
            var url = getFacetAutocompleteUrl("vendor_names", encodeURIComponent(ui.item.value));
            $(event.target).val(ui.item.label);
            window.location = url;
            return false;
          }
        }
      });
      $('#autocomplete_fexpenseCategoryName', context).autocomplete({
        source: "/smart_search/autocomplete/expensecategory" + search_term,
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
            var url = getFacetAutocompleteUrl("expense_categories", encodeURIComponent(ui.item.value));
            $(event.target).val(ui.item.label);
            window.location = url;
            return false;
          }
        }
      });
      $('#autocomplete_fyear', context).autocomplete({
        source: "/smart_search/autocomplete/fiscalyear" + search_term,
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
            var url = getFacetAutocompleteUrl("fiscal_years", encodeURIComponent(ui.item.value));
            $(event.target).val(ui.item.label);
            window.location = url;
            return false;
          }
        }
      });
      $('#autocomplete_regfyear', context).autocomplete({
        source: "/smart_search/autocomplete/regfiscalyear" + search_term,
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
            var url = getFacetAutocompleteUrl("registered_fiscal_years", encodeURIComponent(ui.item.value));
            $(event.target).val(ui.item.label);
            window.location = url;
            return false;
          }
        }
      });
      $('#autocomplete_findustryTypeName', context).autocomplete({
        source: "/smart_search/autocomplete/industrytype" + search_term,
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
            var url = getFacetAutocompleteUrl("industry_type_name", encodeURIComponent(ui.item.value));
            $(event.target).val(ui.item.label);
            window.location = url;
            return false;
          }
        }
      });
    }
  }
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
        var solr_datasource = Drupal.settings.checkbook_smart_search.solr_datasource || 'citywide';
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
  }

}(jQuery));

/**
 *  Redirects to the search results page for the given search criteria
 *  Requires 'prepareSearchFilterUrl' function
 */

function applySearchFilters() {
  jQuery('input[type=checkbox]').attr("disabled", true);
  var cUrl = prepareSearchFilterUrl();
  window.location = cUrl;

}

/**
 *  Returns the search URL
 *  Requires 'getSearchFilterCriteria' function
 */

function prepareSearchFilterUrl() {
  var domainNames = getSearchFilterCriteria('fdomainName');
  var ogeAgencyNames = getSearchFilterCriteria('fogeName');
  var agencyNames = getSearchFilterCriteria('fagencyName');
  var vendorNames = getSearchFilterCriteria('fvendorName');
  var vendorType = getSearchFilterCriteria('fvendorType');
  var expenseCategories = getSearchFilterCriteria('fexpenseCategoryName');
  var revenueCategories = getSearchFilterCriteria('frevenueCategoryName');
  var fiscalYears = getSearchFilterCriteria('fyear');
  var regfiscalYears = getSearchFilterCriteria('regfyear');
  var contractCategories = getSearchFilterCriteria('fcontractCatName');
  var contractStatus = getSearchFilterCriteria('fcontractStatus');
  var spendingCategories = getSearchFilterCriteria('fspendingCatName');
  var mwbeCategory = getSearchFilterCriteria('fmwbeCategory');
  var industryTypes = getSearchFilterCriteria('findustryTypeName');
  var payrollType = getSearchFilterCriteria('fpayrollTypeName');


  var searchTerm = '';
  var cUrl = null;

  var qsParm = getQuerystringValues();
  if (!qsParm) {
    searchTerm = ""
  } else if (qsParm["search_term"]) {
    var searchTerms = qsParm.search_term.split("*|*");
    searchTerm = searchTerms[0];
  }

  cUrl = "?search_term=" + searchTerm + "*|*";

  if (domainNames) {
    cUrl += "domains=" + encodeURIComponent(domainNames) + '*|*';
  }
  if (ogeAgencyNames) {
    cUrl += "oge_agency_names=" + encodeURIComponent(ogeAgencyNames) + '*|*';
  }
  if (agencyNames) {
    cUrl += "agency_names=" + encodeURIComponent(agencyNames) + '*|*';
  }
  if (vendorNames) {
    cUrl += "vendor_names=" + encodeURIComponent(vendorNames) + '*|*';
  }
  if (vendorType) {
    cUrl += "vendor_type=" + encodeURIComponent(vendorType) + '*|*';
  }
  if ((fiscalYears && !contractStatus) || (fiscalYears && contractStatus === "active") || (regfiscalYears && contractStatus === "active")) {
    cUrl += "fiscal_years=" + encodeURIComponent((fiscalYears) ? fiscalYears : regfiscalYears) + '*|*';
  }
  if ((regfiscalYears && !contractStatus && domainNames === 'contracts') || (regfiscalYears && contractStatus === "registered" && domainNames === 'contracts') || (fiscalYears && contractStatus === "registered" && domainNames === 'contracts')) {
    cUrl += "registered_fiscal_years=" + encodeURIComponent((regfiscalYears) ? regfiscalYears : fiscalYears) + '*|*';
  }
  if (expenseCategories) {
    cUrl += "expense_categories=" + encodeURIComponent(expenseCategories) + '*|*';
  }
  if (revenueCategories) {
    cUrl += "revenue_categories=" + encodeURIComponent(revenueCategories) + '*|*';
  }
  if (mwbeCategory) {
    cUrl += "minority_type_name=" + encodeURIComponent(mwbeCategory) + '*|*';
  }
  if (industryTypes) {
    cUrl += "industry_type_name=" + encodeURIComponent(industryTypes) + '*|*';
  }
  if (payrollType) {
    cUrl += "payroll_type=" + encodeURIComponent(payrollType) + '*|*';
  }
  if (domainNames) {
    if (contractCategories) {
      cUrl += "contract_categories=" + encodeURIComponent(contractCategories) + '*|*';
    }
    if (contractStatus) {
      cUrl += "contract_status=" + encodeURIComponent(contractStatus) + '*|*';
    }
    if (spendingCategories) {
      cUrl += "spending_categories=" + encodeURIComponent(spendingCategories) + '*|*';
    }
  }
  cUrl = cUrl.substring(0, cUrl.length - 3);

  return cUrl;
}


/**
 *  Returns the selected filter parameters on the form
 * @param filterName
 */

/*jshint evil:true */
function getSearchFilterCriteria(filterName) {
  var filterId = '';
  var oFilterIds = document.getElementsByName(filterName);
  /*jshint evil:true */
  if (!eval(oFilterIds)) {
    return filterId;
  }
  for (var i = 0; i < oFilterIds.length; i++) {
    if (oFilterIds[i].checked) {
      if (filterId.length > 0) {
        filterId = filterId + '~' + oFilterIds[i].value;
      } else {
        filterId = oFilterIds[i].value;
      }
    }
  }
  return filterId;
}

/**
 *  Returns the query string values from the current URL
 *
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
  var searchString = getQuerystringValues();
  var newUrl = '?search_term=';
  var count = 0;

  if (searchString.search_term) {
    var searchTerms = searchString.search_term.split("*|*");
    newUrl += searchTerms[0];

    for (var i = 1; i < searchTerms.length; i++) {
      var params = searchTerms[i].split('=');
      if (params[0] == category) {
        count++;
        params[1] = params[1] + '~' + value;
      }
      newUrl += "*|*" + params[0] + '=' + params[1];
    }

    if (count == 0) {
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
