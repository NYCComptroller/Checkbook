(function ($, Drupal, drupalSettings) {
  Drupal.advancedSearchAndAlertsRevenue = {
    'advanced_search_revenue_init': function () {
        let revenue_div = function (data_source, div_contents) {
          this.div_elements = {
            'budget_fy': 'select[name=' + data_source + '_revenue_budget_fiscal_year]',
            'agency': 'select[name=' + data_source + '_revenue_agency]',
            'revenue_category': 'select[name=' + data_source + '_revenue_revenue_category]',
            'revenue_source': 'input:text[name=' + data_source + '_revenue_revenue_source]',
            'catastrophic_events': 'select[name=' + data_source + '_revenue_catastrophic_events]',
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
            'resp_center': 'select[name=' + data_source + '_revenue_responsibility_center]',
            'fundsrc': 'select[name=' + data_source + '_revenue_fundsrc]',
            'program': 'select[name=' + data_source + '_revenue_program]',
            'project': 'select[name=' + data_source + '_revenue_project]',
            'budget_type': 'select[name=' + data_source + '_revenue_budget_type]',
            'budget_type_chosen': 'select[name=' + data_source + '_revenue_budget_type_chosen]',
            'nycha_budget_name': 'select[name=' + data_source + '_revenue_nycha_budget_name]',
            'nycha_budget_name_chosen': 'select[name=' + data_source + '_revenue_nycha_budget_name_chosen]',
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

        let reloadRevenueBudgetType = function (div) {
          let budget_name = div.ele('nycha_budget_name').val();
          budget_name = (budget_name && budget_name.length > 0) ? encodeURIComponent((budget_name).replace('/', '__')) :0;
          let budget_type = div.ele('budget_type').val() ?? 0;
          let data_source = 'checkbook_nycha';
          let url = '/data-feeds/budget_type/revenue/' + data_source + '/' + budget_name + '/' + true;
          if (url === revenueBudgetTypeAlreadyLoaded) {
            return;
          }
          revenueBudgetTypeAlreadyLoaded = url;
          $.ajax({
            url: url,
            success: function (data) {
              let html = '<option select="selected" value="0" >Select Budget Type</option>';
              if (data[0]) {
                for (i = 0; i < data.length; i++) {
                  html = html + '<option value="' + data[i].value + '" title="' + data[i].value + '">' + data[i].text + '</option>';
                }
              }
              div.ele('budget_type').html(html).val(budget_type).trigger("chosen:updated");
              if (budget_name !== div.ele('nycha_budget_name').val()) {
                //@ToDo: check if below needed to call itself back
                //reloadRevenueBudgetType(div);
              }
            }
          });
        }

        let nychaRevenueBudgetNamesAlreadyLoaded = false;

        let reloadNychaRevenueBudgetName = function (div) {
          let budget_type = div.ele('budget_type').val();
          budget_type = (budget_type && budget_type.length > 0) ? encodeURIComponent((budget_type).replace('/', '__')) :0;
          let budget_name = div.ele('nycha_budget_name').val() ?? 0;
          let data_source = 'checkbook_nycha';

          let url = '/data-feeds/budget_name/revenue/' + data_source + '/' + budget_type + '/' + true;
          if (url === nychaRevenueBudgetNamesAlreadyLoaded) {
            return;
          }
          nychaRevenueBudgetNamesAlreadyLoaded = url;
          $.ajax({
            url: url,
            success: function (data) {
              let html = '<option select="selected" value="0" >Select Budget Name</option>';
              if (data[0]) {
                for (i = 0; i < data.length; i++) {
                  html = html + '<option value="' + data[i].value + '" title="' + data[i].value + '">' + data[i].text + '</option>';
                }
              }
              div.ele('nycha_budget_name').html(html).val(budget_name).trigger("chosen:updated");
              if (budget_type !== div.ele('budget_type').val()) {
                //@ToDo: check if below needed to call itself back
                //reloadNychaRevenueBudgetName(div);
              }
            }
          });
        }

        //checkbook_advanced_search_clear_button.js sets this value by default
        $('input:radio[name=revenue_advanced_search_domain_filter]').click(function () {
          onRevenueDataSourceChange($('input[name=revenue_advanced_search_domain_filter]:checked').val());
        });

        let onRevenueDataSourceChange = function (dataSource) {
          let domain = 'revenue';
          /* Initialize view by data source */
          switch (dataSource) {
            case "checkbook_nycha":
              clearInputFields(div_checkbook_revenue_nycha.contents().children(), domain, dataSource);
              initializeRevenueView(div_checkbook_revenue_nycha, dataSource);
              div_checkbook_revenue.contents().hide();
              div_checkbook_revenue_nycha.contents().show();
              $('label[for^=edit-checkbook-nycha-revenue-expense-category]').html('Revenue<br/>Expense Category');

              //Reset Revenue Name and Revenue Code Chosen drop-downs
              div_checkbook_revenue_nycha.ele('budget_type').val("0").trigger("chosen:updated");
              div_checkbook_revenue_nycha.ele('nycha_budget_name').val("0").trigger("chosen:updated");
              reloadRevenueBudgetType(div_checkbook_revenue_nycha);
              reloadNychaRevenueBudgetName(div_checkbook_revenue_nycha);
              break;

            default:
              clearInputFields(div_checkbook_revenue.contents().children(), domain, dataSource);
              initializeRevenueView(div_checkbook_revenue, dataSource);
              div_checkbook_revenue.contents().show();
              div_checkbook_revenue_nycha.contents().hide();
              onRevenueCatastrophicEventChange(div_checkbook_revenue);
              onRevenueBudgetFiscalYearChange(div_checkbook_revenue);
              div_checkbook_revenue.ele('catastrophic_events').removeAttr('style');
              div_checkbook_revenue.ele('catastrophic_events').removeAttr('disabled');

          }
        }

        let dataSource = $('input:radio[name=revenue_advanced_search_domain_filter]:checked').val() ? $('input:radio[name=revenue_advanced_search_domain_filter]:checked').val() : "checkbook";
        onRevenueDataSourceChange(dataSource);

        function onRevenueBudgetFiscalYearChange(div) {
          //Setting data source value
          let data_source = $('input[type=radio][name=revenue_advanced_search_domain_filter]:checked').val();
          data_source = data_source ? data_source : 'checkbook';
          if (data_source === 'checkbook') {
            let budget_fiscal_year = (div.ele('budget_fy').val()) ? div.ele('budget_fy').val() : 0;
            let catastrophic_event = document.getElementById("edit-checkbook-revenue-catastrophic-events");
            let enabled_count = $('#edit-checkbook-revenue-catastrophic-events > option').length;
            if (!(budget_fiscal_year === "0" || budget_fiscal_year >= 121)) {
              for (let i = 0; i < $('#edit-checkbook-revenue-catastrophic-events > option').length; i++) {
                let event = catastrophic_event.options[i].text.toLowerCase();
                catastrophic_event.options[i].style.display = (event === 'covid-19') ? "none" : "";
                if (catastrophic_event.options[i].style.display === 'none') enabled_count--;
              }
              if (enabled_count <= 1) disable_input(div.ele('catastrophic_events'));
            } else {
              for (let i = 0; i < enabled_count; i++) {
                let event = catastrophic_event.options[i].text.toLowerCase();
                if (event === 'covid-19') {
                  catastrophic_event.options[i].style.display = "";
                  break;
                }
              }
              enable_input(div.ele('catastrophic_events'));
            }
          }
        }

      function onRevenueCatastrophicEventChange(div) {
        //Limit fiscal year to just 'FY 2020', 'FY 2021' and 'All years'
        let cevent = div.ele('catastrophic_events').val();
        updateEventYearValue("select[name='checkbook_revenue_budget_fiscal_year'] option", cevent);
      }


      //On change of "Budget Fiscal Year"
        div_checkbook_revenue.ele('budget_fy').change(function () {
          onRevenueBudgetFiscalYearChange(div_checkbook_revenue);
        });

        //On change of "Catastrophic event"
        div_checkbook_revenue.ele('catastrophic_events').change(function () {
          onRevenueCatastrophicEventChange(div_checkbook_revenue);
        });

        //Prevent the auto-complete from wrapping un-necessarily
        function fixAutoCompleteWrapping(divWrapper) {
          $(divWrapper.children()).find('input.ui-autocomplete-input:text').each(function () {
            $(this).data("autocomplete")._resizeMenu = function () {
              (this.menu.element).outerWidth('100%');
            }
          });
        }

        function initializeRevenueViewAutocomplete(div, data_source) {
          //Set Solr datasource for auto-complete
          let solr_datasource = data_source;
          let agency_id = 0;
          if (data_source === 'checkbook') {
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
            div.ele('revenue_class').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'revenue_class_name', filters)});
            div.ele('revenue_source').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'revenue_source_name', filters)});
          } else if (data_source === 'checkbook_nycha') {
            solr_datasource = 'nycha';
            let fiscal_year_id = parseInt((div.ele('budget_fy').val()) ? div.ele('budget_fy').val() : 0);
            let exp_cat = parseInt((div.ele('expense_category').val()) ? div.ele('expense_category').val() : 0);
            let resp_center = parseInt((div.ele('resp_center').val()) ? div.ele('resp_center').val() : 0);
            let funding_src = parseInt((div.ele('fundsrc').val()) ? div.ele('fundsrc').val() : 0);
            let program = parseInt((div.ele('program').val()) ? div.ele('program').val() : 0);
            let project = parseInt((div.ele('project').val()) ? div.ele('project').val() : 0);
            let budget_type = (div.ele('budget_type').val() ? encodeURIComponent(div.ele('budget_type').val()) : 0);
            let budget_name = (div.ele('nycha_budget_name').val() ? encodeURIComponent(div.ele('nycha_budget_name').val()) : 0);
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
            div.ele('revenue_class').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'revenue_class', filters)});
            div.ele('nycha_revenue_category').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource, 'revenue_category', filters)});
          }

          $('.ui-autocomplete-input').bind('autocompleteselect', function (event, ui) {
            ui.item.value = String(ui.item.value).search('No Matches Found') == -1 ? ui.item.value : '';
            $(this).val(ui.item.value);
            $(this).parent().next().val(ui.item.label);
          });

          //prevent the auto-complete from wrapping un-necessarily
          // fixAutoCompleteWrapping(div.contents());
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
          if ($(this).val() === 'Select Budget Name' || $(this).val() === '' || $(this).val() === 0) {
            reloadNychaRevenueBudgetName(div_checkbook_revenue_nycha);
          }
        });

        div_checkbook_revenue_nycha.ele('budget_type').change(function () {
          reloadNychaRevenueBudgetName(div_checkbook_revenue_nycha);
          if ($(this).val() === 'Select Budget Type' || $(this).val() === '' || $(this).val() === 0) {
            reloadRevenueBudgetType(div_checkbook_revenue_nycha);
          }
        });

      //On clicking "Clear"
      $('div.revenue-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
        onRevenueDataSourceChange('checkbook');
        e.preventDefault();
      });
      $('div.revenue-submit.checkbook-nycha').find('input:submit[value="Clear All"]').click(function (e) {
        onRevenueDataSourceChange('checkbook_nycha');
        e.preventDefault();
      });

    }
  }
}(jQuery, Drupal, drupalSettings));
