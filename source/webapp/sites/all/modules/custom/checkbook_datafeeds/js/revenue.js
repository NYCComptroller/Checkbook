(function ($) {
  //Show/hide fields based on data-source selected
  let showHideRevenueFields = function (dataSource){
    switch (dataSource) {
      case 'checkbook_nycha':
        //Fields
        $('.checkbook_fields').hide();
        $('.checkbook_nycha_fields').show();
        //Multi-select
        $('.form-item-nycha-column-select').show();
        $('.form-item-column-select').hide();
        break;
      default:
        //Fields
        $('.checkbook_fields').show();
        $('.checkbook_nycha_fields').hide();
        //Multi-select
        $('.form-item-nycha-column-select').hide();
        $('.form-item-column-select').show();
    }

    //Sets up jQuery UI autocompletes and autocomplete filtering functionality
    $.fn.initializeAutoComplete(dataSource);
    $('.watch:input').each(function () {
      $(this).focusin(function () {
        $.fn.initializeAutoComplete(dataSource);
      });
    });
  }

  //On Data Source Change
  let onDataSourceChange = function (dataSource) {
    //Remove all the validation errors when data source is changed
    $('div.messages').remove();
    $('.error').removeClass('error');

    //Clear Input Fields
    clearInputFields();

    //Reset the selected columns
    $('#edit-column-select').multiSelect('deselect_all');
    $('#edit-nycha-column-select').multiSelect('deselect_all');

    showHideRevenueFields(dataSource);
  }

  let reloadBudgetType = function(){
    let budget_name = encodeURIComponent($('#edit-nycha-budget-name').val());
    let budget_type_hidden = $('input:hidden[name="nycha_budget_type_hidden"]').val();
    let data_source = 'checkbook_nycha';

    $.ajax({
      url: 'data-feeds/revenue/budget_type/' + data_source + '/' + budget_name + '/'  + true,
      success: function(data) {
        let html = '<option value="" >Select Budget Type</option>';
        if(data[0]){
          for (i = 0; i < data.length; i++) {
            html = html + '<option value="' + data[i].value + '" title="' + data[i].value + '">' + data[i].text  + '</option>';
          }
        }
        $('select[name="nycha_budget_type"]').html(html);
        if(budget_type_hidden){
          $('select[name="nycha_budget_type"]').val(budget_type_hidden);
        }
      }
    });
  }

  let reloadBudgetName = function(){
    let budget_type = encodeURIComponent($('#edit-nycha-budget-type').val());
    let budget_name_hidden = $('input:hidden[name="nycha_budget_name_hidden"]').val();
    let data_source = 'checkbook_nycha';

    $.ajax({
      url: 'data-feeds/revenue/budget_name/' + data_source + '/' + budget_type + '/'  + true,
      success: function(data) {
        let html = '<option value="" >Select Budget Name</option>';
        if(data[0]){
          for (i = 0; i < data.length; i++) {
            html = html + '<option value="' + data[i].value + '" title="' + data[i].value + '">' + data[i].text  + '</option>';
          }
        }
        $('select[name="nycha_budget_name"]').html(html);
        if(budget_name_hidden){
          $('select[name="nycha_budget_name"]').val(budget_name_hidden);
        }
      }
    });
  }

  Drupal.behaviors.revenueDataFeeds = {
    attach: function (context, settings) {
      //DataSource Filter Formatter
      $.fn.formatDatafeedsDatasourceRadio();
      let dataSource = $('input[name="datafeeds-revenue-domain-filter"]:checked', context).val();

      reloadBudgetType();
      reloadBudgetName();

      //Display or hide fields based on data source selection
      showHideRevenueFields(dataSource);

      //Data Source change event
      $('input:radio[name=datafeeds-revenue-domain-filter]', context).change(function () {
        $('input:hidden[name="hidden_multiple_value"]', context).val("");
        onDataSourceChange($(this, context).val());
      });

      $('#edit-nycha-budget-name', context).change(function () {
        $('input:hidden[name="nycha_budget_type_hidden"]', context).val($('#edit-nycha-budget-type', context).val());
        $('input:hidden[name="nycha_budget_name_hidden"]', context).val($(this, context).val());
        reloadBudgetType();
        if($(this, context).val() == 'Select Budget Name' || $(this, context).val() == ''){
          reloadBudgetName();
        }
      });

      $('#edit-nycha-budget-type', context).change(function () {
        $('input:hidden[name="nycha_budget_type_hidden"]', context).val($(this, context).val());
        $('input:hidden[name="nycha_budget_name_hidden"]', context).val($('#edit-nycha-budget-name', context).val());
        reloadBudgetName();
        if($(this, context).val() == 'Select Budget Type' || $(this, context).val() == ''){
          reloadBudgetType();
        }
      });

      // Sets up multi-select/option transfer for CityWide
      $('#edit-column-select',context).multiSelect();
      $('#ms-edit-column-select .ms-selectable .ms-list',context).after('<a class="select">Add All</a>');
      $('#ms-edit-column-select .ms-selection .ms-list',context).after('<a class="deselect">Remove All</a>');
      $('#ms-edit-column-select a.select',context).click(function(){
        $('#edit-column-select',context).multiSelect('select_all');
      });
      $('#ms-edit-column-select a.deselect',context).click(function(){
        $('#edit-column-select',context).multiSelect('deselect_all');
      });

      // Sets up multi-select/option transfer for NYCHA
      $('#edit-nycha-column-select',context).multiSelect();
      $('#ms-edit-nycha-column-select .ms-selectable .ms-list',context).after('<a class="select">Add All</a>');
      $('#ms-edit-nycha-column-select .ms-selection .ms-list',context).after('<a class="deselect">Remove All</a>');
      $('#ms-edit-nycha-column-select a.select',context).click(function(){
        $('#edit-nycha-column-select',context).multiSelect('select_all');
      });
      $('#ms-edit-nycha-column-select a.deselect',context).click(function(){
        $('#edit-nycha-column-select',context).multiSelect('deselect_all');
      });
    }
  }

  //Initializes auto-completes
  $.fn.initializeAutoComplete = function (data_source = 'checkbook'){
    //Set Solr datasource for auto-complete
    let solr_datasource = data_source;
    let agency = 0;
    if(data_source === 'checkbook') {
      agency = emptyToZero($('#edit-agency').val());
      let fiscalYear = ($('#edit-fiscal-year').val() === 'All Years') ? 0 : $('#edit-fiscal-year').val();
      let fundClass = emptyToZero($('#edit-fund-class').val());
      let budgetYear = ($('#edit-budget-fiscal-year').val() === 'All Years') ? 0 : $('#edit-budget-fiscal-year').val();
      let revCat = emptyToZero($('#edit-revenue-category').val());
      let fundingSrc = emptyToZero($('#edit-funding-class').val());
      let filters = {
        fiscal_year: fiscalYear,
        fund_class_code: fundClass,
        agency_code: agency,
        revenue_budget_fiscal_year: budgetYear,
        revenue_category_code: revCat,
        funding_class_code: fundingSrc
      };
      $('#edit-revenue-class').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_class_name_code_autocomplete',filters)});
      $('#edit-revenue-source').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_source_name_code_autocomplete',filters)});
    }else if (data_source === 'checkbook_nycha') {
      solr_datasource = 'nycha';
      let budgetFY = ($('#edit-nycha-budget-year').val() === 'All Years') ? 0 : $('#edit-nycha-budget-year').val();
      let expCat = emptyToZero($('#edit-nycha-expense-category').val());
      let respCanter = emptyToZero($('#edit-nycha-resp-center').val());
      let fundingSrc = emptyToZero($('#edit-nycha-funding-source').val());
      let program = emptyToZero($('#edit-nycha-program').val());
      let project = emptyToZero($('#edit-nycha-project').val());
      let budgetType = emptyToZero($('#edit-nycha-budget-type').val());
      let budgetName = emptyToZero($('#edit-nycha-budget-name').val());
      let filters = {
        fiscal_year: budgetFY,
        expenditure_type_code: expCat,
        responsibility_center_code: respCanter,
        funding_source_number: fundingSrc,
        program_phase_code: program,
        gl_project_code: project,
        budget_type: budgetType,
        budget_name: budgetName,
      };
      $('#edit-nycha-rev-cat').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_category',filters)});
      $('#edit-nycha-rev-class').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_class',filters)});
    }
  }

  //Function to retrieve values enclosed in brackets or return zero if none
  function emptyToZero(input) {
    const p = /\[(.*?)]$/;
    const code = p.exec(input.trim());
    if (code) {
      return code[1];
    }
    return 0;
  }

  //Function to clear text fields and drop-downs
  let clearInputFields = function () {
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
