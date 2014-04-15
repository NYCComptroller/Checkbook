(function ($) {

    $.fn.disableStuff = function () {
        if ($('#edit-agency').val() == 'Citywide (All Agencies)') {
            $('select[name="dept"]').val('');
            $('select[name="dept"]').attr('disabled', 'disabled');
            $('select[name="expense_category"]').val('');
            $('select[name="expense_category"]').attr('disabled', 'disabled');
        }
        else if ($('#edit-agency').val() == 'Other Government Entities') {
            $('select[name="dept"]').val('');
            $('select[name="dept"]').attr('disabled', 'disabled');
            $('select[name="expense_category"]').val('');
            $('select[name="expense_category"]').attr('disabled', 'disabled');
        }

    };

    $.fn.onDataSourceChange = function () {

        //clear all text fields
        var enclosingDiv = $("#dynamic-filter-data-wrapper").children('#edit-filter').children('div.fieldset-wrapper').children();
        jQuery(enclosingDiv).find(':input').each(function () {
            if (this.type == 'text') {
                jQuery(this).val('');
            }
        });

        //reset the date filter
        $('input:radio[name=date_filter][value=0]').attr('checked', 'checked').button("refresh");
        $('select[name="year"]').removeAttr('disabled');
        $('input[name="issuedfrom"]').attr('disabled', 'disabled');
        $('input[name="issuedto"]').attr('disabled', 'disabled');

        //reset the Spending Category
        $('select[name="expense_type"]').val('Total Spending [ts]');
        $('input[name="payee_name"]').removeAttr('disabled');
        $('option[value="Payee Name"]').removeAttr('disabled');
        $('option[value="payee_name"]').removeAttr('disabled');
        $('input[name="contractno"]').removeAttr('disabled');
        $('option[value="Contract ID"]').removeAttr('disabled');
        $('option[value="contract_ID"]').removeAttr('disabled');

        //reset the selected columns
        $('#edit-column-select').multiSelect('deselect_all');

        //disable fields
        $.fn.disableStuff();
    };

    $.fn.refreshMultiSelect = function () {
        $('#edit-column-select').multiSelect('refresh');
        $('.ms-selection').after('<a class="deselect">Remove All</a>');
        $('.ms-selection').after('<a class="select">Add All</a>');
        $('a.select').click(function () {
            $('#edit-column-select').multiSelect('select_all');
        });
        $('a.deselect').click(function () {
            $('#edit-column-select').multiSelect('deselect_all');
        });
    };

    Drupal.behaviors.spendingDataFeeds = {
        attach:function (context, settings) {
            var p = /\[(.*?)\]$/;

            var dataSource = $('input:radio[name=datafeeds-spending-domain-filter]:checked').val();
            var datefilter = $('input[name="date_filter"]:checked').val();
            var exptypeval = $('select[name="expense_type"]').val();
            $.fn.disableStuff();
            if (datefilter == 0) {
                $('input[name="issuedfrom"]').attr('disabled', 'disabled');
                $('input[name="issuedto"]').attr('disabled', 'disabled');
            } else if (datefilter == 1) {
                $('select[name="year"]').attr('disabled', 'disabled');
            }
            if (exptypeval == 'Payroll Spending [p]') {
                $('input[name="contractno"]').attr('disabled', 'disabled');
                $('input[name="payee_name"]').attr('disabled', 'disabled');
                $('option[value="Payee Name"]').attr('disabled', 'disabled');
                $('option[value="payee_name"]').attr('disabled', 'disabled');
                $('option[value="Contract ID"]').attr('disabled', 'disabled');
                $('option[value="contract_ID"]').attr('disabled', 'disabled');
            } else if (exptypeval == 'Other Spending [o]') {
                $('input[name="contractno"]').attr('disabled', 'disabled');
                $('input[name="payee_name"]').removeAttr('disabled');
                $('option[value="Contract ID"]').attr('disabled', 'disabled');
                $('option[value="contract_ID"]').attr('disabled', 'disabled');
                $('option[value="Payee Name"]').removeAttr('disabled');
                $('option[value="payee_name"]').removeAttr('disabled');
            }
            // Sets up multi-select/option transfer
            $('#edit-column-select', context).multiSelect();
            $('.ms-selection', context).after('<a class="deselect">Remove All</a>');
            $('.ms-selection', context).after('<a class="select">Add All</a>');
            $('a.select', context).click(function () {
                $('#edit-column-select', context).multiSelect('select_all');
            });
            $('a.deselect', context).click(function () {
                $('#edit-column-select', context).multiSelect('deselect_all');
            });
            //Sets up jQuery UI datepickers
            $('.datepicker', context).datepicker({dateFormat:"yy-mm-dd"});

            //Sets up jQuery UI autocompletes and autocomplete filtering functionality
            var year;
            if ($('input:radio[name=date_filter]:checked').val() == 0) {
                year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
            }
            var dept = emptyToZero($('#edit-dept',context).val());
            var agency = emptyToZero($('#edit-agency',context).val());
            var expcategory = emptyToZero($('#edit-expense-category',context).val());
            var exptype = emptyToZero($('#edit-expense-type',context).val());
            var data_source = $('input:radio[name=datafeeds-spending-domain-filter]:checked').val();

            //Sets up jQuery UI autocompletes and autocomplete filtering functionality
            $('#edit-payee-name',context).autocomplete({source:'/autocomplete/spending/payee/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source});
            $('#edit-contractno',context).autocomplete({source:'/autocomplete/spending/contractno/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source});
            $('#edit-document-id',context).autocomplete({source:'/autocomplete/spending/documentid/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source});
            $('#edit-capital-project',context).autocomplete({source:'/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source});
            $('#edit-entity-contract-number',context).autocomplete({source:'/autocomplete/spending/entitycontractnum/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source});
            $('#edit-commodity-line',context).autocomplete({source:'/autocomplete/spending/commodityline/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source});
            $('#edit-budget-name',context).autocomplete({source:'/autocomplete/spending/budgetname/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source});
            $('.watch:input',context).each(function () {
                $(this).focusin(function () {
                    //set variables for each field's value
                    if($('input:radio[name=date_filter]:checked').val() == 0){
                        year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
                    }
                    dept = emptyToZero($('#edit-dept',context).val());
                    agency = emptyToZero($('#edit-agency',context).val());
                    expcategory = emptyToZero($('#edit-expense-category',context).val());
                    exptype = emptyToZero($('#edit-expense-type',context).val());
                    data_source = $('input:radio[name=datafeeds-spending-domain-filter]:checked').val();

                    $("#edit-payee-name",context).autocomplete("option", "source", '/autocomplete/spending/payee/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source);
                    $('#edit-contractno',context).autocomplete("option", "source", '/autocomplete/spending/contractno/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source);
                    $('#edit-document-id',context).autocomplete("option", "source", '/autocomplete/spending/documentid/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source);
                    $('#edit-capital-project',context).autocomplete("option", "source", '/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source);
                    $('#edit-entity-contract-number',context).autocomplete("option", "source", '/autocomplete/spending/entitycontractnum/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source);
                    $('#edit-commodity-line',context).autocomplete("option", "source", '/autocomplete/spending/commodityline/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source);
                    $('#edit-budget-name',context).autocomplete("option", "source", '/autocomplete/spending/budgetname/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + data_source);

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