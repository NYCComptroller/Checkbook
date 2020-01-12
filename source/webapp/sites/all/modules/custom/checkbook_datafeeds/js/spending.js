(function ($) {

  // When Agency Filter is changed reload Department and Expense Category drop-downs
  let reloadSpendingDepartments = function () {
    let agency = $('#edit-agency').val();
    let html = '<option value="0" selected="selected">Select Department</option>';
    let old_val = $('#edit-dept').val();
    let data_source = $('input[name="datafeeds-spending-domain-filter"]:checked').val();

    if(data_source === 'checkbook' && $.inArray(agency, ["", null, "0", 'Select One', 'Citywide (All Agencies)']) != -1){
      $('#edit-dept').html(html);
      disable_input($('#edit-dept'));
    }else{
      $('#edit-dept').addClass('loading');
      let year = 0;
      if ($('input:radio[name=date_filter]:checked').val() == 0) {
        year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
      }
      //We need agency filter only for citywide
      if(data_source === 'checkbook'){agency = emptyToZero(agency);}else{agency = 0;}
      let spending_cat = getSpendingExpenseType(data_source);

      //let filter = new URLSearchParams();
      //if(agency){filter.set('agency_code',agency);}
      //if(year){filter.set('fiscal_year',dfSpendingGetYearDigitValue(year));}
      //if(spending_cat){filter.set('spending_category_id', spending_cat);}

      $.ajax({
        //url: '/solr_options/'+data_source+'/spending/department_name_code/?'+filter
        url: '/advanced-search/autocomplete/spending/department/' + year + '/' + agency + '/' + spending_cat + '/' + data_source + '/feeds'
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
        $('#edit-dept').html(html);
        }, complete: function () {
          enable_input($('#edit-dept'));
          $('#edit-dept').removeClass('loading');
        }
      });
      if(0 != $('#edit-dept option[value="'+old_val+'"]').length) {
        $('#edit-dept').val(old_val);
      }
    }
  };

  // When Department Filter is changed reload Expense category Drop-down
  let reloadSpendingExpenceCategories = function () {
    let agency = $('#edit-agency').val();
    let data_source = $('input[name="datafeeds-spending-domain-filter"]:checked').val();
    let html = '<option value="0" selected="selected">Select Expense Category</option>';

    if(data_source == 'checkbook' && $.inArray(agency, ["", null, "0", 'Select One', 'Citywide (All Agencies)']) != -1){
      $('#edit-expense-category').html(html);
      disable_input($('#edit-expense-category'));
    }else{
      $('#edit-expense-category').addClass('loading');

      let dept = emptyToZero($('#edit-dept').val());
      let old_val = $('#edit-expense-category').val();
      let year = 0;
      if ($('input:radio[name=date_filter]:checked').val() == 0) {
        year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
      }
      //We need agency filter only for citywide
      if(data_source === 'checkbook'){agency = emptyToZero(agency);}else{agency = 0;}
      let spending_cat = getSpendingExpenseType(data_source);

      /*let filter = new URLSearchParams();
      if(agency){filter.set('agency_code', agency);}
      if(dept){filter.set('department_code', dept);}
      if(spending_cat){filter.set('spending_category_id', spending_cat);}
      if(year){filter.set('fiscal_year', dfSpendingGetYearDigitValue(year));}*/

      $.ajax({
        //url: '/solr_options/'+data_source+'/spending/expenditure_object_name_code/?'+filter
        url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept + '/' + spending_cat + '/' + data_source + '/feeds'
        , success: function (data) {
          let html = '<option select="selected" value="0" >Select Expense Category</option>';
          if (data[0]) {
            if (data[0] !== 'No Matches Found') {
              $.each(data, function (key, exp_cat) {
                html = html + '<option value="' + exp_cat.code + '" title="' + exp_cat.title +'">' + exp_cat.name + '</option>';
              });
            }
            else {
              html = html + '<option value="">' + data[0] + '</option>';
            }
          }
          $('#edit-expense-category').html(html);
        }, complete: function () {
          enable_input($('#edit-expense-category'));
          $('#edit-expense-category').removeClass('loading');
        }
      });
      if(0 != $('#edit-expense-category option[value="'+old_val+'"]').length) {
        $('#edit-expense-category').val(old_val);
      }
    }
  };

  //On Data Source Change
  let onSpendingDataSourceChange = function (dataSource) {
    //Remove all the validation errors when data source is changed
    $('div.messages').remove();
    $('.error').removeClass('error');

    //Show Hide fields
    showHideSpendingFields(dataSource);

    //Clear all text fields and drop-downs
    clearSpendingInput();

    //Reset the Spending Category
    $('select[name="expense_type"]').val('Total Spending [ts]');
    enable_input($('input[name="payee_name"]'));
    enable_input($('input[name="contractno"]'));
  };

  //ShowHide fields based on selected data source
  let showHideSpendingFields = function (data_source) {
    $('.datafield.citywide').add('.datafield.nycha').add('.datafield.nycedc').hide();
    $('#edit-columns .form-item').hide();
    //let datefilter = $('input:radio[name=date_filter]:checked').val();

    // Department and Expense Category drop-downs are reset
    reloadSpendingDepartments();
    reloadSpendingExpenceCategories();

    switch (data_source) {
      case 'checkbook_oge':
        $('.datafield.nycedc').show();

        //Hide agency and enable and get department and expense category drop-down option
        $('.data-feeds-wizard .datafield.agency').hide();

        // Enable year
        $('input:radio[name=date_filter]')[0].checked = true;
        enable_input($('select[name="year"]'));
        $('select[name="year"]').val(0);
        //Disable Issue date
        disable_input($('input:radio[name=date_filter][value="1"]'));
        $('input[name="issuedfrom"]').val("");
        disable_input($('input[name="issuedfrom"]'));
        $('input[name="issuedto"]').val("");
        disable_input($('input[name="issuedto"]'));

        $('.form-item-oge-column-select').show();

        //Move Issue Date fields to left column for OGE
        $('#df-payeename').detach().appendTo('.spending.data-feeds-wizard .column.column-left');
        $('label[for=edit-payee-name]').text('Payee Name');
        $('#df-check_amount').detach().appendTo('.spending.data-feeds-wizard .column.column-left');
        $('label[for=edit-agency]').text('Other Government Entity:');
        break;
      case 'checkbook_nycha':
        $('.datafield.nycha').show();

        //Hide agency and enable and get department and expense category drop-down option
        $('.data-feeds-wizard .datafield.agency').hide();

        // Date filter
        $('input:radio[name=date_filter]')[0].checked = true;
        enable_input($('select[name="year"]'));
        $('select[name="year"]').val(0);
        //enable Issue date
        enable_input($('input:radio[name=date_filter][value="1"]'));
        disable_input($('input[name="issuedfrom"]'));
        disable_input($('input[name="issuedto"]'));

        $('.form-item-nycha-column-select').show();

        //Move Issue Date fields to left column for NYCHA
        $('#df-check_amount').detach().prependTo('.spending.data-feeds-wizard .column.column-right');
        $('#df-payeename').detach().prependTo('.spending.data-feeds-wizard .column.column-right');
        $('label[for=edit-payee-name]').text('Vendor');
        $('label[for=edit-agency]').text('Other Government Entity:');
        break;
      default:
        $('.datafield.citywide').show();

        //Show agency drop-down and disable department and expense category drop-downs
        $('.data-feeds-wizard .datafield.agency').show();
        disable_input($('#edit-dept'));
        disable_input($('#edit-expense-category'));

        //Date Filter
        $('input:radio[name=date_filter]')[0].checked = true;
        enable_input($('select[name="year"]'));
        $('select[name="year"]').val(0);
        //enable Issue date
        enable_input($('input:radio[name=date_filter][value="1"]'));
        disable_input($('input[name="issuedfrom"]'));
        disable_input($('input[name="issuedto"]'));

        $('.form-item-column-select').show();

        //Move Issue Date fields to left column for Citywide
        $('#df-check_amount').detach().prependTo('.spending.data-feeds-wizard .column.column-right');
        $('#df-payeename').detach().appendTo('.spending.data-feeds-wizard .column.column-left');
        $('label[for=edit-payee-name]').text('Payee Name');
        $('label[for=edit-agency]').text('Agency:');
        break;
    }

    //Reset enabling/disabling fields
     onSpendingCategoryChange();
  };

  let getSpendingExpenseType = function(data_source){
    switch(data_source){
      case 'checkbook_nycha':
        return emptyToZero($('select[name="nycha_expense_type"]').val());
      case 'checkbook_oge':
        return emptyToZero($('select[name="nycedc_expense_type"]').val());
      default:
        return emptyToZero($('select[name="expense_type"]').val());
    }
  };

  let onSpendingCategoryChange = function() {
    //Data source value
    let data_source = $('input[name="datafeeds-spending-domain-filter"]:checked').val();

    if(data_source === 'checkbook_nycha') {
      let exptype = $('select[name="nycha_expense_type"]').val();
      //NYCHA - disabling fields based on Spending category selected
      if (exptype === "Payroll [PAYROLL]") {
        disable_input([$('#edit-payee-name'), $('#edit-contractno'), $('#edit-document-id'),$('#edit-nycha-industry'),
                       $('#edit-funding-source'),$('#edit-resp-center'), $('#edit-purchase-order-type'), $('#edit-spent_amt_from'),
                       $('#edit-spent_amt_to')]);
        enable_input([$('#edit-dept'), $('#edit-expense-category')]);
        $('#edit-contractno').val("");
        $('#edit-payee-name').val("");
      }else if(exptype == "Section 8 [SECTION8]") {
        disable_input([ $('#edit-dept'),$('#edit-contractno'), $('#edit-nycha-industry'), $('#edit-purchase-order-type'),]);
        enable_input([$('#edit-payee-name'),$('#edit-document-id'),$('#edit-resp-center'), $('#edit-spent_amt_from'),
                      $('#edit-spent_amt_to'),$('#edit-expense-category'),$('#edit-funding-source')]);
      }else if(exptype == "Other [OTHER]") {
        disable_input([$('#edit-dept'),$('#edit-contractno')]);
        enable_input([ $('#edit-expense-category'),$('#edit-payee-name'),$('#edit-document-id'),
          $('#edit-nycha-industry'), $('#edit-funding-source'), $('#edit-resp-center'),
          $('#edit-purchase-order-type'), $('#edit-spent_amt_from'), $('#edit-spent_amt_to')]);
      }else{
        enable_input([$('#edit-dept'), $('#edit-expense-category'),$('#edit-payee-name'),$('#edit-document-id'),
          $('#edit-nycha-industry'), $('#edit-funding-source'),$('#edit-resp-center'), $('#edit-contractno'),
          $('#edit-purchase-order-type'), $('#edit-spent_amt_from'), $('#edit-spent_amt_to')]);
      }
    }else{
      //CITYWIDE and OGE - disabling fields based on Spending category selected
      let exptype = $('select[name="expense_type"]').val();
      enable_input([$('input[name="contractno"]'), $('input[name="payee_name"]'), $('option[value="Payee Name"]'),
                    $('option[value="payee_name"]'),$('option[value="Contract ID"]'), $('option[value="contract_ID"]'),
                    $('#edit-document-id')]);
      if (exptype === 'Payroll [p]') {
        //Disable Payee Name and ContractID fields for Payroll Spending Category
        disable_input([$('input[name="contractno"]'), $('input[name="payee_name"]'), $('option[value="Payee Name"]'),
                        $('option[value="Contract ID"]'),$('option[value="contract_ID"]')]);

      }
      if (exptype === 'Others [o]') {
        //Disable ContractID field for Others Spending Category
        disable_input([$('input[name="contractno"]'),$('option[value="Contract ID"]'), $('option[value="contract_ID"]')]);
      }
    }
  };

  Drupal.behaviors.spendingDataFeeds = {
    attach: function (context) {
      let dataSource = $('input[name="datafeeds-spending-domain-filter"]:checked', context).val();

      $.fn.formatDatafeedsDatasourceRadio();

      $('#checkbook-datafeeds-data-feed-wizard', context).submit(function () {
        enable_input($('#edit-agency'));
      });

      // Sets up multi-select/option transfer for CityWide
      $('#edit-column-select', context).multiSelect();
      if (!$('#ms-edit-column-select .ms-selection', context).next().is("a")) {
        $('#ms-edit-column-select .ms-selection', context).after('<a class="deselect">Remove All</a>');
        $('#ms-edit-column-select .ms-selection', context).after('<a class="select">Add All</a>');
      }
      $('#ms-edit-column-select a.select', context).click(function () {
        $('#edit-column-select', context).multiSelect('select_all');
      });
      $('#ms-edit-column-select a.deselect', context).click(function () {
        $('#edit-column-select', context).multiSelect('deselect_all');
      });

      // Sets up multi-select/option transfer for OGE
      $('#edit-oge-column-select', context).multiSelect();
      if (!$('#ms-edit-oge-column-select .ms-selection', context).next().is("a")) {
        $('#ms-edit-oge-column-select .ms-selection', context).after('<a class="deselect">Remove All</a>');
        $('#ms-edit-oge-column-select .ms-selection', context).after('<a class="select">Add All</a>');
      }
      $('#ms-edit-oge-column-select a.select', context).click(function () {
        $('#edit-oge-column-select', context).multiSelect('select_all');
      });
      $('#ms-edit-oge-column-select a.deselect', context).click(function () {
        $('#edit-oge-column-select', context).multiSelect('deselect_all');
      });

      // Sets up multi-select/option transfer for nycha
      $('#edit-nycha-column-select', context).multiSelect();
      if (!$('#ms-edit-nycha-column-select .ms-selection', context).next().is("a")) {
        $('#ms-edit-nycha-column-select .ms-selection', context).after('<a class="deselect">Remove All</a>');
        $('#ms-edit-nycha-column-select .ms-selection', context).after('<a class="select">Add All</a>');
      }
      $('#ms-edit-nycha-column-select a.select', context).click(function () {
        $('#edit-nycha-column-select', context).multiSelect('select_all');
      });
      $('#ms-edit-nycha-column-select a.deselect', context).click(function () {
        $('#edit-nycha-column-select', context).multiSelect('deselect_all');
      });

      //Display or hide fields based on data source selection
      showHideSpendingFields(dataSource);

      //Preserve field dsplay configuration based on Spending category value
      onSpendingCategoryChange();

      //Data Source change event
      $('input:radio[name=datafeeds-spending-domain-filter]', context).change(function () {
        onSpendingDataSourceChange($(this, context).val());
      });

      //Agency drop-down change event
      $('select[name="agency"]', context).change(function () {
        reloadSpendingDepartments();
        reloadSpendingExpenceCategories();
      });

      //Agency drop-down change event
      $('select[name="year"]', context).change(function () {
        reloadSpendingDepartments();
        reloadSpendingExpenceCategories();
      });

      //Department drop-down change event
      $('select[name="dept"]', context).change(function () {
        reloadSpendingExpenceCategories();
      });

      //Spending Category change event
      $('select[name="expense_type"]', context).change(function () {
          onSpendingCategoryChange();
      });

      $('select[name="nycedc_expense_type"]', context).change(function () {
        onSpendingCategoryChange();
      });

      $('select[name="nycha_expense_type"]', context).change(function () {
        onSpendingCategoryChange();
      });


      //On Date Filter change
      $("#edit-date-filter", context).change(function () {
        if ($('input:radio[name=date_filter]:checked', context).val() === '1') {
          // issue date
          disable_input($('select[name="year"]', context));
          enable_input($('input[name="issuedfrom"]', context));
          enable_input($('input[name="issuedto"]', context));
        } else{
          // year
          enable_input($('select[name="year"]', context));
          $('input[name="issuedfrom"]', context).val("");
          disable_input($('input[name="issuedfrom"]', context));
          $('input[name="issuedto"]', context).val("");
          disable_input($('input[name="issuedto"]', context));
        }
      });

      //Sets up jQuery UI datepickers
      const currentYear = new Date().getFullYear();
      $('.datepicker', context).datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        yearRange: '-' + (currentYear - 1900) + ':+' + (2500 - currentYear)
      });

      //Sets up jQuery UI autocompletes and autocomplete filtering functionality
      let year = 0;
      if ($('input:radio[name=date_filter]:checked').val() === '0') {
        year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
      }

      let data_source = $('input[name="datafeeds-spending-domain-filter"]:checked', context).val();
      let dept = encodeURIComponent($('#edit-dept', context).val());
      let agency = emptyToZero($('#edit-agency', context).val());
      let exp_cat = encodeURIComponent($('#edit-expense-category', context).val());
      let spend_cat = getSpendingExpenseType(data_source);
      let mwbe_cat = emptyToZero($('#edit-mwbe-category', context).val());
      let industry = emptyToZero($('#edit-industry', context).val());

      //Sets up jQuery UI autocompletes and autocomplete filtering functionality
      $('#edit-payee-name', context).autocomplete({source: '/autocomplete/spending/payee/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source});
      $('#edit-contractno', context).autocomplete({source: '/autocomplete/spending/contractno/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source});
      $('#edit-document-id', context).autocomplete({source: '/autocomplete/spending/documentid/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source});
      $('#edit-capital-project', context).autocomplete({source: '/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source});
      $('#edit-entity-contract-number', context).autocomplete({source: '/autocomplete/spending/entitycontractnum/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source});
      $('#edit-commodity-line', context).autocomplete({source: '/autocomplete/spending/commodityline/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + '/' + industry + data_source});
      $('#edit-budget-name', context).autocomplete({source: '/autocomplete/spending/budgetname/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + '/' + industry + data_source});
      $('.watch:input', context).each(function () {
        $(this).focusin(function () {
          //set variables for each field's value
          year = 0;
          if ($('input:radio[name=date_filter]:checked').val() === '0') {
            year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
          }
          dept = encodeURIComponent($('#edit-dept', context).val());
          agency = emptyToZero($('#edit-agency', context).val());

          exp_cat = encodeURIComponent($('#edit-expense-category', context).val());
          mwbe_cat = emptyToZero($('#edit-mwbe-category', context).val());
          industry = emptyToZero($('#edit-industry', context).val());
          data_source = $('input[name="datafeeds-spending-domain-filter"]:checked', context).val();
          spend_cat = getSpendingExpenseType(data_source);

          $("#edit-payee-name", context).autocomplete("option", "source", '/autocomplete/spending/payee/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source);
          $('#edit-contractno', context).autocomplete("option", "source", '/autocomplete/spending/contractno/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source);
          $('#edit-document-id', context).autocomplete("option", "source", '/autocomplete/spending/documentid/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source);
          $('#edit-capital-project', context).autocomplete("option", "source", '/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source);
          $('#edit-entity-contract-number', context).autocomplete("option", "source", '/autocomplete/spending/entitycontractnum/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source);
          $('#edit-commodity-line', context).autocomplete("option", "source", '/autocomplete/spending/commodityline/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source);
          $('#edit-budget-name', context).autocomplete("option", "source", '/autocomplete/spending/budgetname/' + year + '/' + agency + '/' + exp_cat + '/' + dept + '/' + spend_cat + '/' + mwbe_cat + '/' + industry + '/' + data_source);
        });
      });
      fixAutoCompleteWrapping($("#dynamic-filter-data-wrapper").children());
    }
  };

  //Prevent the auto-complete from wrapping un-necessarily
  function fixAutoCompleteWrapping(divWrapper) {
    jQuery(divWrapper.children()).find('input.ui-autocomplete-input:text').each(function () {
      $(this).data("autocomplete")._resizeMenu = function () {
        (this.menu.element).outerWidth('100%');
      }
    });
  }

  //Function to retrieve values enclosed in brackets or return zero if none
  function emptyToZero(input) {
    if (null === input) {return 0}
    const p = /\[(.*?)]$/;
    const code = p.exec(input.trim());
    if (code) {
      return code[1];
    }
    return 0;
  }

  //Function to clear text fields and drop-downs
  let clearSpendingInput = function () {
    $('.fieldset-wrapper').find(':input').each(function () {
      switch (this.type) {
        case 'select-one':
          const default_option = $(this).attr('default_selected_value');
          if (default_option) {
            $(this).find('option[value=' + default_option + ']').attr("selected", "selected");
          } else {
            $(this).find('option:first').attr("selected", "selected");
          }
          break;
        case 'text':
          $(this).val('');
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
    })
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
    })
  }

}(jQuery));
