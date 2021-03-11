(function ($) {

  var advancedSearchFormLoading = false;

  function show_loading_spinner() {
    $('a.advanced-search').before("<span class='as-loading'>" +
      "<img style='float:right' src='/sites/all/themes/checkbook/images/loading_large.gif' title='Loading Data...' />" +
      "</span>");
    advancedSearchFormLoading = true;
  }

  function hide_loading_spinner() {
    $('.as-loading').remove();
    advancedSearchFormLoading = false;
  }

  Drupal.behaviors.advancedSearchAndAlerts = {
    attach: function (context, settings) {

      // Advanced Search link once
      $('a.advanced-search').click(function (e) {
        e.preventDefault();
        if (advancedSearchFormLoading) {
          return;
        }
        show_advanced_search_form(advanced_search_bootstrap);
      });

      // Create Alert link once
      $('span.advanced-search-create-alert').click(function () {
        if (advancedSearchFormLoading) {
          return;
        }
        show_advanced_search_form(create_alert_bootstrap);
      });

      // Show second step buttons after iframe is loaded
      $("#checkbook_advanced_search_result_iframe").load(function () {
        $('.create-alert-submit').css('display', 'block');
      });

      function show_advanced_search_form(callback) {
        show_loading_spinner();

        if ($('#checkbook-advanced-search-form').length) {
          callback();
        } else {
          $('.block-checkbook-advanced-search .content').load('/advanced-search-ajax', function () {
            common_run_after_ajax_once(callback);
          });
        }
      }

      function common_run_after_ajax_once(callback) {

        bind_enter_keyboard_keypress();

        $('input[name="budget_submit"], input[name="revenue_submit"], input[name="spending_submit"],' +
          'input[name="contracts_submit"], input[name="payroll_submit"]').addClass('adv-search-submit-btn');

        $('input[name="budget_next"], input[name="revenue_next"], input[name="spending_next"],' +
          'input[name="contracts_next"], input[name="payroll_next"]').addClass('create-alert-next-btn');

        $('.ranges input[id*=datepicker]:not(.hasDatepicker)').datepicker({
          dateFormat: 'yy-mm-dd',
          changeMonth: true,
          changeYear: true,
          autoPopUp: 'focus',
          closeAtTop: false,
          speed: 'immediate',
          yearRange: '-25:+1'
        });

        bind_create_alert_buttons();

        callback();
      }

      function advanced_search_bootstrap() {

        $('#checkbook-advanced-search-form').attr('action', '/advanced-search');

        let href = window.location.href.replace(/(http|https):\/\//, '');
        let n = href.indexOf('?');
        href = href.substring(0, n !== -1 ? n : href.length);
        let data_source = 'checkbook';
        if(href.indexOf('datasource/checkbook_oge') !== -1){
          data_source = 'checkbook_oge';
        }else if(href.indexOf('datasource/checkbook_nycha') !== -1){
          data_source = 'checkbook_nycha';
        }

        var page_clicked_from = this.id ? this.id : href.split('/')[1];
        var active_accordion_window = initializeActiveAccordionWindow(page_clicked_from, data_source);

        $('#block-checkbook-advanced-search-checkbook-advanced-search-form').dialog({
          title: "Advanced Search",
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
            $('.adv-search-submit-btn').css('display', 'none');
          }
        }).css('min-height', '0%');
        /* Correct min-height for IE9, causes hover event to add spaces */

        //Initialize Attributes and styling
        initializeAccordionAttributes('advanced_search');

        $('.advanced-search-accordion').accordion({
          autoHeight: false,
          active: active_accordion_window
        });

        /* For EDC, Budget, Revenue & Payroll are not applicable and are disabled */
        disableAccordionSections(data_source);

        clearInputFieldByDataSource("#payroll-advanced-search", 'payroll', data_source);
        clearInputFieldByDataSource("#contracts-advanced-search", 'contracts', data_source);
        clearInputFieldByDataSource("#spending-advanced-search", 'spending', data_source);
        clearInputFieldByDataSource("#budget-advanced-search", 'budget', data_source);
        clearInputFieldByDataSource("#revenue-advanced-search", 'revenue', data_source);

        bootstrap_complete();

        return false;
      }

      function bootstrap_complete() {
        hide_loading_spinner();

        function formfreeze_advancedsearch(e) {
          setTimeout(function () {
            $(".ui-dialog-titlebar").addClass('transparent').addClass('disable_me');
            $("#spending-advanced-search").addClass('transparent');
            $("#revenue-advanced-search").addClass('transparent');
            $("#budget-advanced-search").addClass('transparent');
            $("#contracts-advanced-search").addClass('transparent');
            $("#payroll-advanced-search").addClass('transparent');
            $(".advanced-search-accordion").addClass('transparent');
            $("#block-checkbook-advanced-search-checkbook-advanced-search-form").addClass('disable_me');
            disable_input("#block-checkbook-advanced-search-checkbook-advanced-search-form :input");
          }, 1);
        }


        function gif_rotator(e) {
          setTimeout(function () {
            $("#advanced-search-rotator").css('display', 'block').addClass('loading_bigger_gif');
          }, 1);
        }

        // Disable form
        $('.adv-search-submit-btn').click(formfreeze_advancedsearch);
        //create alerts
        $('.create-alert-next-btn').click(formfreeze_advancedsearch);

        // Loading gif
        $('.adv-search-submit-btn').click(gif_rotator);
        //create alerts
        $('.create-alert-next-btn').click(gif_rotator);

        $(document).bind("ajaxSend", function () {
          setTimeout(function () {
            //Advanced search
            disable_input('.adv-search-submit-btn');
            //create alert
            disable_input('.create-alert-next-btn');
          }, 1);
        }).bind("ajaxComplete", function () {
          setTimeout(function () {
            //Advanced search
            enable_input('.adv-search-submit-btn');
            //Create alert
            enable_input('.create-alert-next-btn');
          }, 1);
        });
      }

      function advanced_search_bootstrap_domains() {
        advanced_search_budget_init();
        advanced_search_contracts_init();
        advanced_search_payroll_init();
        advanced_search_revenue_init();
        advanced_search_spending_init();
        advanced_search_buttons_init();
        var dataSourceDomains = ["spending", "contracts", "payroll", "budget", "revenue"];
        $.each(dataSourceDomains, function( index, value ) {
          let dataSourceDiv = "div_" + value + "_data_source";
          if($("#"+ dataSourceDiv).length <= 0) {
            $("#edit-" + value + "-advanced-search-domain-filter").wrap("<div id='div_" + value + "_data_source'></div>");
            $("#" + dataSourceDiv + " .form-radios").before("<span class='data_source-label'>Data Source</span><br/><br/>");
            $("#" + dataSourceDiv).after('<br/>');
            let oge_datasources = $("#" + dataSourceDiv + " .form-item:not(:first-child)");
            let oge_fieldset = $('<fieldset />').addClass('oge-datasource-fieldset');
            let oge_fieldset_legend = $('<legend />').text('Other Government Entities:');
            oge_fieldset.append(oge_fieldset_legend);
            oge_datasources.detach();
            oge_fieldset.append(oge_datasources);
            $("#" + dataSourceDiv + " .form-radios").append(oge_fieldset);
            $("#" + dataSourceDiv).append($('<div />').addClass('clear2'));
          }
        });
      }

// advanced-search-budget
      function advanced_search_budget_init() {
        //Hide EDC radio for Budget data-source
        $('#edit-budget-advanced-search-domain-filter-checkbook-oge').parent().hide();
        let budget_div = function (data_source, div_contents) {
          this.div_elements = {
            'agency': 'select[name=' + data_source + '_budget_agency]',
            'department': 'select[name=' + data_source + '_budget_department]',
            'expense_category': 'select[name=' + data_source + '_budget_expense_category]',
            'budget_code': 'select[name=' + data_source + '_budget_budget_code]',
            'budget_code_chosen': 'select[name=' + data_source + '_budget_budget_code_chosen]',
            'budget_name': 'select[name=' + data_source + '_budget_budget_name]',
            'budget_name_chosen': 'select[name=' + data_source + '_budget_budget_name_chosen]',
            'catastrophic_events': 'select[name=' + data_source + '_budget_catastrophic_events]',
            'year': 'select[name=' + data_source + '_budget_year]',
            'adopted_from': 'input:text[name=' + data_source + '_budget_adopted_from]',
            'adopted_to': 'input:text[name=' + data_source + '_budget_adopted_to]',
            'modified_from': 'input:text[name=' + data_source + '_budget_modified_from]',
            'modified_to': 'input:text[name=' + data_source + '_budget_modified_to]',
            'pre_encumbered_from': 'input:text[name=' + data_source + '_budget_pre_encumbered_from]',
            'pre_encumbered_to': 'input:text[name=' + data_source + '_budget_pre_encumbered_to]',
            'encumbered_from': 'input:text[name=' + data_source + '_budget_encumbered_from]',
            'encumbered_to': 'input:text[name=' + data_source + '_budget_encumbered_to]',
            'accrued_expense_from': 'input:text[name=' + data_source + '_budget_accrued_expense_from]',
            'accrued_expense_to': 'input:text[name=' + data_source + '_budget_accrued_expense_to]',
            'cash_payments_from': 'input:text[name=' + data_source + '_budget_cash_payments_from]',
            'cash_payments_to': 'input:text[name=' + data_source + '_budget_cash_payments_to]',
            'post_adjustments_from': 'input:text[name=' + data_source + '_budget_post_adjustments_from]',
            'post_adjustments_to': 'input:text[name=' + data_source + '_budget_post_adjustments_to]',
            'resp_center':'select[name=' + data_source + '_budget_responsibility_center]',
            'fundsrc':'select[name=' + data_source + '_budget_fundsrc]',
            'program':'select[name=' + data_source + '_budget_program]',
            'project':'select[name=' + data_source + '_budget_project]',
            'budget_type':'select[name=' + data_source + '_budget_budget_type]',
            'budget_type_chosen':'select[name=' + data_source + '_budget_budget_type_chosen]',
            'nycha_budget_name':'select[name=' + data_source + '_budget_nycha_budget_name]',
            'nycha_budget_name_chosen':'select[name=' + data_source + '_budget_nycha_budget_name_chosen]',
            'committed_from': 'input:text[name=' + data_source + '_budget_committed_from]',
            'committed_to': 'input:text[name=' + data_source + '_budget_committed_to]',
            'actual_from': 'input:text[name=' + data_source + '_budget_actual_from]',
            'actual_to': 'input:text[name=' + data_source + '_budget_actual_to]',
          };

          this.data_source = data_source;
          this.div_contents = div_contents;
        };
        budget_div.prototype.contents = function () {
          return this.div_contents;
        };
        budget_div.prototype.ele = function (element_name) {
          let selector = this.div_elements[element_name];
          return this.div_contents.find(selector);
        };

        //Initialise divs for checkbook and checkbook_nycha
        let div_budget_main = $("#budget-advanced-search");
        let div_checkbook_budget = new budget_div('checkbook', div_budget_main.children('div.checkbook'));
        let div_checkbook_budget_nycha = new budget_div('checkbook_nycha', div_budget_main.children('div.checkbook-nycha'));

        //checkbook_advanced_search_clear_button.js sets this value by default
        $('input:radio[name=budget_advanced_search_domain_filter]').click(function () {
          onBudgetDataSourceChange($('input[name=budget_advanced_search_domain_filter]:checked').val());
        });

        let onBudgetAgencyChange = function(div){
          if (div.ele('agency').val() === "0") {
            disable_input([div.ele('department').val("0"), div.ele('expense_category').val("0")]);
          } else {
            reloadDepartment(div);
            reloadExpenseCategory(div);
          }
          reloadBudgetCode(div);
          reloadBudgetName(div);
        }

        let reloadDepartment = function(div) {
          let val;
          let fiscal_year = (val = div.ele('year').val()) ? val : 0;
          let agency = (val = div.ele('agency').val()) ? val : 0;
          $.ajax({
            url: '/advanced-search/autocomplete/budget/department/' + fiscal_year + '/' + agency,
            success: function (data) {
              var html = '<option select="selected" value="0" >Select Department</option>';
              if (data[0]) {
                if (data[0].label !== 'No Matches Found') {
                  for (var i = 0; i < data.length; i++) {
                    html = html + '<option value="' + data[i] + ' ">' + data[i] + '</option>';
                  }
                }
              }
              div.ele('department').html(html).removeAttr("disabled");
            }
          });
        }

        let reloadExpenseCategory = function(div) {
          let val;
          let fiscal_year = (val = div.ele('year').val()) ? val : 0;
          let agency = (val = div.ele('agency').val()) ? val : 0;
          let dept = (val = div.ele('department').val()) ? val : 0;

          $.ajax({
            url: '/advanced-search/autocomplete/budget/expcategory/' + fiscal_year + '/' + agency + '/' + dept.toString().replace(/\//g, "__"),
            success: function (data) {
              let html = '<option select="selected" value="0" >Select Expense Category</option>';
              if (data[0]) {
                if (data[0].label !== 'No Matches Found') {
                  for (var i = 0; i < data.length; i++) {
                    html = html + '<option value="' + data[i] + ' ">' + data[i] + '</option>';
                  }
                }
              }
              div.ele('expense_category').html(html).removeAttr("disabled");
            }
          });
        }

        let budgetCodeAlreadyLoaded = false;

        let reloadBudgetCode = function(div) {
          let fiscal_year = div.ele('year').val() || 0;
          let agency = div.ele('agency').val() || 0;
          let dept = div.ele('department').val() || 0;
          let exp_cat = div.ele('expense_category').val() || 0;
          let budget_code = div.ele('budget_code').val() || 0;
          let budget_name = div.ele('budget_name').val() || 0;
          let catastrophic_events = div.ele('catastrophic_events').val() || 0;

          let url = '/advanced-search/autocomplete/budget/budgetcode/' + fiscal_year + '/' + agency + '/' +
            dept.toString().replace(/\//g, "__") + '/' +
            exp_cat.toString().replace(/\//g, "__") + '/' +
            budget_name.toString().replace(/\//g, "__") + '/' +
            catastrophic_events ;

          if (url === budgetCodeAlreadyLoaded) {
            return;
          }
          budgetCodeAlreadyLoaded = url;

          $.ajax({
            url: url,
            success: function (data) {
              let html = '<option select="selected" value="0" title="">Select Budget Code</option>';
              if (data[0]) {
                if (data[0].label !== 'No Matches Found') {
                  for (let i = 0; i < data.length; i++) {
                    html = html + '<option title="' + data[i] + '" value="' + data[i] + ' ">' + data[i] + '</option>';
                  }
                }
              }
              div.ele('budget_code').html(html).val(budget_code).trigger("chosen:updated");
              if (budget_name !== div.ele('budget_name').val()) {
                reloadBudgetCode(div);
              }
            }
          });
        }

        let budgetNamesAlreadyLoaded = false;

        let reloadBudgetName = function(div) {
          let fiscal_year = div.ele('year').val() || 0;
          let agency = div.ele('agency').val() || 0;
          let dept = div.ele('department').val() || 0;
          let exp_cat = div.ele('expense_category').val() || 0;
          let budget_code = div.ele('budget_code').val() || 0;
          let budget_name = div.ele('budget_name').val() || 0;
          let catastrophic_events = div.ele('catastrophic_events').val() || 0;

          let url = '/advanced-search/autocomplete/budget/budgetname/' + fiscal_year + '/' + agency + '/' +
            dept.toString().replace(/\//g, "__") + '/' +
            exp_cat.toString().replace(/\//g, "__") + '/' +
            budget_code + '/' + catastrophic_events;

          if (url === budgetNamesAlreadyLoaded) {
            return;
          }
          budgetNamesAlreadyLoaded = url;

          $.ajax({
            url: url,
            success: function (data) {
              let html = '<option select="selected" value="0" title="">Select Budget Name</option>';
              if (data[0]) {
                if (data[0].label !== 'No Matches Found') {
                  for (let i = 0; i < data.length; i++) {
                    html = html + '<option title="' + data[i].value + '" value="' + data[i].value + '">' + data[i].label + '</option>';
                  }
                }
              }
              div.ele('budget_name').html(html).val(budget_name).trigger("chosen:updated");
              if (budget_code !== div.ele('budget_code').val()) {
                reloadBudgetName(div);
              }
            }
          });
        }

        let budgetTypeAlreadyLoaded = false;

        let reloadBudgetType = function(div) {
          let budget_name = encodeURIComponent(div.ele('nycha_budget_name').val());
          let budget_type = div.ele('budget_type').val();
          let data_source = 'checkbook_nycha';

          let url = '/data-feeds/budget/budget_type/' + data_source + '/' + budget_name.toString().replace(/\//g, "__") + '/'  + true;
          if (url === budgetTypeAlreadyLoaded) {
            return;
          }
          budgetTypeAlreadyLoaded = url;
          $.ajax({
            url: url,
            success: function(data) {
              let html = '<option value="" >Select Budget Type</option>';
              if(data[0]){
                for (i = 0; i < data.length; i++) {
                  html = html + '<option value="' + data[i].value + '" title="' + data[i].value + '">' + data[i].text  + '</option>';
                }
              }
              div.ele('budget_type').html(html).val(budget_type).trigger("chosen:updated");
              if (budget_name !== div.ele('nycha_budget_name').val()) {
                reloadBudgetType(div);
              }
            }
          });
        }

        let nychaBudgetNamesAlreadyLoaded = false;

        let reloadNychaBudgetName = function(div) {
          let budget_type = encodeURIComponent(div.ele('budget_type').val());
          let budget_name = div.ele('nycha_budget_name').val();
          let data_source = 'checkbook_nycha';

          let url = '/data-feeds/budget/budget_name/' + data_source + '/' + budget_type.toString().replace(/\//g, "__") + '/'  + true;
          if (url === nychaBudgetNamesAlreadyLoaded) {
            return;
          }
          nychaBudgetNamesAlreadyLoaded = url;
          $.ajax({
            url: url,
            success: function(data) {
              let html = '<option value="" >Select Budget Name</option>';
              if(data[0]){
                for (i = 0; i < data.length; i++) {
                  html = html + '<option value="' + data[i].value + '" title="' + data[i].value + '">' + data[i].text  + '</option>';
                }
              }
              div.ele('nycha_budget_name').html(html).val(budget_name).trigger("chosen:updated");
              if (budget_type !== div.ele('budget_type').val()) {
                reloadNychaBudgetName(div);
              }
            }
          });
        }

        let onBudgetDataSourceChange = function(dataSource) {
          /* Initialize view by data source */
          switch (dataSource) {
            case "checkbook_nycha":
              resetFields(div_checkbook_budget_nycha.contents());
              div_checkbook_budget.contents().hide();
              div_checkbook_budget_nycha.contents().show();

              //Reset Budget Name and Budget Code Chosen drop-downs
              div_checkbook_budget_nycha.ele('budget_type').val("0").trigger("chosen:updated");
              div_checkbook_budget_nycha.ele('nycha_budget_name').val("0").trigger("chosen:updated");
              reloadBudgetType(div_checkbook_budget_nycha);
              reloadNychaBudgetName(div_checkbook_budget_nycha);

              break;

            default:
              resetFields(div_checkbook_budget.contents());
              div_checkbook_budget.contents().show();
              div_checkbook_budget_nycha.contents().hide();
              //Disable department and Expense Category Drop-downs
              if (div_checkbook_budget.ele('agency').val() == "0") {
                disable_input([div_checkbook_budget.ele('department').val("0"), div_checkbook_budget.ele('expense_category').val("0")]);
              }
              //Reset Budget Name and Budget Code Chosen drop-downs
              div_checkbook_budget.ele('budget_code').val("0").trigger("chosen:updated");
              div_checkbook_budget.ele('budget_name').val("0").trigger("chosen:updated");
              reloadBudgetCode(div_checkbook_budget);
              reloadBudgetName(div_checkbook_budget);
          }
        }

        //Citywide Budget - Trigger Chosen input tool for 'Budget Code' and 'Budget Name'
        div_checkbook_budget.ele('budget_code').chosen({
          no_results_text: "No matches found"
        });
        div_checkbook_budget.ele('budget_code_chosen').find('.chosen-search-input').attr("placeholder", "Search Budget Code");
        div_checkbook_budget.ele('budget_name').chosen({
          no_results_text: "No matches found"
        });
        div_checkbook_budget.ele('budget_name_chosen').find('.chosen-search-input').attr("placeholder", "Search Budget Name");
        reloadBudgetCode(div_checkbook_budget);
        reloadBudgetName(div_checkbook_budget);

        //Citywide Budget - Drop-down change events
        div_checkbook_budget.ele('agency').change(function () {
          onBudgetAgencyChange(div_checkbook_budget);
        });

        div_checkbook_budget.ele('department').change(function () {
          reloadExpenseCategory(div_checkbook_budget);
          reloadBudgetCode(div_checkbook_budget);
          reloadBudgetName(div_checkbook_budget);
        });

        div_checkbook_budget.ele('expense_category').change(function () {
          reloadBudgetCode(div_checkbook_budget);
          reloadBudgetName(div_checkbook_budget);
        });

        div_checkbook_budget.ele('budget_code').change(function () {
          reloadBudgetName(div_checkbook_budget);
        });

        div_checkbook_budget.ele('budget_name').change(function () {
          reloadBudgetCode(div_checkbook_budget);
        });

        div_checkbook_budget.ele('year').change(function () {
          let yval =  $(this).find("option:selected").text();
          reloadBudgetCode(div_checkbook_budget);
          reloadBudgetName(div_checkbook_budget);
          if ( yval < 2020){
            div_checkbook_budget.ele('catastrophic_events').attr("disabled", "disabled");
            div_checkbook_budget.ele('catastrophic_events').val('0');
          }
          else {
            div_checkbook_budget.ele('catastrophic_events').removeAttr("disabled");
          }
        });

        //Reload budget type and budget code on catastrophic event reload
        div_checkbook_budget.ele('catastrophic_events').change(function () {
          let cevent = div_checkbook_budget.ele('catastrophic_events').val();
          //let div_val = "#edit-checkbook-budget-year option";
          reloadBudgetCode(div_checkbook_budget);
          reloadBudgetName(div_checkbook_budget);
          updateEventYearValue("#edit-checkbook-budget-year option",cevent);
        });

        //NYCHA Budget- Trigger Chosen input tool for 'Budget Type' and 'Budget Name'
        div_checkbook_budget_nycha.ele('budget_type').chosen({
          no_results_text: "No matches found"
        });
        div_checkbook_budget_nycha.ele('budget_type_chosen').find('.chosen-search-input').attr("placeholder", "Search Budget Type");

        div_checkbook_budget_nycha.ele('nycha_budget_name').chosen({
          no_results_text: "No matches found"
        });
        div_checkbook_budget_nycha.ele('nycha_budget_name_chosen').find('.chosen-search-input').attr("placeholder", "Search Budget Name");
        reloadBudgetType(div_checkbook_budget_nycha);
        reloadNychaBudgetName(div_checkbook_budget_nycha);

        //NYCHA Budget - Drop-down change events
        div_checkbook_budget_nycha.ele('nycha_budget_name').change(function () {
          reloadBudgetType(div_checkbook_budget_nycha);
          if($(this).val() == 'Select Budget Name' || $(this).val() == '' || $(this).val() === 0){
            reloadNychaBudgetName(div_checkbook_budget_nycha);
          }
        });

        div_checkbook_budget_nycha.ele('budget_type').change(function () {
          reloadNychaBudgetName(div_checkbook_budget_nycha);
          if($(this).val() == 'Select Budget Type' || $(this).val() == '' || $(this).val() === 0){
            reloadBudgetType(div_checkbook_budget_nycha);
          }
        });
      }

     // advanced-search-clear-button
      function clearInputFields(enclosingDiv, domain, dataSourceName) {
        $(enclosingDiv).find(':input').each(function () {
          switch (this.type) {
            case 'select-one':
              var default_option = $(this).attr('default_selected_value');
              if (default_option) {
                $(this).find('option[value=' + default_option + ']').attr("selected", "selected");
              } else {
                $(this).find('option:first').attr("selected", "selected");
              }
              break;
            case 'text':
              $(this).val('');
              $(this).removeAttr('storedvalue');
              break;
            case 'select-multiple':
            case 'password':
            case 'textarea':
              $(this).val('');
              break;
            case 'checkbox':
            case 'radio':
              switch (domain) {
                case 'payroll':
                  $('#edit-payroll-amount-type-0').attr('checked', 'checked');
                  break;
              }
              break;
          }
        });
      }


// advanced-search-contracts
      function advanced_search_contracts_init() {

        var contracts_div = function (data_source, div_contents) {
          this.div_elements = {
            'status': 'select[name=' + data_source + '_contracts_status]',
            'vendor_name': 'input:text[name=' + data_source + '_contracts_vendor_name]',
            'mwbe_category': 'select[name=' + data_source + '_contracts_mwbe_category]',
            'contract_type': 'select[name=' + data_source + '_contracts_type]',
            'contract_id': 'input:text[name=' + data_source + '_contracts_contract_num]',
            'includes_sub_vendors': 'select[name="' + data_source + '_contracts_includes_sub_vendors"]',
            'pin': 'input:text[name=' + data_source + '_contracts_pin]',
            'curremt_amount_from': 'input:text[name="' + data_source + '_contracts_current_contract_amount_from"]',
            'curremt_amount_to': 'input:text[name="' + data_source + '_contracts_current_contract_amount_to"]',
            'end_date_from': 'input:text[name="' + data_source + '_contracts_end_date_from[date]"]',
            'end_date_to': 'input:text[name="' + data_source + '_contracts_end_date_to[date]"]',
            'registration_date_from': 'input:text[name="' + data_source + '_contracts_registration_date_from[date]"]',
            'registration_date_to': 'input:text[name="' + data_source + '_contracts_registration_date_to[date]"]',
            'category': 'select[name=' + data_source + '_contracts_category]',
            'catastrophic_events': 'select[name=' + data_source + '_contracts_catastrophic_events]',
            'sub_vendor_status': 'select[name="' + data_source + '_contracts_sub_vendor_status"]',
            'purpose': 'input:text[name=' + data_source + '_contracts_purpose]',
            'agency': 'select[name=' + data_source + '_contracts_agency]',
            'industry': 'select[name=' + data_source + '_contracts_industry]',
            'apt_pin': 'input:text[name=' + data_source + '_contracts_apt_pin]',
            'award_method': 'select[name=' + data_source + '_contracts_award_method]',
            'start_date_from': 'input:text[name="' + data_source + '_contracts_start_date_from[date]"]',
            'start_date_to': 'input:text[name="' + data_source + '_contracts_start_date_to[date]"]',
            'received_date_from': 'input:text[name="' + data_source + '_contracts_received_date_from[date]"]',
            'received_date_to': 'input:text[name="' + data_source + '_contracts_received_date_to[date]"]',
            'year': 'select[name="' + data_source + '_contracts_year"]',
            'commodity_line': 'input:text[name=' + data_source + '_contracts_commodity_line]',
            'entity_contract_number': 'input:text[name=' + data_source + '_contracts_entity_contract_number]',
            'budget_name': 'input:text[name=' + data_source + '_contracts_budget_name_text]',
            'purchase_order_type': 'select[name="' + data_source + '_contracts_purchase_order_type"]',
            'responsibility_center': 'select[name="' + data_source + '_contracts_responsibility_center"]',
            'approved_date': 'input:text[name="' + data_source + '_contracts_approved_date"]'
          };

          this.data_source = data_source;
          this.div_contents = div_contents;
        };
        contracts_div.prototype.contents = function () {
          return this.div_contents;
        };
        contracts_div.prototype.ele = function (element_name) {
          var selector = this.div_elements[element_name];
          return this.div_contents.find(selector);
        };

        var div_contracts_main = $("#contracts-advanced-search");
        var div_checkbook_contracts = new contracts_div('checkbook', div_contracts_main.children('div.checkbook'));
        var div_checkbook_contracts_oge = new contracts_div('checkbook_oge', div_contracts_main.children('div.checkbook-oge'));
        var div_checkbook_contracts_nycha = new contracts_div('checkbook_nycha', div_contracts_main.children('div.checkbook-nycha'));

        //On change of data source
        ///checkbook_advanced_search_clear_button.js sets this value by default
        $('input:radio[name=contracts_advanced_search_domain_filter]').click(function () {
          onChangeDataSource($('input[name=contracts_advanced_search_domain_filter]:checked').val());
        });

        $('#edit-checkbook-nycha-contracts-purchase-order-type').change(function(){
          onAgreementTypeChange();
        });

        function onAgreementTypeChange(){
          var agreement_type = $("#edit-checkbook-nycha-contracts-purchase-order-type option:selected").val().toUpperCase();
          switch (agreement_type) {
            case 'PO':
              disable_input(['.form-item-start-date :input','.form-item-end-date :input']);
              enable_input('.form-item-approved-date :input');
              break;
            case 'BA':
            case 'PA':
              enable_input(['.form-item-start-date :input','.form-item-end-date :input']);
              disable_input('.form-item-approved-date :input');
              break;
            case 'ALL':
            default:
              disable_input(['.form-item-start-date :input','.form-item-end-date :input']);
              disable_input('.form-item-approved-date :input');
              break;
          }
        }

        function showHidePrimeAndSubFields(div) {

          var note = $(".prime-and-sub-note");
          var contract_status = div.ele('status').parent();
          var vendor = div.ele('vendor_name').parent();
          var mwbe_category = div.ele('mwbe_category').parent();
          var current_amt_from = div.ele('curremt_amount_from').parent().parent().parent();
          var category = div.ele('category').parent();
          var sub_vendor_status_in_pip = div.ele('sub_vendor_status').parent();
          var purpose = div.ele('purpose').parent();
          var industry = div.ele('industry').parent();
          var year = div.ele('year').parent();

          // Remove all asterisk fields & note
          note.remove();
          removePrimeAndSubIcon(contract_status);
          removePrimeAndSubIcon(vendor);
          removePrimeAndSubIcon(mwbe_category);
          removePrimeAndSubIcon(current_amt_from);
          removePrimeAndSubIcon(category);
          removePrimeAndSubIcon(sub_vendor_status_in_pip);
          removePrimeAndSubIcon(purpose);
          removePrimeAndSubIcon(industry);
          removePrimeAndSubIcon(year);

          var contract_status_val = div.ele('status').val();
          var category_val = div.ele('category').val();

          if ((contract_status_val == 'A' || contract_status_val == 'R') && (category_val == 'expense' || category_val == 'all')) {
            $("<div class='prime-and-sub-note'>All Fields are searchable by Prime data, unless designated as Prime & Sub (<img src='/sites/all/themes/checkbook3/images/prime-and-sub.png' />).</div>").insertBefore($("#edit-contracts-advanced-search-domain-filter").parent());
            addPrimeAndSubIcon(contract_status);
            addPrimeAndSubIcon(vendor);
            addPrimeAndSubIcon(mwbe_category);
            addPrimeAndSubIcon(current_amt_from);
            addPrimeAndSubIcon(category);
            addPrimeAndSubIcon(sub_vendor_status_in_pip);
            addPrimeAndSubIcon(purpose);
            addPrimeAndSubIcon(industry);
            addPrimeAndSubIcon(year);
          }
        }

        /**
         * Function will remove the asterisk icon css from a field
         * @param ele
         */
        function removePrimeAndSubIcon(ele) {
          ele.find('.prime-and-sub').remove();
          ele.removeClass('asterisk-style');

        }

        /**
         * Function will add the asterisk icon css to a field
         * @param ele
         */
        function addPrimeAndSubIcon(ele) {
          var primeAndSubIcon = "<img class='prime-and-sub' src='/sites/all/themes/checkbook3/images/prime-and-sub.png' />";
          $(ele).find('label').first().prepend(primeAndSubIcon);
          ele.addClass('asterisk-style');
        }


        function onChangeDataSource(dataSource) {
          /* Reset all the fields for the data source */
          resetFields(div_checkbook_contracts.contents());
          resetFields(div_checkbook_contracts_oge.contents());
          resetFields(div_checkbook_contracts_nycha.contents());

          /* Initialize the disabled fields */
          onStatusChange(div_checkbook_contracts);
          onStatusChange(div_checkbook_contracts_oge);
          onCategoryChange(div_checkbook_contracts);

          /* Initialize view by data source */
          switch (dataSource) {
            case "checkbook_oge":
              initializeContractsView(div_checkbook_contracts_oge);
              div_checkbook_contracts.contents().hide();
              div_checkbook_contracts_oge.contents().show();
              div_checkbook_contracts_nycha.contents().hide();
              //handle oge attributes
              div_checkbook_contracts_oge.ele('status').find('option[value=P]').remove();
              div_checkbook_contracts_oge.ele('category').find('option[value=revenue]').remove();
              div_checkbook_contracts_oge.ele('category').find('option[value=all]').remove();
              div_checkbook_contracts_oge.ele('apt_pin').attr('disabled', 'disabled');
              div_checkbook_contracts_oge.ele('received_date_from').attr('disabled', 'disabled');
              div_checkbook_contracts_oge.ele('received_date_to').attr('disabled', 'disabled');
              div_checkbook_contracts_oge.ele('registration_date_from').attr('disabled', 'disabled');
              div_checkbook_contracts_oge.ele('registration_date_to').attr('disabled', 'disabled');

              // Remove note
              $(".prime-and-sub-note").remove();
              break;
            case "checkbook_nycha":
              initializeContractsView(div_checkbook_contracts_nycha);
              div_checkbook_contracts.contents().hide();
              div_checkbook_contracts_oge.contents().hide();
              div_checkbook_contracts_nycha.contents().show();
              onAgreementTypeChange();
              // Remove note
              $(".prime-and-sub-note").remove();
              break;
            default:
              //Fix the default for category
              $("select#edit-checkbook-contracts-category").val("all");
              initializeContractsView(div_checkbook_contracts);
              div_checkbook_contracts.contents().show();
              div_checkbook_contracts_oge.contents().hide();
              div_checkbook_contracts_nycha.contents().hide();
              //handle attributes
              div_checkbook_contracts.ele('sub_vendor_status').val('0');
              updateIncludeSubvendorsField(div_checkbook_contracts);
              showHidePrimeAndSubFields(div_checkbook_contracts);
              break;
          }
        }

        function autoCompletes(div) {
          let contract_status = div.ele('status').val() || 0;
          let contract_category_name = div.ele('category').val()|| 0;
          let minority_type_id = div.ele('mwbe_category').val() || 0;
          let industry_type_id = div.ele('industry').val() || 0;
          let contract_type_id = div.ele('contract_type').val() || 0;
          let agency_id = div.ele('agency').val() || 0;
          let award_method_id = div.ele('award_method').val() || 0;
          let scntrc_status = div.ele('includes_sub_vendors').val() || 0;
          let aprv_sta = div.ele('sub_vendor_status').val() || 0;
          let data_source = $('input:radio[name=contracts_advanced_search_domain_filter]:checked').val();
          let catastrophic_events_id = div.ele('catastrophic_events').val() || 0;
          let solr_datasource = data_source;
          let year = div.ele('year').val() || 0;
          let year_id = 0;
          let reg_year_id = 0;

          if(year.indexOf("fy") >= 0){
            year_id = year.split('~')[1];
          }

          if ('checkbook_nycha' == data_source){solr_datasource = 'nycha'}

          if('nycha' == solr_datasource){
            let agreement_type_code_nycha = $('#edit-checkbook-nycha-contracts-purchase-order-type').val() || 0;
            let responsibility_center_nycha = $('#edit-checkbook-nycha-contracts-responsibility-center').val() || 0;
            let contract_type_id_nycha = extractId($('#edit-checkbook-nycha-contracts-type').val()) || 0;
            let award_method_id_nycha = extractId($('#edit-checkbook-nycha-contracts-award-method').val()) || 0;
            let industry_type_id_nycha = $('#edit-checkbook-nycha-contracts-industry').val() || 0;
            let nycha_filters = {
              agreement_type_code: agreement_type_code_nycha,
              responsibility_center_id: responsibility_center_nycha,
              contract_type_id: contract_type_id_nycha,
              award_method_id: award_method_id_nycha,
              industry_type_id: industry_type_id_nycha,
              agency_id: agency_id,
              fiscal_year_id: year_id
            };

            div.ele('vendor_name').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'vendor_name', nycha_filters)});
            div.ele('contract_id').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'contract_number', nycha_filters)});
            div.ele('pin').autocomplete({source:$.fn.autoCompleteSourceUrl(solr_datasource,'pin', nycha_filters)});
          }else {
              if (contract_status == 'R') {
                reg_year_id = year_id;
                year_id = 0;
              }
              var filters = {
                event_id:catastrophic_events_id,
              contract_status: contract_status,
              contract_category_name: contract_category_name,
              agency_id: agency_id,
              award_method_id: award_method_id,
              minority_type_id: minority_type_id,
              industry_type_id: industry_type_id,
              scntrc_status: scntrc_status,
              aprv_sta: aprv_sta,
              fiscal_year_id: year_id,
              registered_fiscal_year_id: reg_year_id,
                contract_type_id: contract_type_id,
            };

            div.ele('vendor_name').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'vendor_name',filters)});
            div.ele('contract_id').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'contract_number', filters)});
            div.ele('apt_pin').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'apt_pin',filters)});
            div.ele('pin').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'pin',filters)});
            div.ele('entity_contract_number').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'contract_entity_contract_number',filters)});
            div.ele('commodity_line').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'contract_commodity_line',filters)});
            div.ele('budget_name').autocomplete({source:$.fn.autoCompleteSourceUrl(solr_datasource,'contract_budget_name',filters)});
          }

          $('.ui-autocomplete-input').bind('autocompleteselect', function (event, ui) {$(this).parent().next().val(ui.item.label);});
        }

        function initializeContractsView(div) {
          autoCompletes(div);
          $('#contracts-advanced-search').each(function () {
            $(this).focusout(function () {
              autoCompletes(div);
            });
          });
          //prevent the auto-complete from wrapping un-necessarily
          fixAutoCompleteWrapping(div.contents());
        }

        //Prevent the auto-complete from wrapping un-necessarily
        function fixAutoCompleteWrapping(divWrapper) {
          $(divWrapper.children()).find('input.ui-autocomplete-input:text').each(function () {
            $(this).data("autocomplete")._resizeMenu = function () {
              (this.menu.element).outerWidth('100%');
            }
          });
        }

        //On change of "Status"
        div_checkbook_contracts.ele('status').change(function () {
          showHidePrimeAndSubFields(div_checkbook_contracts);
          onStatusChange(div_checkbook_contracts);
        });
        div_checkbook_contracts_oge.ele('status').change(function () {
          onStatusChange(div_checkbook_contracts_oge);
        });

        function onStatusChange(div) {
          var data_source = $('input[name=contracts_advanced_search_domain_filter]:checked').val();
          var contract_status = div.ele('status').val();
          if (contract_status === 'P') {
            if (data_source === 'checkbook') {
              div.ele('registration_date_from').val('').attr("disabled", "disabled");
              div.ele('registration_date_to').val('').attr("disabled", "disabled");
            }
            div.ele('year').attr("disabled", "disabled");
            div.ele('year').val("");
            div.ele('received_date_from').removeAttr("disabled");
            div.ele('received_date_to').removeAttr("disabled");
          }  else {
            if (data_source === 'checkbook') {
              div.ele('registration_date_from').removeAttr("disabled");
              div.ele('registration_date_to').removeAttr("disabled");
            }
            div.ele('year').removeAttr("disabled");
            div.ele('received_date_from').attr("disabled", "disabled");
            div.ele('received_date_to').attr("disabled", "disabled");
            div.ele('received_date_from').val("");
            div.ele('received_date_to').val("");

          }
          updateSubVendorFields(div);
        }

        //On change of "Category"
        div_checkbook_contracts.ele('category').change(function () {
          showHidePrimeAndSubFields(div_checkbook_contracts);
          onCategoryChange(div_checkbook_contracts);
        });

        function onCategoryChange(div) {
          updateEventsField(div);
          updateSubVendorFields(div);
        }

        function updateSubVendorFields(div) {
          var contract_status = div.ele('status').val();
          var contract_category = div.ele('category').val();

          if (contract_status === 'P' || contract_category === 'revenue') {
            div.ele('includes_sub_vendors').attr("disabled", "disabled");
            div.ele('sub_vendor_status').attr("disabled", "disabled");
          }
          else {
            div.ele('includes_sub_vendors').removeAttr("disabled");
            div.ele('sub_vendor_status').removeAttr("disabled");
          }
        }

        //On change of "Contract Includes Sub Vendors" status - NYCCHKBK-6187
        div_checkbook_contracts.ele('includes_sub_vendors').change(function () {
          onIncludeSubvendorChange(div_checkbook_contracts);
        });

        function onIncludeSubvendorChange(div) {
          updateSubvendorStatusField(div);
        }

        function updateSubvendorStatusField(div) {
          var includes_sub_vendors = div.ele('includes_sub_vendors').val();
          if (includes_sub_vendors === '3' || includes_sub_vendors === '1' || includes_sub_vendors === '4') {
            div.ele('sub_vendor_status').attr("disabled", "disabled");
          } else {
            div.ele('sub_vendor_status').removeAttr("disabled");
          }
        }

        //On change of "Sub Vendor Status in PIP" status -  NYCCHKBK-6187
        div_checkbook_contracts.ele('sub_vendor_status').change(function () {
          onSubvendorStatusChange(div_checkbook_contracts);
        });

        function updateEventsField(div) {
          let yval = (div_checkbook_contracts.ele('year').find("option:selected").text()).split(' ')[1];
          let contract_category = div.ele('category').val();
          if(contract_category == 'revenue' || yval < 2020 ){
            div.ele('catastrophic_events').attr("disabled", "disabled");
            div.ele('catastrophic_events').val("");
            let catas_event = div_checkbook_contracts.ele('catastrophic_events').val();
            updateEventYearValue("#edit-checkbook-contracts-year option",'0');
          }
          else {
            div.ele('catastrophic_events').removeAttr("disabled");
          }

        }
        //On change of event value update the the year options
        div_checkbook_contracts.ele('catastrophic_events').change(function () {
          let catas_event = div_checkbook_contracts.ele('catastrophic_events').val();
          updateEventYearValue("#edit-checkbook-contracts-year option",catas_event);
        });

        function onSubvendorStatusChange(div) {
          updateIncludeSubvendorsField(div);
        }

        //On year of "year" if chosen year is less than 2020 disable catastrophic event field
        div_checkbook_contracts.ele('year').change(function () {
          let yval =  ($(this).find("option:selected").text()).split(' ')[1];
          let contract_category = div_checkbook_contracts.ele('category').val();
          if ( contract_category == 'revenue' || yval < 2020){
            div_checkbook_contracts.ele('catastrophic_events').attr("disabled", "disabled");
            div_checkbook_contracts.ele('catastrophic_events').val('0');
          }
          else{
            div_checkbook_contracts.ele('catastrophic_events').removeAttr("disabled");
          }
        });


        function updateIncludeSubvendorsField(div) {
          var sub_vendor_status = div.ele('sub_vendor_status').val();
          var includes_sub_vendors = div.ele('includes_sub_vendors').val();

          if (['1', '2', '3', '4', '5', '6'].includes(sub_vendor_status)) {
            if (includes_sub_vendors === '2') {
              div.ele('includes_sub_vendors').html('<option value="0">Select Status</option>' +
                '<option value="2" selected>Yes</option>');
            }
            else {
              div.ele('includes_sub_vendors').html('<option value="0" selected>Select Status</option>' +
                '<option value="2">Yes</option>');
            }
          }
          if(sub_vendor_status === '0') {
            if (includes_sub_vendors === '2') {
              div.ele('includes_sub_vendors').html('<option value="0">Select Status</option>' +
                '<option value="2" selected>Yes</option>' +
                '<option value="3">No</option>' +
                '<option value="1">No Data Entered</option>' +
                '<option value="4">Not Required</option>');
            }
            else if (sub_vendor_status === '0') {
              div.ele('includes_sub_vendors').html('<option value="0" selected>Select Status</option>' +
                '<option value="2">Yes</option>' +
                '<option value="3">No</option>' +
                '<option value="1">No Data Entered</option>' +
                '<option value="4">Not Required</option>');
            }
          }
        }

        $("#edit-contracts-clear").click(function () {
          showHidePrimeAndSubFields(div_checkbook_contracts);
          div_checkbook_contracts.ele('includes_sub_vendors').html('<option value="0" selected>Select Status</option>' +
            '<option value="2">Yes</option>' +
            '<option value="3">No</option>' +
            '<option value="1">No Data Entered</option>' +
            '<option value="4">Not Required</option>');
        });
      }

      function advanced_search_payroll_init_autocomplete(){
        var pay_frequency = $('#edit-payroll-pay-frequency').val() || 0;
        var year = $('#edit-payroll-year').val() || 0;
        var data_source = $('input[name=payroll_advanced_search_domain_filter]:checked').val();
        var agency_id = 0;

        var solr_datasource = data_source;
        if ('checkbook_nycha' == data_source) {
          solr_datasource = 'nycha';
        }else{
          agency_id = $("#edit-checkbook-payroll-agencies").val() || 0;
        }

        var filters = {
          agency_id: agency_id,
          year: year,
          pay_frequency: pay_frequency
        };

        $('#edit-payroll-employee-name').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'civil_service_title',filters)});
        $('.ui-autocomplete-input').bind('autocompleteselect', function (event, ui) {$(this).parent().next().val(ui.item.label);});

      }

      // advanced-search-payroll
      function advanced_search_payroll_init() {
        advanced_search_payroll_init_autocomplete();

        $('#payroll-advanced-search').each(function () {
          $(this).focusout(function () {
            advanced_search_payroll_init_autocomplete();
          });
        });

        //On change of data source
        ///checkbook_advanced_search_clear_button.js sets this value by default
        $('input:radio[name=payroll_advanced_search_domain_filter]').click(function () {
          onChangeDataSource($('input[name=payroll_advanced_search_domain_filter]:checked').val());
        });

        function getPayrollYears(data_source){
          $.ajax({
            url: '/payroll/years/' + data_source
            , success: function (data) {
              var html = '';
              if (data[0]) {
                if (data[0] !== 'No Matches Found') {
                  $.each(data, function (key, year) {
                    html = html + '<option value="' + year.value + '" title="' + year.label +'">' + year.label + '</option>';
                  });
                }
                else {
                  html = html + '<option value="">' + data[0] + '</option>';
                }
              }
              $("#edit-payroll-year").html(html);
            }
          });
        }

        function onChangeDataSource(dataSource) {
          /* Reset all the fields for the data source */
          clearInputFields("#payroll-advanced-search", 'payroll', dataSource);
          getPayrollYears(dataSource);

          /** Hide Fiscal Year values for NYCHA **/
          if(dataSource == 'checkbook_nycha'){
            $(".form-item-checkbook-payroll-agencies").hide();
          }else{
            $(".form-item-checkbook-payroll-agencies").show();
          }
        }
      }

      // advanced-search-revenue
      function advanced_search_revenue_init() {
        //Hide EDC radio for Revenue data-source
        $('#edit-revenue-advanced-search-domain-filter-checkbook-oge').parent().hide();
        let revenue_div = function (data_source, div_contents) {
          this.div_elements = {
            'budget_fy': 'select[name=' + data_source + '_revenue_budget_fiscal_year]',
            'agency': 'select[name=' + data_source + '_revenue_agency]',
            'revenue_category': 'select[name=' + data_source + '_revenue_revenue_category]',
            'revenue_source': 'input:text[name=' + data_source + '_revenue_revenue_source]',
            'catastrophic_events' : 'select[name=' + data_source + '_revenue_catastrophic_events]',
            'adopted_from': 'input:text[name=' + data_source + '_revenue_adopted_budget_from]',
            'adopted_to': 'input:text[name=' + data_source + '_revenue_adopted_budget_to]',
            'recognized_from': 'input:text[name=' + data_source + '_revenue_recognized_from]',
            'recognized_to': 'input:text[name=' + data_source + '_revenue_recognized_to]',
            'fiscal_year': 'select[name=' + data_source + '_revenue_fiscal_year]',
            'funding_class': 'select[name=' + data_source + '_revenue_funding_class]',
            'revenue_class': 'input:text[name=' + data_source + '_revenue_revenue_class]',
            'fund_class': 'select[name=' + data_source + '_revenue_fund_class]',
            'modified_from': 'input:text[name=' + data_source + '_revenue_modified_from]',
            'modified_to': 'input:text[name=' + data_source + '_revenue_modified_to]',
            'expense_category': 'select[name=' + data_source + '_revenue_expense_category]',
            'resp_center':'select[name=' + data_source + '_revenue_responsibility_center]',
            'fundsrc':'select[name=' + data_source + '_revenue_fundsrc]',
            'program':'select[name=' + data_source + '_revenue_program]',
            'project':'select[name=' + data_source + '_revenue_project]',
            'budget_type':'select[name=' + data_source + '_revenue_budget_type]',
            'budget_type_chosen':'select[name=' + data_source + '_revenue_budget_type_chosen]',
            'nycha_budget_name':'select[name=' + data_source + '_revenue_nycha_budget_name]',
            'nycha_budget_name_chosen':'select[name=' + data_source + '_revenue_nycha_budget_name_chosen]',
            'nycha_revenue_category': 'input:text[name=' + data_source + '_revenue_nycha_revenue_category]',
            'remaining_from': 'input:text[name=' + data_source + '_revenue_remaining_from]',
            'remaining_to': 'input:text[name=' + data_source + '_revenue_remaining_to]',
          };

          this.data_source = data_source;
          this.div_contents = div_contents;
        };
        revenue_div.prototype.contents = function () {
          return this.div_contents;
        };
        revenue_div.prototype.ele = function (element_name) {
          let selector = this.div_elements[element_name];
          return this.div_contents.find(selector);
        };

        //Initialise divs for checkbook and checkbook_nycha
        let div_revenue_main = $("#revenue-advanced-search");
        let div_checkbook_revenue = new revenue_div('checkbook', div_revenue_main.children('div.checkbook'));
        let div_checkbook_revenue_nycha = new revenue_div('checkbook_nycha', div_revenue_main.children('div.checkbook-nycha'));

        let revenueBudgetTypeAlreadyLoaded = false;

        let reloadRevenueBudgetType = function(div) {
          let budget_name = encodeURIComponent(div.ele('nycha_budget_name').val());
          let budget_type = div.ele('budget_type').val();
          let data_source = 'checkbook_nycha';

          let url = '/data-feeds/revenue/budget_type/' + data_source + '/' + budget_name.toString().replace(/\//g, "__") + '/'  + true;
          if (url === revenueBudgetTypeAlreadyLoaded) {
            return;
          }
          revenueBudgetTypeAlreadyLoaded = url;
          $.ajax({
            url: url,
            success: function(data) {
              let html = '<option value="" >Select Budget Type</option>';
              if(data[0]){
                for (i = 0; i < data.length; i++) {
                  html = html + '<option value="' + data[i].value + '" title="' + data[i].value + '">' + data[i].text  + '</option>';
                }
              }
              div.ele('budget_type').html(html).val(budget_type).trigger("chosen:updated");
              if (budget_name !== div.ele('nycha_budget_name').val()) {
                reloadRevenueBudgetType(div);
              }
            }
          });
        }

        let nychaRevenueBudgetNamesAlreadyLoaded = false;

        let reloadNychaRevenueBudgetName = function(div) {
          let budget_type = encodeURIComponent(div.ele('budget_type').val());
          let budget_name = div.ele('nycha_budget_name').val();
          let data_source = 'checkbook_nycha';

          let url = '/data-feeds/revenue/budget_name/' + data_source + '/' + budget_type.toString().replace(/\//g, "__") + '/'  + true;
          if (url === nychaRevenueBudgetNamesAlreadyLoaded) {
            return;
          }
          nychaRevenueBudgetNamesAlreadyLoaded = url;
          $.ajax({
            url: url,
            success: function(data) {
              let html = '<option value="" >Select Budget Name</option>';
              if(data[0]){
                for (i = 0; i < data.length; i++) {
                  html = html + '<option value="' + data[i].value + '" title="' + data[i].value + '">' + data[i].text  + '</option>';
                }
              }
              div.ele('nycha_budget_name').html(html).val(budget_name).trigger("chosen:updated");
              if (budget_type !== div.ele('budget_type').val()) {
                reloadNychaRevenueBudgetName(div);
              }
            }
          });
        }

        //checkbook_advanced_search_clear_button.js sets this value by default
        $('input:radio[name=revenue_advanced_search_domain_filter]').click(function () {
          onRevenueDataSourceChange($('input[name=revenue_advanced_search_domain_filter]:checked').val());
        });

        let onRevenueDataSourceChange = function(dataSource) {
          /* Initialize view by data source */
          switch (dataSource) {
            case "checkbook_nycha":
              resetFields(div_checkbook_revenue_nycha.contents());
              initializeRevenueView(div_checkbook_revenue_nycha, dataSource);
              div_checkbook_revenue.contents().hide();
              div_checkbook_revenue_nycha.contents().show();

              //Reset Revenue Name and Revenue Code Chosen drop-downs
              div_checkbook_revenue_nycha.ele('budget_type').val("0").trigger("chosen:updated");
              div_checkbook_revenue_nycha.ele('nycha_budget_name').val("0").trigger("chosen:updated");
              reloadRevenueBudgetType(div_checkbook_revenue_nycha);
              reloadNychaRevenueBudgetName(div_checkbook_revenue_nycha);
              break;

            default:
              resetFields(div_checkbook_revenue.contents());
              initializeRevenueView(div_checkbook_revenue, dataSource);
              div_checkbook_revenue.contents().show();
              div_checkbook_revenue_nycha.contents().hide();
          }
        }

        function onRevenueBudgetFiscalYearChange(div) {
          //Setting data source value
          let data_source = $('input[type=radio][name=revenue_advanced_search_domain_filter]:checked').val();
          if(data_source == 'checkbook') {
            let budget_fiscal_year = (div.ele('budget_fy').val()) ? div.ele('budget_fy').val() : 0;
            let catastrophic_event = document.getElementById("edit-checkbook-revenue-catastrophic-events");
            let enabled_count = catastrophic_event.length;

            if(!(budget_fiscal_year === "0" || budget_fiscal_year === "122" || budget_fiscal_year === "121")){
              for (let i = 0; i < catastrophic_event.length; i++) {
                let event = catastrophic_event.options[i].text.toLowerCase();
                catastrophic_event.options[i].style.display = (event === 'covid-19')? "none":"";
                if(catastrophic_event.options[i].style.display === 'none') enabled_count--;
              }
              if(enabled_count <=1) disable_input(div.ele('catastrophic_events'));
            }
            else{
              for (let i = 0; i < catastrophic_event.length; i++) {
                let event = catastrophic_event.options[i].text.toLowerCase();
                if(event === 'covid-19'){
                  catastrophic_event.options[i].style.display = "";
                  break;
                }
              }
              enable_input(div.ele('catastrophic_events'));
            }
          }
        }

        function onRevenueCatastrophicEventChange(div){
          //Limit fiscal year to just 'FY 2020', 'FY 2021' and 'All years'
          let budget_fiscal_year = div.ele('budget_fy').attr("name");
          budget_fiscal_year = document.getElementsByName(budget_fiscal_year)[0];

          if(div.ele('catastrophic_events').val() === "1"){
            for (let i = 0; i < budget_fiscal_year.length; i++) {
              let year = budget_fiscal_year.options[i].text.toLowerCase();
              let include = (year === "all fiscal years" || year === "2021" || year === "2020");
              budget_fiscal_year.options[i].style.display = include ? '':'none';
            }
          }
          else{
            for (let i = 0; i < budget_fiscal_year.length; i++) {
              budget_fiscal_year.options[i].style.display = '';
            }
          }
      }

        //On change of "Catastrophic event"
        div_checkbook_revenue.ele('catastrophic_events').change(function(){
          onRevenueCatastrophicEventChange(div_checkbook_revenue);
        });

        //On change of "Budget Fiscal Year"
        div_checkbook_revenue.ele('budget_fy').change(function(){
          onRevenueBudgetFiscalYearChange(div_checkbook_revenue);
        });

        //Prevent the auto-complete from wrapping un-necessarily
        function fixAutoCompleteWrapping(divWrapper) {
          $(divWrapper.children()).find('input.ui-autocomplete-input:text').each(function () {
            $(this).data("autocomplete")._resizeMenu = function () {
              (this.menu.element).outerWidth('100%');
            }
          });
        }

        function initializeRevenueViewAutocomplete(div, data_source){
          //Set Solr datasource for auto-complete
          let solr_datasource = data_source;
          let agency_id = 0;
          if(data_source === 'checkbook') {
            agency_id = parseInt((div.ele('agency').val()) ? div.ele('agency').val() : 0);
            let fund_class_id = parseInt((div.ele('fund_class').val()) ? div.ele('fund_class').val() : 0);
            let budget_fiscal_year_id = parseInt((div.ele('budget_fy').val()) ? div.ele('budget_fy').val() : 0);
            let fiscal_year_id = parseInt((div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0);
            let revenue_category_id = parseInt((div.ele('revenue_category').val()) ? div.ele('revenue_category').val() : 0);
            let funding_class_code = parseInt((div.ele('funding_class').val()) ? div.ele('funding_class').val() : 0);
            let catastrophic_event_id = (div.ele('catastrophic_events').val()) ? div.ele('catastrophic_events').val() : 0;
            let filters = {
              fund_class_id: fund_class_id,
              agency_id: agency_id,
              revenue_budget_fiscal_year_id: budget_fiscal_year_id,
              fiscal_year_id: fiscal_year_id,
              revenue_category_id: revenue_category_id,
              funding_class_code: funding_class_code,
              event_id: catastrophic_event_id
            };
            div.ele('revenue_class').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_class_name',filters)});
            div.ele('revenue_source').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_source_name',filters)});
          }else if (data_source === 'checkbook_nycha'){
            solr_datasource = 'nycha';
            let fiscal_year_id = parseInt((div.ele('budget_fy').val()) ? div.ele('budget_fy').val() : 0);
            let exp_cat = parseInt((div.ele('expense_category').val()) ? div.ele('expense_category').val() : 0);
            let resp_center = parseInt((div.ele('resp_center').val()) ? div.ele('resp_center').val() : 0);
            let funding_src = parseInt((div.ele('fundsrc').val()) ? div.ele('fundsrc').val() : 0);
            let program = parseInt((div.ele('program').val()) ? div.ele('program').val() : 0);
            let project = parseInt((div.ele('project').val()) ? div.ele('project').val() : 0);
            let budget_type = (div.ele('budget_type').val() ? encodeURIComponent(div.ele('budget_type').val()) : 0);
            let budget_name = (div.ele('nycha_budget_name').val() ?encodeURIComponent(div.ele('nycha_budget_name').val()) : 0);
            let filters = {
              fiscal_year_id: fiscal_year_id,
              expenditure_type_id: exp_cat,
              responsibility_center_id: resp_center,
              funding_source_id: funding_src,
              program_phase_id: program,
              gl_project_id: project,
              budget_type: budget_type,
              budget_name: budget_name
            };
            div.ele('revenue_class').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_class',filters)});
            div.ele('nycha_revenue_category').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_category',filters)});
          }

          $('.ui-autocomplete-input').bind('autocompleteselect', function (event, ui) {
            $(this).parent().next().val(ui.item.label);
          });

          //prevent the auto-complete from wrapping un-necessarily
          fixAutoCompleteWrapping(div.contents());
        }

        //Initialize the field elements in the view based on data source selected
        function initializeRevenueView(div, dataSource) {
          initializeRevenueViewAutocomplete(div, dataSource);
          div_revenue_main.each(function () {
            $(this).focusout(function () {
              initializeRevenueViewAutocomplete(div, dataSource);
            });
          });
        }

        //NYCHA Revenue- Trigger Chosen input tool for 'Budget Type' and 'Budget Name'
        div_checkbook_revenue_nycha.ele('budget_type').chosen({
          no_results_text: "No matches found"
        });
        div_checkbook_revenue_nycha.ele('budget_type_chosen').find('.chosen-search-input').attr("placeholder", "Search Budget Type");

        div_checkbook_revenue_nycha.ele('nycha_budget_name').chosen({
          no_results_text: "No matches found"
        });
        div_checkbook_revenue_nycha.ele('nycha_budget_name_chosen').find('.chosen-search-input').attr("placeholder", "Search Budget Name");
        reloadRevenueBudgetType(div_checkbook_revenue_nycha);
        reloadNychaRevenueBudgetName(div_checkbook_revenue_nycha);

        //NYCHA Revenue - Drop-down change events
        div_checkbook_revenue_nycha.ele('nycha_budget_name').change(function () {
          reloadRevenueBudgetType(div_checkbook_revenue_nycha);
          if($(this).val() == 'Select Budget Name' || $(this).val() == '' || $(this).val() === 0){
            reloadNychaRevenueBudgetName(div_checkbook_revenue_nycha);
          }
        });

        div_checkbook_revenue_nycha.ele('budget_type').change(function () {
          reloadNychaRevenueBudgetName(div_checkbook_revenue_nycha);
          if($(this).val() == 'Select Budget Type' || $(this).val() == '' || $(this).val() === 0){
            reloadRevenueBudgetType(div_checkbook_revenue_nycha);
          }
        });
      }

// advanced-search-spending
      function advanced_search_spending_init() {
        var year, department_name, agency_id, spending_category_id, expenditure_object_code, minority_type_id, industry_type_id, datasource;

        var spending_div = function (data_source, div_contents) {
          this.div_elements = {
            'agency': 'select[name=' + data_source + '_spending_agency]',
            'dept': 'select[name=' + data_source + '_spending_department]',
            'exp_category': 'select[name=' + data_source + '_spending_expense_category]',
            'spending_category': 'select[name=' + data_source + '_spending_expense_type]',
            'industry': 'select[name=' + data_source + '_spending_industry]',
            'mwbe_category': 'select[name=' + data_source + '_spending_mwbe_category]',
            'catastrophic_events' : 'select[name=' + data_source + '_spending_catastrophic_events]',
            'payee_name': 'input:text[name=' + data_source + '_spending_payee_name]',
            'check_amt_from': 'input:text[name=' + data_source + '_spending_check_amount_from]',
            'check_amt_to': 'input:text[name=' + data_source + '_spending_check_amount_to]',
            'contract_id': 'input:text[name=' + data_source + '_spending_contract_num]',
            'document_id': 'input:text[name=' + data_source + '_spending_document_id]',
            'capital_project': 'input:text[name=' + data_source + '_spending_capital_project]',
            'date_filter': 'input:radio[name=' + data_source + '_spending_date_filter]',
            'date_filter_year': 'input:radio[name=' + data_source + '_spending_date_filter][value=0]',
            'date_filter_issue_date': 'input:radio[name=' + data_source + '_spending_date_filter][value=1]',
            'date_filter_checked': 'input:radio[name=' + data_source + '_spending_date_filter]:checked',
            'fiscal_year': 'select[name="' + data_source + '_spending_fiscal_year"]',
            'issue_date_from': 'input:text[name="' + data_source + '_spending_issue_date_from[date]"]',
            'issue_date_to': 'input:text[name="' + data_source + '_spending_issue_date_to[date]"]',
            'commodity_line': 'input:text[name=' + data_source + '_spending_commodity_line]',
            'entity_contract_number': 'input:text[name=' + data_source + '_spending_entity_contract_number]',
            'budget_name': 'input:text[name=' + data_source + '_spending_budget_name_text]',
            'fundsrc': 'select[name=' + data_source + '_spending_fundsrc]',
            'resp_center': 'select[name=' + data_source + '_spending_responsibility_center]',
            'vendor_name': 'input:text[name=' + data_source + '_spending_vendor_name]',
            'po_type': 'select[name=' + data_source + '_spending_purchase_order_type]',
            'amount_spent_from': 'input:text[name=' + data_source + '_spending_amount_spent_from]',
            'amount_spent_to': 'input:text[name=' + data_source + '_spending_amount_spent_to]'
          };
          this.data_source = data_source;
          this.div_contents = div_contents;
        };
        spending_div.prototype.contents = function () {
          return this.div_contents;
        };
        spending_div.prototype.ele = function (element_name) {
          var selector = this.div_elements[element_name];
          return this.div_contents.find(selector);
        };

        var div_spending_main = $("#spending-advanced-search");
        var div_checkbook_spending = new spending_div('checkbook', div_spending_main.children('div.checkbook'));
        var div_checkbook_spending_oge = new spending_div('checkbook_oge', div_spending_main.children('div.checkbook-oge'));
        var div_checkbook_spending_nycha = new spending_div('checkbook_nycha', div_spending_main.children('div.checkbook-nycha'));

        //On change of "Agency"
        div_checkbook_spending.ele('agency').change(function () {
          onAgencyChange(div_checkbook_spending);
        });

        //Populate Spending Domain Expense Categories drop-down
        function loadSpendingExpenseCategories(div, data_source) {
          var year = 0;
          if (div.ele('date_filter_checked').val() === '0') {
            year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
          }
          var agency = 0;
          if(data_source == 'checkbook') {
            agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            if(agency == 0)
              return;
          }
          var exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
          var dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
          dept = dept.toString().replace(/\//g, "__");

          $.ajax({
            url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept + '/' + exptype + '/' + data_source
            , success: function (data) {
              var html = '<option select="selected" value="0" >Select Expense Category</option>';
              if (data[0]) {
                if (data[0] !== 'No Matches Found') {
                  $.each(data, function (key, exp_cat) {
                    // Remove null data from drop down
                    if (exp_cat.name !== '') {
                      html = html + '<option value="' + exp_cat.code + '" title="' + exp_cat.title + '">' + exp_cat.name + '</option>';
                    }
                  });
                }
                else {
                  html = html + '<option value="">' + data[0] + '</option>';
                }
              }
              div.ele('exp_category').html(html);
            }
          });
          enable_input(div.ele('exp_category'));
        }

        //Populate Spending Domain departments drop-down
        function loadSpendingDepartments(div, data_source){
          var year = 0;
          if (div.ele('date_filter_checked').val() === '0') {
            year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
          }
          var agency = 0;
          if(data_source == 'checkbook') {
            agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            if(agency == 0)
              return;
          }
          var exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;

          $.ajax({
            url: '/advanced-search/autocomplete/spending/department/' + year + '/' + agency + '/' + exptype + '/' + data_source
            , success: function (data) {
              var html = '<option select="selected" value="0" >Select Department</option>';
              if (data[0]) {
                if (data[0] !== 'No Matches Found') {
                  for (var i = 0; i < data.length; i++) {
                    html = html + '<option value="' + data[i] + ' ">' + data[i] + '</option>';
                  }
                }
                else {
                  html = html + '<option value="">' + data[0] + '</option>';
                }
              }
              div.ele('dept').html(html);
            }
          });
          enable_input(div.ele('dept'));
        }

        //When agency is selected
        function onAgencyChange(div) {
          //Setting 'data source' value
          var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();

          if(data_source === 'checkbook') {
            var agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            if(agency === '0') {
              div.ele('dept').val('0');
              div.ele('exp_category').val('0');
              disable_input(div.ele('dept'));
              disable_input(div.ele('exp_category'));
            }else{
              //Load departments and expense categories drop-downs
              loadSpendingDepartments(div, data_source);
              loadSpendingExpenseCategories(div, data_source);
            }
          }else {
            //Load departments and expense categories drop-downs
            loadSpendingDepartments(div, data_source);
            loadSpendingExpenseCategories(div, data_source);
          }
        }

        //On change of "Department"
        div_checkbook_spending.ele('dept').change(function () {
          onDeptChange(div_checkbook_spending);
        });
        div_checkbook_spending_oge.ele('dept').change(function () {
          onDeptChange(div_checkbook_spending_oge);
        });
        div_checkbook_spending_nycha.ele('dept').change(function () {
          onDeptChange(div_checkbook_spending_nycha);
        });

        function onDeptChange(div) {
          var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
          loadSpendingExpenseCategories(div, data_source);
        }

        //On change of "Expense Type"
        div_checkbook_spending.ele('spending_category').change(function () {
          onExpenseTypeChange(div_checkbook_spending);
        });
        div_checkbook_spending_oge.ele('spending_category').change(function () {
          onExpenseTypeChange(div_checkbook_spending_oge);
        });
        div_checkbook_spending_nycha.ele('spending_category').change(function () {
          onExpenseTypeChange(div_checkbook_spending_nycha);
        });

        function onExpenseTypeChange(div) {
          var exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
          var year = 0;
          if (div.ele('date_filter_checked').val() === '0') {
            year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
          }
          //Setting data source value
          var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
          if(data_source === 'checkbook_nycha') {
            //NYCHA - disabling fields based on Spending category selected
            if (exptype === '2') {
              disable_input([div.ele('vendor_name'), div.ele('contract_id'),div.ele('document_id'),
                             div.ele('industry'), div.ele('fundsrc'), div.ele('resp_center'),
                             div.ele('po_type'), div.ele('amount_spent_from'), div.ele('amount_spent_to')]);
              enable_input([div.ele('dept'), div.ele('exp_category')]);
              div.ele('contract_id').val("");
              div.ele('vendor_name').val("");
            }else if(exptype == 1) {
              disable_input([ div.ele('dept'),div.ele('contract_id'),
                             div.ele('industry'), div.ele('po_type'),]);
              enable_input([div.ele('vendor_name'),div.ele('document_id'),div.ele('resp_center'),
                            div.ele('amount_spent_from'), div.ele('amount_spent_to'),div.ele('exp_category'),div.ele('fundsrc')]);
            }else if(exptype == 4) {
              disable_input([div.ele('dept'),div.ele('contract_id')]);
              enable_input([ div.ele('exp_category'),div.ele('vendor_name'),div.ele('document_id'),
                div.ele('industry'), div.ele('fundsrc'), div.ele('resp_center'),
                div.ele('po_type'), div.ele('amount_spent_from'), div.ele('amount_spent_to')]);
            }else{
              enable_input([div.ele('dept'), div.ele('exp_category'),div.ele('vendor_name'),div.ele('document_id'),
                div.ele('industry'), div.ele('fundsrc'), div.ele('resp_center'), div.ele('contract_id'),
                div.ele('po_type'), div.ele('amount_spent_from'), div.ele('amount_spent_to')]);
            }
          }else {
            //CITYWIDE and OGE - disabling fields based on Spending category selected
            if (exptype === '2') {
              disable_input([div.ele('contract_id'),div.ele('payee_name'),div.ele('catastrophic_events')]);
              div.ele('contract_id').val("");
              div.ele('payee_name').val("");
              onCatastrophicEventChange(div);
            }
            else if (exptype === '4') {
              disable_input([div.ele('contract_id'),div.ele('catastrophic_events')]);
              div.ele('contract_id').val("");
              enable_input(div.ele('payee_name'));
              onCatastrophicEventChange(div);
            }
            else {
              enable_input([div.ele('contract_id'), div.ele('payee_name'),div.ele('catastrophic_events')]);
              onFiscalYearChange(div);
            }
          }
        }

        //On change of "Catastrophic event"
        div_checkbook_spending.ele('catastrophic_events').change(function(){
          onCatastrophicEventChange(div_checkbook_spending);
        });

        function onCatastrophicEventChange(div){
            //Selecting 'COVID-19' option causes the following changes:
            //Data within following fields update: Payee Name, Contract ID, Document ID, Capital Project
            //Limit fiscal year to just 'FY 2020', 'FY 2021' and 'All years'
            let fiscal_year = div.ele('fiscal_year').attr("name");
            fiscal_year = document.getElementsByName(fiscal_year)[0];

            if(div.ele('catastrophic_events').val() === "1"){
              for (let i = 0; i < fiscal_year.length; i++) {
                let year = fiscal_year.options[i].text.toLowerCase();
                let include = (year === "fy 2020" || year === "fy 2021" || year === "all years");
                fiscal_year.options[i].style.display = include ? '':'none';
              }
            }
            else{
              for (let i = 0; i < fiscal_year.length; i++) {
                fiscal_year.options[i].style.display = '';
              }
            }
        }

        //On change of "Fiscal Year"
        div_checkbook_spending.ele('fiscal_year').change(function () {
          onFiscalYearChange(div_checkbook_spending);
        });
        div_checkbook_spending_oge.ele('fiscal_year').change(function () {
          onFiscalYearChange(div_checkbook_spending_oge);
        });
        div_checkbook_spending_nycha.ele('fiscal_year').change(function () {
          onFiscalYearChange(div_checkbook_spending_nycha);
        });

        function onFiscalYearChange(div) {
          //Setting data source value
          var data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
          var agency = 0;
          if(data_source == 'checkbook') {
            let fiscal_year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            let exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
            if(fiscal_year && !(fiscal_year === "fy~all" || fiscal_year === "fy~122" || fiscal_year === "fy~121")) disable_input(div.ele('catastrophic_events'));
            else if(exptype == '2' || exptype == '4') disable_input(div.ele('catastrophic_events'));
            else enable_input(div.ele('catastrophic_events'));
            agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            if(agency == 0)
              return;
          }
          //Reload Department and Expense Category drop-downs for CityWide
          loadSpendingExpenseCategories(div, data_source);
          loadSpendingDepartments(div, data_source);
        }

        //On clicking "Clear"
        $('div.spending-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
          onClearClick(div_checkbook_spending);
          e.preventDefault();
        });
        $('div.spending-submit.checkbook-oge').find('input:submit[value="Clear All"]').click(function (e) {
          onClearClick(div_checkbook_spending_oge);
          e.preventDefault();
        });
        $('div.spending-submit.checkbook-nycha').find('input:submit[value="Clear All"]').click(function (e) {
          onClearClick(div_checkbook_spending_nycha);
          e.preventDefault();
        });

        function onClearClick(div) {
          disable_input(div.ele('exp_category'));
          disable_input(div.ele('dept'));
          div.ele('spending_category').val('Total Spending');
          div.ele('date_filter_year').attr('checked', 'checked');
          div.ele('date_filter_issue_date').removeAttr('checked');

          //reset Expense Type
          if (div.ele('spending_category').val() === '2') {
            disable_input(div.ele('contract_id'));
            div.ele('contract_id').val("");
            disable_input(div.ele('payee_name'));
            div.ele('payee_name').val("");
          }
          else if (div.ele('spending_category').val() === '4') {
            disable_input(div.ele('contract_id'));
            div.ele('contract_id').val("");
            disable_input(div.ele('payee_name'));
          }
          else {
            enable_input(div.ele('contract_id'));
            enable_input(div.ele('payee_name'));
          }

          //reset Date Filter
          var value = div.ele('date_filter_checked').val();
          if (value === '0') {
            enable_input(div.ele('fiscal_year'));
            disable_input(div.ele('issue_date_from'));
            disable_input(div.ele('issue_date_to'));
          } else if (value === '1') {
            disable_input(div.ele('fiscal_year'));
            enable_input(div.ele('issue_date_from'));
            enable_input(div.ele('issue_date_to'));
          }
        }

        //On click of "Date Filter"
        div_checkbook_spending.ele('date_filter').click(function () {
          onDateFilterClick(div_checkbook_spending);
        });
        div_checkbook_spending_oge.ele('date_filter').click(function () {
          onDateFilterClick(div_checkbook_spending_oge);
        });
        div_checkbook_spending_nycha.ele('date_filter').click(function () {
          onDateFilterClick(div_checkbook_spending_nycha);
        });

        function onDateFilterClick(div) {
          var value = div.ele('date_filter_checked').val();
          div.ele('fiscal_year').val(0);
          div.ele('issue_date_from').val("");
          div.ele('issue_date_to').val("");
          if (value === "0") {
            enable_input(div.ele('fiscal_year'));
            disable_input(div.ele('issue_date_from'));
            disable_input(div.ele('issue_date_to'));
          } else if (value === "1") {
            disable_input(div.ele('fiscal_year'));
            enable_input(div.ele('issue_date_from'));
            enable_input(div.ele('issue_date_to'));
          }
        }

        //On change of data source
        //checkbook_advanced_search_clear_button.js sets this value by default
        $('input:radio[name=spending_advanced_search_domain_filter]').click(function () {
          onChangeDataSource($('input[name=spending_advanced_search_domain_filter]:checked').val());
        });

        function onChangeDataSource(dataSource) {

          /* Reset all the fields for the data source */
          resetFields(div_checkbook_spending.contents());
          resetFields(div_checkbook_spending_oge.contents());
          resetFields(div_checkbook_spending_nycha.contents());

          /* Initialize view by data source */
          switch (dataSource) {
            case "checkbook_oge":
              resetFields(div_checkbook_spending_oge.contents());
              initializeSpendingView(div_checkbook_spending_oge, dataSource);
              div_checkbook_spending.contents().hide();
              div_checkbook_spending_oge.contents().show();
              div_checkbook_spending_nycha.contents().hide();
              //Load department and spending categories by default for OGE
              loadSpendingDepartments(div_checkbook_spending_oge, dataSource);
              loadSpendingExpenseCategories(div_checkbook_spending_oge, dataSource);

              //Disable issue date radio button for OGE
              disable_input(div_checkbook_spending_oge.ele('date_filter_issue_date'));
              disable_input(div_checkbook_spending_oge.ele('issue_date_from'));
              disable_input(div_checkbook_spending_oge.ele('issue_date_to'));

              break;
            case "checkbook_nycha":
              resetFields(div_checkbook_spending_nycha.contents());
              initializeSpendingView(div_checkbook_spending_nycha, dataSource);
              div_checkbook_spending.contents().hide();
              div_checkbook_spending_oge.contents().hide();
              div_checkbook_spending_nycha.contents().show();
              //Load department and spending categories by default for NYCHA
              loadSpendingDepartments(div_checkbook_spending_nycha, dataSource);
              loadSpendingExpenseCategories(div_checkbook_spending_nycha, dataSource);

              enable_input(div_checkbook_spending_nycha.ele('date_filter_issue_date'));
              //Disable issue date inputs when year radio button is checked
              if (div_checkbook_spending_nycha.ele('date_filter_checked').val() === '0') {
                disable_input([div_checkbook_spending_nycha.ele('issue_date_from'), div_checkbook_spending_nycha.ele('issue_date_to')]);
              }
              //Enable NYCHA Spending fields
              enable_input([div_checkbook_spending_nycha.ele('dept'), div_checkbook_spending_nycha.ele('exp_category'),
                div_checkbook_spending_nycha.ele('vendor_name'), div_checkbook_spending_nycha.ele('document_id'),
                div_checkbook_spending_nycha.ele('industry'), div_checkbook_spending_nycha.ele('fundsrc'),
                div_checkbook_spending_nycha.ele('resp_center'), div_checkbook_spending_nycha.ele('contract_id'),
                div_checkbook_spending_nycha.ele('po_type'), div_checkbook_spending_nycha.ele('amount_spent_from'),
                div_checkbook_spending_nycha.ele('amount_spent_to')]);

              break;

            default:
              resetFields(div_checkbook_spending.contents());
              initializeSpendingView(div_checkbook_spending, dataSource);
              div_checkbook_spending.contents().show();
              div_checkbook_spending_oge.contents().hide();
              div_checkbook_spending_nycha.contents().hide();

              enable_input(div_checkbook_spending.ele('date_filter_issue_date'));
              //Disable issue date inputs when year radio button is checked
              if (div_checkbook_spending.ele('date_filter_checked').val() === '0') {
                disable_input([div_checkbook_spending.ele('issue_date_from'), div_checkbook_spending.ele('issue_date_to')]);
              }
              //Disable Department and Expense Category drop-downs when agency is not selected
              agency_id = parseInt((div_checkbook_spending.ele('agency').val()) ? div_checkbook_spending.ele('agency').val() : 0);
              if (!agency_id) {
                disable_input([div_checkbook_spending.ele('dept'), div_checkbook_spending.ele('exp_category')]);
              }

              break;
          }
        }

        function initializeSpendingViewAutocomplete(div, data_source){
          //Set Solr datasource for auto-complete
          var solr_datasource = data_source;
          if (data_source === 'checkbook_nycha'){
            solr_datasource = 'nycha'
          }
          var agency_id = 0;
          if(data_source === 'checkbook') {
            agency_id = parseInt((div.ele('agency').val()) ? div.ele('agency').val() : 0);
          }
          year = 0;
          var year_id = 0;
          if (div.ele('date_filter_checked').val() === '0') {
            year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            if(year.indexOf("fy") >= 0){
              year_id = year.split('~')[1];
            }
          }
          department_name = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
          department_name = department_name.toString().replace(/\//g, "__");
          expenditure_object_code = (div.ele('exp_category').val()) ? (div.ele('exp_category').val()) : 0;
          spending_category_id = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
          minority_type_id = (div.ele('mwbe_category').val()) ? (div.ele('mwbe_category').val()) : 0;
          industry_type_id = (div.ele('industry').val()) ? (div.ele('industry').val()) : 0;
          let catastrophic_event_id = (div.ele('catastrophic_events').val()) ? div.ele('catastrophic_events').val() : 0;
          datasource = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
          // enable purchase order filter for nycha
          let agreement_type_code = (div.ele('po_type').val()) ? (div.ele('po_type').val()) : 0;
          let resp_center_id = (div.ele('resp_center').val()) ? (div.ele('resp_center').val()) : 0;
          let fund_src_id = (div.ele('fundsrc').val()) ? (div.ele('fundsrc').val()) : 0;
          var filters = {
            department_name: department_name,
            agency_id: agency_id,
            expenditure_object_code: expenditure_object_code,
            spending_category_id: spending_category_id,
            minority_type_id: minority_type_id,
            industry_type_id: industry_type_id,
            fiscal_year_id: year_id,
            agreement_type_code: agreement_type_code,
            responsibility_center_id:resp_center_id,
            funding_source_id:fund_src_id,
            event_id: catastrophic_event_id
          };

          div.ele('payee_name').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'vendor_name', filters)});
          div.ele('contract_id').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'contract_number', filters)});
          div.ele('capital_project').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'reporting_code', filters)});
          div.ele('document_id').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'expense_id', filters)});
          div.ele('commodity_line').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'spending_commodity_line', filters)});
          div.ele('budget_name').autocomplete({source:$.fn.autoCompleteSourceUrl(solr_datasource, 'spending_budget_name', filters)});
          div.ele('entity_contract_number').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'spending_entity_contract_number', filters)});
          div.ele('vendor_name').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'vendor_name', filters)});
          div.ele('document_id').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'document_id', filters)});

          $('.ui-autocomplete-input').bind('autocompleteselect', function (event, ui) {
            $(this).parent().next().val(ui.item.label);
          });

          //prevent the auto-complete from wrapping un-necessarily
          fixAutoCompleteWrapping(div.contents());

        }

        //Initialize the field elements in the view based on data source selected
        function initializeSpendingView(div, dataSource) {
          initializeSpendingViewAutocomplete(div, dataSource);
          div_spending_main.each(function () {
            $(this).focusout(function () {
              initializeSpendingViewAutocomplete(div, dataSource);
            });
          });
        }

        //Prevent the auto-complete from wrapping un-necessarily
        function fixAutoCompleteWrapping(divWrapper) {
          $(divWrapper.children()).find('input.ui-autocomplete-input:text').each(function () {
            $(this).data("autocomplete")._resizeMenu = function () {
              (this.menu.element).outerWidth('100%');
            }
          });
        }
      }

      function advanced_search_buttons_init() {
        $('#edit-payroll-clear').click(function (e) {
          //$('#checkbook-advanced-search-form')[0].reset(); //this works
          let data_source = 'checkbook';
          let href = window.location.href.replace(/(http|https):\/\//, '');
          if(href.indexOf('datasource/checkbook_oge') !== -1){
            data_source = 'checkbook_oge';
          }else if(href.indexOf('datasource/checkbook_nycha') !== -1){
            data_source = 'checkbook_nycha';
          }

          clearInputFieldByDataSource('#payroll-advanced-search', 'payroll', data_source);
          $(this).blur();
          /* Remove focus */
          e.preventDefault();
        });
        $('div.budget-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
          clearInputFieldByDataSource("#budget-advanced-search", 'budget', 'checkbook');
          $(this).blur();
          /* Remove focus */
          e.preventDefault();
        });
        $('div.budget-submit.checkbook-nycha').find('input:submit[value="Clear All"]').click(function (e) {
          clearInputFieldByDataSource("#budget-advanced-search", 'budget', 'checkbook_nycha');
          $(this).blur();
          /* Remove focus */
          e.preventDefault();
        });
        $('div.revenue-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
          clearInputFieldByDataSource("#revenue-advanced-search", 'revenue', 'checkbook');
          $(this).blur();
          /* Remove focus */
          e.preventDefault();
        });
        $('div.revenue-submit.checkbook-nycha').find('input:submit[value="Clear All"]').click(function (e) {
          clearInputFieldByDataSource("#revenue-advanced-search", 'revenue', 'checkbook_nycha');
          $(this).blur();
          /* Remove focus */
          e.preventDefault();
        });
        $('div.contracts-submit.checkbook-nycha').find('input:submit[value="Clear All"]').click(function (e) {
          clearInputFieldByDataSource("#contracts-advanced-search", 'contracts', 'checkbook_nycha');
          $(this).blur();
          /* Remove focus */
          e.preventDefault();
        });
        $('div.contracts-submit.checkbook-oge').find('input:submit[value="Clear All"]').click(function (e) {
          clearInputFieldByDataSource("#contracts-advanced-search", 'contracts', 'checkbook_oge');
          $(this).blur();
          /* Remove focus */
          e.preventDefault();
        });
        $('div.contracts-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
          clearInputFieldByDataSource("#contracts-advanced-search", 'contracts', 'checkbook');
          $(this).blur();
          /* Remove focus */
          e.preventDefault();
        });
        $('div.spending-submit.checkbook-oge').find('input:submit[value="Clear All"]').click(function (e) {
          clearInputFieldByDataSource("#spending-advanced-search", 'spending', 'checkbook_oge');
          $(this).blur();
          /* Remove focus */
          e.preventDefault();
        });
        $('div.spending-submit.checkbook-nycha').find('input:submit[value="Clear All"]').click(function (e) {
          clearInputFieldByDataSource("#spending-advanced-search", 'spending', 'checkbook_nycha');
          $(this).blur();
          /* Remove focus */
          e.preventDefault();
        });
        $('div.spending-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
          clearInputFieldByDataSource("#spending-advanced-search", 'spending', 'checkbook');
          $(this).blur();
          /* Remove focus */
          e.preventDefault();
        });
      }

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

        clearInputFieldByDataSource("#payroll-advanced-search", 'payroll', data_source);
        clearInputFieldByDataSource("#contracts-advanced-search", 'contracts', data_source);
        clearInputFieldByDataSource("#spending-advanced-search", 'spending', data_source);
        clearInputFieldByDataSource("#budget-advanced-search", 'budget', data_source);
        clearInputFieldByDataSource("#revenue-advanced-search", 'revenue', data_source);

        return active_accordion_window;
      }

      function initializeAccordionAttributes(accordion_type) {

        advanced_search_bootstrap_domains();

        $('#advanced-search-rotator').css('display', 'none');
        $("#block-checkbook-advanced-search-checkbook-advanced-search-form").find(":input").removeAttr("disabled");
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
        if (param && (param.indexOf('id=>') > -1)){
          return param.split('~')[0].split('=>')[1];
        }
        return param;
      }

      /* autoCompleteSourceUrl function exist in global.js
      /*  function autoCompleteSource(solr_datasource, facet, filters) {
        let url = '/advanced_autocomplete/';

        let fq = '';

        Object.keys(filters).forEach(function (key) {
          let val = extractId(String(filters[key]));
          if (val && ("0" !== val)){
            // remove trailing space from search terms
            fq += '*!*'+key+'='+val.trim();
          }
        });

        let search_term = '/?search_term=' + fq;

        return url+solr_datasource+'/'+facet+search_term;
      }*/

      /* For oge, Budget, Revenue & Payroll are not applicable and are disabled */
      function disableAccordionSections(data_source) {
        //Disable Payroll, Budget and Revenue for EDC
        if (data_source === "checkbook_oge") {
          let ogeDisabledDomains = ["budget", "Revenue", "Payroll"];
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


      /**
       * CREATE ALERT FUNCTIONS
       */

      function create_alert_bootstrap() {
        let href = window.location.href.replace(/(http|https):\/\//, '');
        let n = href.indexOf('?');
        href = href.substring(0, n !== -1 ? n : href.length);
        let page_clicked_from = this.id ? this.id : href.split('/')[1];
        let data_source = "checkbook";
        if(href.indexOf('datasource/checkbook_oge') !== -1){
          data_source = 'checkbook_oge';
        }else if(href.indexOf('datasource/checkbook_nycha') !== -1){
          data_source = 'checkbook_nycha';
        }
        let active_accordion_window = initializeActiveAccordionWindow(page_clicked_from, data_source);

        let createAlertsDiv = "<span class='create-alert-instructions'>Follow the three step process to schedule alert.<ul><li>Please select one of the following domains and also select the desired filters.<\/li><li>Click 'Next' button to view and customize the results.<\/li><li>Click 'Clear All' to clear out the filters applied.<\/li><\/ul><\/br></span>";
        createAlertsDiv += "<span style='visibility: hidden;display: none;' class='create-alert-results-loading'><div id='loading-icon'><img src='/sites/all/themes/checkbook/images/loading_large.gif'></div></span>";
        createAlertsDiv += "<div class='create-alert-customize-results' style='display: none'><br/><br/><br/></div>";
        createAlertsDiv += "<div class='create-alert-schedule-alert' style='display: none'>&nbsp;<br/><br/></div>";
        createAlertsDiv = "<div class='create-alert-view'>" + createAlertsDiv + "</div>";
        $('.create-alert-view').replaceWith(createAlertsDiv);

        //Initialize Attributes and styling
        initializeAccordionAttributes('advanced_search_create_alerts');

        $('#block-checkbook-advanced-search-checkbook-advanced-search-form').dialog({
          title: "<span class='create-alert-header'><span class='active'>1. Select Criteria</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>2. Customize Results</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>3. Schedule Alert</span></span>",
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
        $('#block-checkbook-advanced-search-checkbook-advanced-search-form').css('min-height', '0%');

        $('.advanced-search-accordion').accordion({
          autoHeight: false,
          active: active_accordion_window
        });

        /* For EDC, Budget, Revenue & Payroll are not applicable and are disabled */
        disableAccordionSections(data_source);

        clearInputFieldByDataSource("#payroll-advanced-search", 'payroll', data_source);
        clearInputFieldByDataSource("#contracts-advanced-search", 'contracts', data_source);
        clearInputFieldByDataSource("#spending-advanced-search", 'spending', data_source);
        clearInputFieldByDataSource("#budget-advanced-search", 'budget', data_source);
        clearInputFieldByDataSource("#revenue-advanced-search", 'revenue', data_source);

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
        $("#block-checkbook-advanced-search-checkbook-advanced-search-form").addClass('disable_me');
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
        $("#block-checkbook-advanced-search-checkbook-advanced-search-form").removeClass('disable_me');
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
        }
        else {
          disable_input('#edit-back-submit');
        }

        $('.tableHeader').each(function (i) {
          if ($(this).find('.contCount').length > 0) {
            $(this).find('h2').append("<span class='contentCount'>" + $('span.contCount').html() + '</span>');
            $(this).find('.contCount').remove();
          }
        });

      });

      $.fn.onScheduleAlertNextClick = function (step) {
        var next_step = '';
        var header = '';
        var instructions = '';

        /* Clear auto-completes */
        $(".ui-autocomplete-input").autocomplete("close");

        switch (step) {
          case 'select_criteria':
            next_step = 'customize_results';

            /* Hide the rotator */
            $('#advanced-search-rotator').css('display', 'none');
            create_alert_form_enable();

            /* Hide the iFrame */
            $('#checkbook_advanced_search_result_iframe').css('visibility', 'hidden');

            /* Show loading icon */
            $('.create-alert-results-loading').css('visibility', 'visible');
            $('.create-alert-results-loading').css('display', 'block');

            /* Show the results page */
            $('.create-alert-customize-results').css('display', 'block');

            /* Update width of dialog dimension */
            $('div.ui-dialog-titlebar').css('width', 994);
            $('div.ui-dialog').css('width', 1023);
            $('div.ui-dialog').css('height', '385px');

            /* Update header */
            header = "<span class='create-alert-header'><span class='inactive'>1. Select Criteria</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='active'>2. Customize Results</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>3. Schedule Alert</span></span>";
            $('.create-alert-header').replaceWith(header);

            /* Update wizard instructions */
            instructions = "<span class='create-alert-instructions'>Further narrow down the results using the 'Narrow down your search' functionality.<ul><li>Click 'Export' button to download the results into excel.<\/li><li>Click 'Back' to go back to Step1: Select Criteria.<\/li><li>Click 'Next' button to Schedule Alert.<\/li><\/ul><\/br></span>";
            $('.create-alert-instructions').replaceWith(instructions);

            /* Hide the accordion */
            $('.advanced-search-accordion').css('display', 'none');

            /* Buttons */
            $('#edit-next-submit').css('display', 'inline');
            $('#edit-back-submit').css('display', 'inline');

            /* Update hidden field for new step */
            $('input:hidden[name="step"]').val(next_step);

            break;

          case 'customize_results':
            next_step = 'schedule_alert';

            /* Update width of dialog dimension */
            $('div.ui-dialog-titlebar').css('width', 'auto');
            $('div.ui-dialog').css('height', 'auto');
            $('div.ui-dialog').css('width', '800px');

            /* Update header */
            header = "<span class='create-alert-header'><span class='inactive'>1. Select Criteria</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>2. Customize Results</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='active'>3. Schedule Alert</span></span>";
            $('.create-alert-header').replaceWith(header);

            /* Update wizard instructions */
            instructions = "<span class='create-alert-instructions'><ul><li>Checkbook alerts will notify you by email when new results matching your current search criteria are available. Use options below for alert settings.<\/li><li>Provide email address, in order to receive alerts. Emails will be sent based on the frequency selected and only after the minimum number of additional results entered has been reached since the last alert.<\/li><li>Click 'Back' to go back to Step2: Customize Results.<\/li><li>Click 'Schedule Alert' to schedule the alert.<\/li><li>The user shall receive email confirmation once the alert is scheduled.<\/li><\/ul></span>";
            $('.create-alert-instructions').replaceWith(instructions);

            /* Hide close button */
            $('a.ui-dialog-titlebar-close').hide();

            /* Buttons */
            $('div.create-alert-submit #edit-next-submit').val('Schedule Alert');
            disable_input([
              '#edit-next-submit',
              '#edit-back-submit'
            ]);
            $('#edit-next-submit').css('display', 'inline');
            $('#edit-back-submit').css('display', 'inline');

            /* Hide the results page */
            $('.create-alert-customize-results').css('display', 'none');

            /* Show loading icon */
            $('.create-alert-results-loading').css('visibility', 'visible');
            $('.create-alert-results-loading').css('display', 'block');


            /* Show the schedule alert page */
            $('.create-alert-schedule-alert').css('display', 'block');

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

        /* Clear auto-completes */
        $(".ui-autocomplete-input").autocomplete("close");

        switch (step) {
          case 'customize_results':
            previous_step = 'select_criteria';

            //enable form
            enable_input("#block-checkbook-advanced-search-checkbook-advanced-search-form :input");

            /* Update width of dialog dimension */
            $('div.ui-dialog-titlebar').css('width', 'auto');
            $('div.ui-dialog').css('height', 'auto');
            $('div.ui-dialog').css('width', '800px');

            /* Update header */
            header = "<span class='create-alert-header'><span class='active'>1. Select Criteria</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>2. Customize Results</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>3. Schedule Alert</span></span>";
            $('.create-alert-header').replaceWith(header);

            /* Update wizard instructions */
            instructions = "<span class='create-alert-instructions'>Follow the three step process to schedule alert.<ul><li>Please select one of the following domains and also select the desired filters.<\/li><li>Click 'Next' button to view and customize the results.<\/li><li>Click 'Clear All' to clear out the filters applied.<\/li><\/ul><\/br></span>";
            $('.create-alert-instructions').replaceWith(instructions);

            /* Hide the results page */
            $('.create-alert-customize-results').css('display', 'none');

            /* Hide results buttons */
            $('.create-alert-submit').css('display', 'none');

            /* Buttons */
            $('#edit-next-submit').css('display', 'none');
            $('#edit-back-submit').css('display', 'none');

            /* Show the accordion and disable the input fields based on the selection criteria */
            $('.advanced-search-accordion').css('display', 'block');
            //disableInputFields();

            break;

          case 'schedule_alert':
            previous_step = 'customize_results';

            /* Update width of dialog dimension */
            $('div.ui-dialog-titlebar').css('width', 994);
            $('div.ui-dialog').css('width', 1023);
            $('div.ui-dialog').css('height', 'auto');

            /* Update header */
            header = "<span class='create-alert-header'><span class='inactive'>1. Select Criteria</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='active'>2. Customize Results</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>3. Schedule Alert</span></span>";
            $('.create-alert-header').replaceWith(header);

            /* Update wizard instructions */
            instructions = "<span class='create-alert-instructions'>Further narrow down the results using the 'Narrow down your search' functionality.<ul><li>Click 'Export' button to download the results into excel.<\/li><li>Click 'Back' to go back to Step1: Select Criteria.<\/li><li>Click 'Next' button to Schedule Alert.<\/li><\/ul><\/br></span>";
            $('.create-alert-instructions').replaceWith(instructions);

            /* Hide the schedule alert page */
            $('.create-alert-schedule-alert').replaceWith("<div class='create-alert-schedule-alert'>&nbsp;<br/><br/></div>");
            $('.create-alert-schedule-alert').css('display', 'none');

            /* Show the results page */
            $('.create-alert-customize-results').css('display', 'block');

            /* Update button text */
            $('div.create-alert-submit #edit-next-submit').val('Next');

            /* Remove focus from back */
            $('#edit-back-submit').blur();

            /* Show results buttons */
            $('.create-alert-submit').css('display', 'block');

            /* Buttons */
            $('#edit-next-submit').css('display', 'inline');
            $('#edit-back-submit').css('display', 'inline');

            /* Enable Next button on back to results page  */
            enable_input('#edit-next-submit');

            break;

        }

        /* Update hidden field for new step */
        $('input:hidden[name="step"]').val(previous_step);
      };

      /*------------------------------------------------------------------------------------------------------------*/

      $.fn.onScheduleAlertClick = function () {

        var scheduleAlertDiv = $(".create-alert-schedule-alert");
        var scheduleAlertUrl = '/alert/transactions/advanced/search/form';

        /* Load */
        $.ajax({
          url: scheduleAlertUrl,
          success: function (data) {
            $(scheduleAlertDiv).replaceWith("<div class='create-alert-schedule-alert'>" + data + "</div>");
            $("input[name='alert_end[date]']").datepicker({
              "changeMonth": true,
              "changeYear": true,
              "autoPopUp": "focus",
              "closeAtTop": false,
              "speed": "immediate",
              "firstDay": 0,
              "dateFormat": "yy-mm-dd",
              "yearRange": "-113:+487",
              "fromTo": false,
              "defaultDate": "0y"
            });
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

        var alertDiv = $('.create-alert-schedule-alert');
        var alertLabel = $(alertDiv).find('input[name=alert_label]').val();
        var alertEmail = $(alertDiv).find('input[name=alert_email]').val();
        var alertMinimumResults = $(alertDiv).find('input[name=alert_minimum_results]').val();
        var alertMinimumDays = $(alertDiv).find('select[name=alert_minimum_days]').val();
        var alertEnd = $(alertDiv).find("input[name='alert_end[date]']").val();
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
        var selectedDate = $("input[name='alert_end[date]']").datepicker('getDate');
        if ((alertEnd.length > 1 && alertEnd.length !== 10) || (alertEnd.length > 1 && !alertEnd.match(dateRegEx))) {
          alertMsgs.push("Expiration Date is not valid.");
        }
        else if (selectedDate != null && selectedDate < new Date()) {
          alertMsgs.push("Expiration date should be greater than current date.");
        }

        if (alertMsgs.length > 0) {
          $(alertDiv).find('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul>' + '<li>' + alertMsgs.join('</li><li>') + '</li></ul></div>');
          /* back button needs to be enabled*/
          enable_input('#edit-back-submit');
          /* Update hidden field for new step */
          $('input:hidden[name="step"]').val('schedule_alert');
        } else {
          $('a.ui-dialog-titlebar-close').hide();
          disable_input('#edit-next-submit');
          create_alert_loading();
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
            userURL: ajaxUserUrl,
            alert_theme_file: 'checkbook_alerts_advanced_search_confirm_theme'
          };
          $this = $(this);

          $.get(url, data, function (data) {
            data = JSON.parse(data);
            if (data.success) {
              $('a.ui-dialog-titlebar-close').show();
              $this.dialog('close');
              $('#block-checkbook-advanced-search-checkbook-advanced-search-form').dialog('close');
              var dialog = $("#dialog");
              if (!$("#dialog").length) {
                dialog = $('<div id="dialog" style="display:none"></div>');
              } else {
                $(dialog).replaceWith('<div id="dialog" style="display:none"></div>');
              }

              dialog.html(data.html);
              dialog.dialog({
                position: ['center', 'center'],
                modal: true,
                width: 550,
                height: 80,
                autoResize: true,
                resizable: false,
                dialogClass: 'noTitleDialog',
                close: function () {
                  var dialog = $("#dialog");
                  $(dialog).replaceWith('<div id="dialog" style="display:none"></div>');
                }
              });
            } else {
              /* back button needs to be enabled*/
              disable_input('#edit-back-submit');
              /* Update hidden field for new step */
              $('input:hidden[name="step"]').val('schedule_alert');

              $(alertDiv).find('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul><li>' + data.errors.join('<li/>') + '</ul></div>');
            }
          });
        }
      };

      $(window).load(function () {
        if (inIframe() && document.URL.indexOf("/createalert") >= 0) {
          if ($('.create-alert-customize-results', window.parent.document).css('display') === 'none') {
            return;
          }
          //No results
          $('#checkbook_advanced_search_result_iframe', window.parent.document).css('height', '100%');
          $('#checkbook_advanced_search_result_iframe', window.parent.document).attr('scrolling', 'no');
          $('#checkbook_advanced_search_result_iframe', window.parent.document).attr('scroll', 'no');
          $('#checkbook_advanced_search_result_iframe', window.parent.document).css('padding-left', '10px');

          //Fix content formatting
          var body = $(document).find('html body');
          $(body).css('background', '#ffffff');
          $(body).css('overflow', 'hidden');

          var bodyInner = $(document).find('html body #body-inner');
          $(bodyInner).css('box-shadow', 'none');

          $('.create-alert-results-loading', window.parent.document).css('display', 'none');
          $('.create-alert-results-loading', window.parent.document).css('visibility', 'hidden');
          $('#checkbook_advanced_search_result_iframe', window.parent.document).css('visibility', 'visible');
          $('a.ui-dialog-titlebar-close', window.parent.document).show();

          /* On parent back button click, need to re-stick the header */
          $('#edit-back-submit', window.parent.document).click(function (event) {
            var step = $('input:hidden[name="step"]').val();
            if (step === 'schedule_alert' || 'customize_results') {
              setTimeout(function () {
                fnCustomInitCompleteReload();
              }, 250);
            }
          });

          $(document).ajaxComplete(function () {

            if ($('.create-alert-customize-results', window.parent.document).css('display') === 'none') {
              return;
            }

            $('#checkbook_advanced_search_result_iframe', window.parent.document).css('height', 600);
            $('#checkbook_advanced_search_result_iframe', window.parent.document).attr('scrolling', 'yes');
            $('#checkbook_advanced_search_result_iframe', window.parent.document).attr('scroll', 'yes');
            $('#checkbook_advanced_search_result_iframe', window.parent.document).css('overflow-x', 'hidden');
            $('#checkbook_advanced_search_result_iframe', window.parent.document).css('overflow-y', 'scroll');
            $('#checkbook_advanced_search_result_iframe', window.parent.document).css('padding-left', '0px');
            $('div.ui-dialog', window.parent.document).css('height', 835);
            //Links should be disable in the iframe
            $(this).find('.dataTable tbody tr td div a').each(function () {
              $(this).addClass('disableLinks');
              $(this).click(function () {
                return false;
              });
            });

            //Fix content formatting
            var body = $(this).find('html body');
            $(body).css('background', '#ffffff');
            $(body).css('overflow-x', 'hidden');
            $(body).css('overflow-y', 'auto');

            var bodyInner = $(this).find('html body #body-inner');
            $(bodyInner).css('box-shadow', 'unset');
            $(bodyInner).css('padding-bottom', '0px');
            $(bodyInner).css('margin-bottom', '0px');

            /* Add hidden field for ajax referral Url to parent*/
            var alertsid = $(this).find('span.alerts').attr('alertsid');
            var refUrl = $('#table_' + alertsid).dataTable().fnSettings().sAjaxSource;
            $('input:hidden[name="ajax_referral_url"]', window.parent.document).val(refUrl);

            /* Enable button for results page after ajax loads */
            $('#edit-next-submit', window.parent.document).attr('disabled', false);
            $('#edit-back-submit', window.parent.document).attr('disabled', false);
          });
        }
        //No results
        if ($('#no-records').css('display') === 'block') {
          $('#edit-back-submit', window.parent.document).attr('disabled', false);
        }
      });

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
            // element_settings.progress = { 'type': 'throbber' };

            var base = $(this).attr('id');
            Drupal.ajax[base] = new Drupal.ajax(base, this, element_settings);
          });

        });

        $('#edit-next-submit').once('createAlertNextSubmit').click(function (event) {
          disable_input('#edit-back-submit');
          $.fn.onScheduleAlertNextClick($('input:hidden[name="step"]').val());
          event.preventDefault();
        });
        $('#edit-back-submit').once('createAlertBackSubmit').click(function (event) {
          disable_input('#edit-next-submit');
          $.fn.onScheduleAlertBackClick($('input:hidden[name="step"]').val());
          create_alert_form_enable();
          event.preventDefault();
        });
      }
    }
  };

  Drupal.behaviors.alertTransactions = {
    attach: function (context, settings) {
      // The span.alert is the object in Drupal to which you link the click button, I don�t know how it is actually named for the alert
      $('span.alerts').die().live("click", function () {
        var dialog = $("#dialog");
        if (!$("#dialog").length) {
          dialog = $('<div id="dialog" style="display:none"></div>');
        }

        // This is where you add the alerted table to which you link the output data from '/alert/transactions/form�
        var oSettings = $('#table_' + $(this).attr('alertsid')).dataTable().fnSettings();

        // This is the part where we get the data from to show in the dialogue we open, I don�t know if you process the following parameters  maxPages , record and so on but it won�t hurt if it stayed here
        var dialogUrl = '/alert/transactions/form';

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
              position: "center",
              modal: true,
              title: 'Alert',
              dialogClass: "alert",
              width: 700,
              open: function () {
                $("input[name='alert_end[date]']").datepicker({
                  "changeMonth": true,
                  "changeYear": true,
                  "autoPopUp": "focus",
                  "closeAtTop": false,
                  "speed": "immediate",
                  "firstDay": 0,
                  "dateFormat": "yy-mm-dd",
                  "yearRange": "-113:+487",
                  "fromTo": false,
                  "defaultDate": "0y"
                });
              },
              buttons: {
                "Create Alert": function () {
                  var alertLabel = $('input[name=alert_label]').val();
                  var alertEmail = $('input[name=alert_email]').val();
                  var alertMinimumResults = $('input[name=alert_minimum_results]').val();
                  var alertMinimumDays = $('select[name=alert_minimum_days]').val();
                  var alertEnd = $("input[name='alert_end[date]']").val();
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
                  var selectedDate = $("input[name='alert_end[date]']").datepicker('getDate');
                  if ((alertEnd.length > 1 && alertEnd.length != 10) || (alertEnd.length > 1 && !alertEnd.match(dateRegEx))) {
                    alertMsgs.push("Expiration Date is not valid.");
                  }
                  else if (selectedDate != null && selectedDate < new Date()) {
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
                      data = JSON.parse(data);
                      if (data.success) {
                        $this.dialog('close');

                        var dialog = $("#dialog_schedule_confirm");
                        if (!$("#dialog_schedule_confirm").length) {
                          dialog = $('<div id="dialog_schedule_confirm" style="display:none"></div>');
                        }
                        dialog.html(data.html);
                        dialog.dialog({
                          position: ['center', 'center'],
                          modal: true,
                          width: 550,
                          height: 80,
                          autoResize: true,
                          resizable: false,
                          dialogClass: 'noTitleDialog',
                          close: function () {
                            var dialog = $("#dialog_schedule_confirm");
                            $(dialog).replaceWith('<div id="dialog_schedule_confirm" style="display:none"></div>');
                          }
                        });
                      } else {
                        $('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul><li>' + data.errors.join('<li/>') + '</ul></div>');
                      }
                    });
                  }
                },
                "Cancel": function () {
                  $(this).dialog('close');
                }
              }
            });
          }
        );
        return false;
      });
    }
  };

  function bind_enter_keyboard_keypress() {
    $("#block-checkbook-advanced-search-checkbook-advanced-search-form input").bind("keypress", function (e) {
      if (e.keyCode === 13) {
        e.preventDefault();
        var id = this.id;
        if (id.match(/contract/g) != null) {
          $('#edit-contracts-submit').click();
        }
        else if (id.match(/payroll/g) != null) {
          $('#edit-payroll-submit').click();
        }
        else if (id.match(/budget/g) != null) {
          $('#edit-budget-submit').click();
        }
        else if (id.match(/revenue/g) != null) {
          $('#edit-revenue-submit').click();
        }
        else if (id.match(/spending/g) != null) {
          $('#edit-spending-submit').click();
        }
      }
      else return true;
    });
  }

  function clearInputFieldByDataSource(enclosingDiv, domain, dataSource) {
    $(enclosingDiv).find(':input').each(function () {
      switch (this.type) {
        case 'select-one':
          $("select#edit-checkbook-contracts-category").val("all");
          var defaultoption = $(this).attr('default_selected_value');
          if (defaultoption) {
            $(this).find('option[value=' + defaultoption + ']').attr("selected", "selected");
          } else {
            $(this).find('option:first').attr("selected", "selected");
          }
          break;
        case 'text':
          $(this).val('');
          $(this).removeAttr('storedvalue');
          break;
        case 'select-multiple':
        case 'password':
        case 'textarea':
          $(this).val('');
          break;
        case 'checkbox':
        case 'radio':
          switch (domain) {
            case 'payroll':
              $(':radio[name="payroll_advanced_search_domain_filter"][value="' + dataSource + '"]').click();
              break;
            case 'spending':
              $(':radio[name="spending_advanced_search_domain_filter"][value="' + dataSource + '"]').click();
              break;
            case 'contracts':
              $(':radio[name="contracts_advanced_search_domain_filter"][value="' + dataSource + '"]').click();
              break;
            case 'budget':
              $(':radio[name="budget_advanced_search_domain_filter"][value="' + dataSource + '"]').click();
              break;
            case 'revenue':
              $(':radio[name="revenue_advanced_search_domain_filter"][value="' + dataSource + '"]').click();
              break;
          }
          break;
      }
    });
  }

  function disable_input(selector){
    if(Array.isArray(selector)) {
      selector.forEach(disable_input);
      return;
    }
    $(selector).each(function () {
      $(this).attr('disabled','disabled');

      // store value
      if ('text' == $(this).attr('type')) {
        if ($(this).val()){
          $(this).attr('storedvalue', $(this).val());
        }
        $(this).val('');
      }

      if (this.type == 'select-one') {
        var default_option = $(this).attr('default_selected_value');
        if (!default_option)
          $(this).find('option:first').attr("selected", "selected");
        else
          $(this).find('option[value=' + default_option + ']').attr("selected", "selected");
      }
    });
    return;
  }

  function enable_input(selector){
    if(Array.isArray(selector)) {
      selector.forEach(enable_input);
      return;
    }

    $(selector).each(function () {
      $(this).removeAttr('disabled');

      // restore value
      if ('text' == $(this).attr('type')) {
        if ($(this).attr('storedvalue')){
          $(this).val($(this).attr('storedvalue'));
        }
        $(this).removeAttr('storedvalue');
      }
    });
    return;
  }

  function resetFields(divWrapper) {
    $(divWrapper.children()).find(':input').each(function () {
      if (this.type == 'text') {
        $(this).val('');
      }
      if (this.type == 'select-one') {
        var default_option = $(this).attr('default_selected_value');
        if (!default_option)
          $(this).find('option:first').attr("selected", "selected");
        else
          $(this).find('option[value=' + default_option + ']').attr("selected", "selected");
      }
    });
  }

  // updateYearValue - change year value display for catastrophic events
  function updateEventYearValue(div_val,cevent) {
    $(div_val).each(function() {
      var yval =  (this.text).split(' ')[1] ;
      if ((this.text < '2020' || yval < '2020') && cevent != 0){
        $(" option[value='" + $(this).val() + "']").hide();
      }
      else{
        $(" option[value='" + $(this).val() + "']").show();
      }
    });
  }

}(jQuery));
