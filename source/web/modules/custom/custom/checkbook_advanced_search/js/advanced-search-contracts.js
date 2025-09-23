(function ($, Drupal, drupalSettings) {
  Drupal.advancedSearchAndAlertsContract = {
    'advanced_search_contracts_init': function () {
      //var year, department_name, agency_id, spending_category_id, expenditure_object_code, minority_type_id,
      //  industry_type_id, datasource;
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
          'end_date_from': 'input[type="date"][name="' + data_source + '_contracts_end_date_from"]',
          'end_date_to': 'input[type="date"][name="' + data_source + '_contracts_end_date_to"]',
          'registration_date_from': 'input[type="date"][name="' + data_source + '_contracts_registration_date_from"]',
          'registration_date_to': 'input[type="date"][name="' + data_source + '_contracts_registration_date_to"]',
          'category': 'select[name=' + data_source + '_contracts_category]',
          'conditional_categories': 'select[name=' + data_source + '_contracts_conditional_categories]',
          'sub_vendor_status': 'select[name="' + data_source + '_contracts_sub_vendor_status"]',
          'purpose': 'input:text[name=' + data_source + '_contracts_purpose]',
          'agency': 'select[name=' + data_source + '_contracts_agency]',
          'industry': 'select[name=' + data_source + '_contracts_industry]',
          'apt_pin': 'input:text[name=' + data_source + '_contracts_apt_pin]',
          'award_method': 'select[name=' + data_source + '_contracts_award_method]',
          'start_date_from': 'input[type="date"][name="' + data_source + '_contracts_start_date_from"]',
          'start_date_to': 'input[type="date"][name="' + data_source + '_contracts_start_date_to"]',
          'received_date_from': 'input[type="date"][name="' + data_source + '_contracts_received_date_from"]',
          'received_date_to': 'input[type="date"][name="' + data_source + '_contracts_received_date_to"]',
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

      div_checkbook_contracts_nycha.ele('purchase_order_type').change(function () {
        onAgreementTypeChange();
      });


      function onAgreementTypeChange() {
        var agreement_type_val = div_checkbook_contracts_nycha.ele('purchase_order_type').val();
        //alert(agreement_type_val);
        if (agreement_type_val === '0'){
           agreement_type_val = 'ALL';
        }
        switch (agreement_type_val) {
          case 'PO':
            disable_input(['.form-item-checkbook-nycha-contracts-start-date-from :input', '.form-item-checkbook-nycha-contracts-start-date-to :input','.form-item-checkbook-nycha-contracts-end-date-from :input','.form-item-checkbook-nycha-contracts-end-date-to :input']);
            enable_input(['.form-item-checkbook-nycha-contracts-approved-date-from :input','.form-item-checkbook-nycha-contracts-approved-date-to :input']);
            break;
          case 'BA':
          case 'PA':
            enable_input(['.form-item-checkbook-nycha-contracts-start-date-from :input','.form-item-checkbook-nycha-contracts-start-date-to :input','.form-item-checkbook-nycha-contracts-end-date-from :input', '.form-item-checkbook-nycha-contracts-end-date-to :input']);
            disable_input(['.form-item-checkbook-nycha-contracts-approved-date-from :input','.form-item-checkbook-nycha-contracts-approved-date-to :input']);
            break;
          case 'ALL':
          default:
            disable_input(['.form-item-checkbook-nycha-contracts-start-date-from :input','.form-item-checkbook-nycha-contracts-start-date-to :input','.form-item-checkbook-nycha-contracts-end-date-from :input','.form-item-checkbook-nycha-contracts-end-date-to :input']);
            disable_input(['.form-item-checkbook-nycha-contracts-approved-date-from :input','.form-item-checkbook-nycha-contracts-approved-date-to :input']);
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
        var sub_contract_status = div.ele('sub_vendor_status').parent();
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
        removePrimeAndSubIcon(sub_contract_status);
        removePrimeAndSubIcon(purpose);
        removePrimeAndSubIcon(industry);
        removePrimeAndSubIcon(year);

        var contract_status_val = div.ele('status').val();
        var category_val = div.ele('category').val();

        if ((contract_status_val === 'A' || contract_status_val === 'R') && (category_val === 'expense' || category_val === 'all')) {
          $("<div class='prime-and-sub-note'>All Fields are searchable by Prime data, unless designated as Prime & Sub (<img src='/themes/custom/nyccheckbook/images/prime-and-sub.png' />).</div>").insertBefore($("#div_contracts_data_source"));
          addPrimeAndSubIcon(contract_status);
          addPrimeAndSubIcon(vendor);
          addPrimeAndSubIcon(mwbe_category);
          addPrimeAndSubIcon(current_amt_from);
          addPrimeAndSubIcon(category);
          addPrimeAndSubIcon(sub_contract_status);
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
        var primeAndSubIcon = "<img class='prime-and-sub' src='/themes/custom/nyccheckbook/images/prime-and-sub.png' />";
        $(ele).find('label').first().prepend(primeAndSubIcon);
        ele.addClass('asterisk-style');
      }


      function onChangeDataSource(dataSource) {
        /* Reset all the fields for the data source */
        let domain = 'contracts';

        /* Initialize the disabled fields */
        onStatusChange(div_checkbook_contracts);
        onStatusChange(div_checkbook_contracts_oge);
        onCategoryChange(div_checkbook_contracts);

        /* Initialize view by data source */
        switch (dataSource) {
          case "checkbook_oge":
            clearInputFields(div_checkbook_contracts_oge.contents().children(), domain, dataSource);
            initializeContractsView(div_checkbook_contracts_oge);
            div_checkbook_contracts.contents().hide();
            div_checkbook_contracts_oge.contents().show();
            div_checkbook_contracts_nycha.contents().hide();
            //handle oge attributes
            div_checkbook_contracts_oge.ele('status').find('option[value=P]').remove();
            div_checkbook_contracts_oge.ele('category').find('option[value=revenue]').remove();
            div_checkbook_contracts_oge.ele('category').find('option[value=all]').remove();
            disable_input(div_checkbook_contracts_oge.ele('apt_pin'));
            disable_input(div_checkbook_contracts_oge.ele('received_date_from'));
            disable_input(div_checkbook_contracts_oge.ele('received_date_to'));
            disable_input(div_checkbook_contracts_oge.ele('registration_date_from'));
            disable_input(div_checkbook_contracts_oge.ele('registration_date_to'));

            // Remove note
            $(".prime-and-sub-note").remove();
            break;
          case "checkbook_nycha":
            clearInputFields(div_checkbook_contracts_nycha.contents().children(), domain, dataSource);
            div_checkbook_contracts_nycha.ele('purchase_order_type').val('0');
            initializeContractsView(div_checkbook_contracts_nycha);
            div_checkbook_contracts.contents().hide();
            div_checkbook_contracts_oge.contents().hide();
            div_checkbook_contracts_nycha.contents().show();
            onAgreementTypeChange();
            // Remove note
            $(".prime-and-sub-note").remove();
            break;
          default:
            clearInputFields(div_checkbook_contracts.contents().children(), domain, dataSource);
            initializeContractsView(div_checkbook_contracts);
            div_checkbook_contracts.contents().show();
            div_checkbook_contracts_oge.contents().hide();
            div_checkbook_contracts_nycha.contents().hide();
            //handle attributes
            onStatusChange(div_checkbook_contracts);
            div_checkbook_contracts.ele('sub_vendor_status').val('0');
            updateIncludeSubvendorsField(div_checkbook_contracts);
            showHidePrimeAndSubFields(div_checkbook_contracts);
            updateEventYearValue("select[name='checkbook_contracts_year'] option", '0');
            enable_input(div_checkbook_contracts.ele('conditional_categories'));
            break;
        }
      }

      function autoCompletes(div) {
        let contract_status = div.ele('status').val() || 0;
        let contract_category_name = div.ele('category').val() || 0;
        let minority_type_id = div.ele('mwbe_category').val() || 0;
        let industry_type_id = div.ele('industry').val() || 0;
        let contract_type_id = div.ele('contract_type').val();
        let agency_id = div.ele('agency').val() || 0;
        let award_method_code = div.ele('award_method').val() || 0;
        let scntrc_status = div.ele('includes_sub_vendors').val() || 0;
        let aprv_sta = div.ele('sub_vendor_status').val() || 0;
        let data_source = $('input:radio[name=contracts_advanced_search_domain_filter]:checked').val();
        let conditional_categories_id = div.ele('conditional_categories').val() || 0;
        let solr_datasource = data_source;
        let year = div.ele('year').val();
        let year_id=0;

        if(year){
          year_id = year.split('~')[1];
        }

        if ('checkbook_nycha' === data_source) {
          solr_datasource = 'nycha'
        }

        if ('nycha' === solr_datasource) {
          let agreement_type_code_nycha = $('[id^="edit-checkbook-nycha-contracts-purchase-order-type"]').val() || 0;
          let responsibility_center_nycha = $('[id^="edit-checkbook-nycha-contracts-responsibility-center"]').val() || 0;
          let contract_type_id_nycha = extractId($('[id^="edit-checkbook-nycha-contracts-type"]').val()) || 0;
          let award_method_id_nycha = extractId($('[id^="edit-checkbook-nycha-contracts-award-method"]').val()) || 0;
          let industry_type_id_nycha = $('[id^="edit-checkbook-nycha-contracts-industry"]').val() || 0;
          let nycha_filters = {
            agreement_type_code: agreement_type_code_nycha,
            responsibility_center_id: responsibility_center_nycha,
            contract_type_id: contract_type_id_nycha,
            award_method_id: award_method_id_nycha,
            industry_type_id: industry_type_id_nycha,
            agency_id: agency_id,
            fiscal_year_id: year_id
          };

           div.ele('vendor_name').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource,'vendor_name', nycha_filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('contract_id').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource,'contract_number', nycha_filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('pin').autocomplete({
             source:$.fn.autoCompleteSourceUrl(solr_datasource,'pin', nycha_filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
        } else {
          let filters = {
            event_id: conditional_categories_id,
            contract_status: contract_status,
            contract_category_name: contract_category_name,
            agency_id: agency_id,
            award_method_code: award_method_code,
            minority_type_id: minority_type_id,
            industry_type_id: industry_type_id,
            scntrc_status: scntrc_status,
            aprv_sta: aprv_sta,
            contract_type_id: contract_type_id,
          };
          if (contract_type_id === '0') {
            filters['contract_type_id'] = 0;
          }
          let year;
          if ('checkbook_oge' === solr_datasource && year_id !== 'all') {
            year = (div.ele('year').find("option:selected").text()).split(' ')[1];
          } else if ('checkbook' === solr_datasource && year_id !== 'all') {
            year = (div.ele('year').find("option:selected").text()).split(' ')[1];
          }

          if (year) {
            if (contract_status === 'R') {
              filters['registered_fiscal_year'] = year;
            }

            if (contract_status === 'A') {
              filters['facet_year_array'] = year;
            }
          }

           div.ele('vendor_name').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource,'vendor_name',filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('contract_id').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource,'contract_number', filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('apt_pin').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource,'apt_pin',filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('pin').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource,'pin',filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('entity_contract_number').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource,'contract_entity_contract_number',filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('commodity_line').autocomplete({
             source: $.fn.autoCompleteSourceUrl(solr_datasource,'contract_commodity_line',filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
           div.ele('budget_name').autocomplete({
             source:$.fn.autoCompleteSourceUrl(solr_datasource,'contract_budget_name',filters),
             select: function (event, ui) {
               $.fn.preventSelectionDefault(event, ui, "No Matches Found");
             }
           });
        }

        $('.ui-autocomplete-input').bind('autocompleteselect', function (event, ui) {
          ui.item.value = String(ui.item.value).search('No Matches Found') == -1 ? ui.item.value : '';
          $(this).val(ui.item.value);
          $(this).parent().next().val(ui.item.label);
        });
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
          //var ul = this.menu.element;
          //ul.outerWidth(this.element.outerWidth());
          //$(this).data("autocomplete")._resizeMenu = function () {
          //  (this.menu.element).outerWidth('100%');
          //}
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
        //console.log(div);
        var data_source = $('input[name=contracts_advanced_search_domain_filter]:checked').val();
        var contract_status = div.ele('status').val();
        let contract_category = div.ele('category').val();
        let yval = (div_checkbook_contracts.ele('year').find("option:selected").text()).split(' ')[1];
        if (contract_status === 'P') {
          if (data_source === 'checkbook') {
            disable_input(['.form-item-checkbook-contracts-registration-date-from :input', '.form-item-checkbook-contracts-registration-date-to :input']);
            disable_input(div.ele('conditional_categories'));
            disable_input(div.ele('includes_sub_vendors'));
            disable_input(div.ele('sub_vendor_status'));
            disable_input(div.ele('year'));
          }
          disable_input(div.ele('year'));
          div.ele('year')[0].selectedIndex = 0;
          enable_input(['.form-item-checkbook-contracts-received-date-from :input', '.form-item-checkbook-contracts-received-date-to :input']);
        } else {
          if (data_source === 'checkbook') {
            enable_input(['.form-item-checkbook-contracts-registration-date-from :input', '.form-item-checkbook-contracts-registration-date-to :input']);
            if (contract_category !== 'revenue' || yval > 2020) {
              enable_input(div.ele('conditional_categories'));
            }
            enable_input(div.ele('year'));
          }
          disable_input(['.form-item-checkbook-contracts-received-date-from :input', '.form-item-checkbook-contracts-received-date-to :input']);
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
          disable_input(div.ele('includes_sub_vendors'));
          disable_input(div.ele('sub_vendor_status'));
        } else {
          enable_input(div.ele('includes_sub_vendors'));
          enable_input(div.ele('sub_vendor_status'));

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
          disable_input(div.ele('sub_vendor_status'));
        } else {
          enable_input(div.ele('sub_vendor_status'));
        }
      }

      //On change of "Subcontract Status" -  NYCCHKBK-6187
      div_checkbook_contracts.ele('sub_vendor_status').change(function () {
        onSubvendorStatusChange(div_checkbook_contracts);
      });

      function updateEventsField(div) {
        let yval = (div_checkbook_contracts.ele('year').find("option:selected").text()).split(' ')[1];
        let contract_category = div.ele('category').val();
        let contract_status = div.ele('status').val();

        if (contract_category === 'revenue' || yval < 2020 || contract_status === 'P') {
          disable_input(div.ele('conditional_categories'));
          let catas_event = div_checkbook_contracts.ele('conditional_categories').val();
          updateEventYearValue("select[name='checkbook_contracts_year'] option", '0');
        } else {
          enable_input(div.ele('conditional_categories'));
        }

      }

      // On change of event value update the year options.
      div_checkbook_contracts.ele('conditional_categories').change(function () {
        let catas_event = div_checkbook_contracts.ele('conditional_categories').val();
        updateEventYearValue("select[name='checkbook_contracts_year'] option", catas_event);
      });

      function onSubvendorStatusChange(div) {
        updateIncludeSubvendorsField(div);
      }

      // On year of "year" if chosen year is less than 2020 disable conditional category field.
      div_checkbook_contracts.ele('year').change(function () {
        let yval = ($(this).find("option:selected").text()).split(' ')[1];
        let contract_category = div_checkbook_contracts.ele('category').val();
        updateConditionalEventValue(div_checkbook_contracts.ele('conditional_categories'), yval, contract_category === 'revenue');
      });

      function updateIncludeSubvendorsField(div) {
        var sub_vendor_status = div.ele('sub_vendor_status').val();
        var includes_sub_vendors = div.ele('includes_sub_vendors').val();

        if (['1', '2', '3', '4', '5', '6'].includes(sub_vendor_status)) {
          if (includes_sub_vendors === '2') {
            div.ele('includes_sub_vendors').html('<option value="0">Select Status</option>' +
              '<option value="2" selected>Yes</option>');
          } else {
            div.ele('includes_sub_vendors').html('<option value="0" selected>Select Status</option>' +
              '<option value="2">Yes</option>');
          }
        }
        if (sub_vendor_status === '0') {
          if (includes_sub_vendors === '2') {
            div.ele('includes_sub_vendors').html('<option value="0">Select Status</option>' +
              '<option value="2" selected>Yes</option>' +
              '<option value="3">No</option>' +
              '<option value="1">No Data Entered</option>' +
              '<option value="4">Not Required</option>');
          } else if (sub_vendor_status === '0') {
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

      //On clicking "Clear"
      $('div.contracts-submit.checkbook').find('input:submit[value="Clear All"]').click(function (e) {
        let dataSource = $('input:radio[name=contracts_advanced_search_domain_filter]:checked').val();
        onChangeDataSource('checkbook');
        e.preventDefault();
      });
      $('div.contracts-submit.checkbook-oge').find('input:submit[value="Clear All"]').click(function (e) {
        onChangeDataSource('checkbook_oge');
        e.preventDefault();
      });
      $('div.contracts-submit.checkbook-nycha').find('input:submit[value="Clear All"]').click(function (e) {
        onChangeDataSource('checkbook_nycha');
        e.preventDefault();
      });

      function extractId(param) {
        if (param && (param.indexOf('id=>') > -1)) {
          return param.split('~')[0].split('=>')[1];
        }
        return param;
      }
      //for displaying citywide form on first load
      let dataSource = $('input:radio[name=contracts_advanced_search_domain_filter]:checked').val() ? $('input:radio[name=contracts_advanced_search_domain_filter]:checked').val() : "checkbook";
      onChangeDataSource(dataSource);
    }
  };
}(jQuery, Drupal, drupalSettings));
