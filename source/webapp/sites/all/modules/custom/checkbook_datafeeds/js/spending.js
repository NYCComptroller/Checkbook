(function ($) {
    //On Data Source Change
    $.fn.onDataSourceChange = function (data_source) {
        //Change the Agency drop-down label
        if(data_source == 'checkbook') {
            $("label[for = edit-agency]").text("Agency:");
        }else if(data_source == 'checkbook_oge') {
            $("label[for = edit-agency]").text("Other Government Entity:");
        }

        //Reload Agency drop-down options
        $.ajax({
            url: '/datafeeds/spending/agency/' + data_source + '/1'
            ,success: function(data) {
                var html = '';
                if (data[0]) {
                    if (data[0].label !== 'No Matches Found') {
                        for (var i = 0; i < data.length; i++) {
                            html = html + '<option title="' + data[i].value + '" value="' + data[i].value + ' ">' + data[i].label + '</option>';
                        }
                    }
                }
                $('#edit-agency').html(html);
            }
        });

        //Disable Department and Expense Category drop-downs
        $.fn.disableDeptExpCat();

        //Show Hide fields
        $.fn.showHideFields(data_source);

        //Clear all text fields
        var enclosingDiv = $("#dynamic-filter-data-wrapper").children('#edit-filter').children('div.fieldset-wrapper').children();
        jQuery(enclosingDiv).find(':input').each(function () {
            if (this.type == 'text') {
                jQuery(this).val('');
            }
        });

        //Reset the Spending Category
        $('select[name="expense_type"]').val('Total Spending [ts]');
        $('input[name="payee_name"]').removeAttr('disabled');
        $('input[name="contractno"]').removeAttr('disabled');

        //reset the selected columns
        $('#edit-column-select').multiSelect('deselect_all');
        $('#edit-oge-column-select').multiSelect('deselect_all');

        if(data_source == 'checkbook_oge'){
            $('.form-item-oge-column-select').show();
            $('.form-item-column-select').hide();
        }else{
            $('.form-item-oge-column-select').hide();
            $('.form-item-column-select').show();
        }

    };

    //ShowHide fields based on selected data source
    $.fn.showHideFields = function (data_source) {
        $.fn.disableDeptExpCat();
        switch (data_source){
            case 'checkbook_oge':
                $('.datafield.industry').hide();
                $('.datafield.mwbecategory').hide();
                $('.datafield.expenseid').hide();

                $('.datafield.commadityline').show();
                $('.datafield.entity_contract_number').show();
                $('.datafield.budgetname').show();

                $('input:radio[name=date_filter]')[0].checked = true;
                $('select[name="year"]').removeAttr('disabled');
                $('input:radio[name=date_filter][value="1"]').attr('disabled', 'disabled');
                $('input[name="issuedfrom"]').attr('disabled', 'disabled');
                $('input[name="issuedto"]').attr('disabled', 'disabled');
                break;
            default:
                $('.datafield.industry').show();
                $('.datafield.mwbecategory').show();
                $('.datafield.expenseid').show();

                $('.datafield.commodityline').hide();
                $('.datafield.entity_contract_number').hide();
                $('.datafield.budgetname').hide();

                $('input:radio[name=date_filter]')[0].checked = true;
                $('input:radio[name=date_filter][value="1"]').removeAttr('disabled')
        }
    };

    $.fn.disableDeptExpCat = function(){
        if ($('#edit-agency').val() == 'Citywide (All Agencies)' || $('#edit-agency').val() == 'Select One') {
            $('select[name="dept"]').val('');
            $('select[name="dept"]').attr('disabled', 'disabled');
            $('select[name="expense_category"]').val('');
            $('select[name="expense_category"]').attr('disabled', 'disabled');
        }else{
            $('select[name="dept"]').removeAttr('disabled');
            $('select[name="expense_category"]').removeAttr('disabled');
        }
    };

    // When Agency Filter is changed reload Department and Expense Category drop-downs
    $.fn.onAgencyChange = function onAgencyChange() {
        $.fn.disableDeptExpCat();
        if ($('#edit-agency').val() != 'Citywide (All Agencies)' || $('#edit-agency').val() != 'Select One') {
            var year = 0;
            if ($('input:radio[name=date_filter]:checked').val() == 0) {
                year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
            }
            var agency = emptyToZero($('#edit-agency').val());
            var spending_cat = emptyToZero($('#edit-expense-type').val());
            var data_source = $('input:radio[name=datafeeds-spending-domain-filter]:checked').val();
            $.ajax({
                url: '/autocomplete/spending/dept/' + year + '/' + agency + '/' + spending_cat + '/' + data_source
                , success: function (data) {
                    var html = '<option select="selected" value="0" >Select Department</option>';
                    if (data[0]) {
                        if (data[0] != 'No Matches Found') {
                            for (var i = 0; i < data.length; i++) {
                                html = html + '<option value="' + data[i] + ' ">' + data[i] + '</option>';
                            }
                        }
                        else {
                            html = html + '<option value="">' + data[0] + '</option>';
                        }
                    }
                    //Reload Department Drop-down
                    $('#edit-dept').html(html);
                    //Reload Expense Category Drop-down
                    $.fn.onDeptChange();
                }
            });
        }
    }

    // When Department Filter is changed reload Expense category Drop-down
    $.fn.onDeptChange = function onDeptChange() {
        var year = 0;
        if($('input:radio[name=date_filter]:checked').val() == 0){
            year = ($('#edit-year').val()) ? $('#edit-year').val() : 0;
        }
        var agency = emptyToZero($('#edit-agency').val());
        var dept = emptyToZero($('#edit-dept').val());
        var spending_cat = emptyToZero($('#edit-expense-type').val());
        var data_source = $('input:radio[name=datafeeds-spending-domain-filter]:checked').val();

        $.ajax({
            url: '/autocomplete/spending/expcategory/' + agency + '/' + dept + '/' + spending_cat + '/' + year + '/' + data_source
            ,success: function(data) {
                var html = '<option select="selected" value="0" >Select Expense Category</option>';
                if(data[0]){
                    if(data[0]!= 'No Matches Found'){
                        for (var i = 0; i < data.length; i++) {
                            html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                        }
                    }
                    else { html=html + '<option value="">' + data[0]  + '</option>'; }
                }
                $('#edit-expense-category').html(html);
            }
        });
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
            var p = /\[(.*?)\]$/;

            var dataSource = $('input:radio[name=datafeeds-spending-domain-filter]:checked', context).val();
            var datefilter = $('input[name="date_filter"]:checked', context).val();
            var exptypeval = $('select[name="expense_type"]', context).val();
            $.fn.showHideFields(dataSource);

            //Data Source change event
            $('input:radio[name=datafeeds-spending-domain-filter]', context).change(function (){
                $.fn.onDataSourceChange($(this, context).val());
            });

            //Agency drop-down change event
            $('select[name="agency"]', context).change(function (){
                $.fn.onAgencyChange();
            });

            //Department drop-down change event
            $('select[name="dept"]', context).change(function (){
                $.fn.onDeptChange();
            });

            //Spending Category change event
            $('select[name="expense_type"]', context).change(function (){
                $.fn.onSpendingCategoryChange();
            });

            //Date Filter
            if (datefilter == 0) {
                $('input[name="issuedfrom"]', context).attr('disabled', 'disabled');
                $('input[name="issuedto"]', context).attr('disabled', 'disabled');
            } else if (datefilter == 1) {
                $('select[name="year"]', context).attr('disabled', 'disabled');
            }

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

            //Show CityWide multi-select by default and hide OGE multi-select
            $('.form-item-oge-column-select', context).hide();
            $('.form-item-column-select', context).show();

            //On Date Filter change
            $("#edit-date-filter input[name='date_filter']", context).click(function(){
                if($('input:radio[name=date_filter]:checked', context).val() == 0){
                    $('select[name="year"]', context).removeAttr("disabled");
                    $('input[name="issuedfrom"]', context).attr('disabled', 'disabled');
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
            var dept = emptyToZero($('#edit-dept',context).val());
            var agency = emptyToZero($('#edit-agency',context).val());
            var expcategory = emptyToZero($('#edit-expense-category',context).val());
            var exptype = emptyToZero($('#edit-expense-type',context).val());
            var mwbecat = emptyToZero($('#edit-mwbe-category',context).val());
            var industry = emptyToZero($('#edit-industry',context).val());
            var data_source = $('input:radio[name=datafeeds-spending-domain-filter]:checked').val();

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
                    dept = emptyToZero($('#edit-dept',context).val());
                    agency = emptyToZero($('#edit-agency',context).val());
                    expcategory = emptyToZero($('#edit-expense-category',context).val());
                    exptype = emptyToZero($('#edit-expense-type',context).val());
                    mwbecat = emptyToZero($('#edit-mwbe-category',context).val());
                    industry = emptyToZero($('#edit-industry',context).val());
                    data_source = $('input:radio[name=datafeeds-spending-domain-filter]:checked').val();

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

}(jQuery));