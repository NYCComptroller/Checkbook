(function ($, Drupal, drupalSettings) {
  Drupal.advancedSearchAndAlertsSpending = {
      'advanced_search_spending_init' : function() {
        let year, department_name, agency_id, spending_category_id, expenditure_object_code, minority_type_id,
          industry_type_id, datasource;

        let spending_div = function (data_source, div_contents) {
          this.div_elements = {
            'agency': 'select[name=' + data_source + '_spending_agency]',
            'dept': 'select[name=' + data_source + '_spending_department]',
            'exp_category': 'select[name=' + data_source + '_spending_expense_category]',
            'spending_category': 'select[name=' + data_source + '_spending_expense_type]',
            'industry': 'select[name=' + data_source + '_spending_industry]',
            'mwbe_category': 'select[name=' + data_source + '_spending_mwbe_category]',
            'conditional_categories': 'select[name=' + data_source + '_spending_conditional_categories]',
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
            'issue_date_from': 'input[type="date"][name="' + data_source + '_spending_issue_date_from"]',
            'issue_date_to': 'input[type="date"][name="' + data_source + '_spending_issue_date_to"]',
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

        let div_spending_main = $("#spending-advanced-search");
        let div_checkbook_spending = new spending_div('checkbook', div_spending_main.children('div.checkbook'));
        let div_checkbook_spending_oge = new spending_div('checkbook_oge', div_spending_main.children('div.checkbook-oge'));
        let div_checkbook_spending_nycha = new spending_div('checkbook_nycha', div_spending_main.children('div.checkbook-nycha'));

        //On change of "Agency"
        div_checkbook_spending.ele('agency').change(function () {
          onAgencyChange(div_checkbook_spending);
        });

        //Populate Spending Domain Expense Categories drop-down
        function loadSpendingExpenseCategories(div, data_source) {
          let year = 0;
          if (div.ele('date_filter_checked').val() === '0') {
            year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
          }
          let agency = 0;
          if (data_source === 'checkbook') {
            agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            if (agency === 0) {
              let html = '<option select="selected" value="" >Select Expense Category</option>';
              div.ele('exp_category').html(html);
              return;
            }
          }
          div.ele('exp_category').addClass('loading');
          let dept = 0;
          if (data_source !== 'checkbook_nycha') {
            dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
            //dept = dept.toString().replace(/\//g, "__");
          }
          let exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
          let expCat = (div.ele('exp_category').val() && div.ele('exp_category').text() !== "Select Expense Category")
            ? (div.ele('exp_category').val()) : '';
          $.ajax({
            url: '/datafeeds/spending/expcategory/' + year + '/' + agency + '/' + dept + '/' + exptype + '/' + data_source + '/0',
            success: function (data) {
              let html = '<option select="selected" value="" >Select Expense Category</option>';
              if (data[0]) {
                if (data[0] !== 'No Matches Found') {
                  $.each(data, function (key, exp_cat) {
                    // Remove null data from drop down
                    if (exp_cat.name !== '') {
                      html = html + '<option value="' + exp_cat.code + '" title="' + exp_cat.title + '"'+ ((exp_cat.code === expCat)? ' selected' : '') +'>' + exp_cat.name + '</option>';
                    }
                  });
                } else {
                  html = html + '<option value="">' + data[0] + '</option>';
                }
              }
              div.ele('exp_category').html(html);
              //div.ele('exp_category').val(expCat);
            },
            complete: function () {
              div.ele('exp_category').removeClass('loading');
              enable_input(div.ele('exp_category'));
            }
          });
        }

        //Populate Spending Domain departments drop-down
        function loadSpendingDepartments(div, data_source) {
          let year = 0;
          if (div.ele('date_filter_checked').val() === '0') {
            year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
          }
          let agency = 0;
          if (data_source === 'checkbook') {
            agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            if (agency === 0)
              return;
          }
          div.ele('dept').addClass('loading');
          if (data_source === 'checkbook_oge') {
            agency = 9000;
          }
          let exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
          let dept = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
          $.ajax({
            url: '/datafeeds/spending/department/' + year + '/' + agency + '/' + exptype + '/' + data_source + '/0',
            success: function (data) {
              let html = '<option select="selected" value="0" >Select Department</option>';
              if (data[0]) {
                if (data[0] !== 'No Matches Found') {
                  for (let i = 0; i < data.length; i++) {
                    html = html + '<option value="' + data[i].code + '">' + data[i].title + '</option>';
                  }
                } else {
                  dept = 0;
                  html = html + '<option value="">No Matches Found</option>';
                }
              }
              div.ele('dept').html(html);
              //div.ele('dept').val(dept);
            },
            complete: function () {
              div.ele('dept').removeClass('loading');
              enable_input(div.ele('dept'));
            }
          });
        }

        //When agency is selected
        function onAgencyChange(div) {
          //Setting 'data source' value
          let data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
          if (data_source === 'checkbook') {
            let agency_id = parseInt(div.ele('agency').val() ?? '');
            if (!agency_id) {
              div.ele('dept').val('0');
              div.ele('exp_category').val('0');
              disable_input(div.ele('dept'));
              disable_input(div.ele('exp_category'));
            } else {
              //Load departments and expense categories drop-downs
              div.ele('dept').val('0');
              div.ele('exp_category').val('0');
              loadSpendingExpenseCategories(div, data_source);
              loadSpendingDepartments(div, data_source);
            }
          } else {
            //Load departments and expense categories drop-downs
            loadSpendingExpenseCategories(div, data_source);
            loadSpendingDepartments(div, data_source);
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
          let data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
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
          let exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
          let year = 0;
          if (div.ele('date_filter_checked').val() === '0') {
            year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
          }
          //Setting data source value
          let data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
          if (data_source === 'checkbook_nycha') {
            //NYCHA - disabling fields based on Spending category selected
            if (exptype === '2') {//payroll
              disable_input([div.ele('vendor_name'), div.ele('contract_id'), div.ele('document_id'),
                div.ele('industry'), div.ele('fundsrc'), div.ele('resp_center'),
                div.ele('po_type'), div.ele('amount_spent_from'), div.ele('amount_spent_to')]);
              enable_input([div.ele('dept'), div.ele('exp_category')]);
              div.ele('contract_id').val("");
              div.ele('vendor_name').val("");
            } else if (exptype === '1') {//section 8
              disable_input([div.ele('dept'), div.ele('contract_id'),
                div.ele('industry'), div.ele('po_type'),]);
              enable_input([div.ele('vendor_name'), div.ele('document_id'), div.ele('resp_center'),
                div.ele('amount_spent_from'), div.ele('amount_spent_to'), div.ele('exp_category'), div.ele('fundsrc')]);
            } else if (exptype === '4') {//Other
              disable_input([div.ele('dept'), div.ele('contract_id')]);
              enable_input([div.ele('exp_category'), div.ele('vendor_name'), div.ele('document_id'),
                div.ele('industry'), div.ele('fundsrc'), div.ele('resp_center'),
                div.ele('po_type'), div.ele('amount_spent_from'), div.ele('amount_spent_to')]);
            } else {
              enable_input([div.ele('dept'), div.ele('exp_category'), div.ele('vendor_name'), div.ele('document_id'),
                div.ele('industry'), div.ele('fundsrc'), div.ele('resp_center'), div.ele('contract_id'),
                div.ele('po_type'), div.ele('amount_spent_from'), div.ele('amount_spent_to')]);
            }
          } else {
            //CITYWIDE and OGE - disabling fields based on Spending category selected
            if (exptype === '2') {
              disable_input([div.ele('contract_id'), div.ele('payee_name'), div.ele('conditional_categories')]);
              div.ele('contract_id').val("");
              div.ele('payee_name').val("");
              onCatastrophicEventChange(div);
            } else if (exptype === '4') {
              disable_input([div.ele('contract_id'), div.ele('conditional_categories')]);
              div.ele('contract_id').val("");
              enable_input(div.ele('payee_name'));
              onCatastrophicEventChange(div);
            } else {
              enable_input([div.ele('contract_id'), div.ele('payee_name'), div.ele('conditional_categories')]);
              onFiscalYearChange(div);
            }
          }
        }

        // On change of "Conditional Category".
        div_checkbook_spending.ele('conditional_categories').change(function () {
          onCatastrophicEventChange(div_checkbook_spending);
        });

        function onCatastrophicEventChange(div) {
          //Selecting 'COVID-19' option causes the following changes:
          //Data within following fields update: Payee Name, Contract ID, Document ID, Capital Project

          let cevent = div.ele('conditional_categories').val();
          updateEventYearValue("select[name='checkbook_spending_fiscal_year'] option", cevent);
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
          let data_source = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
          let agency = 0;
          if (data_source === 'checkbook') {
            let fiscal_year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            let exptype = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
            updateConditionalEventValue(div_checkbook_spending.ele('conditional_categories'), removeFY(fiscal_year),(exptype === '2'|| exptype === '4'));
            agency = (div.ele('agency').val()) ? div.ele('agency').val() : 0;
            if (agency === 0) {
              return;
            }
          }
          //Reload Department and Expense Category drop-downs for CityWide
          div.ele('dept').val('0');
          div.ele('exp_category').val('0');
          loadSpendingDepartments(div, data_source);
          loadSpendingExpenseCategories(div, data_source);
        }

        //On clicking "Clear"
        $('div.spending-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
          //onClearClick(div_checkbook_spending);
          onChangeDataSource('checkbook');
          e.preventDefault();
        });
        $('div.spending-submit.checkbook-oge').find('input:submit[value="Clear All"]').click(function (e) {
          //onClearClick(div_checkbook_spending_oge);
          onChangeDataSource('checkbook_oge');
          e.preventDefault();
        });
        $('div.spending-submit.checkbook-nycha').find('input:submit[value="Clear All"]').click(function (e) {
          //onClearClick(div_checkbook_spending_nycha);
          onChangeDataSource('checkbook_nycha');
          e.preventDefault();
        });

        function resetSelectValue(select) {
          if (select.length) {
            select[0].selectedIndex = 0;
          }
        }

        //On click of "Date Filter"
        div_checkbook_spending.ele('date_filter').click(function () {
          let value = div_checkbook_spending.ele('date_filter_checked').val();
          onDateFilterClick(div_checkbook_spending, value);
        });
        div_checkbook_spending_oge.ele('date_filter').click(function () {
          let value = div_checkbook_spending_oge.ele('date_filter_checked').val();
          onDateFilterClick(div_checkbook_spending_oge, value);
        });
        div_checkbook_spending_nycha.ele('date_filter').click(function () {
          let value = div_checkbook_spending_nycha.ele('date_filter_checked').val();
          onDateFilterClick(div_checkbook_spending_nycha, value);
        });

        /**
         * Prevents selection of specified Item for autocomplete field
         * @param event
         * @param ui
         * @param selection_to_prevent
         */
        $.fn.preventSelectionDefault = function(event, ui, selection_to_prevent = "No Matches Found") {
          var label = ui.item.label;
          if (label === selection_to_prevent) {
            // prevent `selection_to_prevent` item from being selected
            event.preventDefault();
          }
        };

        function onDateFilterClick(div, value) {
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
          let domain = 'spending';
          /* Initialize view by data source */
          switch (dataSource) {
            case "checkbook_oge":
              clearInputFields(div_checkbook_spending_oge.contents().children(), domain, dataSource);
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

              enable_input(div_checkbook_spending_oge.ele('contract_id'));
              enable_input(div_checkbook_spending_oge.ele('payee_name'));

              break;
            case "checkbook_nycha":
              clearInputFields(div_checkbook_spending_nycha.contents().children(), domain, dataSource);
              initializeSpendingView(div_checkbook_spending_nycha, dataSource);
              div_checkbook_spending.contents().hide();
              div_checkbook_spending_oge.contents().hide();
              div_checkbook_spending_nycha.contents().show();
              //Load Expense categories by default for NYCHA
              loadSpendingExpenseCategories(div_checkbook_spending_nycha, dataSource);

              //Reset Date Filter
              div_checkbook_spending_nycha.ele('date_filter_year').prop('checked', true);
              onDateFilterClick(div_checkbook_spending_nycha, "0");

              //Enable NYCHA Spending fields
              enable_input([div_checkbook_spending_nycha.ele('dept'), div_checkbook_spending_nycha.ele('exp_category'),
                div_checkbook_spending_nycha.ele('vendor_name'), div_checkbook_spending_nycha.ele('document_id'),
                div_checkbook_spending_nycha.ele('industry'), div_checkbook_spending_nycha.ele('fundsrc'),
                div_checkbook_spending_nycha.ele('resp_center'), div_checkbook_spending_nycha.ele('contract_id'),
                div_checkbook_spending_nycha.ele('po_type'), div_checkbook_spending_nycha.ele('amount_spent_from'),
                div_checkbook_spending_nycha.ele('amount_spent_to')]);
              break;

            default:
              clearInputFields(div_checkbook_spending.contents().children(), domain, dataSource);
              initializeSpendingView(div_checkbook_spending, dataSource);
              div_checkbook_spending.contents().show();
              div_checkbook_spending_oge.contents().hide();
              div_checkbook_spending_nycha.contents().hide();
              loadSpendingExpenseCategories(div_checkbook_spending, dataSource);
              //Reset Date Filter
              div_checkbook_spending.ele('date_filter_year').prop('checked', true);
              onDateFilterClick(div_checkbook_spending, "0");

              //Disable Department and Expense Category drop-downs when agency is not selected
              agency_id = parseInt(div_checkbook_spending.ele('agency').val() ?? '');
              if (!agency_id) {
                disable_input([div_checkbook_spending.ele('dept'), div_checkbook_spending.ele('exp_category')]);
              }
              onCatastrophicEventChange(div_checkbook_spending);
              div_checkbook_spending.ele('conditional_categories').removeAttr('style');
              div_checkbook_spending.ele('conditional_categories').removeAttr('disabled');
              enable_input([div_checkbook_spending.ele('contract_id'), div_checkbook_spending.ele('payee_name'), div_checkbook_spending.ele('conditional_categories')]);
              break;
          }
        }

        function initializeSpendingViewAutocomplete(div, data_source) {
          //Set Solr datasource for auto-complete
          let solr_datasource = data_source;
          if (data_source === 'checkbook_nycha') {
            solr_datasource = 'nycha'
          }
          let agency_id = 0;
          if (data_source === 'checkbook') {
            agency_id = div.ele('agency').val() ? parseInt( div.ele('agency').val()) : '';
          }
          year = 0;
          let year_id = 0;
          if (div.ele('date_filter_checked').val() === '0') {
            year = (div.ele('fiscal_year').val()) ? div.ele('fiscal_year').val() : 0;
            if (typeof year.indexOf == 'function' && year.indexOf("fy") >= 0) {
              year_id = year.split('~')[1];
            }
          }
          department_code = (div.ele('dept').val()) ? (div.ele('dept').val()) : 0;
          department_code = department_code.toString().replace(/\//g, "__");
          expenditure_object_code = (div.ele('exp_category').val()) ? (div.ele('exp_category').val()) : 0;
          spending_category_id = (div.ele('spending_category').val()) ? (div.ele('spending_category').val()) : 0;
          minority_type_id = (div.ele('mwbe_category').val()) ? (div.ele('mwbe_category').val()) : 0;
          industry_type_id = (div.ele('industry').val()) ? (div.ele('industry').val()) : 0;
          let conditional_category_id = (div.ele('conditional_categories').val()) ? div.ele('conditional_categories').val() : 0;
          datasource = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
          // enable purchase order filter for nycha
          let agreement_type_code = (div.ele('po_type').val()) ? (div.ele('po_type').val()) : 0;
          let resp_center_id = (div.ele('resp_center').val()) ? (div.ele('resp_center').val()) : 0;
          let fund_src_id = (div.ele('fundsrc').val()) ? (div.ele('fundsrc').val()) : 0;
          let filters = {
            department_code: department_code,
            agency_id: agency_id,
            expenditure_object_code: expenditure_object_code,
            spending_category_id: spending_category_id,
            minority_type_id: minority_type_id,
            industry_type_id: industry_type_id,
            fiscal_year_id: year_id,
            agreement_type_code: agreement_type_code,
            responsibility_center_id: resp_center_id,
            funding_source_id: fund_src_id,
            event_id: conditional_category_id
          };

           div.ele('payee_name').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource, 'vendor_name', filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('contract_id').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource, 'contract_number', filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('capital_project').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource, 'reporting_code', filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('document_id').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource, 'expense_id', filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('commodity_line').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource, 'spending_commodity_line', filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('budget_name').autocomplete({
             source:$.fn.autoCompleteSourceUrl(solr_datasource, 'spending_budget_name', filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('entity_contract_number').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource, 'spending_entity_contract_number', filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('vendor_name').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource, 'vendor_name', filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           //@ToDo: check which one correct below or line 510 above
           div.ele('document_id').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource, 'document_id', filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });

          $('.ui-autocomplete-input').bind('autocompleteselect', function (event, ui) {
            ui.item.value = String(ui.item.value).search('No Matches Found') == -1 ? ui.item.value : '';
            $(this).val(ui.item.value);
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
            //$(this).data("autocomplete")._resizeMenu = function () {
            //  (this.menu.element).outerWidth('100%');
            //}
          })
        }
        //display form depending on domain filter radiobox
        let dataSource = $('input:radio[name=spending_advanced_search_domain_filter]:checked').val() ? $('input:radio[name=spending_advanced_search_domain_filter]:checked').val() : "checkbook";

        onChangeDataSource(dataSource);
      }
  };
}(jQuery, Drupal, drupalSettings));
