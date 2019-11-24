(function ($) {
  //On Data Source Change
  let onSpendingDataSourceChange = function (dataSource) {
    //Remove all the validation errors when data source is changed
    $('div.messages').remove();
    $('.error').removeClass('error');

    //Reload Agency Drop-down Options
    reloadSpendingAgencies(dataSource);

    //Show Hide fields
    showHideSpendingFields(dataSource);

    //Clear all text fields and drop-downs
    clearSpendingInput();

    //Reset the Spending Category
    $('select[name="expense_type"]').val('Total Spending [ts]');
    $('input[name="payee_name"]').removeAttr('disabled');
    $('input[name="contractno"]').removeAttr('disabled');
  };

  //ShowHide fields based on selected data source
  let showHideSpendingFields = function (data_source) {
    $('.datafield.citywide').add('.datafield.nycha').add('.datafield.nycedc').hide();
    $('#edit-columns .form-item').hide();

    let datefilter = $('input:radio[name=date_filter]:checked').val();

    switch (data_source) {
      case 'checkbook_oge':
        $('.datafield.nycedc').show();

        $('input:radio[name=date_filter]')[0].checked = true;
        $('select[name="year"]').removeAttr('disabled');
        //Disable Issue date
        $('input:radio[name=date_filter][value="1"]').attr('disabled', 'disabled');
        $('input[name="issuedfrom"]').val("");
        $('input[name="issuedfrom"]').attr('disabled', 'disabled');
        $('input[name="issuedto"]').val("");
        $('input[name="issuedto"]').attr('disabled', 'disabled');

        $('.form-item-oge-column-select').show();

        //Move Issue Date fields to left column for OGE
        $('#df-payeename').detach().appendTo('.spending.data-feeds-wizard .column.column-left');
        $('label[for=edit-payee-name]').text('Payee Name');
        $('#df-check_amount').detach().appendTo('.spending.data-feeds-wizard .column.column-left');
        $('label[for=edit-agency]').text('Other Government Entity:');
        break;
      case 'checkbook_nycha':
        $('.datafield.nycha').show();

        // Date filter
        $('input:radio[name=date_filter]')[0].checked = true;
        $('select[name="year"]').removeAttr('disabled');
        //Disable Issue date
        $('input:radio[name=date_filter][value="1"]').removeAttr('disabled');
        //Date Filter

        if (datefilter === '0') {
          $('input[name="issuedfrom"]').val("").attr('disabled', 'disabled');
          $('input[name="issuedto"]').val("").attr('disabled', 'disabled');
        } else {
          $('input:radio[name=date_filter]')[1].checked = true;
          $('select[name="year"]').attr('disabled', 'disabled');
        }

        $('.form-item-nycha-column-select').show();

        //Move Issue Date fields to left column for NYCHA
        $('#df-check_amount').detach().prependTo('.spending.data-feeds-wizard .column.column-right');
        $('#df-payeename').detach().prependTo('.spending.data-feeds-wizard .column.column-right');
        $('label[for=edit-payee-name]').text('Vendor');
        $('label[for=edit-agency]').text('Other Government Entity:');
        break;
      default:
        $('.datafield.citywide').show();

        //Date Filter
        $('input:radio[name=date_filter][value="1"]').removeAttr('disabled');

        if (datefilter === '0') {
          $('input[name="issuedfrom"]').val("").attr('disabled', 'disabled');
          $('input[name="issuedto"]').val("").attr('disabled', 'disabled');
        } else {
          $('input:radio[name=date_filter]')[1].checked = true;
          $('select[name="year"]').attr('disabled', 'disabled');
        }

        $('.form-item-column-select').show();

        //Move Issue Date fields to left column for Citywide
        $('#df-check_amount').detach().prependTo('.spending.data-feeds-wizard .column.column-right');
        $('#df-payeename').detach().appendTo('.spending.data-feeds-wizard .column.column-left');
        $('label[for=edit-payee-name]').text('Payee Name');
        $('label[for=edit-agency]').text('Agency:');
        break;
    }
  };

  //Load Agency Drop-Down
  let reloadSpendingAgencies = function (dataSource) {
    html = '';
    if ('citywide' == dataSource) {
      html = '<option value="0" selected="selected">Citywide (All Agencies)</option>';
    }
    $.ajax({
      url: '/solr_options/'+dataSource+'/spending/agency_name_code/'
      , success: function (data) {
        $('#edit-agency').html(html);
        if (data[0]) {
          for (let i = 0; i < data.length; i++) {
            $('#edit-agency').append(
              $('<option>').attr('title',data[i].value)
                .val(data[i].value)
                .text(data[i].label)
            );
          }
        } else {
          $('#edit-agency').attr('disabled', 'disabled');
        }
        $('#edit-agency').trigger('change');

        spendingDatafeedsDis(dataSource);
      }
      , complete: function () {
        $('#edit-agency').removeClass('loading');
      }
    });
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

  // When Agency Filter is changed reload Department and Expense Category drop-downs
  let reloadSpendingDepartments = function () {
    let agency = $('#edit-agency').val();
    let html = '<option value="0" selected="selected">Select Department</option>';

    if ($.inArray(agency, ["", null, 'Select One', 'Citywide (All Agencies)', 0]) === -1) {
      agency = emptyToZero(agency);
      $('#edit-dept').addClass('loading');
      let year = 0;
      if ($('input:radio[name=date_filter]:checked').val() === '0') {
        year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
        year = year.replace('ALL','').replace('FY','').trim();
        if(year){
          console.log(year);
          year = year.match(/\d+/)[0];
        }
      }
      const data_source = $('input[name="datafeeds-spending-domain-filter"]:checked').val();
      const spending_cat = getSpendingExpenseType(data_source);

      let filter = new URLSearchParams();
      if(agency){filter.set('agency_code',agency)}
      if(year){filter.set('fiscal_year',year)}
      if(spending_cat){filter.set('spending_category_id', spending_cat)}

      $.ajax({
        url: '/solr_options/'+data_source+'/spending/department_name_code/?'+filter
        , success: function (data) {
          $('#edit-dept').html(html);
          if (data[0]) {
            for (let i = 0; i < data.length; i++) {
              $('#edit-dept').append(
                $('<option>').attr('title',data[i].value.toUpperCase())
                  .val(data[i].value)
                  .text(data[i].label)
              );
            }
            $('#edit-dept').removeAttr('disabled');
          } else {
            $('#edit-dept').attr('disabled', 'disabled');
          }
        }
        , complete: function () {
          $('#edit-dept').removeClass('loading');
        }
      });
    } else {
      $('#edit-dept').html(html);
      $('#edit-dept').attr('disabled', 'disabled');
    }
  };

  // When Department Filter is changed reload Expense category Drop-down
  let reloadSpendingExpenceCategories = function () {
    let agency = $('#edit-agency').val();
    let dept = emptyToZero($('#edit-dept').val());
    let data_source = $('input[name="datafeeds-spending-domain-filter"]:checked').val();

    if ($.inArray(agency, ["", null, 'Select One', 'Citywide (All Agencies)']) === -1 ||
      ('checkbook_nycha'==data_source && dept)) {
      $('#edit-expense-category').addClass('loading');
      let year = 0;
      if ($('input:radio[name=date_filter]:checked').val() === '0') {
        year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
        year = year.replace('ALL','').replace('FY','').trim();
        if(year){
          console.log(year);
          year = year.match(/\d+/)[0];
        }
      }
      agency = emptyToZero(agency);
      let spending_cat = getSpendingExpenseType(data_source);

      let filter = new URLSearchParams();
      if(agency){filter.set('agency_code',agency)}
      if(dept){filter.set('department_code',dept)}
      if(spending_cat){filter.set('spending_category_id', spending_cat)}
      if(year){filter.set('fiscal_year',year)}

      $.ajax({
        url: '/solr_options/'+data_source+'/spending/expenditure_object_name_code/?'+filter
        , success: function (data) {
          let html = '<option select="selected" value="0" >Select Expense Category</option>';
          $('#edit-expense-category').html(html);
          if (data[0]) {
            if (data[0].label !== '') {
              for (let i = 0; i < data.length; i++) {
                $('#edit-expense-category').append(
                  $('<option>').attr('title',data[i].value.toUpperCase())
                    .val(data[i].value)
                    .text(data[i].label)
                );
              }
            }
            $('#edit-expense-category').removeAttr('disabled');
          } else {
            $('#edit-expense-category').attr('disabled', 'disabled');
          }
        }
        , complete: function () {
          $('#edit-expense-category').removeClass('loading');
        }
      });
    } else {
      let html = '<option value="0" selected="selected">Select Expense Category</option>';
      $('#edit-expense-category').html(html);
      $('#edit-expense-category').attr('disabled', 'disabled');
    }
  };

  let onSpendingCategoryChange = function() {
    $('input[name="contractno"]').removeAttr('disabled');
    $('input[name="payee_name"]').removeAttr('disabled');
    $('option[value="Payee Name"]').removeAttr('disabled');
    $('option[value="payee_name"]').removeAttr('disabled');
    $('option[value="Contract ID"]').removeAttr('disabled');
    $('option[value="contract_ID"]').removeAttr('disabled');

    const exptypeval = $('select[name="expense_type"]').val();
    if (exptypeval === 'Payroll [p]') {
      //Disable Payee Name and ContractID fields for Payroll Spending Category
      $('input[name="contractno"]').attr('disabled', 'disabled');
      $('input[name="payee_name"]').attr('disabled', 'disabled');
      $('option[value="Payee Name"]').attr('disabled', 'disabled');
      $('option[value="payee_name"]').attr('disabled', 'disabled');
      $('option[value="Contract ID"]').attr('disabled', 'disabled');
      $('option[value="contract_ID"]').attr('disabled', 'disabled');
    }
    if (exptypeval === 'Others [o]') {
      //Disable ContractID field for Others Spending Category
      $('input[name="contractno"]').attr('disabled', 'disabled');
      $('option[value="Contract ID"]').attr('disabled', 'disabled');
      $('option[value="contract_ID"]').attr('disabled', 'disabled');
    }
  };

  let spendingDatafeedsDis = function (dataSource) {
    if (1 === $('#edit-agency option').length) {
      $('#edit-agency').attr('disabled', 'disabled');
    } else {
      $('#edit-agency').removeAttr('disabled');
    }

    if (1 === $('#edit-dept option').length || 'Select Department' === $('#edit-dept').val()) {
      $('#edit-dept').attr('disabled', 'disabled');
    }

    if (1 === $('#edit-expense-category option').length || 'Select Expense Category' === $('#edit-dept').val()) {
      $('#edit-expense-category').attr('disabled', 'disabled');
    }
    if ('checkbook' === dataSource) {
      $('.data-feeds-wizard .datafield.agency').show();
    } else {
      $('.data-feeds-wizard .datafield.agency').hide();
    }
  };

  Drupal.behaviors.spendingDataFeeds = {
    attach: function (context) {
      let dataSource = $('input[name="datafeeds-spending-domain-filter"]:checked', context).val();

      $.fn.formatDatafeedsDatasourceRadio();

      $('#checkbook-datafeeds-data-feed-wizard', context).submit(function () {
        $('#edit-agency').removeAttr('disabled');
      });

      spendingDatafeedsDis(dataSource);

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

      //On Date Filter change
      $("#edit-date-filter", context).change(function () {
        if ($('input:radio[name=date_filter]:checked', context).val() === '1') {
          // issue date
          $('select[name="year"]', context).attr('disabled', 'disabled');
          $('input[name="issuedfrom"]', context).removeAttr("disabled");
          $('input[name="issuedto"]', context).removeAttr("disabled");
        } else{
          // year
          $('select[name="year"]', context).removeAttr("disabled");
          $('input[name="issuedfrom"]', context).val("");
          $('input[name="issuedfrom"]', context).attr('disabled', 'disabled');
          $('input[name="issuedto"]', context).val("");
          $('input[name="issuedto"]', context).attr('disabled', 'disabled');
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
      let expcategory = encodeURIComponent($('#edit-expense-category', context).val());
      let exptype = getSpendingExpenseType(data_source);
      let mwbecat = emptyToZero($('#edit-mwbe-category', context).val());
      let industry = emptyToZero($('#edit-industry', context).val());

      //Sets up jQuery UI autocompletes and autocomplete filtering functionality
      $('#edit-payee-name', context).autocomplete({source: '/autocomplete/spending/payee/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source});
      $('#edit-contractno', context).autocomplete({source: '/autocomplete/spending/contractno/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source});
      $('#edit-document-id', context).autocomplete({source: '/autocomplete/spending/documentid/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source});
      $('#edit-capital-project', context).autocomplete({source: '/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source});
      $('#edit-entity-contract-number', context).autocomplete({source: '/autocomplete/spending/entitycontractnum/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source});
      $('#edit-commodity-line', context).autocomplete({source: '/autocomplete/spending/commodityline/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + '/' + industry + data_source});
      $('#edit-budget-name', context).autocomplete({source: '/autocomplete/spending/budgetname/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + '/' + industry + data_source});
      $('.watch:input', context).each(function () {
        $(this).focusin(function () {
          //set variables for each field's value
          year = 0;
          if ($('input:radio[name=date_filter]:checked').val() === '0') {
            year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
          }
          dept = encodeURIComponent($('#edit-dept', context).val());
          agency = emptyToZero($('#edit-agency', context).val());

          expcategory = encodeURIComponent($('#edit-expense-category', context).val());
          mwbecat = emptyToZero($('#edit-mwbe-category', context).val());
          industry = emptyToZero($('#edit-industry', context).val());
          data_source = $('input[name="datafeeds-spending-domain-filter"]:checked', context).val();
          exptype = getSpendingExpenseType(data_source);

          $("#edit-payee-name", context).autocomplete("option", "source", '/autocomplete/spending/payee/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
          $('#edit-contractno', context).autocomplete("option", "source", '/autocomplete/spending/contractno/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
          $('#edit-document-id', context).autocomplete("option", "source", '/autocomplete/spending/documentid/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
          $('#edit-capital-project', context).autocomplete("option", "source", '/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
          $('#edit-entity-contract-number', context).autocomplete("option", "source", '/autocomplete/spending/entitycontractnum/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
          $('#edit-commodity-line', context).autocomplete("option", "source", '/autocomplete/spending/commodityline/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
          $('#edit-budget-name', context).autocomplete("option", "source", '/autocomplete/spending/budgetname/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
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

}(jQuery));
