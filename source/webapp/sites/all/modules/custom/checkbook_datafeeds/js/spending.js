(function ($) {

    $.fn.disableStuff = function(){
        if ($('#edit-agency').val() == 'Citywide (All Agencies)'){
            $('select[name="dept"]').val('');
            $('select[name="dept"]').attr('disabled','disabled');
            $('select[name="expense_category"]').val('');
            $('select[name="expense_category"]').attr('disabled','disabled');
        }
    }

    $.fn.refreshMultiSelect = function(){
        $('#edit-column-select').multiSelect('refresh');
        $('.ms-selection').after('<a class="deselect">Remove All</a>');
        $('.ms-selection').after('<a class="select">Add All</a>');
        $('a.select').click(function () {
            $('#edit-column-select').multiSelect('select_all');
        });
        $('a.deselect').click(function () {
            $('#edit-column-select').multiSelect('deselect_all');
        });
    }

    Drupal.behaviors.spendingDataFeeds = {
        attach:function (context, settings) {
            var p = /\[(.*?)\]$/;
            var datefilter = $('input[name="date_filter"]:checked').val();
            var exptypeval = $('select[name="expense_type"]').val();
            $.fn.disableStuff();
            if (datefilter == 0){
                $('input[name="issuedfrom"]').attr('disabled','disabled');
                $('input[name="issuedto"]').attr('disabled','disabled');
            } else if (datefilter == 1){
                $('select[name="year"]').attr('disabled','disabled');
            }
            if (exptypeval == 'Payroll Spending [p]'){
                $('input[name="contractno"]').attr('disabled','disabled');
                $('input[name="payee_name"]').attr('disabled','disabled');
                $('option[value="Payee Name"]').attr('disabled','disabled');
                $('option[value="payee_name"]').attr('disabled','disabled');
                $('option[value="Contract ID"]').attr('disabled','disabled');
                $('option[value="contract_ID"]').attr('disabled','disabled');
            } else if (exptypeval == 'Other Spending [o]'){
                $('input[name="contractno"]').attr('disabled','disabled');
                $('input[name="payee_name"]').removeAttr('disabled');
                $('option[value="Contract ID"]').attr('disabled','disabled');
                $('option[value="contract_ID"]').attr('disabled','disabled');
                $('option[value="Payee Name"]').removeAttr('disabled');
                $('option[value="payee_name"]').removeAttr('disabled');
            }
            // Sets up multi-select/option transfer
            $('#edit-column-select',context).multiSelect();
            $('.ms-selection',context).after('<a class="deselect">Remove All</a>');
            $('.ms-selection',context).after('<a class="select">Add All</a>');
            $('a.select',context).click(function () {
                $('#edit-column-select',context).multiSelect('select_all');
            });
            $('a.deselect',context).click(function () {
                $('#edit-column-select',context).multiSelect('deselect_all');
            });
            //Sets up jQuery UI datepickers
            $('.datepicker',context).datepicker({dateFormat:"yy-mm-dd"});
            //Sets up jQuery UI autocompletes and autocomplete filtering functionality

            var year;
            if($('input:radio[name=date_filter]:checked').val() == 0){
                year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
            }
            var dept = emptyToZero($('#edit-dept--2',context).val());
            var agency = emptyToZero($('#edit-agency',context).val());
            var expcategory = emptyToZero($('#edit-expense-category--2',context).val());
            var exptype = emptyToZero($('#edit-expense-type',context).val());
            //Sets up jQuery UI autocompletes and autocomplete filtering functionality
            $('#edit-payee-name',context).autocomplete({source:'/autocomplete/spending/payee/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype});
            $('#edit-contractno',context).autocomplete({source:'/autocomplete/spending/contractno/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype});
            $('#edit-document-id',context).autocomplete({source:'/autocomplete/spending/documentid/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype});
            $('#edit-capital-project',context).autocomplete({source:'/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype});
            $('.watch:input',context).each(function () {
                $(this).focusin(function () {
                    //set variables for each field's value
                    if($('input:radio[name=date_filter]:checked').val() == 0){
                        year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
                    }
                    dept = emptyToZero($('#edit-dept--2',context).val());
                    agency = emptyToZero($('#edit-agency',context).val());
                    expcategory = emptyToZero($('#edit-expense-category--2',context).val());
                    exptype = emptyToZero($('#edit-expense-type',context).val());
                    $("#edit-payee-name",context).autocomplete("option", "source", '/autocomplete/spending/payee/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype);
                    $('#edit-contractno',context).autocomplete("option", "source", '/autocomplete/spending/contractno/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype);
                    $('#edit-document-id',context).autocomplete("option", "source", '/autocomplete/spending/documentid/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype);
                    $('#edit-capital-project',context).autocomplete("option", "source", '/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype);
                });
            });
        }
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