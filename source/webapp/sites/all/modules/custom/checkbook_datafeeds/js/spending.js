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
//
//    var autoComplete = function () {
//        this.fields = {
//            'payee':'#edit-payee-name',
//            'contractno':'#edit-contractno',
//            'documentid':'#edit-document-id',
//            'capitalproject':'#edit-capital-project',
//            'entitycontractnum':'#edit-entity-contract-num',
//            'commodityline':'#edit-commodity-line',
//            'budgetname':'#edit-budget-name'
//        };
//        this.params = {
//            'agency':'select[name="=agency"]',
//            'dept':'select[name="dept"]',
//            'expCategory':'select[name="expense_category"]',
//            'expType':'select[name="expense_type"]',
//            'entityContractNum':'input:text[name=entity_contract_num]',
//            'commodityLine':'input:text[name=commodity_line]',
//            'budgetName':'input:text[name=spending_budget_name]'
//        };
//    };
//    autoComplete.prototype.initialize = function (context, dataSource, mode) {
//        var year, path;
//
//        if ($('input:radio[name=date_filter]:checked').val() == 0)
//            year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
//
//        path = year + '/';
//
//        $.each(this.params, function (name, selector) {
//            path += emptyToZero($(selector, context).val()) + '/';
//        });
//
//        $.each(this.fields, function (fieldName, fieldId) {
//            var fullPath = '/autocomplete/spending/' + fieldName + '/' + path + dataSource;
//
//            if (mode == 'option')
//                $(fieldId, context).autocomplete("option", "source", fullPath);
//            else
//                $(fieldId, context).autocomplete({source:fullPath});
//        });
//    };
//
//    $.fn.addAutoComplete = function (context, dataSource, mode) {
//        var objAutoComplete = new autoComplete();
//        objAutoComplete.initialize(context, dataSource, mode);
//    };

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
            var contractid = $('#edit-contractno',context).val();
            var capitalproject = $('#edit-capital-project',context).val();
            var entitycontractnum = $('#edit-entity-contract-num',context).val();
            var commodityline = $('#edit-commodity-line',context).val();
            var budgetname = $('#edit-budget-name',context).val();


            //$.fn.addAutoComplete(context, dataSource, null);

            //Sets up jQuery UI autocompletes and autocomplete filtering functionality
            var path = year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + contractid  + '/' + capitalproject + '/' + entitycontractnum + '/'+ exptype + '/'+ commodityline + '/' + budgetname + '/' + dataSource;

            $('#edit-payee-name',context).autocomplete({source:'/autocomplete/spending/payee/' + path});
            $('#edit-contractno',context).autocomplete({source:'/autocomplete/spending/contractno/' + path});
            $('#edit-document-id',context).autocomplete({source:'/autocomplete/spending/documentid/' + path});
            $('#edit-capital-project',context).autocomplete({source:'/autocomplete/spending/capitalproject/' + path});


            $('#edit-entity-contract-num',context).autocomplete({source:'/autocomplete/spending/entitycontractnum/' + path});
            $('#edit-commodity-line',context).autocomplete({source:'/autocomplete/spending/commodityline/' + path});
            $('#edit-budget-name',context).autocomplete({source:'/autocomplete/spending/budgetname/' + path});
            $('.watch:input', context).each(function () {
                $(this).focusin(function () {
                    //set variables for each field's value
                    if ($('input:radio[name=date_filter]:checked').val() == 0) {
                        year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
                    }
                    //$.fn.addAutoComplete(context, dataSource, 'option');
                    dept = emptyToZero($('#edit-dept',context).val());
                    agency = emptyToZero($('#edit-agency',context).val());
                    expcategory = emptyToZero($('#edit-expense-category',context).val());
                    exptype = emptyToZero($('#edit-expense-type',context).val());
                    contractid = $('#edit-contractno',context).val();
                    capitalproject = $('#edit-capital-project',context).val();
                    entitycontractnum = $('#edit-entity-contract-num',context).val();
                    commodityline = $('#edit-commodity-line',context).val();
                    budgetname = $('#edit-budget-name',context).val();
                    path = year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + contractid  + '/' + capitalproject + '/' + entitycontractnum + '/'+ exptype + '/'+ commodityline + '/' + budgetname + '/' + dataSource;

                    $("#edit-payee-name",context).autocomplete("option", "source", '/autocomplete/spending/payee/'  + path);
                    $('#edit-contractno',context).autocomplete("option", "source", '/autocomplete/spending/contractno/'  + path);
                    $('#edit-document-id',context).autocomplete("option", "source", '/autocomplete/spending/documentid/'  + path);
                    $('#edit-capital-project',context).autocomplete("option", "source", '/autocomplete/spending/capitalproject/' + path);
                    $('#edit-entity-contract-num',context).autocomplete("option", "source", '/autocomplete/spending/entitycontractnum/' + path);
                    $('#edit-commodity-line',context).autocomplete("option", "source", '/autocomplete/spending/commodityline/' + path);
                    $('#edit-budget-name',context).autocomplete("option", "source", '/autocomplete/spending/budgetname/' + path);
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