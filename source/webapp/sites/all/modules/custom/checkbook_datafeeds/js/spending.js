(function ($) {

    $.fn.disableStuff = function(){
        if ($('#edit-agency').val() == 'Citywide (All Agencies)'){
            $('select[name="dept"]').val('');
            $('select[name="dept"]').attr('disabled','disabled');
            $('select[name="expense_category"]').val('');
            $('select[name="expense_category"]').attr('disabled','disabled');
        }
        if ($('#edit-other-government-entities').val() == 'Citywide (All Agencies)'){
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
            //var othergovernmententities = emptyToZero($('#edit-other-government-entities',context).val());
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
                   // othergovernmententities = emptyToZero($('#edit-other-government-entities',context).val());
                    expcategory = emptyToZero($('#edit-expense-category--2',context).val());
                    exptype = emptyToZero($('#edit-expense-type',context).val());
                    $("#edit-payee-name",context).autocomplete("option", "source", '/autocomplete/spending/payee/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype);
                    $('#edit-contractno',context).autocomplete("option", "source", '/autocomplete/spending/contractno/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype);
                    $('#edit-document-id',context).autocomplete("option", "source", '/autocomplete/spending/documentid/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype);
                    $('#edit-capital-project',context).autocomplete("option", "source", '/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype);
                });
            });

            //Hide/show fields based on data source, default to checkbook data source

            //Initialize Filter Data
            $('input:radio[name=spending_datafeeds_domain_filter][value="0"]').attr('checked',true);
            changeFilterView($('input:radio[name=spending_datafeeds_domain_filter]:checked').val());

            //Initialize Selectable Columns
            $('.ms-selectable li[ms-value="Agency"]').show();
            $('.ms-selectable li[ms-value="Other Government Entities"]').hide();
            $('.ms-selectable li[ms-value="Entity Contract #"]').hide();
            $('.ms-selectable li[ms-value="Commodity Line"]').hide();
            $('.ms-selectable li[ms-value="Budget Name"]').hide();

            //Initialize Selected Columns
            $('.ms-selection li[ms-value="Agency"]').show();
            $('.ms-selection li[ms-value="Other Government Entities"]').hide();
            $('.ms-selection li[ms-value="Entity Contract #"]').hide();
            $('.ms-selection li[ms-value="Commodity Line"]').hide();
            $('.ms-selection li[ms-value="Budget Name"]').hide();

            //Initialize Hidden Columns
            $('#edit-column-select option[value="Agency"]').show();
            $('#edit-column-select option[value="Other Government Entities"]').hide();
            $('#edit-column-select option[value="Entity Contract #"]').hide();
            $('#edit-column-select option[value="Commodity Line"]').hide();
            $('#edit-column-select option[value="Budget Name"]').hide();

            //Update all views based on data source selection and reset any selected columns
            $('input:radio[name=spending_datafeeds_domain_filter]').change(function () {
                $('#edit-column-select').multiSelect('deselect_all');
                changeFilterView($('input[name=spending_datafeeds_domain_filter]:checked').val());
                changeSelectableColumnView($('input[name=spending_datafeeds_domain_filter]:checked').val());
            });
            //Update selected columns on 'Add All' button
            $('a.select').click(function () {
                changeSelectedColumnView($('input[name=spending_datafeeds_domain_filter]:checked').val());
            });
            //Update selectable columns on 'Remove All' button
            $('a.deselect').click(function () {
                changeSelectableColumnView($('input[name=spending_datafeeds_domain_filter]:checked').val());
            });

            //Update filter data fields based on data source radio button selection
            function changeFilterView(dataSource) {
                switch (dataSource)
                {
                    case "1":
                        //Filter Data
                        $("#edit-filter").children('div.fieldset-wrapper').children('div.checkbook').hide();
                        $("#edit-filter").children('div.fieldset-wrapper').children('div.checkbook-oge').show();
                        break;

                    default:
                        //Filter Data
                        $("#edit-filter").children('div.fieldset-wrapper').children('div.checkbook').show();
                        $("#edit-filter").children('div.fieldset-wrapper').children('div.checkbook-oge').hide();
                        break;
                }
            }
            //Update selected columns based on data source radio button selection
            function changeSelectedColumnView(dataSource) {

                switch (dataSource)
                {
                    case "1":
                        //Selected Columns
                        $('.ms-selection li[ms-value="Agency"]').hide();
                        $('.ms-selection li[ms-value="Other Government Entities"]').show();
                        $('.ms-selection li[ms-value="Entity Contract #"]').show();
                        $('.ms-selection li[ms-value="Commodity Line"]').show();
                        $('.ms-selection li[ms-value="Budget Name"]').show();
                        break;

                    default:
                        //Selected Columns
                        $('.ms-selection li[ms-value="Agency"]').show();
                        $('.ms-selection li[ms-value="Other Government Entities"]').hide();
                        $('.ms-selection li[ms-value="Entity Contract #"]').hide();
                        $('.ms-selection li[ms-value="Commodity Line"]').hide();
                        $('.ms-selection li[ms-value="Budget Name"]').hide();
                        break;
                }
            }
            //Update selectable columns based on data source radio button selection
            function changeSelectableColumnView(dataSource) {
                switch (dataSource)
                {
                    case "1":
                        //Selectable Columns
                        $('.ms-selectable li[ms-value="Agency"]').hide();
                        $('.ms-selectable li[ms-value="Other Government Entities"]').show();
                        $('.ms-selectable li[ms-value="Entity Contract #"]').show();
                        $('.ms-selectable li[ms-value="Commodity Line"]').show();
                        $('.ms-selectable li[ms-value="Budget Name"]').show();

                        //Hidden Columns
                        $('#edit-column-select option[value="Agency"]').hide();
                        $('#edit-column-select option[value="Other Government Entities"]').show();
                        $('#edit-column-select option[value="Entity Contract #"]').show();
                        $('#edit-column-select option[value="Commodity Line"]').show();
                        $('#edit-column-select option[value="Budget Name"]').show();
                        break;

                    default:
                        //Selectable Columns
                        $('.ms-selectable li[ms-value="Agency"]').show();
                        $('.ms-selectable li[ms-value="Other Government Entities"]').hide();
                        $('.ms-selectable li[ms-value="Entity Contract #"]').hide();
                        $('.ms-selectable li[ms-value="Commodity Line"]').hide();
                        $('.ms-selectable li[ms-value="Budget Name"]').hide();

                        //Hidden Columns
                        $('#edit-column-select option[value="Agency"]').show();
                        $('#edit-column-select option[value="Other Government Entities"]').hide();
                        $('#edit-column-select option[value="Entity Contract #"]').hide();
                        $('#edit-column-select option[value="Commodity Line"]').hide();
                        $('#edit-column-select option[value="Budget Name"]').hide();
                        break;
                }
            }
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