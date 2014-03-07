(function ($) {
    Drupal.behaviors.contractsDataFeeds = {
        attach:function (context, settings) {
            //use jQuery to hide/show the correct column select
            var $contractStatus = $('select[name="df_contract_status"]', context);
            var $category = $('#edit-category', context);
            var csval = $('select[name="df_contract_status"]', context).val();
            var catval = $('#edit-category', context).val();
            hideShow(csval, catval);
            $contractStatus.change(function () {
                csval = $('select[name="df_contract_status"]', context).val();
                catval = $('#edit-category', context).val();
                hideShow(csval, catval);
            });
            $category.change(function () {
                csval = $('select[name="df_contract_status"]', context).val();
                catval = $('#edit-category', context).val();
                hideShow(csval, catval);
            });
            function hideShow(csval, catval) {
                var $expense = $('.form-item-column-select-expense', context);
                var $revenue = $('.form-item-column-select-revenue', context);
                var $pending = $('.form-item-column-select-pending', context);
                if (csval == 'active') {
                    if (catval == 'expense') {
                        $('.form-item-column-select-expense label').html('Columns (Active Expense)<span class="form-required">*</span>');
                        $expense.show();
                        $revenue.hide();
                        $pending.hide();
                    } else {
                        $('.form-item-column-select-revenue label').html('Columns (Active Revenue)<span class="form-required">*</span>');
                        $expense.hide();
                        $revenue.show();
                        $pending.hide();
                    }
                } else if (csval == 'registered'){
                    if (catval == 'expense'){
                        $('.form-item-column-select-expense label').html('Columns (Registered Expense)<span class="form-required">*</span>');
                        $expense.show();
                        $revenue.hide();
                        $pending.hide();
                    } else {
                        $('.form-item-column-select-revenue label').html('Columns (Registered Revenue)<span class="form-required">*</span>');
                        $expense.hide();
                        $revenue.show();
                        $pending.hide();
                    }
                } else {
                    if(catval == 'expense'){
                        $('.form-item-column-select-pending label').html('Columns (Pending Expense)<span class="form-required">*</span>');
                        $expense.hide();
                        $revenue.hide();
                        $pending.show();
                    } else {
                        $('.form-item-column-select-pending label').html('Columns (Pending Revenue)<span class="form-required">*</span>');
                        $expense.hide();
                        $revenue.hide();
                        $pending.show();
                    }

                }
            }
            //Set up jQuery datepickers
            $('.datepicker', context).datepicker({dateFormat:"yy-mm-dd"});
            //Disable Year option for All Years
            if ($('#edit-year', context).val() == 'ALL'){
                $('#edit-column-select-expense option[value="Year"]',context).attr('disabled','disabled');
                $('#edit-column-select-expense option[value="year"]',context).attr('disabled','disabled');
                $('#edit-column-select-revenue option[value="Year"]',context).attr('disabled','disabled');
                $('#edit-column-select-revenue option[value="year"]',context).attr('disabled','disabled');
            }
            //Set up multiselects/option transfers
            //Active/Registered Expense
            $('#edit-column-select-expense', context).multiSelect();
            $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="select">Add All</a>');
            $('#ms-edit-column-select-expense a.select', context).click(function () {
                $('#edit-column-select-expense', context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-expense a.deselect', context).click(function () {
                $('#edit-column-select-expense', context).multiSelect('deselect_all');
            });
            //Active/Registered Revenue
            $('#edit-column-select-revenue', context).multiSelect();
            $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="select">Add All</a>');
            $('#ms-edit-column-select-revenue a.select', context).click(function () {
                $('#edit-column-select-revenue', context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-revenue a.deselect', context).click(function () {
                $('#edit-column-select-revenue', context).multiSelect('deselect_all');
            });
            //Pending
            $('#edit-column-select-pending', context).multiSelect();
            $('#ms-edit-column-select-pending .ms-selection', context).after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select-pending .ms-selection', context).after('<a class="select">Add All</a>');
            $('#ms-edit-column-select-pending a.select', context).click(function () {
                $('#edit-column-select-pending', context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-pending a.deselect', context).click(function () {
                $('#edit-column-select-pending', context).multiSelect('deselect_all');
            });
            //Set up autocompletes
            var status = $('select[name="df_contract_status"]', context).val();
            var category = $('#edit-category', context).val();
            var contract_type = emptyToZero($('#edit-contract-type', context).val());
            var agency = emptyToZero($('#edit-agency', context).val());
            var award_method = emptyToZero($('#edit-award-method', context).val());
            var year = ($('#edit-year', context).attr('disabled')) ? 0 : $('#edit-year', context).val();
            $('#edit-vendor', context).autocomplete({source:'/autocomplete/contracts/vendor/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});
            $('#edit-contractno', context).autocomplete({source:'/autocomplete/contracts/contract_number/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});
            $('#edit-apt-pin',context).autocomplete({source:'/autocomplete/contracts/apt_pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});
            $('#edit-pin',context).autocomplete({source:'/autocomplete/contracts/pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});
            $('.watch:input', context).each(function () {
                $(this).focusin(function () {
                    var status = $('select[name="df_contract_status"]', context).val();
                    var category = $('#edit-category', context).val();
                    var contract_type = emptyToZero($('#edit-contract-type', context).val());
                    var agency = emptyToZero($('#edit-agency', context).val());
                    var award_method = emptyToZero($('#edit-award-method', context).val());
                    var year = ($('#edit-year', context).attr('disabled')) ? 0 : $('#edit-year', context).val();
                    $('#edit-vendor', context).autocomplete('option', 'source', '/autocomplete/contracts/vendor/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year);
                    $('#edit-contractno', context).autocomplete('option', 'source', '/autocomplete/contracts/contract_number/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year);
                    $('#edit-apt-pin',context).autocomplete('option','source','/autocomplete/contracts/apt_pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year);
                    $('#edit-pin',context).autocomplete('option','source','/autocomplete/contracts/pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year);
                });
            });
            $('#edit-year',context).change(function(){
               if ($(this).val() == 'ALL'){
                   $('#edit-column-select-expense option[value="Year"]',context).attr('disabled','disabled');
                   $('#edit-column-select-expense option[value="year"]',context).attr('disabled','disabled');
                   $('#edit-column-select-expense', context).multiSelect('refresh');
                   $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="deselect">Remove All</a>');
                   $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="select">Add All</a>');
                   $('#ms-edit-column-select-expense a.select', context).click(function () {
                       $('#edit-column-select-expense', context).multiSelect('select_all');
                   });
                   $('#ms-edit-column-select-expense a.deselect', context).click(function () {
                       $('#edit-column-select-expense', context).multiSelect('deselect_all');
                   });
                   $('#edit-column-select-revenue option[value="Year"]',context).attr('disabled','disabled');
                   $('#edit-column-select-revenue option[value="year"]',context).attr('disabled','disabled');
                   $('#edit-column-select-revenue', context).multiSelect('refresh');
                   $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="deselect">Remove All</a>');
                   $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="select">Add All</a>');
                   $('#ms-edit-column-select-revenue a.select', context).click(function () {
                       $('#edit-column-select-revenue', context).multiSelect('select_all');
                   });
                   $('#ms-edit-column-select-revenue a.deselect', context).click(function () {
                       $('#edit-column-select-revenue', context).multiSelect('deselect_all');
                   });
               } else {
                   $('#edit-column-select-expense option[value="Year"]',context).attr('disabled','');
                   $('#edit-column-select-expense option[value="year"]',context).attr('disabled','');
                   $('#edit-column-select-expense', context).multiSelect('refresh');
                   $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="deselect">Remove All</a>');
                   $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="select">Add All</a>');
                   $('#ms-edit-column-select-expense a.select', context).click(function () {
                       $('#edit-column-select-expense', context).multiSelect('select_all');
                   });
                   $('#ms-edit-column-select-expense a.deselect', context).click(function () {
                       $('#edit-column-select-expense', context).multiSelect('deselect_all');
                   });
                   $('#edit-column-select-revenue option[value="Year"]',context).attr('disabled','');
                   $('#edit-column-select-revenue option[value="year"]',context).attr('disabled','');
                   $('#edit-column-select-revenue', context).multiSelect('refresh');
                   $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="deselect">Remove All</a>');
                   $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="select">Add All</a>');
                   $('#ms-edit-column-select-revenue a.select', context).click(function () {
                       $('#edit-column-select-revenue', context).multiSelect('select_all');
                   });
                   $('#ms-edit-column-select-revenue a.deselect', context).click(function () {
                       $('#edit-column-select-revenue', context).multiSelect('deselect_all');
                   });
               }
            });

            //Hide/show fields based on data source, default to checkbook data source

            //Initialize Filter Data
            $('input:radio[name=contracts_datafeeds_domain_filter][value="0"]').attr('checked',true);
            changeFilterView($('input:radio[name=contracts_datafeeds_domain_filter]:checked').val());

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

            $('#edit-column-select-expense option[value="Agency"]',context).show();
            $('#edit-column-select-expense option[value="Other Government Entities"]',context).hide();
            $('#edit-column-select-expense option[value="Entity Contract #"]',context).hide();
            $('#edit-column-select-expense option[value="Commodity Line"]',context).hide();
            $('#edit-column-select-expense option[value="Budget Name"]',context).hide();
            $('#edit-column-select-revenue option[value="Agency"]',context).show();
            $('#edit-column-select-revenue option[value="Other Government Entities"]',context).hide();
            $('#edit-column-select-revenue option[value="Entity Contract #"]',context).hide();
            $('#edit-column-select-revenue option[value="Commodity Line"]',context).hide();
            $('#edit-column-select-revenue option[value="Budget Name"]',context).hide();
            $('#edit-column-select-pending option[value="Agency"]',context).show();
            $('#edit-column-select-pending option[value="Other Government Entities"]',context).hide();
            $('#edit-column-select-pending option[value="Entity Contract #"]',context).hide();
            $('#edit-column-select-pending option[value="Commodity Line"]',context).hide();
            $('#edit-column-select-pending option[value="Budget Name"]',context).hide();

            //Update all views based on data source selection and reset any selected columns
            $('input:radio[name=contracts_datafeeds_domain_filter]').change(function () {

                $('#edit-column-select-expense', context).multiSelect('deselect_all');
                $('#edit-column-select-revenue', context).multiSelect('deselect_all');
                $('#edit-column-select-pending', context).multiSelect('deselect_all');
                changeFilterView($('input[name=contracts_datafeeds_domain_filter]:checked').val());
                changeSelectableColumnView($('input[name=contracts_datafeeds_domain_filter]:checked').val());
            });
            //Update selected columns on 'Add All' button
            $('a.select').click(function () {
                changeSelectedColumnView($('input[name=contracts_datafeeds_domain_filter]:checked').val());
            });
            //Update selectable columns on 'Remove All' button
            $('a.deselect').click(function () {
                changeSelectableColumnView($('input[name=contracts_datafeeds_domain_filter]:checked').val());
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
                        $('#edit-column-select-expense option[value="Agency"]',context).hide();
                        $('#edit-column-select-expense option[value="Other Government Entities"]',context).show();
                        $('#edit-column-select-expense option[value="Entity Contract #"]',context).show();
                        $('#edit-column-select-expense option[value="Commodity Line"]',context).show();
                        $('#edit-column-select-expense option[value="Budget Name"]',context).show();
                        $('#edit-column-select-revenue option[value="Agency"]',context).hide();
                        $('#edit-column-select-revenue option[value="Other Government Entities"]',context).show();
                        $('#edit-column-select-revenue option[value="Entity Contract #"]',context).show();
                        $('#edit-column-select-revenue option[value="Commodity Line"]',context).show();
                        $('#edit-column-select-revenue option[value="Budget Name"]',context).show();
                        $('#edit-column-select-pending option[value="Agency"]',context).hide();
                        $('#edit-column-select-pending option[value="Other Government Entities"]',context).show();
                        $('#edit-column-select-pending option[value="Entity Contract #"]',context).show();
                        $('#edit-column-select-pending option[value="Commodity Line"]',context).show();
                        $('#edit-column-select-pending option[value="Budget Name"]',context).show();
                        break;

                    default:
                        //Selectable Columns
                        $('.ms-selectable li[ms-value="Agency"]').show();
                        $('.ms-selectable li[ms-value="Other Government Entities"]').hide();
                        $('.ms-selectable li[ms-value="Entity Contract #"]').hide();
                        $('.ms-selectable li[ms-value="Commodity Line"]').hide();
                        $('.ms-selectable li[ms-value="Budget Name"]').hide();

                        //Hidden Columns
                        $('#edit-column-select-expense option[value="Agency"]',context).show();
                        $('#edit-column-select-expense option[value="Other Government Entities"]',context).hide();
                        $('#edit-column-select-expense option[value="Entity Contract #"]',context).hide();
                        $('#edit-column-select-expense option[value="Commodity Line"]',context).hide();
                        $('#edit-column-select-expense option[value="Budget Name"]',context).hide();
                        $('#edit-column-select-revenue option[value="Agency"]',context).show();
                        $('#edit-column-select-revenue option[value="Other Government Entities"]',context).hide();
                        $('#edit-column-select-revenue option[value="Entity Contract #"]',context).hide();
                        $('#edit-column-select-revenue option[value="Commodity Line"]',context).hide();
                        $('#edit-column-select-revenue option[value="Budget Name"]',context).hide();
                        $('#edit-column-select-pending option[value="Agency"]',context).show();
                        $('#edit-column-select-pending option[value="Other Government Entities"]',context).hide();
                        $('#edit-column-select-pending option[value="Entity Contract #"]',context).hide();
                        $('#edit-column-select-pending option[value="Commodity Line"]',context).hide();
                        $('#edit-column-select-pending option[value="Budget Name"]',context).hide();
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