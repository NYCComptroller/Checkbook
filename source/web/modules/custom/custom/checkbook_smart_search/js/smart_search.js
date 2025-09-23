/**
 *  Outputs the auto suggestions for the entered text in the search textbox.
 */

(function ($, Drupal, once, drupalSettings) {

  // On back button reload page with the url parameters
  let perfEntries = performance.getEntriesByType("navigation");
  if (perfEntries[0].type === "back_forward") {
    window.location.reload(true);
  }

  Drupal.behaviors.disableOnClick = {
    attach: function (context, settings) {
      $(once('disableOnClick', '.export', context)).click(function (event) {
        event.preventDefault();
        let $this = $(this);
        if (!$this.hasClass('clicked')) {
          $this.addClass('clicked').attr('disabled', 'disabled');
          setTimeout(function() {
            $this.removeClass('clicked').removeAttr('disabled');
          }, 15000); // Change this to the appropriate delay time in milliseconds.
        }
      });
    }
  };

  Drupal.behaviors.smart_search_autocomplete = {
    attach: function (context, settings) {
      once('smart_search_autocomplete', 'input#edit-search-box').forEach(function(el){

        // Extend Widget with the Widget Factory
        $.widget("custom.smart_search_autocomplete", $.ui.autocomplete, {
          _create: function() {
            this._super();
            this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
          },
          _renderMenu: function(ul, items){
            var that = this,
                currentCategory = "";
            $.each(items, function(index, item){
              var li;

              if ( item.category && item.category !== currentCategory ) {
                ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                currentCategory = item.category;
              }

              li = that._renderItemData( ul, item );

              if ( item.category ) {
                li.attr( "aria-label", item.category + " : " + item.label );
              }
            });
          },
          _renderItem: function(ul, item) {
            if(item.value === '<span>No matches found</span>') return ul;
            if(item.value === 'No matches found') {
              return $( "<li class='ui-autocomplete-category'>" )
                .append(item.label)
                .appendTo( ul );
            }
            // Build URL
            item.url = item.url + encodeURIComponent(item.value);

            return $( "<li>" )
              .attr( "data-value", item.value )
              .append('<a href="' + item.url + '">' + htmlEntities(item.label) + '</a>')
              .appendTo( ul );
          }
        });

        // Attach autocomplete to smart search input
        $('#edit-search-box').smart_search_autocomplete({
          source: '/smart_search/autocomplete/' + $('#checkbook-smart-search-form input[name=domain]').val(),
          minLength: 0,
          classes: {
            "ui-autocomplete": "smart-search-autocomplete"
          },
          select: function (event, ui) {
            $(event.target).data('selected-label', ui.item.label);
          },
          close: function (event) {
            if ($(event.target).data('selected-label')) {
              $(event.target).val($(event.target).data('selected-label'));
            }
          }
        });
      });
    }
  };

  // @TODO(cv): This should go in a separate file
  Drupal.behaviors.faceted_seach = {
    attach: function(context, settings) {
      once('faceted_seach', 'body').forEach(function(el){
        $('.smart-search-right .filter-title').click(function(event) {
          if ($(this).next().css('display') === 'block') {
            $(this).next().css('display', 'none');
            $(this).children('span').removeClass('open');
          }
          else {
            $(this).next().css('display', 'block');
            $(this).children('span').addClass('open');

            $('div.facet-content .options').mCustomScrollbar("destroy");
            $('div.facet-content .options').mCustomScrollbar({
              horizontalScroll: false,
              scrollButtons: {
                enable: false
              },
              theme: 'dark'
            });
          }
        });

      });
    }
  }

  Drupal.behaviors.mySearchBehaviors = {
    attach: function (context, settings) {
      // Add keypress handler to hide autocomplete suggestions on pressing enter key
      $("#edit-search-box", context).keypress(function (e) {
        if (e.which === 13) {
          $(this).autocomplete("off").autocomplete("close");
        }
      });

      // Add click handler for search button
      $(".checkbook-smart-search-form", context).submit(function (e) {
        $("#edit-search-box").addClass('transparent')
          .addClass('loadinggif')
          .addClass('disabled-element')
          .attr('readonly', 'readonly')
          .focus();
        // This is to fix the issue with chrome when trying to disable the search button.
        $('input[type=submit]')
          .addClass('disable_button')
          .attr('disabled', 'disabled')
          .css('cursor', 'default');
      });

      $(window, context).on('dialogcreate', function (e, dialog, $element, settings) {
        $('form.new-checkbook-advanced-search-form .ui-dialog-title').each(function () {
          let dialog_title = this;
          $('.ui-dialog-titlebar .ui-dialog-title').replaceWith(dialog_title);
        })
      });
    }
  };

  Drupal.behaviors.exportSmartSearchTransactions = {
    attach: function (context, settings) {

      $(once('exportSmartSearch', 'span.exportSmartSearch')).each(function () {
        $("span.exportSmartSearch").on("click", function () {
          let $that = $(this);
          let dialog = $("#dialog");
          if (!dialog.length) {
            dialog = $('<div id="dialog" style="display:none"></div>');
          }
          let domains = '';
          $.each($('input[facet=domain]:checked'), function () {
            domains = domains + "~" + this.value;
          });
          if (domains === '') {
            $.each($('input[facet=domain]'), function () {
              domains = domains + "~" + this.value;
            });
          }
          let solr_datasource = drupalSettings.solr_datasource || 'citywide';
          let dialogUrl = '/exportSmartSearch/form/' + solr_datasource +
            '?search_term=' + getParameterByName("search_term") +
            '&totalRecords=' + $(this).attr("value") +
            '&resultsdomains=' + domains;

          let checked_domains = '';
          $.each($('input[facet=domain]:checked'), function () {
            checked_domains = checked_domains === '' ? this.value : checked_domains + "~" + this.value;
          });
          if (checked_domains === '') {
            $.each($('input[facet=domain]'), function () {
              checked_domains = checked_domains === '' ? this.value : checked_domains + "~" + this.value;
            });
          }
          let array_domains = checked_domains.split('~');

          //Re-order checked priority as doesn't match the facet order
          let array_checked_domains = [];
          if ($.inArray('spending', array_domains) > -1) {
            array_checked_domains.push('spending');
          }
          if ($.inArray('payroll', array_domains) > -1) {
            array_checked_domains.push('payroll');
          }
          if ($.inArray('contracts', array_domains) > -1) {
            array_checked_domains.push('contracts');
          }
          if ($.inArray('budget', array_domains) > -1) {
            array_checked_domains.push('budget');
          }
          if ($.inArray('revenue', array_domains) > -1) {
            array_checked_domains.push('revenue');
          }

          let dialog_html = '';
          dialog_html += '<div id="loading_gif" class="loading_bigger_gif" style="display:none"></div>';
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

          // Open dialog.
          dialog.dialog({
            position: {my: "center", at: "center", of: window},
            modal: true,
            title: 'Download Search Results',
            dialogClass: "export",
            resizable: false,
            width: 700,
            open: function () {
              $("#dialog").html(dialog_html);
              $that.removeClass('clicked').removeAttr('disabled');
            },
            buttons: {
              "Download Data": function () {
                let solr_datasource = drupalSettings.solr_datasource || 'citywide';
                let domain = $('input[name=domain]:checked').val();
                let url = '/exportSmartSearch/download/' + solr_datasource;

                url += '?search_terms=' + encodeURIComponent(getParameterByName("search_term"));
                url += '&domain=' + domain;

                $('.export #loading_gif').show();

                $.get(url, function(csvString) {
                  if (csvString) {
                    let csvData = new Blob([csvString], { type: 'text/csv' });
                    let csvUrl = URL.createObjectURL(csvData);
                    downloadAnchor(csvUrl, 'csv', solr_datasource + (domain && domain[0].toUpperCase() + domain.slice(1)))
                  }
                  $('.export #loading_gif').hide();
                });
              },
              "Cancel": function () {
                $(this).dialog('close');
              }
            }
          });
          onChangeDomain('spending');

          // On change of domain.
          $('input:radio[name=domain]').change(function () {
            onChangeDomain($('input[name=domain]:checked').val());
          });

          function onChangeDomain(domain) {
            let totalRecords = 0;
            let selectedRecords = 0;
            let domainCounts = $('.exportSmartSearch').attr("value");
            let arrayDomainCounts = domainCounts.split('~');
            let selectedDomain = $('input[name=domain]:checked').val();
            $.each(arrayDomainCounts, function (i, val) {
              var domainCount = val.split('|');
              if (selectedDomain === domainCount[0]) {
                selectedRecords = domainCount[1];
              }
              totalRecords += parseInt(domainCount[1]);
            });
            let message = '';
            if (selectedRecords <= 200000) {
              message = addCommas(selectedRecords) + " " + selectedDomain + " records available for download. ";
            }
            else {
              message = "Maximum of 200,000 records available for download from " + addCommas(selectedRecords) + " available " + selectedDomain + " records. ";
            }

            message += "The report will be in Comma Delimited format. Only one domain can be selected at a time to download the data. ";
            message += "Use <a href='/data-feeds'>Data Feeds</a> to get all data. ";

            $('#export-message').html(message);
          }

          function downloadAnchor(content, ext, filename) {
            let anchor = document.createElement("a");
            anchor.style = "display:none !important";
            anchor.id = "downloadanchor";
            document.body.appendChild(anchor);

            // If the [download] attribute is supported, try to use it
            if ("download" in anchor) {
              anchor.download = filename + "." + ext;
            }
            anchor.href = content;
            anchor.click();
            anchor.remove();
          }

          return false;
        });
      });

function getCheckboxAttributes(domain, array_domains) {
  let checked_domain = array_domains[0];
        return (checked_domain === domain ? ' checked' : '') + ($.inArray(domain, array_domains) > -1 ? '' : ' disabled');
      }

    }
  };

  Drupal.behaviors.narrowDownFilters = {
      attach: function (context, settings) {
        let search_term = '?' + window.location.href.toString().split('?')[1];

        $('.smart-search-right input:checkbox', context).each(function () {
          $(this).click(applySearchFilters);
        });

        $('.smart-search-right input:radio', context).each(function () {
          $(this).click(applySearchFilters);
        });

        //Sets up jQuery UI autocompletes and autocomplete filtering
        // functionality for agency name facet

        $('.solr_autocomplete', context).each(function () {
          let solr_datasource = window.location.pathname.split('/')[2];
          let facet_name = $(this).attr('facet');
          let initialValue = $(this).val(); // Store the initial search value
          let recentTypedValue = $(this).val(); // Store the most recent typed value
          let blockRewrite = false; // Initialize blockRewrite variable. Used to preserve values on click select.

          $(this).autocomplete({
            source: "/solr_autocomplete/" + solr_datasource + "/" + facet_name + search_term,
            focus: function (event, ui) {
              if (ui.item.label.toString().toLowerCase() === 'no matches found') {
                return false;
              } else {
                $(event.target).val(ui.item.label);
                return false;
              }
            },
            select: function (event, ui) {
              if (ui.item.label.toString().toLowerCase() === 'no matches found') {
                return false;
              } else {
                var url = getFacetAutocompleteUrl(facet_name, encodeURIComponent(ui.item.value));
                $(event.target).val(ui.item.label);
                blockRewrite = true; // Set blockRewrite to true when an item is selected
                window.location = url;
                return false;
              }
            },
            close: function (event, ui) {
              // Prevent the autocomplete from being dismissed when the user clicks outside the widget.
              event.preventDefault();
            },
            open: function (event, ui) {
              // Bind the mouseleave event to the ui-menu element.
              $('.ui-menu').mouseleave(function () {
                // Restore the recent typed value when the mouse leaves the ui-menu area if blockRewrite is false.
                if (!blockRewrite) {
                  $(event.target).val(recentTypedValue);
                }
              });
            }
          })
            .on('input', function() {
              // Update the initial search value as the user types.
              initialValue = $(this).val();
              recentTypedValue = $(this).val();
              blockRewrite = false; // Reset blockRewrite when the user types
            });
        });

        $('div.facet-content .options').mCustomScrollbar("destroy");
        scroll_facet();

        $(function() {
          let loc = window.location.href.indexOf("/smart_search/");
          if (loc > -1) {
            $(".filter-title").each(function (i, el) {
              if (i === 0 || $(this).next().find(':checkbox').is(':checked')) {
                $(this).next().css('display', 'block');
                $(this).children('span').addClass('open');
              }
              else {
                $(this).next().css('display', 'none');
                $(this).children('span').removeClass('open');
              }
            });
          }
        });

        function scroll_facet() {
          let opts = {
            horizontalScroll: false,
            scrollButtons: {
              enable: false
            },
            theme: 'dark'
          };
          $('.smart-search-right div.facet-content .options').mCustomScrollbar(opts);
        }

        // Move "search-filters" before "smart-search-right".
        $('.smart-search-right + .search-filters').each(function () {
          let search_filters = this;
          $(search_filters).parent().find('.smart-search-right').before(search_filters);
        });
      }
    };

  Drupal.behaviors.clear_search = {
    attach: function (context) {

      $('#edit-search-box', context).focus(function () {
        if (this.value === this.defaultValue) {
          $(this).val("");
        }
      });

      $('a.pagerItemDisabled').click(function (e) {
        e.preventDefault();
      });
    }
  };

// Filter Results Paginations
  Drupal.behaviors.smartSearchResults = {
    attach: function (context, settings) {
      $('.smart-search-left').off('click').on('click', 'a.page-link', function (e) {
        e.preventDefault();
        let search_string = jQuery(this).attr('href').split("?")[1];
        let search_term = search_string.split("*!*");
        let newURL = search_term[0];
        for (var i = 1; i < search_term.length; i++) {
          let search_filter = search_term[i].split("=");
          let value = encodeURIComponent(search_filter[1]);
          newURL = newURL + '*!*' + search_filter[0] + '=' + value;
        }
        let solr_datasource = drupalSettings.solr_datasource || 'citywide';
        let curl = '/smart_search/ajax/results/' + solr_datasource + '?' + newURL;
        let progress = jQuery('.smart-search-left .loading');
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

  // Reveal the spinner when the pager item is clicked.
  Drupal.behaviors.changeLoadingClass = {
    attach: function (context, settings) {
      $('.pager__items a', context).click(function(event) {
        event.preventDefault();
        $('.loading').css('display', 'inline-block');
        window.location = $(this).attr('href');
      });
    }
  };
})(jQuery, Drupal, once, drupalSettings);

/**
 *  Redirects to the search results page for the given search criteria
 *  Requires 'prepareSearchFilterUrl' function
 */
function applySearchFilters() {
  //Disable facet selection while results are loading
  jQuery('.smart-search-right input[type=checkbox]').attr("disabled", true);
  jQuery('.smart-search-right input[type=radio]').attr("disabled", true);
  let subFacet = ["spending_category",
    "spending_category_name",
    "agreement_type_name",
    "payroll_type",
    "contract_category_name",
    "contract_status",
    "registered_fiscal_year"];

  let fq = [];
  //Add checked checkboxes to the query string
  jQuery('.smart-search-right .narrow-down-filter input:checkbox:checked').each(function () {
    let facet_name = jQuery(this).attr('facet');
    if (!(facet_name in fq)) {
      fq[facet_name] = [];
    }
    //Remove subfacets from url query string when domain is unchecked
    if (subFacet.includes(facet_name) === true) {
      if (fq["domain"] === undefined) {
        delete fq[facet_name];
      }
      else {
        fq[facet_name].push(jQuery(this).val());
      }
    }
    else {
      fq[facet_name].push(jQuery(this).val());
    }
  });

  //Add checked radios to the query string
  jQuery('.smart-search-right .narrow-down-filter input:radio:checked').each(function () {
    let facet_name = jQuery(this).attr('facet');
    if (!(facet_name in fq)) {
      fq[facet_name] = [];
    }
    //Remove sub-facets from url query string when domain is unchecked
    if (subFacet.includes(facet_name) === true) {
      if (fq["domain"] === undefined) {
        delete fq[facet_name];
      }
      else {
        fq[facet_name].push(jQuery(this).val());
      }
    }
    else {
      fq[facet_name].push(jQuery(this).val());
    }
  });

  let fq_string = '';
  let contract_status_reg_flag = false;
  let contract_status_active_flag = false;
  for (let k in fq) {
    if (k === 'contract_status' && fq[k].toString().toLowerCase() === 'registered') {
      contract_status_reg_flag = true;
    }
    if (k === 'contract_status' && fq[k].toString().toLowerCase() === 'active') {
      contract_status_active_flag = true;
    }
  }
  for (let k in fq) {
    //Year parameter changes for Contract Status selection
    if (k === 'facet_year_array' && contract_status_reg_flag) {
      fq['registered_fiscal_year'] = fq[k];
      k = 'registered_fiscal_year';
    }
    else if (k === 'registered_fiscal_year' && contract_status_active_flag) {
      fq['facet_year_array'] = fq[k];
      k = 'facet_year_array';
    }
    fq_string += '*!*' + k + '=' + encodeURIComponent(fq[k].join('~'));
  }

  // adding global q string
  let searchTerm = '';
  let url = new URLSearchParams(window.location.search);
  if (url.get('search_term')) {
    searchTerm = url.get('search_term').split('*!*')[0];
  }
  let cUrl = "?search_term=" + searchTerm;
  cUrl += fq_string;
  window.location = cUrl;
}

function getFacetAutocompleteUrl(category, value) {
  let searchString = new URLSearchParams(window.location.search);
  // var searchString = getQuerystringValues();
  let newUrl = '?search_term=';
  let found = 0;

  if (searchString.get('search_term')) {
    let searchTerms = searchString.get('search_term').split("*!*");
    // newUrl += searchTerms[0];

    for (var i = 1; i < searchTerms.length; i++) {
      let params = searchTerms[i].split('=');
      if (params[0] === category) {
        found++;
        let terms = params[1].split('~');
        terms.push(value);

        searchTerms[i] = params[0] + '=' + terms.join('~');
      }
    }

    newUrl += searchTerms.join('*!*');

    if (!found) {
      newUrl += "*!*" + category + '=' + value;
    }
  }
  else {
    newUrl += "*!*" + category + '=' + value;
  }
  return newUrl.replace('&', '%26');
}

function addCommas(nStr) {
  nStr += '';
  let c = nStr.split(',');
  nStr = c.join('');
  let x = nStr.split('.');
  let x1 = x[0];
  let x2 = x.length > 1 ? '.' + x[1] : '';
  const rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  return x1 + x2;
}

function htmlEntities(str) {
  return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
