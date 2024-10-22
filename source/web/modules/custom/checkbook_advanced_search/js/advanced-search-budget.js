(function ($, Drupal, drupalSettings) {
  // advanced-search-budget
  Drupal.advancedSearchAndAlertsBudget = {
    'advanced_search_budget_init' : function() {
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
          'conditional_categories': 'select[name=' + data_source + '_budget_conditional_categories]',
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
          'resp_center': 'select[name=' + data_source + '_budget_responsibility_center]',
          'fundsrc': 'select[name=' + data_source + '_budget_fundsrc]',
          'program': 'select[name=' + data_source + '_budget_program]',
          'project': 'select[name=' + data_source + '_budget_project]',
          'budget_type': 'select[name=' + data_source + '_budget_budget_type]',
          'budget_type_chosen': 'select[name=' + data_source + '_budget_budget_type_chosen]',
          'nycha_budget_name': 'select[name=' + data_source + '_budget_nycha_budget_name]',
          'nycha_budget_name_chosen': 'select[name=' + data_source + '_budget_nycha_budget_name_chosen]',
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

      let reloadDepartment = function (div) {
        let val;
        let fiscal_year = (val = div.ele('year').val()) ? val : 0;
        let agency = (val = div.ele('agency').val()) ? val : 0;
        let dept = (val = div.ele('department').val()) ? val : 0;
        $.ajax({
          url: '/datafeeds/budget/department/' + fiscal_year + '/' + agency + '/' + 0,
          success: function (data) {
            let html = '<option select="selected" value="0" >Select Department</option>';
            if (data[0]) {
              if (data[0].label !== 'No Matches Found') {
                for (let i = 0; i < data.length; i++) {
                  html = html + '<option value="' + data[i] + ' ">' + data[i] + '</option>';
                }
              }
            }
            div.ele('department').html(html).removeAttr("disabled");
            div.ele('department').val(dept);
          }
        });
      }

      let reloadExpenseCategory = function (div) {
        let val;
        let fiscal_year = (val = div.ele('year').val()) ? val : 0;
        let agency = (val = div.ele('agency').val()) ? val : 0;
        let dept = (val = div.ele('department').val()) ? val : 0;
        let expCat = (val = div.ele('expense_category').val()) ? val : 0;

        $.ajax({
          url: '/advanced-search/autocomplete/budget/expcategory/' + fiscal_year + '/' + agency + '/' + dept.toString().replace(/\//g, "__"),
          // url: '/datafeeds/expcat/budget/' + fiscal_year + '/' + agency + '/' + dept.toString().replace(/\//g, "__"),
          success: function (data) {
            let html = '<option select="selected" value="0" >Select Expense Category</option>';
            if (data[0]) {
              if (data[0].label !== 'No Matches Found') {
                for (let i = 0; i < data.length; i++) {
                  html = html + '<option value="' + data[i] + ' ">' + data[i] + '</option>';
                }
              }
            }
            div.ele('expense_category').html(html).removeAttr("disabled");
            div.ele('expense_category').val(expCat);

          }
        });
      }

      let budgetCodeAlreadyLoaded = false;

      let reloadBudgetCode = function (div, cevent_changed = false) {
        let fiscal_year = div.ele('year').val() || 0;
        let agency = div.ele('agency').val() || 0;
        let dept = div.ele('department').val() || 0;
        let expcategory = div.ele('expense_category').val() || 0;
        let budget_code_div = div.ele('budget_code');
        let budget_code = cevent_changed ? 0 : (budget_code_div.val() || 0);
        let budget_name = cevent_changed ? 0 : (div.ele('budget_name').val() || 0);
        let budget_code_selected = budget_code_div.val() || 0;
        let conditional_categories = div.ele('conditional_categories').val() || 0;
        let url = '/advanced-search/autocomplete/budget/budgetcode/' + fiscal_year + '/' + agency + '/' +
          dept.toString().replace(/\//g, "__") + '/' +
          expcategory.toString().replace(/\//g, "__") + '/' +
          budget_name.toString().replace(/\//g, "__")  + '/' +
          conditional_categories;

        if (url === budgetCodeAlreadyLoaded) {
          return;
        }
        budgetCodeAlreadyLoaded = url;
        if (url) {
          $.ajax({
            url: url,
            success: function (data) {
              let html = '<option value="0" title="">Select Budget Code</option>';
              if (data[0]) {
                if (data[0].label !== 'No Matches Found') {
                  for (let i = 0; i < data.length; i++) {
                    let value = data[i].trim();
                    html = html + '<option title="' + value + '" value="' + value + '">' + value + '</option>';
                  }
                }
              }
              budget_code_div.html(html);
              if (!$('option[value="' + budget_code_selected.trim().replace('"', '') + '"]', budget_code_div).length) {
                budget_code_selected = 0;
              }
              budget_code_div.val(budget_code_selected).trigger("chosen:updated");
              if (budget_name !== div.ele('budget_name').val()) {
                //@ToDo: check if below needed to call itself back
                //reloadBudgetCode(div);
              }
            }
          });
        }
      }

      let budgetNamesAlreadyLoaded = false;

      let reloadBudgetName = function (div, cevent_changed = false) {
        let fiscal_year = div.ele('year').val() || 0;
        let agency = div.ele('agency').val() || 0;
        let dept = div.ele('department').val() || 0;
        let expcategory = div.ele('expense_category').val() || 0;
        let budget_name_div = div.ele('budget_name');
        let budget_code = cevent_changed ? 0 : (div.ele('budget_code').val() || 0);
        let budget_name = cevent_changed ? 0 : (budget_name_div.val() || 0);
        let budget_name_selected = budget_name_div.val() || 0;
        let conditional_categories = div.ele('conditional_categories').val() || 0;
        //  path: '/data-feeds/budget_type/{domain}/{dataSource}/{budgetName}/{json}'
        let url = '/advanced-search/autocomplete/budget/budgetname/' + fiscal_year + '/' + agency + '/' +
          dept.toString().replace(/\//g, "__") + '/' +
          expcategory.toString().replace(/\//g, "__") + '/' +
          budget_code  + '/' + conditional_categories;

        if (url === budgetNamesAlreadyLoaded) {
          return;
        }
        budgetNamesAlreadyLoaded = url;

        $.ajax({
          url: url,
          success: function (data) {
            let html = '<option value="0" title="">Select Budget Name</option>';
            if (data[0]) {
              if (data[0].label !== 'No Matches Found') {
                for (let i = 0; i < data.length; i++) {
                  let value = data[i].value.trim();
                  html = html + '<option title="' + value + '" value="' + value + '">' + data[i].label.trim() + '</option>';
                }
              }
            }
            budget_name_div.html(html);
            if (!$('option[value="' + budget_name_selected.trim().replace('"', '') + '"]', budget_name_div).length) {
              budget_name_selected = 0;
            }
            budget_name_div.val(budget_name_selected).trigger("chosen:updated");
            if (budget_code !== div.ele('budget_code').val()) {
              //@ToDo: check if below needed to call itself back
              //reloadBudgetName(div);
            }
          }
        });
      }

      let onBudgetAgencyChange = function (div) {
        let agency_id = parseInt(div.ele('agency').val() ?? '');
        if (!agency_id) {
          disable_input([div.ele('department').val("0"), div.ele('expense_category').val("0")]);
        } else {
          reloadDepartment(div);
          reloadExpenseCategory(div);
        }
        reloadBudgetCode(div);
        reloadBudgetName(div);
      }

      let agency_id = parseInt($('select[name="checkbook_budget_agency"]').val() ?? '');
      if (!agency_id) {
        disable_input([$('select[name="checkbook_budget_department"]').val("0"), $('select[name="checkbook_budget_expense_category"]').val("0")]);
      } else {
        onBudgetAgencyChange(div_checkbook_budget);
      }

      let budgetTypeAlreadyLoaded = false;

      let reloadBudgetType = function (div) {
        let budget_name = div.ele('nycha_budget_name').val();
        budget_name = (budget_name && budget_name.length > 0) ? encodeURIComponent((budget_name).replace('/', '__')) :0;
        let budget_type = div.ele('budget_type').val() ?? '';
        let data_source = 'checkbook_nycha';
        let url = '/data-feeds/budget_type/budget/' + data_source + '/' + budget_name+ '/' + true;
        if (url === budgetTypeAlreadyLoaded) {
          return;
        }
        budgetTypeAlreadyLoaded = url;
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
              //reloadBudgetType(div);
            }
          }
        });
      }

      let nychaBudgetNamesAlreadyLoaded = false;

      let reloadNychaBudgetName = function (div) {
        let budget_type = div.ele('budget_type').val();
        budget_type = (budget_type && budget_type.length > 0) ? encodeURIComponent((budget_type).replace('/', '__')) :0;
        let budget_name = div.ele('nycha_budget_name').val();
        let data_source = 'checkbook_nycha';
        let url = '/data-feeds/budget_name/budget/' + data_source + '/' + budget_type + '/' + true;

        if (url === nychaBudgetNamesAlreadyLoaded) {
          return;
        }
        nychaBudgetNamesAlreadyLoaded = url;
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
              //reloadNychaBudgetName(div);
            }
          }
        });
      }

      let onBudgetDataSourceChange = function (dataSource) {
        let domain = 'budget';
        /* Initialize view by data source */
        switch (dataSource) {
          case "checkbook_nycha":
            //resetFields(div_checkbook_budget_nycha.contents());
            clearInputFields(div_checkbook_budget_nycha.contents().children(), domain, dataSource);
            div_checkbook_budget.contents().hide();
            div_checkbook_budget_nycha.contents().show();

            //Reset Budget Name and Budget Code Chosen drop-downs
            div_checkbook_budget_nycha.ele('budget_type').val("0").trigger("chosen:updated");
            div_checkbook_budget_nycha.ele('nycha_budget_name').val("0").trigger("chosen:updated");
            reloadBudgetType(div_checkbook_budget_nycha);
            reloadNychaBudgetName(div_checkbook_budget_nycha);

            break;

          default:
            clearInputFields(div_checkbook_budget.contents().children(), domain, dataSource);
            div_checkbook_budget.contents().show();
            div_checkbook_budget_nycha.contents().hide();
            //Disable department and Expense Category Drop-downs
            let agency_id = parseInt(div_checkbook_budget.ele('agency').val() ?? '');
            if (!agency_id) {
              disable_input([div_checkbook_budget.ele('department').val("0"), div_checkbook_budget.ele('expense_category').val("0")]);
            }
            //Reset Budget Name and Budget Code Chosen drop-downs
            div_checkbook_budget.ele('budget_code').val("0").trigger("chosen:updated");
            div_checkbook_budget.ele('budget_name').val("0").trigger("chosen:updated");
            reloadBudgetCode(div_checkbook_budget);
            reloadBudgetName(div_checkbook_budget);
            // Update year drop down
            div_checkbook_budget.ele('conditional_categories').removeAttr("disabled");
            updateEventYearValue("select[name='checkbook_budget_year'] option", '0', domain);

        }
      }

      let dataSource = $('input:radio[name=budget_advanced_search_domain_filter]:checked').val() ? $('input:radio[name=revenue_advanced_search_domain_filter]:checked').val() : "checkbook";
      onBudgetDataSourceChange(dataSource);

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
        let yval = $(this).find("option:selected").text();
        reloadBudgetCode(div_checkbook_budget);
        reloadBudgetName(div_checkbook_budget);
        updateConditionalEventValue(div_checkbook_budget.ele('conditional_categories'), yval, false,'budget');
      });

      // Reload budget type and budget code on conditional category reload.
      div_checkbook_budget.ele('conditional_categories').change(function () {
        let cevent = div_checkbook_budget.ele('conditional_categories').val();
        reloadBudgetCode(div_checkbook_budget, true);
        reloadBudgetName(div_checkbook_budget, true);
        updateEventYearValue("select[name='checkbook_budget_year'] option", cevent, 'budget');
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
        if ($(this).val() === 'Select Budget Name' || $(this).val() === '' || $(this).val() === 0) {
          reloadNychaBudgetName(div_checkbook_budget_nycha);
        }
      });

      div_checkbook_budget_nycha.ele('budget_type').change(function () {
        reloadNychaBudgetName(div_checkbook_budget_nycha);
        if ($(this).val() === 'Select Budget Type' || $(this).val() === '' || $(this).val() === 0) {
          reloadBudgetType(div_checkbook_budget_nycha);
        }
      });

      //On clicking "Clear"
      $('div.budget-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
        onBudgetDataSourceChange('checkbook');
        e.preventDefault();
      });
      $('div.budget-submit.checkbook-nycha').find('input:submit[value="Clear All"]').click(function (e) {
        onBudgetDataSourceChange('checkbook_nycha');
        e.preventDefault();
      });
    }
  };
}(jQuery, Drupal, drupalSettings));
