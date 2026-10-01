/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero
 * General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @file
 * Retroactivity AJAX Chart Filter logic.
 */
(function ($, Drupal, drupalSettings) {
  'use strict';

  let isProcessing = false;
  let rotatorElement = '#retroactivity-rotator';
  let chartOuterContainer = '#retroactivity-chart-outer-wrapper';
  let chartContainerElement = '.retroactivity-chart-container';
  let overViewElement = '.block-inline-blockbasic';
  let tileContainerElement = '.retroactivity-tiles-container';
  let messageContainer = '#retroactivity-chart-message';
  let currentTabId = 'overview';
  let currentTileId = '';
  let breadcrumbObserver = null;
  let breadcrumbUpdateLocked = false;

  function showLoadingRotator() {
    $(rotatorElement).show();
    $('.region--content').css('opacity', '0.20');
    //$(chartOuterContainer).css('opacity', '0.20');
  }

  function hideLoadingRotator() {
    $(rotatorElement).hide();
    $('.region--content').css('opacity', '1');
    //$(chartOuterContainer).css('opacity', '0.20');
  }

  /**
   * The main function to collect filters and update the chart.
   * Attached to the window object so the inline 'onclick' can find it.
   */
  window.updateChartWithFilters = function (chartId) {
    // Skip if ajax request is already initiated.
    if (isProcessing) {
      return;
    }

    const baseUrl = window.location.origin;

    const $rotator = $(rotatorElement);
    const $submitButton = $('#edit-submit');
    // Important: Update the chart container div element in the layout if required.
    const $chartContainer = $(chartContainerElement);//.layout__region-datatable
    if (!chartId) {
      chartId = "contract-volume-chart-1";
    }
    const targetDiv = "#node-widget-" + chartId;
    const agencyVal = $('#edit-agency-selected').val() || '';
    const vendorVal = $('#edit-vendor-selected').val() || '';
    const sortOrder = $('#edit-sort-order').val() || '';
    let yearStart = $('#edit-year-from').val() || '';
    let yearEnd = $('#edit-year-to').val() || '';
    let agencyCode = '';
    let vendorCode = '';
    let mayoralFlag = '';

    // Synchronize the chart ID.
    $('#edit-chart-id').val(chartId);

    // YTD Comparison: hide year-from and "to" label, we only need year-to.
    // Would be better to do this in the widget config, but for now we'll do it here.
    const $yearFrom = $('#edit-year-from').closest('.datafield.year');
    const $yearTo = $('#edit-year-to').closest('.datafield.year');
    const isYearFromDisabled = skipYearFilterForChart(chartId, 'edit-year-from');
    const isYearToDisabled = skipYearFilterForChart(chartId, 'edit-year-to');

    if (isYearFromDisabled && isYearToDisabled) {
      $('#edit-fy-range').hide();
      yearStart = yearEnd = '';
    }
    else if (isYearFromDisabled) {
      $('#edit-fy-range').show();
      $yearFrom.hide();
      if (!$yearTo.data('original-html')) {
        $yearTo.data('original-html', $yearTo.html());
        $yearTo.data('original-value', yearEnd);
      }
      $yearTo.html($yearTo.html().replace(/&nbsp;to&nbsp;/, ''));
      // Retain previous year-to value.
      $('#edit-year-to').val(yearEnd);
      // Reset yearStart.
      yearStart = '';
    } else {
      $('#edit-fy-range').show();
      $yearFrom.show();
      if ($yearTo.data('original-html')) {
        $yearTo.html($yearTo.data('original-html'));
        $yearTo.removeData('original-html');
      }
      // Retain previous year-to value.
      if ($yearTo.data('original-value')) {
        $('#edit-year-to').val($yearTo.data('original-value'));
        $yearTo.removeData('original-value');
      }
    }

    // Validate selected years.
    if (yearStart != '' && yearEnd != '' && yearStart > yearEnd) {
      displayMessage("Invalid date range detected.<br/>Selected start date must be before end date.");
      $(chartContainerElement).html('');
      $(chartContainerElement).hide();
      //$('#edit-fy-range').append('<div class="messages messages--error">Please select a realistic year range</div>');
      return;
    }
    else {
      displayMessage('');
      $(chartContainerElement).html('');
      $(chartContainerElement).show();
    }

    // Process Agency code with the format: "Agency name [ShortName] [009] [BCA Short Name]"

    if (agencyVal !== '' && agencyVal.includes('[') && agencyVal.includes(']')) {

      const agencyName = agencyVal.match(/^(.*?)\[/);

      if (agencyName && agencyName[1].trim().toLowerCase() === "all mayoral agencies") {
        mayoralFlag = 1;
      }
      else {
        // Get all values inside brackets
        const matches = [...agencyVal.matchAll(/\[([^\]]+)\]/g)];

        // Second bracket contains the Agency Code
        if (matches.length >= 2) {
          agencyCode = matches[1][1].trim();
        }
      }
    }
    // Process agency code with the format "Vendor name [AG836DFINA]"
    if (vendorVal !== '' && vendorVal.includes('[') && vendorVal.includes(']')) {
      const match = vendorVal.match(/\[\s*([^\]]+?)\s*\]/);
      if (match && match[1]) {
        vendorCode = match[1].trim();
      }
    }

    // Helper to get checkbox values
    const getCheckedValues = (selector) => {
      let values = [];
      // Use the ^= operator to match Drupal's array-style names
      $(`input[name^="${selector}"]:checked`).each(function () {
        if ($(this).val()) {
          values.push($(this).val());
        }
      });
      return values.length > 0 ? values.join('~') : '';
    };

    const mwbe = getCheckedValues('mwbe_category');
    const industry = getCheckedValues('industry');
    const npStatus = getCheckedValues('non_profit');

    let dynamicUrl = `${baseUrl}/widget/${chartId}/retroactivity`;

    if (yearStart !== '') {
      dynamicUrl += `/year-start/${encodeURIComponent(yearStart)}`;
    }

    dynamicUrl += `/year-end/${encodeURIComponent(yearEnd)}`;

    if (agencyCode != '') {
      dynamicUrl += `/agency/${encodeURIComponent(agencyCode)}`;
    }
    if (vendorCode != '') {
      dynamicUrl += `/vendor/${encodeURIComponent(vendorCode)}`;
    }
    if (mwbe != '') {
      dynamicUrl += `/mwbe/${encodeURIComponent(mwbe)}`;
    }
    if (industry != '') {
      dynamicUrl += `/industry/${encodeURIComponent(industry)}`;
    }
    if (npStatus != '') {
      dynamicUrl += `/np-status/${encodeURIComponent(npStatus)}`;
    }
    if(mayoralFlag != ''){
      dynamicUrl += `/mayoral_flag/${encodeURIComponent(mayoralFlag)}`;
    }
    if(sortOrder != ''){
      dynamicUrl += `/sort-order/${encodeURIComponent(sortOrder)}`;
    }
    // Show loading image, disable submit and grey out chart area.
    showLoadingRotator();

    isProcessing = true;

    $.ajax({
      url: dynamicUrl,
      method: 'GET',
      beforeSend: function (xhr) {
        // Hide and reset sort and legends
        if (chartId == 'avg-days-late-by-agency' || chartId == 'average-days-late-1') {
          if (chartId == 'avg-days-late-by-agency') {
            loadSortContainer(false);
          }
          // Disable legends.
          loadLegendContainer(false);
        }
        showLoadingRotator();
      },
      success: function (response) {
        const $responseHtml = $(response);
        const specificDivHtml = $responseHtml.find(targetDiv).prop('outerHTML');
        // Update parent chart container div with chart html.
        $(chartContainerElement).html(specificDivHtml);
        hideLoadingRotator();
        Drupal.attachBehaviors($(chartContainerElement)[0], drupalSettings);
        // check if data exists.
        // Load sort container.
        if (chartId == 'avg-days-late-by-agency' || chartId == 'average-days-late-1') {
          // Show Sort link only if data exists.
          var chartLoaded = $('#node-chart-avg-days-late-by-agency').find('#no-records') || $('#node-chart-average-days-late-1').find('#no-records');
          if (chartLoaded.length !== 'undefined' && chartLoaded.length === 1) {
            // Hide sort icon.
            loadSortContainer(false);
            // Hide helper text.
            loadHelperContainer(false, '');
          }
          else {
            //Copy footer text.
            if (chartId == 'avg-days-late-by-agency') {
              // Hide helper text.
              loadHelperContainer(true, $('.retroactivity-chart-temp-footer').html());
              //Enable sort container.
              setTimeout(function(){
              var charts = Highcharts.charts;
              for (const key in charts) {
                if (charts[key].renderTo.id == 'node-chart-avg-days-late-by-agency') {
                  if (charts[key].axes[0].categories.length > 1) {
                    loadSortContainer(true);
                  }
                  else {
                    loadSortContainer(false);
                  }
                }
              }
              }, 100);
            }
            else {
            // Hide helper text.
            loadHelperContainer(false, '');
            }
            // Enable legends.
            loadLegendContainer(true);
            // Show the sort icon only if there is data to be sorted.
          }
        }

        createRetroactivityBreadcrumb(currentTabId, chartId);
      },
      error: function (xhr, status, error) {
        console.error("Chart update failed:", error);
        displayMessage(Drupal.t('Chart is unavailable!'));
        // Reset chart area.
        $(chartContainerElement).html('');
        isProcessing = false;
        //Hide sort link.
        loadSortContainer(false);
        // Disable legends.
        loadLegendContainer(false);
      },
      complete: function () {
        hideLoadingRotator();
        isProcessing = false;
        createRetroactivityBreadcrumb(currentTabId, currentTileId);
      }
    });
  };

  //Generates Solr URL for auto-completes
  $.fn.autoCompleteSourceUrl = function (solr_datasource, facet, filters) {
    let url = '/advanced_autocomplete/';
    let fq = '';

    $.fn.extractId = function (param) {
      if (param && (param.indexOf('id=>') > -1)) {
        return param.split('~')[0].split('=>')[1];
      }
      return param;
    };

    Object.keys(filters).forEach(function (key) {
      let val = $.fn.extractId(String(filters[key]));
      if (val && ("0" !== val)) {
        // remove trailing space from search terms
        fq += '*!*' + key + '=' + val.trim();
      }
    });
    let search_term = '/?search_term=' + fq;
    return url + solr_datasource + '/' + facet + search_term;
  };

  $.fn.preventSelectionDefault = function (event, ui, selection_to_prevent = "No Matches Found") {
    var label = ui.item.label;
    if (label === selection_to_prevent) {
      // prevent `selection_to_prevent` item from being selected
      event.preventDefault();
      $(event.target).val('');
    }
  };

  // Bind autocomplete listeners.
  Drupal.behaviors.retroactivityFilterAutocomplete = {
    attach: function (context, settings) {
      //Agency autocomplete.
      $('#edit-agency').autocomplete({
        source: $.fn.autoCompleteSourceUrl('checkbook', 'agency_shortname_code', []) + '&source_form=retroactivity',
        delay: 500,
        minLength: 3,
        select: function (event, ui) {
          // Update hidden field.
          $('#edit-agency-selected').val(ui.item.value);
          //Update selected val.
          let cleanValue = ui.item.label.replace(/\[.*?\]/g, "").trim();
          $(this).val(toCamelCase(cleanValue));
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
          return false;
        },
        change: function (event, ui) {
          // If ui.item is null, it means no item from the list was selected
          if (!ui.item) {
            // Reset value for both vendor field and hidden field.
            $(this).val("");
            $('#edit-agency-selected').val("");
          }
        },
        search: function (event, ui) {
          $('#checkbook-widget-retroactivity-filter-form #edit-agency').addClass('loadinggif');
        },
        // Triggers when the AJAX request finishes and results are ready
        response: function (event, ui) {
          $('#checkbook-widget-retroactivity-filter-form #edit-agency').removeClass('loadinggif');
        },
        // Optional: Ensure it hides if the user clears the input or cancels
        close: function (event, ui) {
          $('#checkbook-widget-retroactivity-filter-form #edit-agency').removeClass('loadinggif');
        }
      }).data("ui-autocomplete")._renderItem = function (ul, item) {
        return $("<li>")
          .append("<div class='agency-item'>" +
            "<span class='agency-name'>" + toCamelCase(item.label.replace(/\[.*?\]/g, "").trim()) + "</span>" +
            "</div>")
          .appendTo(ul);
      };

      // Vendor autocomplete starts.
      $('#edit-vendor').autocomplete({
        source: $.fn.autoCompleteSourceUrl('checkbook', 'vendor_name_code', []) + '&source_form=retroactivity',
        delay: 500,
        minLength: 3,
        select: function (event, ui) {
          // Update hidden field.
          $('#edit-vendor-selected').val(ui.item.value);
          //Update selected val.
          let cleanValue = ui.item.label.replace(/\[.*?\]/g, "").trim();
          $(this).val(toCamelCase(cleanValue));
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
          return false;
        },
        change: function (event, ui) {
          // If ui.item is null, it means no item from the list was selected
          if (!ui.item) {
            // Reset value for both vendor field and hidden field.
            $(this).val("");
            $('#edit-vendor-selected').val("");
          }
        },
        search: function (event, ui) {
          $('#checkbook-widget-retroactivity-filter-form #edit-vendor').addClass('loadinggif');
        },
        // Triggers when the AJAX request finishes and results are ready
        response: function (event, ui) {
          $('#checkbook-widget-retroactivity-filter-form #edit-vendor').removeClass('loadinggif');
        },
        // Optional: Ensure it hides if the user clears the input or cancels
        close: function (event, ui) {
          $('#checkbook-widget-retroactivity-filter-form #edit-vendor').removeClass('loadinggif');
        }
      }).data("ui-autocomplete")._renderItem = function (ul, item) {
        return $("<li>")
          .append("<div class='vendor-item'>" +
            "<span class='vendor-name'>" + toCamelCase(item.label.replace(/\[.*?\]/g, "").trim()) + "</span>" +
            "</div>")
          .appendTo(ul);
      };
    }
  };

    // Bind button actions.
    Drupal.behaviors.formButtonActions = {
        attach: function (context, settings) {
            //Submit action.
            $(once('form-submit-action', '#checkbook-widget-retroactivity-filter-form #edit-submit', context)).on('click', function (e) {
                e.preventDefault();
                const chartId = $('#checkbook-widget-retroactivity-filter-form #edit-chart-id').val();
                updateChartWithFilters(chartId);
            });
            //Clear form.
            $(once('form-clear-action', '#checkbook-widget-retroactivity-filter-form #edit-clear', context)).on('click', function (e) {
                e.preventDefault();
                this.form.reset();
                //Reset hidden fields.
                $('#edit-agency-selected').val('');
                $('#edit-vendor-selected').val('');
                $('#checkbook-widget-retroactivity-filter-form #edit-sort-order').val('');
                const chartId = $('#checkbook-widget-retroactivity-filter-form #edit-chart-id').val();
                updateChartWithFilters(chartId);
            });

            //Sort button.
            $(once('form-sort-action', '#retroactivity-sort-container', context)).on('click', function (e) {
                e.preventDefault();
                $('#checkbook-widget-retroactivity-filter-form #edit-sort-order').val($('#checkbook-widget-retroactivity-filter-form #edit-sort-order').val() == 0 ? 1 : 0);
                const chartId = $('#checkbook-widget-retroactivity-filter-form #edit-chart-id').val();
                updateChartWithFilters(chartId);
            });

        }
    };
  // Bind button actions.
  Drupal.behaviors.formButtonActions = {
    attach: function (context, settings) {
      //Submit action.
      $(once('form-submit-action', '#checkbook-widget-retroactivity-filter-form #edit-submit', context)).on('click', function (e) {
        e.preventDefault();
        const chartId = $('#checkbook-widget-retroactivity-filter-form #edit-chart-id').val();
        updateChartWithFilters(chartId);
      });
      //Clear form.
      $(once('form-clear-action', '#checkbook-widget-retroactivity-filter-form #edit-clear', context)).on('click', function (e) {
        e.preventDefault();
        this.form.reset();
        //Reset hidden fields.
        $('#edit-agency-selected').val('');
        $('#edit-vendor-selected').val('');
        const chartId = $('#checkbook-widget-retroactivity-filter-form #edit-chart-id').val();
        updateChartWithFilters(chartId);
      });

      //Sort button.
      $(once('form-sort-action', '#retroactivity-sort-container img', context)).on('click', function (e) {
        e.preventDefault();
        $('#checkbook-widget-retroactivity-filter-form #edit-sort-order').val($('#checkbook-widget-retroactivity-filter-form #edit-sort-order').val() == 0 ? 1 : 0);
        const chartId = $('#checkbook-widget-retroactivity-filter-form #edit-chart-id').val();
        updateChartWithFilters(chartId);
        $('#retroactivity-sort-image').toggleClass('retroactivity-sort-desc');
        $('#retroactivity-sort-image').toggleClass('retroactivity-sort-asc');
      });

      //LEGEND buttons.
      $(once('form-sort-action', '#retroactivity-legend-container .legend-item', context)).on('click', function (e) {
        e.preventDefault();
        // Toggle the visual strike-through class
        this.classList.toggle('strikethrough');

        const clicked_legend = $(this).attr('id');
        var charts = Highcharts.charts;
        for (const key in charts) {
          if (charts[key].renderTo.id == 'node-chart-avg-days-late-by-agency'
            || charts[key].renderTo.id == 'node-chart-average-days-late-1'
          ) {
            if (charts[key].axes[0].categories.length > 0) {
              var chart = charts[key];
              chart.series.forEach(function (series, index) {
                // Process Late legend
                if (clicked_legend == 'legend-ontime') {
                  // Labels  = Series for bar labels from Average date late Agency chart.
                  // Loop through labels and toggle display.
                  if (series.name == "Labels") {
                    series.points.forEach(function (point) {
                      if (point?.options?.legendType !== undefined) {
                        if (point?.options?.legendType === 'ontime') {
                          if (point.visible) {
                            point.graphic.hide();
                            if (point.dataLabel) {
                              point.dataLabel.hide();
                            }
                            point.visible = false;
                          }
                          else {
                            point.graphic.show();
                            if (point.dataLabel) {
                              point.dataLabel.show();
                            }
                            point.visible = true;
                          }
                        }
                      }
                    });
                  }
                  // Late  = Series from Average date late Agency chart.
                  // Late Bars, Late Bars Small = Series from Average date late chart 1.
                  if (series.name == 'On Time' || series.name == 'On Time Bars' || series.name == 'On Time Bars Small') {
                    if (series.visible) {
                      series.hide();
                    }
                    else {
                      series.show();
                    }
                  }
                }
                // Process On Time legend
                else if(clicked_legend == 'legend-late'){
                  if (series.name == "Labels") {
                    series.points.forEach(function (point) {
                      if (point?.options?.legendType !== undefined) {
                        if (point?.options?.legendType === 'late') {
                          if (point.visible) {
                            point.graphic.hide();
                            if (point.dataLabel) {
                              point.dataLabel.hide();
                            }
                            point.visible = false;
                          }
                          else {
                            point.graphic.show();
                            if (point.dataLabel) {
                              point.dataLabel.show();
                            }
                            point.visible = true;
                          }
                        }
                      }
                    });
                  }
                  // Late  = Series from Average date late Agency chart.
                  // Late Bars, Late Bars Small = Series from Average date late chart 1.
                  if (series.name == 'Late'  || series.name == 'Late Bars' || series.name == 'Late Bars Small') {
                    if (series.visible) {
                      series.hide();
                    }
                    else {
                      series.show();
                    }
                  }
                }
              });
              // Redraw the chart once all point states have been updated
              chart.redraw();
            }
          }
        }
      });

    }
  };

  //Bind chart tile clicks.
  Drupal.behaviors.retroactivityTiles = {
    attach: function (context) {
      $(once('chart-tile-click', '.chart-tile', context)).on('click', function () {
        // Load the chart id from data ID.
        const newChartId = $(this).data('chart-id');

        currentTileId = newChartId;

        // Set Tab heading.
        setDashboardHeading('chart', newChartId);

        createRetroactivityBreadcrumb(currentTabId, currentTileId);

        // Reset filter form.
        resetFilterFields();
        initializeTabContainer('');
        // UI Update: Set active class.
        $('.chart-tile').removeClass('is-active');
        $(this).addClass('is-active');
        // Load chart with current chart ID.
        window.updateChartWithFilters(newChartId);
        //Hide sort link.
        loadSortContainer(false);
        // Disable legends.
        loadLegendContainer(false);
      });
    }
  };

  //Bind tab clicks.
  Drupal.behaviors.retroactivityTabs = {
    attach: function (context, settings) {

      // Tab Click Listener
      $(once('tab-loader', '.js-tab-trigger', context)).on('click', function (e) {
        e.preventDefault();
        const tabId = $(this).data('tab-id');
        if (currentTabId != tabId) {
          currentTabId = tabId;
          currentTileId = '';

          //Set Tab heading.
          setDashboardHeading('tab', tabId);
          setDashboardHeading('reset', '');

          //Set Breadcrumb
          createRetroactivityBreadcrumb(currentTabId, currentTileId);

          // Reset filter form.
          resetFilterFields();
          // Update UI Active State.
          $('.tab-item').removeClass('is-active');
          $(this).closest('li').addClass('is-active');

          // Load the tab content - Tile content and first chart.
          loadTabContent(tabId);

          //Hide sort link.
          loadSortContainer(false);

          // Disable legends.
          loadLegendContainer(false);
        }
      });
    }
  };

  /**
   * Loads Tab Headings/Content then triggers Tile loading
   */
  function loadTabContent(tabId) {
    initializeTabContainer(tabId);
    if (tabId == 'overview') {
      currentTileId = '';
      // Clear chart heading.
      setDashboardHeading('reset', '');
      createRetroactivityBreadcrumb(currentTabId, currentTileId);
      return;
    }

    showLoadingRotator();

    // Imagine this endpoint returns the headings/header part of your dashboard
    $.ajax({
      url: `/retroactivity-tiles/ajax/${tabId}`,
      success: function (response) {
        //$('.dashboard-headings-container').html(response);

        // CHAIN: Now that the tab info is loaded, load the tiles
        //loadTilesForTab(tabId);
        if (Array.isArray(response) && response[0] && response[0].data) {
          const htmlContent = response[0].data;
          //Enable tiles
          //enableTileBlock(tabConfig);
          //Update tile container with tiles.
          $(tileContainerElement).html(htmlContent);
          Drupal.attachBehaviors(document.body, drupalSettings);

          const firstChartId = $('.chart-tile').first().data('chart-id');
          if (firstChartId) {
            currentTileId = firstChartId;
            setDashboardHeading('chart', firstChartId);
            createRetroactivityBreadcrumb(currentTabId, currentTileId);
            window.updateChartWithFilters(firstChartId);
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("Tile update failed:", error);
        displayMessage(Drupal.t('Unable to load tab content!'));
        // Reset chart area.
        $(chartContainerElement).html('');
        isProcessing = false;
      },
      complete: function () {
        isProcessing = false;
        createRetroactivityBreadcrumb(currentTabId, currentTileId);
      }
    });
  }

  function loadTilesForTab(tabId) {
    //Build tiles.
  }

  function enableTileBlock(tabConfig) {
    //Build tiles.
    tabConfig.tiles_enabled ? $(tileContainerElement).show() : $(tileContainerElement).hide();
  }

  function enableFilterBlock(tabId, displayStatus) {
    //Build tiles.
  }

  function displayMessage(errorMessage) {
    if (errorMessage) {
      const errorContent = `
            <div class="messages-list">
                <div class="messages messages--error" role="alert">
                    <div class="messages__container">
                        <h2 class="visually-hidden">Error message</h2>
                        <div class="messages__content">
                            ${errorMessage}
                        </div>
                    </div>
                </div>
            </div>
        `;
      $(messageContainer).html(errorContent);
      $(messageContainer).show();
    }
    else {
      $(messageContainer).hide();
      $(messageContainer).html('');
    }
  }

  function initializeTabContainer(tabId) {
    // Reset errors if any.
    displayMessage('');
    // Reset chart area.
    $(chartContainerElement).html('');
    if (tabId == 'overview') {
      $(overViewElement).show();
      $(chartOuterContainer).hide();
      $(tileContainerElement).hide();
      $('.node--view-mode-full .flex-3').hide();
    }
    else {
      $(overViewElement).hide();
      $(chartOuterContainer).show();
      $(tileContainerElement).show();
      $('.node--view-mode-full .flex-3').show();
    }
    // Clear the tooltips,.
    hideToolTips();
  }

  /**
   * Load Retroactivity main module configuration from drupalSettings.
   * @param {*} configType
   * @param {*} configItem
   *    Eg: loadConfigItem('tab', 'overview')
   *        loadConfigItem('chart', 'contract-payment-chart-1')
   *        loadConfigItem('', 'heading')
   * @returns
   */
  function loadConfigItem(configType, configItem) {
    const objConfig = drupalSettings.checkbook_retroactivity_config;
    let objReturn;
    switch (configType) {
      case 'tab':
        // Tab object.
        objReturn = typeof objConfig['tabs'][configItem] !== 'undefined' ? objConfig['tabs'][configItem] : null;
        break;
      case 'chart':
        // Chart/Tiles object.
        let objCart = null;
        Object.keys(objConfig['chart_tiles']).forEach(key => {
          const objTile = objConfig['chart_tiles'][key];
          if (objTile.chart_id !== 'undefined' && objTile.chart_id == configItem) {
            objReturn = objTile;
          }
        });
        break;
      default:
        // Any other generic config field, reaches here.
        objReturn = typeof objConfig[configItem] !== 'undefined' ? objConfig[configItem] : null;
        break;
    }

    return objReturn;
  }

  /**
   * Check if year filter can be skipped.
   * @param {*} chartId
   *
   * @returns
   *    boolean
   */
  function skipYearFilterForChart(chartId, filter_id) {
    const chartConfig = loadConfigItem('chart', chartId);
    if (chartConfig && typeof chartConfig.exclude_filters !== 'undefined') {
      if (chartConfig.exclude_filters.includes(filter_id)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Set dashboard headings
   * @param {*} headingType
   *   main/tab/chart
   * @param {*} identifier
   *   ''/chartId/tabId
   */
  function setDashboardHeading(headingType, identifier) {
    switch (headingType) {
      case 'main':
        $('.retroactivity-dashboard-container .dashboard-headings .main-title').html(loadConfigItem('', 'heading'));
        $('.retroactivity-dashboard-container .dashboard-headings .main-title-h2').html(loadConfigItem('', 'subHeading'));
        break;
      case 'tab':
        $('.retroactivity-chart-outer-container .dashboard-headings-sub .sub-title').html(loadConfigItem('tab', identifier)['label']);
        break;
      case 'chart':
        $('.retroactivity-chart-outer-container .dashboard-headings-sub .chart-title').html(loadConfigItem('chart', identifier).label);
        break;
      case 'reset':
        $('.retroactivity-chart-outer-container .dashboard-headings-sub .chart-title').html('');
        break;
    }
  }

  /**
   * Reset filter form fields based on configuration value.
   */
  function resetFilterFields() {
    // Load the config value to see if filter form needs to be reset.
    if (!loadConfigItem('', 'retainFilters')) {
      $('#edit-agency-selected').val('');
      $('#edit-vendor-selected').val('');
      $('#checkbook-widget-retroactivity-filter-form #edit-sort-order').val('');
      $('#checkbook-widget-retroactivity-filter-form')[0].reset();
    }
  }

  /**
   * Safely escape breadcrumb text.
   */
  function escapeBreadcrumbText(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  /**
   * Return breadcrumb HTML for current Retroactivity state.
   */
  function getRetroactivityBreadcrumbHtml(tabId, chartId) {
    const dashboardLabel = loadConfigItem('', 'heading') || 'Late Contracts Dashboard';

    let tabLabel = '';
    if (tabId) {
      const tabConfig = loadConfigItem('tab', tabId);
      if (tabConfig && tabConfig.label) {
        tabLabel = tabConfig.label;
      }
    }

    if (!tabLabel && tabId === 'overview') {
      tabLabel = 'Overview';
    }

    let chartLabel = '';
    if (chartId) {
      const chartConfig = loadConfigItem('chart', chartId);
      if (chartConfig && chartConfig.label) {
        chartLabel = chartConfig.label;
      }
    }

    const breadcrumbItems = [
      '<a href="/" class="homeLink">Home</a>',
      '<span class="inline">' + escapeBreadcrumbText(dashboardLabel) + '</span>'
    ];

    if (tabLabel) {
      breadcrumbItems.push('<span class="inline">' + escapeBreadcrumbText(tabLabel) + '</span>');
    }

    if (chartLabel) {
      breadcrumbItems.push('<span class="inline">' + escapeBreadcrumbText(chartLabel) + '</span>');
    }

    return '<span class="breadcrumb-inner">' +
      breadcrumbItems.join(' <span class="delimiter">»</span> ') +
      '</span>';
  }

  /**
   * Create/update Retroactivity Dashboard breadcrumb.
   *
   * Current Drupal breadcrumb markup:
   * <div id="breadcrumb">
   *   <span class="breadcrumb-inner">...</span>
   * </div>
   *
   * Format:
   * Home > Late Contracts Dashboard > Tab Name > Graph Name
   */
  function createRetroactivityBreadcrumb(tabId, chartId) {
    const breadcrumb = document.getElementById('breadcrumb');

    if (!breadcrumb) {
      return;
    }

    const breadcrumbHtml = getRetroactivityBreadcrumbHtml(tabId, chartId);

    if (breadcrumb.innerHTML !== breadcrumbHtml) {
      breadcrumbUpdateLocked = true;
      breadcrumb.innerHTML = breadcrumbHtml;
      breadcrumbUpdateLocked = false;
    }

    installRetroactivityBreadcrumbGuard();
  }

  /**
   * Guard against Drupal/system breadcrumb behavior resetting this block to "undefined".
   * Optimized: the guard only rewrites if the breadcrumb contains undefined.
   */
  function installRetroactivityBreadcrumbGuard() {
    const breadcrumb = document.getElementById('breadcrumb');

    if (!breadcrumb || breadcrumbObserver) {
      return;
    }

    breadcrumbObserver = new MutationObserver(function () {
      if (breadcrumbUpdateLocked) {
        return;
      }

      if ((breadcrumb.textContent || '').indexOf('undefined') === -1) {
        return;
      }

      breadcrumbUpdateLocked = true;
      breadcrumb.innerHTML = getRetroactivityBreadcrumbHtml(currentTabId, currentTileId);
      breadcrumbUpdateLocked = false;
    });

    breadcrumbObserver.observe(breadcrumb, {
      childList: true,
      subtree: true,
      characterData: true
    });
  }

  /**
   * Convert any string to camel cased.
   * @param {*} str
   * @returns string
   */
  function toCamelCase(str) {
    return str
      .toLowerCase()
      .split(' ')
      .filter(word => word.length > 0)
      .map((word, index) => {
        // Skip the word of just to make the the options look similiar to other drop downs across the site.
        if (word == 'of') {
          return word;
        }
        // Capitalize the first letter of subsequent words.
        return word.charAt(0).toUpperCase() + word.slice(1);
      })
      // Join without spaces.
      .join(' ');
  }

  /**
   * Show/Hide Sort link.
   */
  function loadSortContainer(show) {
   if (!show) {
      $('#retroactivity-sort-container').hide();
    }
    else {
      $('#retroactivity-sort-container').show();
    }
  }

  /**
   * Show/Hide custom legend container.
   */
  function loadLegendContainer(show) {
   if (!show) {
      //Clear strikethrough.
      $('#retroactivity-legend-container .legend-item').removeClass('strikethrough');
      $('#retroactivity-legend-container').hide();
    }
    else {
      $('#retroactivity-legend-container').show();
    }
  }

  /**
   * Show/Hide custom legend container.
   */
  function loadHelperContainer(show, $text) {
    $('.retroactivity-agency-helper').html($text);
    if (show) {
      $('.retroactivity-agency-helper').show();
    }
    else {
      $('.retroactivity-agency-helper').hide();
    }
  }

  /**
   * Hide the tooltips rendered already.
   */
  function hideToolTips() {
    if (typeof Highcharts !== 'undefined' && Highcharts.charts) {
        Highcharts.charts.forEach(function(chart) {
          if (chart) {
            // Safely hide the tooltip if active
            chart.tooltip?.hide();

            // Clear hover states across all series and points
            chart.series?.forEach(function(series) {
              series.setState?.('');
              series.points?.forEach(function(point) {
                point.setState?.('');
              });
            });

            // Force container reflow so layout recalculates correctly on Windows
            setTimeout(function() {
              chart.reflow();
            }, 50);
          }
        });
      }

      // Clean up any stray DOM wrappers
      $('.highcharts-tooltip').remove();
  }

  //Initialize the overview tab.
  initializeTabContainer('overview');
  // Set main heading.
  setDashboardHeading('main', '');
  //Set tab heading for overview.
  setDashboardHeading('tab', 'overview');

  //Initialize breadcrumb on default page load.
  createRetroactivityBreadcrumb(currentTabId, currentTileId);

  // Hide the default overview block.
  $('#block-nyccheckbook-overviewandspotlight').hide();

  // Ensure that the default autocomplete suggestion is turned off for Non-Windows OS.
  var isWindows = navigator.platform.toUpperCase().indexOf('WIN') > -1;
  if (!isWindows) {
    setTimeout(function() {$('#edit-agency').attr('autocomplete', 'new-password')}, 500);
    setTimeout(function() {$('#edit-vendor').attr('autocomplete', 'new-password')}, 500);
  }

})(jQuery, Drupal, drupalSettings);
