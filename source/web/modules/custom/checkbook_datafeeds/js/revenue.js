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
    //The exception for 'N/A' is handled in   public static function 'getBudgetName' function (\Drupal\checkbook_datafeeds\Budget\budgetType)
    let budget_name = $('#edit-nycha-budget-name').val().replace('/','__');
    budget_name = (budget_name.length > 0) ? encodeURIComponent(budget_name) : null;
    let budget_type_hidden = $('input:hidden[name="nycha_budget_type_hidden"]').val();
    let data_source = 'checkbook_nycha';

    $.ajax({
      url: 'data-feeds/budget_type/revenue/' + data_source + '/' + budget_name + '/'  + true,
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
    //The exception for 'N/A' is handled in   public static function 'getBudgetType' function (\Drupal\checkbook_datafeeds\Budget\budgetName)
    let budget_type = $('#edit-nycha-budget-type').val().replace('/','__');
    budget_type = (budget_type.length > 0) ? encodeURIComponent(budget_type) : null;
    let budget_name_hidden = $('input:hidden[name="nycha_budget_name_hidden"]').val();
    let data_source = 'checkbook_nycha';

    $.ajax({
      url: 'data-feeds/budget_name/revenue/' + data_source + '/' + budget_type + '/'  + true,
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

   // On Conditional Category filter change.
   function onCatastrophicEventChange(){
     let cevent = $('#edit-conditional-category').val();
     $("#edit-budget-fiscal-year option").each(function () {
       let year = this.text;
       let include = true;
       if(cevent === "1") {
         include = (year >= 2020);
       }
       this.style.display = include ? '':'none';
     });
  }

function onBudgetFiscalYearChange() {
  //Setting data source value
  let data_source = $('input[name="datafeeds-revenue-domain-filter"]:checked').val();
  if (data_source === 'checkbook') {
    let year_value = $('#edit-budget-fiscal-year').val();
    // disable COVID when category is all and year value is less than 2020
    if (year_value < 2020) {
      $('select[name="conditional_category"] option[value="1"]').attr('disabled', 'disabled');
    } else {
      $('select[name="conditional_category"] option[value="1"]').removeAttr('disabled', 'disabled');
    }
  }
}
  Drupal.behaviors.revenueDataFeeds = {
    attach: function (context, settings) {
      //DataSource Filter Formatter
      $(once('revenue_window_load',document)).ready(function () {
        $.fn.formatDatafeedsDatasourceRadio('edit-datafeeds-revenue-domain-filter');
      });
      let dataSource = $('input[name="datafeeds-revenue-domain-filter"]:checked', context).val();
      if(dataSource === 'checkbook_nycha') {
        reloadBudgetType();
        reloadBudgetName();
      }

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
        if($(this, context).val() === 'Select Budget Name' || $(this, context).val() === ''){
          reloadBudgetName();
        }
      });

      $('#edit-nycha-budget-type', context).change(function () {
        $('input:hidden[name="nycha_budget_type_hidden"]', context).val($(this, context).val());
        $('input:hidden[name="nycha_budget_name_hidden"]', context).val($('#edit-nycha-budget-name', context).val());
        reloadBudgetName();
        if ($(this, context).val() === 'Select Budget Type' || $(this, context).val() === ''){
          reloadBudgetType();
        }
      });

      // On change of "Conditional Category".
      $('select[name="conditional_category"]', context).change(function(){
        onCatastrophicEventChange();
      });

      //On change of "Budget Fiscal Year"
      $('select[name="budget_fiscal_year"]', context).change(function(){
        onBudgetFiscalYearChange();
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
      let conditional_category_id = $('#edit-conditional-category').val() ? $('#edit-conditional-category').val() : 0;
      let filters = {
        fiscal_year: fiscalYear,
        fund_class_code: fundClass,
        agency_code: agency,
        revenue_budget_fiscal_year: budgetYear,
        revenue_category_code: revCat,
        funding_class_code: fundingSrc,
        event_id: conditional_category_id
      };
      $('#edit-revenue-class').autocomplete({
        source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_class_name_code',filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });
      $('#edit-revenue-source').autocomplete({
        source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_source_name_code',filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });
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
      $('#edit-nycha-rev-cat').autocomplete({
        source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_category',filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });
      $('#edit-nycha-rev-class').autocomplete({
        source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_class',filters),
        select: function (event, ui) {
          $.fn.preventSelectionDefault(event, ui, "No Matches Found");
        }
      });
    }
  }

  //Function to retrieve values enclosed in brackets or return zero if none
  function emptyToZero(input) {
    let p = null;
    let code = null;
    if(input) {
      p = /\[(.*?)]$/;
      code = p.exec(input.trim());
    }
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
          $(this).val( $(this).find('option:first').val());
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
      if ('text' === $(this).attr('type')) {
        if ($(this).val()){
          $(this).attr('storedvalue', $(this).val());
        }
        $(this).val('');
      }
      if (this.type === 'select-one') {
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
      if ('text' === $(this).attr('type')) {
        if ($(this).attr('storedvalue')){
          $(this).val($(this).attr('storedvalue'));
        }
        $(this).removeAttr('storedvalue');
      }
    })
  }

  $(document).ready(function () {
    onCatastrophicEventChange();
    onBudgetFiscalYearChange();
  });
}(jQuery));
