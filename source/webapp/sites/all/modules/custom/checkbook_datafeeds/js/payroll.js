(function ($) {
    Drupal.behaviors.payrollDataFeeds = {
        attach:function(context){
            //Citywide multi-select
            $('#edit-column-select',context).multiSelect();
            $('#ms-edit-column-select .ms-selectable',context).after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select .ms-selectable',context).after('<a class="select">Add All</a>');
            $('#ms-edit-column-select a.select',context).click(function(){
                $('#edit-column-select',context).multiSelect('select_all');
            });
            $('#ms-edit-column-select a.deselect',context).click(function(){
                $('#edit-column-select',context).multiSelect('deselect_all');
            });

            //OGE multi-select
            $('#edit-oge-column-select',context).multiSelect();
            $('#ms-edit-oge-column-select .ms-selectable',context).after('<a class="deselect">Remove All</a>');
            $('#ms-edit-oge-column-select .ms-selectable',context).after('<a class="select">Add All</a>');
            $('#ms-edit-oge-column-select a.select',context).click(function(){
                $('#edit-oge-column-select',context).multiSelect('select_all');
            });
            $('#ms-edit-oge-column-select a.deselect',context).click(function(){
                $('#edit-oge-column-select',context).multiSelect('deselect_all');
            });

            //Sets up jQuery UI datepickers
            var currentYear = new Date().getFullYear();
            $('.datepicker', context).datepicker({dateFormat:"yy-mm-dd",
                                                changeMonth:true,
                                                changeYear:true,
                                                yearRange:'-'+(currentYear-1900)+':+'+(2500-currentYear)});
            //Sets up autocompletes
            var year = $('#edit-year', context).val();
            var agency = ($('#edit-agency', context).val() === 'Citywide (All Agencies)') ? 0 : encodeURIComponent($('#edit-agency', context).val()) ;
            var payfrequency = ($('#edit-payfrequency',context).val() === 'All Pay Frequencies') ? 0 : $('#edit-payfrequency',context).val();
            $('#edit-title').autocomplete({
                source:'/autocomplete/payroll/title/'+ agency  + '/' + payfrequency + '/' + year,
                select: function( event, ui ) {
                    $(this).parent().next().val(ui.item.label) ;
                }
            });
            $('.watch:input').each(function () {
                $(this).focusin(function () {
                    year = $('#edit-year', context).val();
                    agency = ($('#edit-agency', context).val() === 'Citywide (All Agencies)') ? 0 : encodeURIComponent($('#edit-agency', context).val()) ;
                    payfrequency = ($('#edit-payfrequency',context).val() === 'All Pay Frequencies') ? 0 : $('#edit-payfrequency',context).val();
                    $("#edit-title").autocomplete("option", "source", '/autocomplete/payroll/title/'+ agency + '/' + payfrequency + '/' + year);
                });
            });

            var dataSource = $('input[name="datafeeds-payroll-domain-filter"]:checked', context).val();
            $.fn.showHideFields(dataSource);

            //Data Source change event
            $('input:radio[name=datafeeds-payroll-domain-filter]', context).change(function (){
                //Remove all the validation errors when data source is changed
                $('div.messages', context).remove();
                $('.error', context).removeClass('error');

                $('input:hidden[name="hidden_multiple_value"]', context).val("");
                $.fn.clearInputFields();
                $.fn.onDataSourceChange($(this, context).val());
            });
        }
    };

    //On Data Source Change
    $.fn.onDataSourceChange = function (dataSource) {
        //reset the selected columns
        $('#edit-column-select').multiSelect('deselect_all');
        $('#edit-oge-column-select').multiSelect('deselect_all');

        $.fn.showHideFields(dataSource);
    };

    $.fn.showHideFields = function (dataSource) {
        if(dataSource == 'checkbook_nycha'){
            $('.datafield.agency').hide();
            $('.datafield.other_government_entity').show();
            $('.form-item-oge-column-select').show();
            $('.form-item-column-select').hide();

            /** Hide Fiscal Year values for OGE **/
            $("#edit-year > option").each(function() {
              if($(this).val().toLowerCase().indexOf("fy") >= 0)
                $(this).hide();
            });
        }else{
            $('.datafield.agency').show();
            $('.datafield.other_government_entity').hide();
            $('.form-item-oge-column-select').hide();
            $('.form-item-column-select').show();

            $("#edit-year > option").each(function() {
              if($(this).val().toLowerCase().indexOf("fy") >= 0)
                $(this).show();
            });
        }
    }

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
        var p = /\[(.*?)\]$/;
        var inputval, output;
        inputval = p.exec(input);
        if (inputval) {
            output = inputval[1];
        } else {
            output = 0;
        }
        return output;
    }

}(jQuery));
