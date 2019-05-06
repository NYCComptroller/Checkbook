(function ($) {
    //On Data Source Change
    $.fn.onDataSourceChange = function (dataSource) {
        //Remove all the validation errors when data source is changed
        $('div.messages').remove();
        $('.error').removeClass('error');

        //Reload Agency Drop-down Options
        $.fn.reloadAgencies(dataSource);

        //Show Hide fields
        $.fn.showHideFields(dataSource);

        //Clear all text fields and drop-downs
        $.fn.clearInput();


        //Reset the Spending Category
        $('select[name="expense_type"]').val('Total Spending [ts]');
        $('input[name="payee_name"]').removeAttr('disabled');
        $('input[name="contractno"]').removeAttr('disabled');

        //reset the selected columns
        $('#edit-column-select').multiSelect('deselect_all');
        $('#edit-oge-column-select').multiSelect('deselect_all');

        if(dataSource == 'checkbook_oge'){
            $('.form-item-oge-column-select').show();
            $('.form-item-column-select').hide();
        }else{
            $('.form-item-oge-column-select').hide();
            $('.form-item-column-select').show();
        }

    };

    //ShowHide fields based on selected data source
    $.fn.showHideFields = function (data_source) {
        switch (data_source){
            case 'checkbook_oge':
                $('.datafield.industry').hide();
                $('.datafield.mwbecategory').hide();
                $('.datafield.expenseid').hide();

                $('.datafield.commodityline').show();
                $('.datafield.entity_contract_number').show();
                $('.datafield.budgetname').show();

                $('input:radio[name=date_filter]')[0].checked = true;
                $('select[name="year"]').removeAttr('disabled');
                //Disable Issue date
                $('input:radio[name=date_filter][value="1"]').attr('disabled', 'disabled');
                $('input[name="issuedfrom"]').val("");
                $('input[name="issuedfrom"]').attr('disabled', 'disabled');
                $('input[name="issuedto"]').val("");
                $('input[name="issuedto"]').attr('disabled', 'disabled');

                $('.form-item-oge-column-select').show();
                $('.form-item-column-select').hide();

                //Move Issue Date fields to left column for OGE
                $('.datafield.datarange.check_amount').appendTo($(".spending.data-feeds-wizard .column.column-left"));
                break;
            default:
                $('.datafield.industry').show();
                $('.datafield.mwbecategory').show();
                $('.datafield.expenseid').show();

                $('.datafield.commodityline').hide();
                $('.datafield.entity_contract_number').hide();
                $('.datafield.budgetname').hide();

                //Date Filter
                var datefilter = $('input:hidden[name="date_filter_hidden"]').val();
                $('input:radio[name=date_filter][value="1"]').removeAttr('disabled');

                if (datefilter == 0) {
                    $('input[name="issuedfrom"]').val("");
                    $('input[name="issuedfrom"]').attr('disabled', 'disabled');
                    $('input[name="issuedto"]').val("");
                    $('input[name="issuedto"]').attr('disabled', 'disabled');
                }else{
                    $('input:radio[name=date_filter]')[1].checked = true;
                    $('select[name="year"]').attr('disabled', 'disabled');
                }

                $('.form-item-oge-column-select').hide();
                $('.form-item-column-select').show();

                //Move Issue Date fields to left column for Citywide
                $('.datafield.datarange.check_amount').prependTo($(".spending.data-feeds-wizard .column.column-right"));
        }

    };

    //Load Agency Drop-Down
    $.fn.reloadAgencies = function(dataSource){
        //Change the Agency drop-down label
        if(dataSource == 'checkbook_oge') {
            $("label[for = edit-agency]").text("Other Government Entity:");
        }else{
            $("label[for = edit-agency]").text("Agency:");
        }
        var agency_hidden = $('input:hidden[name="agency_hidden"]').val();
        $.ajax({
            url: '/datafeeds/spending/agency/' + dataSource + '/1'
            ,success: function(data) {
                var html = '';
                if (data[0]) {
                    if (data[0].label !== 'No Matches Found') {
                        for (var i = 0; i < data.length; i++) {
                            html = html + '<option title="' + data[i].value + '" value="' + data[i].value + '">' + data[i].label + '</option>';
                        }
                    }
                }
                $('#edit-agency').html(html);
                if(agency_hidden){
                    $('#edit-agency').val(agency_hidden);
                }
            }
        });
        $.fn.reloadDepartments();
        $.fn.reloadExpenseCategories();
    }

    // When Agency Filter is changed reload Department and Expense Category drop-downs
    $.fn.reloadDepartments = function reloadDepartments() {
        var agency_hidden = $('input:hidden[name="agency_hidden"]').val();
        if ($.inArray(agency_hidden, ["", null, 'Select One', 'Citywide (All Agencies)']) == -1) {
            var year = 0;
            if ($('input:radio[name=date_filter]:checked').val() == 0) {
                year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
            }
            var agency = emptyToZero($('input:hidden[name="agency_hidden"]').val());
            var spending_cat = emptyToZero($('#edit-expense-type').val());
            var data_source = $('input:hidden[name="data_source"]').val();
            var dept_hidden = $('input:hidden[name="dept_hidden"]').val();

            $.ajax({
                url: '/autocomplete/spending/dept/' + year + '/' + agency + '/' + spending_cat + '/' + data_source
                , success: function (data) {
                    var html = '<option select="selected" value="0" >Select Department</option>';
                    if (data[0]) {
                        if (data[0].label !== '') {
                            for (var i = 0; i < data.length; i++) {
                                html = html + '<option title="' + data[i].value + '" value="' + data[i].value + ' ">' + data[i].label + '</option>';
                            }
                        }
                    }
                    //Reload Department Drop-down
                    $('#edit-dept').removeAttr('disabled');
                    $('#edit-dept').html(html);
                    if(dept_hidden){
                        $('#edit-dept').val(dept_hidden);
                    }
                }
            });
        }else{
            var html= '<option value="" selected="selected">Select Department</option>';
            $('#edit-dept').html(html);
            $('#edit-dept').attr('disabled','disabled');
        }

    }

    // When Department Filter is changed reload Expense category Drop-down
    $.fn.reloadExpenseCategories = function reloadExpenseCategories() {
        var agency_hidden = $('input:hidden[name="agency_hidden"]').val();
        if ($.inArray(agency_hidden, ["", null, 'Select One', 'Citywide (All Agencies)']) == -1) {
            var year = 0;
            if ($('input:radio[name=date_filter]:checked').val() == 0) {
                year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
            }
            var agency = emptyToZero($('input:hidden[name="agency_hidden"]').val());
            var dept = encodeURIComponent($('input:hidden[name="dept_hidden"]').val());
            var spending_cat = emptyToZero($('#edit-expense-type').val());
            var data_source = $('input:hidden[name="data_source"]').val();
            var expense_category_hidden = $('input:hidden[name="expense_category_hidden"]').val();

            $.ajax({
                url: '/autocomplete/spending/expcategory/' + agency + '/' + dept + '/' + spending_cat + '/' + year + '/' + data_source
                , success: function (data) {
                    var html = '<option select="selected" value="0" >Select Expense Category</option>';
                    if (data[0]) {
                        if (data[0].label !== '') {
                            for (var i = 0; i < data.length; i++) {
                                html = html + '<option title="' + data[i].value + '" value="' + data[i].value + ' ">' + data[i].label + '</option>';
                            }
                        }
                    }
                    $('#edit-expense-category').removeAttr('disabled');
                    $('#edit-expense-category').html(html);
                    if (expense_category_hidden) {
                        $('#edit-expense-category').val(expense_category_hidden);
                    }
                }
            });
        }else{
            var html = '<option value="" selected="selected">Select Expense Category</option>';
            $('#edit-expense-category').html(html);
            $('#edit-expense-category').attr('disabled','disabled');
        }
    }

    $.fn.onSpendingCategoryChange = function onSpendingCategoryChange(){

        $('input[name="contractno"]').removeAttr('disabled');
        $('input[name="payee_name"]').removeAttr('disabled');
        $('option[value="Payee Name"]').removeAttr('disabled');
        $('option[value="payee_name"]').removeAttr('disabled');
        $('option[value="Contract ID"]').removeAttr('disabled');
        $('option[value="contract_ID"]').removeAttr('disabled');

        var exptypeval = $('select[name="expense_type"]').val();
        if (exptypeval == 'Payroll [p]') {
             //Disable Payee Name and ContractID fields for Payroll Spending Category
             $('input[name="contractno"]').attr('disabled', 'disabled');
             $('input[name="payee_name"]').attr('disabled', 'disabled');
             $('option[value="Payee Name"]').attr('disabled', 'disabled');
             $('option[value="payee_name"]').attr('disabled', 'disabled');
             $('option[value="Contract ID"]').attr('disabled', 'disabled');
             $('option[value="contract_ID"]').attr('disabled', 'disabled');
        }
        if (exptypeval == 'Others [o]') {
            //Disable ContractID field for Others Spending Category
             $('input[name="contractno"]').attr('disabled', 'disabled');
             $('option[value="Contract ID"]').attr('disabled', 'disabled');
             $('option[value="contract_ID"]').attr('disabled', 'disabled');
        }
    }

    Drupal.behaviors.spendingDataFeeds = {
        attach:function (context, settings) {
            var dataSource = $('input:hidden[name="data_source"]', context).val();
            //Agency drop-down options
            $.fn.reloadAgencies(dataSource);

            // Sets up multi-select/option transfer for CityWide
            $('#edit-column-select', context).multiSelect();
            if(!$('#ms-edit-column-select .ms-selection', context).next().is("a")){
                $('#ms-edit-column-select .ms-selection', context).after('<a class="deselect">Remove All</a>');
                $('#ms-edit-column-select .ms-selection', context).after('<a class="select">Add All</a>');
            }
            $('#ms-edit-column-select a.select', context).click(function () {
                $('#edit-column-select', context).multiSelect('select_all');
            });
            $('#ms-edit-column-select a.deselect', context).click(function () {
                $('#edit-column-select', context).multiSelect('deselect_all');
            });

            // Sets up multi-select/option transfer for OGE
            $('#edit-oge-column-select', context).multiSelect();
            if(!$('#ms-edit-oge-column-select .ms-selection', context).next().is("a")){
                $('#ms-edit-oge-column-select .ms-selection', context).after('<a class="deselect">Remove All</a>');
                $('#ms-edit-oge-column-select .ms-selection', context).after('<a class="select">Add All</a>');
            }
            $('#ms-edit-oge-column-select a.select', context).click(function () {
                $('#edit-oge-column-select', context).multiSelect('select_all');
            });
            $('#ms-edit-oge-column-select a.deselect', context).click(function () {
                $('#edit-oge-column-select', context).multiSelect('deselect_all');
            });

            //Display or hide fields based on data source selection
            $.fn.showHideFields(dataSource);

            //Preserve field dsplay configuration based on Spending category value
            $.fn.onSpendingCategoryChange();

            //Data Source change event
            $('input:radio[name=datafeeds-spending-domain-filter]', context).change(function (){
                $('input:hidden[name="data_source"]', context).val($(this, context).val());
                $('input:hidden[name="agency_hidden"]', context).val("");
                $('input:hidden[name="hidden_multiple_value"]', context).val("");
                $('input:hidden[name="date_filter_hidden"]', context).val("");
                $.fn.onDataSourceChange($(this, context).val());
            });

            //Agency drop-down change event
            $('select[name="agency"]', context).change(function (){
                $('input:hidden[name="agency_hidden"]', context).val($('#edit-agency', context).val());
                $('input:hidden[name="dept_hidden"]', context).val("");
                $('input:hidden[name="expense_category_hidden"]', context).val("");
                $.fn.reloadDepartments();
                $.fn.reloadExpenseCategories();
            });

            //Department drop-down change event
            $('select[name="dept"]', context).change(function (){
                $('input:hidden[name="dept_hidden"]', context).val($('#edit-dept', context).val());
                $('input:hidden[name="expense_category_hidden"]', context).val("");
                $.fn.reloadExpenseCategories();
            });

            //Spending Category change event
            $('select[name="expense_type"]', context).change(function (){
                $.fn.onSpendingCategoryChange();
            });

            //On Date Filter change
            $("#edit-date-filter", context).change(function(){
                $('input:hidden[name="date_filter_hidden"]', context).val($(this, context).val());
                if($('input:radio[name=date_filter]:checked', context).val() == 0){
                    $('select[name="year"]', context).removeAttr("disabled");
                    $('input[name="issuedfrom"]', context).val("");
                    $('input[name="issuedfrom"]', context).attr('disabled', 'disabled');
                    $('input[name="issuedto"]', context).val("");
                    $('input[name="issuedto"]', context).attr('disabled', 'disabled');
                } else if ($('input:radio[name=date_filter]:checked', context).val() == 1) {
                    $('select[name="year"]', context).attr('disabled', 'disabled');
                    $('input[name="issuedfrom"]', context).removeAttr("disabled");
                    $('input[name="issuedto"]', context).removeAttr("disabled");
                }
            });

            //Sets up jQuery UI datepickers
            var currentYear = new Date().getFullYear();
            $('.datepicker', context).datepicker({dateFormat:"yy-mm-dd",
                                                changeMonth:true,
                                                changeYear:true,
                                                yearRange:'-'+(currentYear-1900)+':+'+(2500-currentYear)});

            //Sets up jQuery UI autocompletes and autocomplete filtering functionality
            var year = 0;
            if ($('input:radio[name=date_filter]:checked').val() == 0) {
                year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
            }

            var dept = encodeURIComponent($('#edit-dept',context).val());
            var agency = emptyToZero($('#edit-agency',context).val());
            var expcategory = encodeURIComponent($('#edit-expense-category',context).val());
            var exptype = emptyToZero($('#edit-expense-type',context).val());
            var mwbecat = emptyToZero($('#edit-mwbe-category',context).val());
            var industry = emptyToZero($('#edit-industry',context).val());
            var data_source = $('input:hidden[name="data_source"]').val();

            //Sets up jQuery UI autocompletes and autocomplete filtering functionality
            $('#edit-payee-name',context).autocomplete({source:'/autocomplete/spending/payee/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source});
            $('#edit-contractno',context).autocomplete({source:'/autocomplete/spending/contractno/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source});
            $('#edit-document-id',context).autocomplete({source:'/autocomplete/spending/documentid/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source});
            $('#edit-capital-project',context).autocomplete({source:'/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source});
            $('#edit-entity-contract-number',context).autocomplete({source:'/autocomplete/spending/entitycontractnum/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source});
            $('#edit-commodity-line',context).autocomplete({source:'/autocomplete/spending/commodityline/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + '/' + industry + data_source});
            $('#edit-budget-name',context).autocomplete({source:'/autocomplete/spending/budgetname/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + '/' + industry + data_source});
            $('.watch:input',context).each(function () {
                $(this).focusin(function () {
                    //set variables for each field's value
                    year = 0;
                    if($('input:radio[name=date_filter]:checked').val() == 0){
                        year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
                    }
                    dept = encodeURIComponent($('#edit-dept',context).val());
                    agency = emptyToZero($('#edit-agency',context).val());

                    expcategory = encodeURIComponent($('#edit-expense-category',context).val());
                    exptype = emptyToZero($('#edit-expense-type',context).val());
                    mwbecat = emptyToZero($('#edit-mwbe-category',context).val());
                    industry = emptyToZero($('#edit-industry',context).val());
                    data_source = $('input:hidden[name="data_source"]').val();

                    $("#edit-payee-name",context).autocomplete("option", "source", '/autocomplete/spending/payee/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
                    $('#edit-contractno',context).autocomplete("option", "source", '/autocomplete/spending/contractno/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
                    $('#edit-document-id',context).autocomplete("option", "source", '/autocomplete/spending/documentid/'  + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
                    $('#edit-capital-project',context).autocomplete("option", "source", '/autocomplete/spending/capitalproject/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
                    $('#edit-entity-contract-number',context).autocomplete("option", "source", '/autocomplete/spending/entitycontractnum/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
                    $('#edit-commodity-line',context).autocomplete("option", "source", '/autocomplete/spending/commodityline/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
                    $('#edit-budget-name',context).autocomplete("option", "source", '/autocomplete/spending/budgetname/' + year + '/' + agency + '/' + expcategory + '/' + dept + '/' + exptype + '/' + mwbecat + '/' + industry + '/' + data_source);
                });
            });
            fixAutoCompleteWrapping($("#dynamic-filter-data-wrapper").children());
        }
    }

    //Prevent the auto-complete from wrapping un-necessarily
    function fixAutoCompleteWrapping(divWrapper) {
        jQuery(divWrapper.children()).find('input.ui-autocomplete-input:text').each(function () {
            $(this).data("autocomplete")._resizeMenu = function () {
                (this.menu.element).outerWidth('100%');
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

    //Function to clear text fields and drop-downs
    $.fn.clearInput = function () {
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
            }
        });
    }

}(jQuery));