(function ($, Drupal, drupalSettings) {

  var advancedSearchFormLoading = false;

  Drupal.behaviors.advancedSearchAndAlerts = {
    attach: function (context, settings) {
      // Process form after AJAX.
      if ($(context).hasClass('new-checkbook-advanced-search-form')) {
        common_run_after_ajax_once(advanced_search_bootstrap, context);
      }

      // After AJAX.
      function common_run_after_ajax_once(callback, obj) {

        bind_enter_keyboard_keypress();

        $('input[name="budget_submit"], input[name="revenue_submit"], input[name="spending_submit"],' +
          'input[name="contracts_submit"], input[name="payroll_submit"]', obj).addClass('adv-search-submit-btn');

        $('input[name="budget_next"], input[name="revenue_next"], input[name="spending_next"],' +
          'input[name="contracts_next"], input[name="payroll_next"]', obj).addClass('create-alert-next-btn');

        callback();
      }

      function advanced_search_bootstrap() {
        bootstrap_complete();
        return false;
      }

      function bootstrap_complete() {
        // Disable form.
        $.fn.formFreezeAdvancedSearch = function (e) {
          $(".ui-dialog-titlebar").addClass('transparent').addClass('disable_me');
          $(".new-checkbook-advanced-search-form").addClass('disable_me');
          $("#spending-advanced-search").addClass('transparent');
          $("#revenue-advanced-search").addClass('transparent');
          $("#budget-advanced-search").addClass('transparent');
          $("#contracts-advanced-search").addClass('transparent');
          $("#payroll-advanced-search").addClass('transparent');
          $(".advanced-search-accordion").addClass('transparent');
          $(".ui-dialog-titlebar-close").hide();
          $("#advanced-search-rotator").show();
        }

        // Disable form.
        $.fn.formUnFreezeAdvancedSearch = function (e) {
          $(".ui-dialog-titlebar").removeClass('transparent').removeClass('disable_me');
          $(".new-checkbook-advanced-search-form").removeClass('disable_me');
          $("#spending-advanced-search").removeClass('transparent');
          $("#revenue-advanced-search").removeClass('transparent');
          $("#budget-advanced-search").removeClass('transparent');
          $("#contracts-advanced-search").removeClass('transparent');
          $("#payroll-advanced-search").removeClass('transparent');
          $(".advanced-search-accordion").removeClass('transparent');
          $(".ui-dialog-titlebar-close").show();
          $("#advanced-search-rotator").hide();
        }

        // Disable form + Loading gif
        $(once('alert_back_next_button', '.adv-search-submit-btn, .create-alert-next-btn')).click(function() {
          // Disable form.
          $(null).formFreezeAdvancedSearch();

          // Disable buttons
          disable_input('[id^="edit-next-submit"]');
          disable_input('[id^="edit-back-submit"]');
        });
      }

      function advanced_search_bootstrap_domains() {
        var dataSourceDomains = ["spending", "contracts", "payroll", "budget", "revenue"];
        $.each(dataSourceDomains, function (index, value) {
          let dataSourceDiv = "div_" + value + "_data_source";
          //let dataSourceDiv = value + "_advanced_search_domain_filter";
         // console.log('here ' + dataSourceDiv);
          if ($("#" + dataSourceDiv).length <= 0) {
            let editFilter = "edit-" + value + "-advanced-search-domain-filter";
            // $("#edit-" + value + "-advanced-search-domain-filter").wrap("<div id='div_" + value + "_data_source'></div>");
            $('div[id^=' + editFilter + ']')
              .wrap("<div id='" + dataSourceDiv + "'></div>")
              .prepend("<span class='data_source-label'>Data Source</span><br/>");
            $("#" + dataSourceDiv).after('<br/>');
            let oge_datasources = $("#" + dataSourceDiv + " .form-item:not(:first)");
            let oge_fieldset = $('<fieldset />').addClass('oge-datasource-fieldset');
            let oge_fieldset_legend = $('<legend />').text('Other Government Entities:');
            oge_fieldset.append(oge_fieldset_legend);
            oge_datasources.detach();
            oge_fieldset.append(oge_datasources);
            $('div[id^=' + editFilter + ']').append(oge_fieldset);
            $("#" + dataSourceDiv).append($('<div />').addClass('clear2'));
          }
        });
      }

      //Generates Solr URL for auto-completes
      $.fn.autoCompleteSourceUrl = function (solr_datasource, facet, filters) {
        let url = '/advanced_autocomplete/';
        let fq = '';

        $.fn.extractId = function (param) {
          if (param && (param.indexOf('id=>') > -1)) {
            return param.split('~')[0].split('=>')[1];
          }
          return param;
        }

        Object.keys(filters).forEach(function (key) {
          let val = $.fn.extractId(String(filters[key]));
          if (val && ("0" !== val)) {
            // remove trailing space from search terms
            fq += '*!*' + key + '=' + val.trim();
          }
        });
        let search_term = '/?search_term=' + fq;
        return url + solr_datasource + '/' + facet + search_term;
      }

      // advanced-search-revenue

      // advanced-search-spending


      /*
       * This code is used to determine which window in the accordion should be open when users click the "Advanced Search" link, based on the page
       * from where the link has been clicked
       * Eg: if the "Advanced Search" link from spending page is clicked, the URL would be http://checkbook/SPENDING/transactions.....
       * if the "Advanced Search" link from budget page is clicked, the URL would be http://checkbook/BUDGET/transactions.....
       * based on the url param in the caps above, we have to keep the specific window in the accordion open
       * check the code in checkbook_advanced_search.module where we generate the form
       */
      function initializeActiveAccordionWindow(page_clicked_from, data_source) {
        let active_accordion_window = 2;
        switch (page_clicked_from) {
          case "budget":
          case "nycha_budget":
            active_accordion_window = 0;
            break;
          case "revenue":
          case "nycha_revenue":
            active_accordion_window = 1;
            break;
          case "contracts_revenue_landing":
          case "contracts_landing":
          case "contracts_pending_rev_landing":
          case "contracts_pending_exp_landing":
          case "contracts_pending_landing":
          case "contract":
          case "nycha_contracts":
            active_accordion_window = 3;
            break;
          case "payroll":
            active_accordion_window = 4;
            break;
          default:
            //spending
            active_accordion_window = 2;
            break;
        }
        return active_accordion_window;
      }

      $('input[id*="edit-budget-advanced-search-domain-filter-checkbook-oge"],' +
        'input[id*="edit-revenue-advanced-search-domain-filter-checkbook-oge"]').parent('div').remove();

      advanced_search_bootstrap_domains();

      function initializeAccordionAttributes(accordion_type) {
        // advanced_search_bootstrap_domains();
        $('#advanced-search-rotator').css('display', 'none');
        $("#block-checkbookadvancedsearchformblock").find(":input").removeAttr("disabled");
        $('.create-alert-customize-results').css('display', 'none');
        $('.create-alert-schedule-alert').css('display', 'none');
        $('.create-alert-confirmation').css('display', 'none');
        disable_input([
          '#edit-next-submit',
          '#edit-back-submit'
        ]);
        $('.create-alert-submit').css('display', 'none');
        $('div.ui-dialog-titlebar').css('width', 'auto');
        switch (accordion_type) {
          case 'advanced_search':
            $('.create-alert-view').css('display', 'none');
            $('.adv-search-submit-btn').css('display', 'inline');
            $('.create-alert-next-btn').css('display', 'none');
            $('.advanced-search-accordion').css('display', 'inline');
            break;

          case 'advanced_search_create_alerts':
            $('.create-alert-view').css('display', 'inline');
            $('div.create-alert-submit #edit-next-submit').val('Next');
            $('.adv-search-submit-btn').css('display', 'none');
            $('.create-alert-next-btn').css('display', 'inline');
            $('.advanced-search-accordion').css('display', 'inline');
            break;
        }
      }

      function extractId(param) {
        if (param && (param.indexOf('id=>') > -1)) {
          return param.split('~')[0].split('=>')[1];
        }
        return param;
      }

      /* For oge, Budget, Revenue & Payroll are not applicable and are disabled */
      function disableAccordionSections(data_source) {
        //Disable Payroll for EDC
        if (data_source === "checkbook_oge") {
          let ogeDisabledDomains = ["budget", "revenue", "Payroll"];
          if (Array.isArray(ogeDisabledDomains)) {
            ogeDisabledDomains.forEach(function (ogeDisabledDomain) {
              disableAccordionSection(ogeDisabledDomain);
            });
          }
        }
      }

      /* Function will apply disable the click of the accordian section and apply an attribute for future processing */
      function disableAccordionSection(name) {
        let accordion_section = $("a:contains(" + name + ")").closest("h3");
        accordion_section.attr("data-enabled", "false");
        accordion_section.addClass('ui-state-section-disabled');
        accordion_section.unbind("click");
      }

      // To open proper accordion on load start.
      $(once('accordionAdvancedSearch', '#accordionAdvancedSearch', context)).each(function () {

        // UPDATED RELOCATED FROM D7 DIALOG
        var spendingInit = Drupal.advancedSearchAndAlertsSpending;
        spendingInit.advanced_search_spending_init();
        var budgetInit = Drupal.advancedSearchAndAlertsBudget;
        budgetInit.advanced_search_budget_init();
        var contractInit = Drupal.advancedSearchAndAlertsContract;
        contractInit.advanced_search_contracts_init();
        var payrollInit = Drupal.advancedSearchAndAlertsPayroll;
        payrollInit.advanced_search_payroll_init();
        var revenueInit = Drupal.advancedSearchAndAlertsRevenue;
        revenueInit.advanced_search_revenue_init();

        let href = window.location.href.replace(/(http|https):\/\//, '');
        let n = href.indexOf('?');
        href = href.substring(0, n !== -1 ? n : href.length);
        let data_source = 'checkbook';
        if (href.indexOf('datasource/checkbook_oge') !== -1) {
          data_source = 'checkbook_oge';
        } else if (href.indexOf('datasource/checkbook_nycha') !== -1) {
          data_source = 'checkbook_nycha';
        }
        let page_clicked_from = /*this.id ? this.id :*/ href.split('/')[1];
        let activeWindow = initializeActiveAccordionWindow(page_clicked_from, data_source);
        let $activeNode = $(this).children('.accordion-item').eq(activeWindow);

        let $button = $activeNode.find('.accordion-button');
        let $ariaControls = $button.attr('aria-controls');
        let $accordBody = $activeNode.find('#' + $ariaControls);

        $button.attr('aria-expanded', true);
        $button.removeClass('collapsed');
        $button.addClass('active');
        $button.find('.ui-icon').removeClass('ui-icon-triangle-1-e');
        $button.find('.ui-icon').addClass('ui-icon-triangle-1-s');
        $accordBody.removeClass('collapse');
        $accordBody.addClass('active');
        $accordBody.show();
        //to open proper accordion on load end
      });
    }
  };

  Drupal.behaviors.advancedSearchAccordion = {
    attach: function (context, settings) {
      $('#accordionAdvancedSearch', context).children('.accordion-item').each(function () {
        let $button = $(this).find('.accordion-button');
        let $ariaControls = $button.attr('aria-controls');
        let $accordBody = $(this).find('#' + $ariaControls);
        $button.click(function() {
          let $ariaExpanded = $button.attr('aria-expanded');
          if ($ariaExpanded !== "true") {
            //closing all expanded accordion items
            let $buttonParent = $button.closest('#accordionAdvancedSearch');
            $buttonParent.children('.accordion-item').each(function () {
              let $ariaExpandedTemp = $(this).find('.accordion-button').attr('aria-expanded');
              if ($ariaExpandedTemp === "true") {
                $(this).find('.accordion-button').attr('aria-expanded', false);
                $(this).find('.accordion-button').removeClass('active');
                $(this).find('.accordion-button').addClass('collapsed');
                $(this).find('.accordion-button').find('.ui-icon').removeClass('ui-icon-triangle-1-s');
                $(this).find('.accordion-button').find('.ui-icon').addClass('ui-icon-triangle-1-e');
                let $ariaControlsTemp = $(this).find('.accordion-button').attr('aria-controls');
                $(this).find('#' + $ariaControlsTemp).addClass('collapse');
                $(this).find('#' + $ariaControlsTemp).removeClass('active');
                $(this).find('#' + $ariaControlsTemp).slideUp();
              }
            });
            //opening the one that was clicked
            $button.attr('aria-expanded', true);
            $button.removeClass('collapsed');
            $button.addClass('active');
            $button.find('.ui-icon').removeClass('ui-icon-triangle-1-e');
            $button.find('.ui-icon').addClass('ui-icon-triangle-1-s');
            $accordBody.removeClass('collapse');
            $accordBody.addClass('active');
            $accordBody.slideDown();
          }
        });
      });
    }
  };



  function bind_enter_keyboard_keypress() {
    $(once('bind_enter_keyboard_keypress_once', ".new-checkbook-advanced-search-form input")).on("keypress", function (e) {
      if (e.keyCode === 13) {
        e.preventDefault();
        var id = this.id,
            matches = id.match(/^edit-checkbook(-nycha|-oge|)?-(contract|payroll|budget|revenue|spending)-/);

        $('input[id^="edit-' + matches[2] + '-submit"]:visible,' +
          'input[id^="edit-' + matches[2] + '-next"]:visible').click();
      } else return true;
    });
  }

}(jQuery, Drupal, drupalSettings));
