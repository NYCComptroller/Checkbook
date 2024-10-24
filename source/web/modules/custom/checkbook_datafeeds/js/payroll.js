(function ($) {
  Drupal.behaviors.payrollDataFeeds = {
    attach: function (context) {

      $(once('payroll_window_load',document)).ready(function () {
        $.fn.formatDatafeedsDatasourceRadio('edit-datafeeds-payroll-domain-filter');
      });

      //Citywide multi-select
      $('#edit-column-select', context).multiSelect();
      $('#ms-edit-column-select .ms-selectable', context).after('<a class="deselect">Remove All</a>');
      $('#ms-edit-column-select .ms-selectable', context).after('<a class="select">Add All</a>');
      $('#ms-edit-column-select a.select', context).click(function ()
      {
        $('#edit-column-select', context).multiSelect('select_all');
      });
      $('#ms-edit-column-select a.deselect', context).click(function ()
      {
        $('#edit-column-select', context).multiSelect('deselect_all');
      });

      //OGE multi-select
      $('#edit-oge-column-select', context).multiSelect();
      $('#ms-edit-oge-column-select .ms-selectable', context).after('<a class="deselect">Remove All</a>');
      $('#ms-edit-oge-column-select .ms-selectable', context).after('<a class="select">Add All</a>');
      $('#ms-edit-oge-column-select a.select', context).click(function ()
      {
        $('#edit-oge-column-select', context).multiSelect('select_all');
      });
      $('#ms-edit-oge-column-select a.deselect', context).click(function ()
      {
        $('#edit-oge-column-select', context).multiSelect('deselect_all');
      });
      $('.datafield.other_government_entity').hide();
      let dataSource = $('input[name="datafeeds-payroll-domain-filter"]:checked', context).val();
      //Sets up jQuery UI datepickers
      let currentYear = new Date().getFullYear();

      //Sets up autocompletes
      let year = getYearValue($('#edit-year', context).val());
      let agency = ($('#edit-agency', context).val() === 'Citywide (All Agencies)') ? 0 : emptyToZero($('#edit-agency', context).val());
      let payfrequency = ($('#edit-payfrequency', context).val() === 'All Pay Frequencies') ? 0 : $('#edit-payfrequency', context).val();

      $('.watch:input').each(function ()
      {
        $(this).focusin(function () {
          year = getYearValue($('#edit-year', context).val());
          agency = ($('#edit-agency', context).val() === 'Citywide (All Agencies)') ? 0 : emptyToZero($('#edit-agency', context).val());
          payfrequency = ($('#edit-payfrequency', context).val() === 'All Pay Frequencies') ? 0 : $('#edit-payfrequency', context).val();
          dataSource = $('input[name="datafeeds-payroll-domain-filter"]:checked', context).val();

          let filter = new URLSearchParams();
          if(agency){filter.set('agency_code',agency)}
          if(payfrequency){filter.set('pay_frequency', payfrequency)}
          // Set the correct autocomplete year fields
          if(year[0] === 'CY') {
            filter.set('calendar_fiscal_year', year[2]);
          }else{
            filter.set('fiscal_year', year[2]);
          }

          $("#edit-title").autocomplete({
            source: '/solr_options/'+dataSource+'/payroll/civil_service_title?'+filter,
            select: function (event, ui) {
              ui.item.value = ui.item.label;
              $(this).parent().next().val(ui.item.label);
              $.fn.preventSelectionDefault(event, ui, "No Matches Found");
            }
          });

        });
      });

      //showHidePayroll(dataSource);
      datafeedsPayrollShowHideFields(dataSource);

      //Data Source change event
      $('input:radio[name=datafeeds-payroll-domain-filter]', context).change(function ()
      {
        //Remove all the validation errors when data source is changed
        $('div.messages', context).remove();
        $('.error', context).removeClass('error');

        $('input:hidden[name="hidden_multiple_value"]', context).val("");
        datafeedsPayrollOnDataSourceChange($(this, context).val());
        $.fn.clearInputFields();
      });
    }
  };

  //On Data Source Change
  let datafeedsPayrollOnDataSourceChange = function (dataSource)
  {
    //reset the selected columns
    $('#edit-column-select').multiSelect('deselect_all');
    $('#edit-oge-column-select').multiSelect('deselect_all');
    datafeedsPayrollShowHideFields(dataSource);
  };

  let datafeedsPayrollShowHideFields = function (dataSource) {
    //Add/Remove extra year value based on datasource
    resetYearvalue(dataSource);
    if (dataSource == 'checkbook_nycha') {
     $('.datafield.agency').hide();
      $('#ms-edit-column-select').hide();
      $('.form-item-oge-column-select').show();
      $('.form-item-column-select').hide();
    } else {
     $('.datafield.agency').show();
      $('#ms-edit-column-select').show();
      $('.form-item-oge-column-select').hide();
      $('.form-item-column-select').show();
    }
  };

  let resetYearvalue = function (dataSource) {
    //let yearValue = lastYear.split(/\s+/);
    $("#edit-year > option").each(function() {
      if (dataSource === 'checkbook_nycha') {
        if (/^CY/.test(this.value) || !$("#edit-year > option[value='" + this.value.replace('FY', 'CY') + "']").length) {
          // Hide CY for NYCHA Payroll
          $("#edit-year option[value='" + this.value + "']").attr('disabled','disabled').hide();
        }
      }
      else{
        // Show all
        $("#edit-year option[value='" + this.value + "']").removeAttr('disabled').show();
      }
    });
  };

  $.fn.clearInputFields = function ()
  {
    $('.fieldset-wrapper').find(':input').each(function () {
      switch (this.type) {
        case 'select-one':
          $(this).val( $(this).find('option:not([disabled]):first').val());
          break;
        case 'text':
          $(this).val('');
          break;
        case 'select-multiple':
        case 'password':
        case 'textarea':
          $(this).val('');
          break;
        case 'checkbox':
        case 'radio':
          $("input:radio[name='salary_type'][value='']").prop('checked', true);
          break;
        case 'date':
          $(this).val('');
          $('.date-item-label', $(this).parent()).html('');
          break;
      }
    });
  };

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

  //Function to retrieve year values ignoring FY and CY
  function getYearValue(input)
  {
    let yeardata = input.split(/(\s)/);
    return yeardata;
  }

  //Show/hide function from budget.js based on data-source selected
  let showHidePayroll = function (dataSource){
    switch (dataSource) {
      case 'checkbook':
        //Multi-select
        $('.form-item-column-select-expense').show();
        $('.form-item-nycha-column-select').hide();
        $('#ms-edit-column-select').show();
        //ms-edit-column-select
        break;

      case 'checkbook_nycha':
        //Multi-select
        $('.form-item-nycha-column-select').show();
        $('.form-item-column-select-expense').hide();
        $('#ms-edit-column-select').hide();
        break;
      default:
        //Fields
        $('.checkbook_fields').show();
        $('.checkbook_nycha_fields').hide();
        //Multi-select
        $('.form-item-nycha-column-select').hide();
        $('.form-item-column-select-expense').show();
    }
  }

}(jQuery));
