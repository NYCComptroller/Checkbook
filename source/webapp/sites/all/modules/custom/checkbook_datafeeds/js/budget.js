(function ($) {
    $.fn.reloadDepartment = function(){
       let agency = encodeURIComponent($('#edit-agency').val());
       let dept_hidden = $('input:hidden[name="dept_hidden"]').val();
       let year = ($('#edit-fiscal-year').val() === 'All Years') ? 0 : $('#edit-fiscal-year').val();
       if($('#edit-agency').val() !== 'Citywide (All Agencies)'){
            $.ajax({
                url: '/datafeeds/budget/department/' + year + '/' + agency,
                success: function(data) {
                    let html = '<option select="selected" value="" >Select Department</option>';
                    if(data[0]){
                        for (i = 0; i < data.length; i++) {
                            html = html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                        }
                    }
                    $('select[name="dept"]').removeAttr('disabled');
                    $('select[name="dept"]').html(html);
                    if(dept_hidden){
                        $('select[name="dept"]').val(dept_hidden);
                    }
                }
            });
        }else{
            $('select[name="dept"]').append('<option value="" selected="selected">Select Department</option>');
            $('select[name="dept"]').attr('disabled','disabled');
        }
    }

    $.fn.reloadExpenseCategory = function(){
       let agency = encodeURIComponent($('#edit-agency').val());
       let dept = ($('input:hidden[name="dept_hidden"]').val()) ? encodeURIComponent( $('input:hidden[name="dept_hidden"]').val()) : 0;
       let year = ($('#edit-fiscal-year').val() === 'All Years') ? 0 : $('#edit-fiscal-year').val();
       let expense_category_hidden = $('input:hidden[name="expense_category_hidden"]').val();

       if($('#edit-agency').val() !== 'Citywide (All Agencies)'){
            $.ajax({
                url: '/datafeeds/budget/expcat/' + year + '/' + agency + '/' + dept,
                success: function(data) {
                    let html = '<option select="selected" value="" >Select Expense Category</option>';
                    if(data[0]){
                        for (i = 0; i < data.length; i++) {
                            html = html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                        }
                    }
                    $('select[name="expense_category"]').removeAttr('disabled');
                    $('select[name="expense_category"]').html(html);
                    if(expense_category_hidden){
                        $('select[name="expense_category"]').val(expense_category_hidden);
                    }
                }
            });
        }else{
            $('select[name="expense_category"]').append('<option value="" selected="selected">Select Expense Category</option>');
            $('select[name="expense_category"]').attr('disabled','disabled');
        }
    }

    //Show/hide fields based on data-source selected
    let showHideBudgetFields = function (dataSource){
      switch (dataSource) {
        case 'checkbook_nycha':
          //Fields
          $('.checkbook_fields').hide();
          $('.checkbook_nycha_fields').show();
          //Multi-select
          $('.form-item-nycha-column-select').show();
          $('.form-item-column-select-expense').hide();
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

    //On Data Source Change
    let onDataSourceChange = function (dataSource) {
      //Remove all the validation errors when data source is changed
      $('div.messages').remove();
      $('.error').removeClass('error');

      //Clear Input Fields
      clearInputFields();

      //Disable Expense Category and Department drop-downs
      $('select[name="expense_category"]').attr('disabled','disabled');
      $('select[name="dept"]').attr('disabled','disabled');


      //Reset the selected columns
      $('#edit-column-select-expense').multiSelect('deselect_all');
      $('#edit-nycha-column-select').multiSelect('deselect_all');

      showHideBudgetFields(dataSource);
    }

    let reloadBudgetType = function(){
      let budget_name = encodeURIComponent($('#edit-nycha-budget-name').val());
      let budget_type_hidden = $('input:hidden[name="nycha_budget_type_hidden"]').val();
      let data_source = 'checkbook_nycha';

      $.ajax({
        url: 'data-feeds/budget/budget_type/' + data_source + '/' + budget_name + '/'  + true,
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
        url: 'data-feeds/budget/budget_name/' + data_source + '/' + budget_type + '/'  + true,
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

    Drupal.behaviors.budgetDataFeeds = {
        attach:function(context,settings){
            $.fn.formatDatafeedsDatasourceRadio();
            $.fn.reloadDepartment();
            $.fn.reloadExpenseCategory();
            reloadBudgetType();
            reloadBudgetName();

          let dataSource;
          dataSource = $('input[name="datafeeds-budget-domain-filter"]:checked', context).val() ? $('input[name="datafeeds-budget-domain-filter"]:checked', context).val() : 'checkbook';
            //Display or hide fields based on data source selection
            showHideBudgetFields(dataSource);

            // Reset covid field based on the form input year value
            let yearval = $('select[name="fiscal_year"]', context).val()
            if ( yearval < 2020){
              $("#edit-catastrophic-event").attr('disabled', 'disabled');
              $('#edit-catastrophic-event', context).val('0');
            }
            let cevent =$('#edit-catastrophic-event', context).val();
            updateYearValue(cevent);

            //Data Source change event
            $('input:radio[name=datafeeds-budget-domain-filter]', context).change(function () {
              $('input:hidden[name="hidden_multiple_value"]', context).val("");
              onDataSourceChange($(this, context).val());
            });

            $('#edit-agency', context).change(function () {
                $('input:hidden[name="dept_hidden"]', context).val("");
                $('input:hidden[name="expense_category_hidden"]', context).val("");
                $.fn.reloadDepartment();
                $.fn.reloadExpenseCategory();
            });

            $('#edit-dept', context).change(function () {
                $('input:hidden[name="dept_hidden"]', context).val($('#edit-dept', context).val());
                $('input:hidden[name="expense_category_hidden"]', context).val("");
                $.fn.reloadExpenseCategory();
            });

            $('#edit-catastrophic-event', context).change(function () {
              let cevent = $('#edit-catastrophic-event', context).val();
              updateYearValue(cevent);
            });

            $('#edit-fiscal-year', context).change(function () {
              let yearval = $('select[name="fiscal_year"]', context).val();
              if(yearval < 2020){
                $("#edit-catastrophic-event").attr('disabled', 'disabled');
                $('select[name="catastrophic_event"]', context).val('0');
              }
              else{
                $("#edit-catastrophic-event").removeAttr('disabled');
              }

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

            //Sets up jQuery UI autocompletes and autocomplete filtering functionality
            let year = $('#edit-fiscal-year',context).val() ;
            let agency = emptyToZero($('#edit-agency',context).val());
            let dept = ($('#edit-dept',context).val()) ? $('#edit-dept',context).val() : 0;
            let expcategory = ($('#edit-expense-category',context).val()) ? $('#edit-expense-category',context).val() : 0;
            let budgetcode = ($('#edit-budget-code',context).attr('disabled')) ? 0 : emptyToZero($('#edit-budget-code',context).val());
            let event = emptyToZero($('#edit-catastrophic-event',context).val());

            let filters = {
                object_class_code: expcategory,
                department_code:dept,
                agency_code: agency,
                fiscal_year: year,
                event_id:event
            };
            $('#edit-budget-code').autocomplete({source: $.fn.autoCompleteSourceUrl('citywide','budget_code_name_code',filters)});
            $('.watch:input',context).each(function () {
                $(this,context).focus(function () {
                    //set letiables for each field's value
                  let year = $('#edit-fiscal-year',context).val() ;
                  let agency = emptyToZero($('#edit-agency',context).val());
                  let dept = emptyToZero($('#edit-dept',context).val()) ;
                  let expcategory =  emptyToZero($('#edit-expense-category',context).val());
                  let event = emptyToZero($('#edit-catastrophic-event',context).val());

                    let filters = {
                      object_class_code: expcategory,
                      department_code:dept,
                      agency_code: agency,
                      fiscal_year: year,
                      event_id:event
                    };
                    $('#edit-budget-code').autocomplete({source: $.fn.autoCompleteSourceUrl('citywide','budget_code_name_code',filters)});
                });
            });

              // Sets up multi-select/option transfer for CityWide
            $('#edit-column-select-expense',context).multiSelect();
            $('#ms-edit-column-select-expense .ms-selectable .ms-list',context).after('<a class="select">Add All</a>');
            $('#ms-edit-column-select-expense .ms-selection .ms-list',context).after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select-expense a.select',context).click(function(){
                $('#edit-column-select-expense',context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-expense a.deselect',context).click(function(){
                $('#edit-column-select-expense',context).multiSelect('deselect_all');
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

    //Function to retrieve values enclosed in brackets or return zero if none
    function emptyToZero(input) {
      if(input) {
          var p = /\[(.*?)]$/;
          var code = p.exec(input.trim());
      }
      if (code) {
        return code[1];
      }
      return 0;
    }

    // update year drop down when event is chosen
    function updateYearValue(cevent) {
      $("#edit-fiscal-year option").each(function() {
        var yval =  $(this).val();
        if ( yval < 2020 && cevent != 0){
        $(" option[value='" + $(this).val() + "']").hide();
        }
        else{
        $(" option[value='" + $(this).val() + "']").show();
        }
      });
  }

    //Function to clear text fields and drop-downs
     let clearInputFields = function (dataSource) {
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
