(function ($) {

  // When Agency Filter is changed reload Department and Expense Category drop-downs
  let reloadSpendingDepartments = function () {
    let data_source = $('input[name="datafeeds-spending-domain-filter"]:checked').val();
    //Departments drop-down is not applicable for NYCHA
    if(data_source == 'checkbook_nycha'){
      return;
    }
    let agency = $('#edit-agency').val();
    let html = '<option value="0" selected="selected">Select Department</option>';
    let dept_hidden = $('input:hidden[name="dept_hidden"]').val();

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
          if(dept_hidden) {
            $('#edit-dept').val(dept_hidden);
          }
        }, complete: function () {
          enable_input($('#edit-dept'));
          $('#edit-dept').removeClass('loading');
        }
      });
    }
  };

  // When Department Filter is changed reload Expense category Drop-down
  let reloadSpendingExpenceCategories = function () {
    let data_source = $('input[name="datafeeds-spending-domain-filter"]:checked').val();
    let agency = $('#edit-agency').val();
    let html = '<option value="0" selected="selected">Select Expense Category</option>';
    let dept = 0;
    if(data_source != 'checkbook_nycha') {
      dept = emptyToZero($('input:hidden[name="dept_hidden"]').val());
    }
    let expense_category_hidden = $('input:hidden[name="expense_category_hidden"]').val();

    if(data_source == 'checkbook' && $.inArray(agency, ["", null, "0", 'Select One', 'Citywide (All Agencies)']) != -1){
      $('#edit-expense-category').html(html);
      disable_input($('#edit-expense-category'));
    }else{
      $('#edit-expense-category').addClass('loading');

      let year = 0;
      if ($('input:radio[name=date_filter]:checked').val() == 0) {
        if(data_source =='checkbook'){
        year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
        }
        else{
          year = ($('#edit-nycha-year').val()) ? $('#edit-nycha-year').val() : 0;
        }
      }
      //We need agency filter only for citywide
      if(data_source === 'checkbook'){agency = emptyToZero(agency);}else{agency = 0;}
      let spending_cat = getSpendingExpenseType(data_source);

      $.ajax({
        //url: '/solr_options/'+data_source+'/spending/expenditure_object_name_code/?'+filter
        url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept + '/' + spending_cat + '/' + data_source + '/feeds'
        , success: function (data) {
          let html = '<option select="selected" value="0" >Select Expense Category</option>';
          if (data[0]) {
            if (data[0] !== 'No Matches Found') {
              $.each(data, function (key, exp_cat) {
                // Remove null data from drop down
                if (exp_cat.name !== '\[\]') {
                  html = html + '<option value="' + exp_cat.code + '" title="' + exp_cat.title + '">' + exp_cat.name + '</option>';
                }
              });
            } else {
              html = html + '<option value="">' + data[0] + '</option>';
            }
          }
          $('#edit-expense-category').html(html);
          if (expense_category_hidden) {
            $('#edit-expense-category').val(expense_category_hidden);
          }
        }, complete: function () {
          enable_input($('#edit-expense-category'));
          $('#edit-expense-category').removeClass('loading');
        }
      });
    }
  };

  //On Catastrophic Event filter change
  function onCatastrophicEventChange(){
    //Selecting 'COVID-19' option causes the following changes:
    //Data within following fields update: Payee Name, Contract ID, Document ID, Capital Project
    //Limit year filter options to 'FY 2020', 'FY 2021' and 'All years'
    let fiscal_year = document.getElementById("edit-year");
    let catastrophic_event = document.getElementById("edit-catastrophic-event");

    if(catastrophic_event.value === "1"){
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

  //On Year filter dropdown change
  let onYearFilterChange = function(){
    let data_source = $('input[name="datafeeds-spending-domain-filter"]:checked').val();
    if(data_source != 'checkbook') return;
    //if fiscal year is other than all years, 2020, 2021, then disable COVID-19 dropdown field
    //If no dropdown available, then disable catastrophic events dropdown
    let fiscal_year = document.getElementById("edit-year").value.toLowerCase();
    let catastrophic_event = document.getElementById("edit-catastrophic-event");
    let enabled_count = catastrophic_event.length;
    let spending_cat = ($('select[name="expense_type"]').val()) ? $('select[name="expense_type"]').val() : 0;

    if (spending_cat === "Payroll [p]" || spending_cat === "Others [o]"){
      disable_input($('select[name="catastrophic_event"]'));
      return;
    }
    else if(!(fiscal_year === "0" || fiscal_year === "fy2021" || fiscal_year === "fy2020")){
      for (let i = 0; i < catastrophic_event.length; i++) {
        let event = catastrophic_event.options[i].text.toLowerCase();
        catastrophic_event.options[i].style.display = (event === 'covid-19')? "none":"";
        if(catastrophic_event.options[i].style.display === 'none') enabled_count--;
      }
      if(enabled_count <=1) {
        disable_input($('select[name="catastrophic_event"]'));
      }
    }
    else{
      for (let i = 0; i < catastrophic_event.length; i++) {
        let event = catastrophic_event.options[i].text.toLowerCase();
        if(event === 'covid-19'){
          catastrophic_event.options[i].style.display = "";
          break;
        }
      }
      enable_input($('select[name="catastrophic_event"]'));
    }
  }

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

    onCatastrophicEventChange();
    onYearFilterChange();
  };

  //ShowHide fields based on selected data source
  let showHideSpendingFields = function (data_source) {
    $('.datafield.citywide').add('.datafield.nycha').add('.datafield.nycedc').hide();
    $('#edit-columns .form-item').hide();

    // Department and Expense Category drop-downs are reset
    reloadSpendingDepartments();
    reloadSpendingExpenceCategories();

    switch (data_source) {
      case 'checkbook_oge':
        $('.datafield.nycedc').show();

        //Hide agency and enable and get department and expense category drop-down option
        $('.data-feeds-wizard .datafield.agency').hide();

        //Move Issue Date fields to left column for OGE
        $('#df-payeename').detach().appendTo('.spending.data-feeds-wizard .column.column-left');
        $('label[for=edit-payee-name]').text('Payee Name:');
        $('#df-check_amount').detach().appendTo('.spending.data-feeds-wizard .column.column-left');
        $('label[for=edit-agency]').text('Other Government Entity:');
        break;
      case 'checkbook_nycha':
        $('.datafield.nycha').show();

        //Hide agency and enable and get department and expense category drop-down option
        $('.data-feeds-wizard .datafield.agency').hide();

        //Move Issue Date fields to left column for NYCHA
        $('#df-check_amount').detach().prependTo('.spending.data-feeds-wizard .column.column-right');
       // $('#df-payeename').detach().prependTo('.spending.data-feeds-wizard .column.column-right');
        $('label[for=edit-payee-name]').text('Vendor:');
        $('label[for=edit-agency]').text('Other Government Entity:');
        break;
      default:
        $('.datafield.citywide').show();

        //Show agency drop-down and disable department and expense category drop-downs
        $('.data-feeds-wizard .datafield.agency').show();
        disable_input($('#edit-dept'));
        disable_input($('#edit-expense-category'));

        //Move Issue Date fields to left column for Citywide
        $('#df-check_amount').detach().prependTo('.spending.data-feeds-wizard .column.column-right');
        $('#df-payeename').detach().prependTo('.spending.data-feeds-wizard .column.column-right');
        $('label[for=edit-payee-name]').text('Payee Name:');
        $('label[for=edit-agency]').text('Agency:');
    }

    //Multi-select options
     resetMultiselect(data_source);

    //Reset enabling/disabling fields
     onSpendingCategoryChange();

    //Disable/Enable Date Filter fields
     onDateFilterChange();

  };

  // Reset Multi select option based on datasource
  let resetMultiselect = function (dataSource){
    switch (dataSource) {
      case 'checkbook_oge':
        $('#edit-column-select').multiSelect('deselect_all');
        $('#edit-nycha-column-select').multiSelect('deselect_all');

        $('.form-item-oge-column-select').show();
        $('#edit-oge-column-select').multiSelect('refresh');
        if (!$('#ms-edit-oge-column-select .ms-selection').next().is("a")) {
          $('#ms-edit-oge-column-select .ms-selection').after('<a class="deselect">Remove All</a>');
          $('#ms-edit-oge-column-select .ms-selection').after('<a class="select">Add All</a>');
        }
        $('#ms-edit-oge-column-select a.select').click(function () {
          $('#edit-oge-column-select').multiSelect('select_all');
        });
        $('#ms-edit-oge-column-select a.deselect').click(function () {
          $('#edit-oge-column-select').multiSelect('deselect_all');
        });
        break;
      case 'checkbook_nycha':
        $('#edit-column-select').multiSelect('deselect_all');
        $('#edit-oge-column-select').multiSelect('deselect_all');

        $('.form-item-nycha-column-select').show();
        $('#edit-nycha-column-select').multiSelect('refresh');
        if (!$('#ms-edit-nycha-column-select .ms-selection').next().is("a")) {
          $('#ms-edit-nycha-column-select .ms-selection').after('<a class="deselect">Remove All</a>');
          $('#ms-edit-nycha-column-select .ms-selection').after('<a class="select">Add All</a>');
        }
        $('#ms-edit-nycha-column-select a.select').click(function () {
          $('#edit-nycha-column-select').multiSelect('select_all');
        });
        $('#ms-edit-nycha-column-select a.deselect').click(function () {
          $('#edit-nycha-column-select').multiSelect('deselect_all');
        });
        break;
      default:
        $('#edit-nycha-column-select').multiSelect('deselect_all');
        $('#edit-oge-column-select').multiSelect('deselect_all');

        $('.form-item-column-select').show();
        $('#edit-column-select').multiSelect('refresh');
        if (!$('#ms-edit-column-select .ms-selection').next().is("a")) {
          $('#ms-edit-column-select .ms-selection').after('<a class="deselect">Remove All</a>');
          $('#ms-edit-column-select .ms-selection').after('<a class="select">Add All</a>');
        }
        $('#ms-edit-column-select a.select').click(function () {
          $('#edit-column-select').multiSelect('select_all');
        });
        $('#ms-edit-column-select a.deselect').click(function () {
          $('#edit-column-select').multiSelect('deselect_all');
        });
    }
  };

  //Get Spending Category based on Data Source
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

  //On Spending Category change, enable/disable fields applicable
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
      enable_input([$('input[name="contractno"]'), $('input[name="payee_name"]'), $('#edit-document-id')]);
      if (exptype === 'Payroll [p]') {
        //Disable Payee Name, ContractID, and Catastrophic event field for Payroll Spending Category
        disable_input([$('input[name="contractno"]'), $('input[name="payee_name"]'), $('select[name="catastrophic_event"]')]);
        onCatastrophicEventChange();
      }
      else if (exptype === 'Others [o]') {
        //Disable ContractID and Catastrophic event field for Others Spending Category
        disable_input([$('input[name="contractno"]'), $('select[name="catastrophic_event"]')]);
        onCatastrophicEventChange();
      }
      else{
        //For every other option, enable Payee Name, ContractID, and Catastrophic event field
        enable_input([$('input[name="contractno"]'), $('input[name="payee_name"]'), $('select[name="catastrophic_event"]')]);
        onYearFilterChange();
      }
    }
  };

  //On Date Filter change
  let onDateFilterChange = function(){
    let dateFilter = $('input:hidden[name="date_filter_hidden"]').val();
    let data_source = $('input[name="datafeeds-spending-domain-filter"]:checked').val();
    switch(data_source) {
      case 'checkbook_nycha':
        //enable Issue date
        enable_input($('input:radio[name=date_filter][value="1"]'));
        break;
      case 'checkbook_oge':
        //Disable Issue date
        disable_input($('input:radio[name=date_filter][value="1"]'));
        break;
      default:
        //enable Issue date
        enable_input($('input:radio[name=date_filter][value="1"]'));
    }

    if (dateFilter === '1') {
      disable_input($('select[name="year"]'));
      disable_input($('select[name="nycha_year"]'));
      enable_input($('input[name="issuedfrom"]'));
      enable_input($('input[name="issuedto"]'));
    } else{
      enable_input($('select[name="year"]'));
      enable_input($('select[name="nycha_year"]'));
      disable_input($('input[name="issuedfrom"]'));
      disable_input($('input[name="issuedto"]'));
    }

    //Set default value for Date-filter radios
    $('#edit-date-filter-'+dateFilter).attr('checked', true);
  };

  //Clear date filter input fields
  let clearDateFilterInputs = function(){
    $('input[name="issuedfrom"]').val("");
    $('input[name="issuedto"]').val("");
    $('select[name="year"]').val("0");
    $('select[name="nycha_year"]').val("0");
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
      $('input[type="radio"][name="datafeeds-spending-domain-filter"]', context).change(function () {
        $('input[type="hidden"][name="hidden_multiple_value"]', context).val("");
        $('input[type="hidden"][name="dept_hidden"]', context).val("");
        $('input[type="hidden"][name="expense_category_hidden"]', context).val("");
        $('input[type="hidden"][name="date_filter_hidden"]', context).val("0");
        clearDateFilterInputs();
        onSpendingDataSourceChange($(this, context).val());
      });

      //Agency drop-down change event
      $('select[name="agency"]', context).change(function () {
        $('input:hidden[name="dept_hidden"]', context).val("");
        $('input:hidden[name="expense_category_hidden"]', context).val("");
        reloadSpendingDepartments();
        reloadSpendingExpenceCategories();
      });

      //Year drop-down change event
      $('select[name="year"]', context).change(function () {
        $('input:hidden[name="dept_hidden"]', context).val("");
        $('input:hidden[name="expense_category_hidden"]', context).val("");
        reloadSpendingDepartments();
        reloadSpendingExpenceCategories();
        onYearFilterChange();
      });

      //Department drop-down change event
      $('select[name="dept"]', context).change(function () {
        $('input:hidden[name="dept_hidden"]', context).val($(this, context).val());
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

      //On change of "Catastrophic event"
      $('select[name="catastrophic_event"]', context).change(function(){
        onCatastrophicEventChange();
      });

      //On Date Filter change
      $("#edit-date-filter", context).change(function () {
        $('input:hidden[name="date_filter_hidden"]', context).val($('input:radio[name=date_filter]:checked', context).val());
        clearDateFilterInputs();
        onDateFilterChange();
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
      let data_source = $('input[name="datafeeds-spending-domain-filter"]:checked', context).val();
      let year = 0;
      if ($('input:radio[name=date_filter]:checked').val() === '0') {
        if (data_source === 'checkbook_nycha') {
          year = ($('#edit-nycha-year').val()) ? $('#edit-nycha-year').val() : 0;
        }
        else{
          year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
        }
      }
      let industry=0;
      if (data_source === 'checkbook_nycha'){
        industry = emptyToZero($('#edit-nycha-industry', context).val());}
      else{
        industry = emptyToZero($('#edit-industry', context).val());}
      let dept =  emptyToZero($('#edit-dept', context).val());
      let agency = emptyToZero($('#edit-agency', context).val());
      let exp_cat = emptyToZero($('#edit-expense-category', context).val());
      let mwbe_cat = $('#edit-mwbe-category', context).val()? encodeURIComponent($('#edit-mwbe-category', context).val()) : 0;
      let agg_type = $('#edit-purchase-order-type', context).val() ? emptyToZero($('#edit-purchase-order-type', context).val()) :0;
      let resp_center = $('#edit-resp-center', context).val() ? emptyToZero($('#edit-resp-center', context).val()) :0 ;
      let fund_src = $('#edit-funding-source', context).val() ? emptyToZero($('#edit-funding-source', context).val()) :0 ;
      let spend_cat = getSpendingExpenseType(data_source);
      let catastrophic_event_id = $('#edit-catastrophic-event', context).val() ? $('#edit-catastrophic-event', context).val() : 0;

      if(year.toLowerCase().indexOf("fy") >= 0){
        year = year.toLowerCase().split('fy')[1];
      }
      else if(year.toLowerCase().indexOf("cy") >= 0){
        year = year.toLowerCase().split('cy')[1];
      }

      //Sets up jQuery UI autocompletes and autocomplete filtering functionality
      // Refactoring autocomplete for citywide similar to Nycha to use common autocomplete function
      let filters = {
        "fiscal_year":year,
        "agency_code":agency,
        "expenditure_object_code":exp_cat,
        "department_code":dept,
        "spending_category_id":spend_cat,
        "minority_type_id":mwbe_cat,
        "industry_type_id":industry,
        "agreement_type_code":agg_type,
        "responsibility_center_code":resp_center,
        "funding_source_number":fund_src,
        "event_id":catastrophic_event_id
      };

      if (data_source === 'checkbook'){
        $('#edit-payee-name', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'vendor_name_code', filters)});
      }
      else {
        $('#edit-payee-name', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'vendor_name', filters)});
      }
      $('#edit-payee-name', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'vendor_name_code', filters)});
      $('#edit-contractno', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'contract_number', filters)});
      $('#edit-document-id', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'document_id', filters)});
      $('#edit-capital-project', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'reporting_code', filters)});
      $('#edit-entity-contract-number', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'spending_entity_contract_number', filters)});
      $('#edit-commodity-line', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'spending_commodity_line', filters)});
      $('#edit-budget-name', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'spending_budget_name', filters)});

      $('.watch:input', context).each(function () {
        $(this).focusin(function () {
          //set variables for each field's value
          data_source = $('input[name="datafeeds-spending-domain-filter"]:checked', context).val();
          year = 0;
          if ($('input:radio[name=date_filter]:checked').val() === '0') {
            if (data_source === 'checkbook_nycha') {
              year = ($('#edit-nycha-year').val()) ? $('#edit-nycha-year').val() : 0;
            }
            else{
              year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
            }
          }
          dept =  emptyToZero($('#edit-dept', context).val());
          agency = emptyToZero($('#edit-agency', context).val());
          exp_cat = emptyToZero($('#edit-expense-category', context).val());
          mwbe_cat = $('#edit-mwbe-category', context).val() ? encodeURIComponent($('#edit-mwbe-category', context).val()) : 0;
          agg_type = $('#edit-purchase-order-type', context).val() ? emptyToZero($('#edit-purchase-order-type', context).val()) : 0;
          resp_center = $('#edit-resp-center', context).val() ? emptyToZero($('#edit-resp-center', context).val()) : 0;
          fund_src = $('#edit-funding-source', context).val() ? emptyToZero($('#edit-funding-source', context).val()) : 0;

          if (data_source === 'checkbook_nycha'){
            industry = emptyToZero($('#edit-nycha-industry', context).val());}
          else{
            industry = emptyToZero($('#edit-industry', context).val());}
          spend_cat = getSpendingExpenseType(data_source);
          catastrophic_event_id = $('#edit-catastrophic-event', context).val() ? $('#edit-catastrophic-event', context).val() : 0;

          if(year.toLowerCase().indexOf("fy") >= 0){
            year = year.toLowerCase().split('fy')[1];
          }
          else if(year.toLowerCase().indexOf("cy") >= 0){
            year = year.toLowerCase().split('cy')[1];
          }

          let filters = {
            "fiscal_year":year,
            "agency_code":agency,
            "expenditure_object_code":exp_cat,
            "department_code":dept,
            "spending_category_id":spend_cat,
            "minority_type_id":mwbe_cat,
            "industry_type_id":industry,
            "agreement_type_code":agg_type,
            "responsibility_center_code":resp_center,
            "funding_source_number":fund_src,
            "event_id":catastrophic_event_id
          };

          // No code display for edc payee name in datafeed (refer to 10131 for document with information on code display for necessary fields)
          if (data_source === 'checkbook'){
            $('#edit-payee-name', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'vendor_name_code', filters)});
          }
          else {
            $('#edit-payee-name', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'vendor_name', filters)});
          }

          $('#edit-contractno', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'contract_number', filters)});
          $('#edit-document-id', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'document_id', filters)});
          $('#edit-capital-project', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'reporting_code', filters)});
          $('#edit-entity-contract-number', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'spending_entity_contract_number', filters)});
          $('#edit-commodity-line', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'spending_commodity_line', filters)});
          $('#edit-budget-name', context).autocomplete({source: $.fn.autoCompleteSourceUrl(data_source, 'spending_budget_name', filters)});
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
  };

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

  $(document).ready(function () {
    onSpendingCategoryChange();
    onCatastrophicEventChange();
    onYearFilterChange();
  });

}(jQuery));
