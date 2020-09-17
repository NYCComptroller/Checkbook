(function ($) {

  Drupal.behaviors.revenueDataFeeds = {
    attach: function (context, settings) {
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


      //Sets up jQuery UI autocompletes and autocomplete filtering functionality
      $.fn.initializeAutoComplete();
      $('.watch:input').each(function () {
        $(this).focusin(function () {
          $.fn.initializeAutoComplete();
        });
      });

      $('div.messages').remove();
      $('.error').removeClass('error');
    }
  }

  //Initializes auto-completes
  $.fn.initializeAutoComplete = function (data_source = 'checkbook'){
    //Set Solr datasource for auto-complete
    let solr_datasource = data_source;
    let agency = 0;
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
    $('#edit-revenue-class').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_class_name_code',filters)});
    $('#edit-revenue-source').autocomplete({source: $.fn.autoCompleteSourceUrl(solr_datasource,'revenue_source_name',filters)});
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
