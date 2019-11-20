(function ($) {
  Drupal.behaviors.payrollDataFeeds = {
    attach: function (context) {

      $.fn.formatDatafeedsDatasourceRadio();

      //Citywide multi-select
      $('#edit-column-select', context).multiSelect();
      $('#ms-edit-column-select .ms-selectable', context).after('<a class="deselect">Remove All</a>');
      $('#ms-edit-column-select .ms-selectable', context).after('<a class="select">Add All</a>');
      $('#ms-edit-column-select a.select', context).click(function () {
        $('#edit-column-select', context).multiSelect('select_all');
      });
      $('#ms-edit-column-select a.deselect', context).click(function () {
        $('#edit-column-select', context).multiSelect('deselect_all');
      });

      //OGE multi-select
      $('#edit-oge-column-select', context).multiSelect();
      $('#ms-edit-oge-column-select .ms-selectable', context).after('<a class="deselect">Remove All</a>');
      $('#ms-edit-oge-column-select .ms-selectable', context).after('<a class="select">Add All</a>');
      $('#ms-edit-oge-column-select a.select', context).click(function () {
        $('#edit-oge-column-select', context).multiSelect('select_all');
      });
      $('#ms-edit-oge-column-select a.deselect', context).click(function () {
        $('#edit-oge-column-select', context).multiSelect('deselect_all');
      });

      $('.datafield.other_government_entity').hide();

      var dataSource = $('input[name="datafeeds-payroll-domain-filter"]:checked', context).val();

      //Sets up jQuery UI datepickers
      var currentYear = new Date().getFullYear();
      $('.datepicker', context).datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        yearRange: '-' + (currentYear - 1900) + ':+' + (2500 - currentYear)
      });
      //Sets up autocompletes
      var year = $('#edit-year', context).val();
      var agency = ($('#edit-agency', context).val() === 'Citywide (All Agencies)') ? 0 : encodeURIComponent($('#edit-agency', context).val());
      var payfrequency = ($('#edit-payfrequency', context).val() === 'All Pay Frequencies') ? 0 : $('#edit-payfrequency', context).val();

      $('.watch:input').each(function () {
        $(this).focusin(function () {
          year = $('#edit-year', context).val();
          agency = ($('#edit-agency', context).val() === 'Citywide (All Agencies)') ? 0 : encodeURIComponent($('#edit-agency', context).val());
          payfrequency = ($('#edit-payfrequency', context).val() === 'All Pay Frequencies') ? 0 : $('#edit-payfrequency', context).val();
          dataSource = $('input[name="datafeeds-payroll-domain-filter"]:checked', context).val();

          $("#edit-title").autocomplete({
            source: '/solr_autocomplete/'+dataSource+'/civil_service_title',
            select: function (event, ui) {
              ui.item.value = ui.item.label;
              $(this).parent().next().val(ui.item.label);
            }
          });
        });
      });


      datafeedsPayrollShowHideFields(dataSource);

      //Data Source change event
      $('input:radio[name=datafeeds-payroll-domain-filter]', context).change(function () {
        //Remove all the validation errors when data source is changed
        $('div.messages', context).remove();
        $('.error', context).removeClass('error');

        $('input:hidden[name="hidden_multiple_value"]', context).val("");
        $.fn.clearInputFields();
        datafeedsParyllOnDataSourceChange($(this, context).val());
      });
    }
  };

  //On Data Source Change
  let datafeedsParyllOnDataSourceChange = function (dataSource) {
    //reset the selected columns
    $('#edit-column-select').multiSelect('deselect_all');
    $('#edit-oge-column-select').multiSelect('deselect_all');

    datafeedsPayrollShowHideFields(dataSource);
  };

  let datafeedsPayrollShowHideFields = function (dataSource) {
    if (dataSource == 'checkbook_nycha') {
      $('.datafield.agency').hide();
      $('.form-item-oge-column-select').show();
      $('.form-item-column-select').hide();

      /** Hide Fiscal Year values for OGE **/
      $("#edit-year > option").each(function () {
        if ($(this).val().toLowerCase().indexOf("fy") >= 0)
          $(this).hide();
      });
    } else {
      $('.datafield.agency').show();
      $('.form-item-oge-column-select').hide();
      $('.form-item-column-select').show();

      $("#edit-year > option").each(function () {
        if ($(this).val().toLowerCase().indexOf("fy") >= 0)
          $(this).show();
      });
    }
  };

  $.fn.clearInputFields = function () {
    $('.fieldset-wrapper').find(':input').each(function () {
      switch (this.type) {
        case 'select-one':
          var default_option = $(this).attr('default_selected_value');
          if (default_option) {
            $(this).find('option[value=' + default_option + ']').attr("selected", "selected");
          } else {
            $(this).find('option:first').attr("selected", "selected");
          }
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
          $('#edit-salary-type-all').attr('checked', 'checked');
          break;
      }
    });
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

}(jQuery));
