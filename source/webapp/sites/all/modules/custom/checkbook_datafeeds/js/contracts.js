(function ($) {

    $.fn.onDataSourceChange = function(){
        //clear all text fields
        var enclosingDiv = $("#dynamic-filter-data-wrapper").children('#edit-filter').children('div.fieldset-wrapper').children();
        jQuery(enclosingDiv).find(':input').each(function() {
            if(this.type == 'text') {
                jQuery(this).val('');
            }
        });

        //reset the drop-downs
        $('select[name="df_contract_status"]').val('active');
        $('select[name="contract_type"]').val('');
        $('select[name="category"]').val('expense');
        $('select[name="award_method"]').val('');
        $('select[name="year"]').val('');

        //reset the selected columns
        $('#edit-column-select-expense').multiSelect('deselect_all');
        $('#edit-column-select-revenue').multiSelect('deselect_all');
        $('#edit-column-select-pending').multiSelect('deselect_all');
    }

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
            if(!$('#ms-edit-column-select-expense .ms-selection', context).next().is("a")){
                $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="deselect">Remove All</a>');
                $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="select">Add All</a>');
            }
            $('#ms-edit-column-select-expense a.select', context).click(function () {
                $('#edit-column-select-expense', context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-expense a.deselect', context).click(function () {
                $('#edit-column-select-expense', context).multiSelect('deselect_all');
            });
            //Active/Registered Revenue
            $('#edit-column-select-revenue', context).multiSelect();
            if(!$('#ms-edit-column-select-revenue .ms-selection', context).next().is("a")){
                $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="deselect">Remove All</a>');
                $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="select">Add All</a>');
            }
            $('#ms-edit-column-select-revenue a.select', context).click(function () {
                $('#edit-column-select-revenue', context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-revenue a.deselect', context).click(function () {
                $('#edit-column-select-revenue', context).multiSelect('deselect_all');
            });
            //Pending
            $('#edit-column-select-pending', context).multiSelect();
            if(!$('#ms-edit-column-select-pending .ms-selection', context).next().is("a")){
                $('#ms-edit-column-select-pending .ms-selection', context).after('<a class="deselect">Remove All</a>');
                $('#ms-edit-column-select-pending .ms-selection', context).after('<a class="select">Add All</a>');
            }
            $('#ms-edit-column-select-pending a.select', context).click(function () {
                $('#edit-column-select-pending', context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-pending a.deselect', context).click(function () {
                $('#edit-column-select-pending', context).multiSelect('deselect_all');
            });

            var status = $('select[name="df_contract_status"]', context).val();
            var category = $('#edit-category', context).val();
            var contract_type = emptyToZero($('#edit-contract-type', context).val());
            var agency = emptyToZero($('#edit-agency', context).val());
            var award_method = emptyToZero($('#edit-award-method', context).val());
            var year = ($('#edit-year', context).attr('disabled')) ? 0 : $('#edit-year', context).val();
            var data_source = $('input:radio[name=datafeeds-contracts-domain-filter]:checked').val();

            $('#edit-vendor', context).autocomplete({source:'/autocomplete/contracts/vendor/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source});
            $('#edit-contractno', context).autocomplete({source:'/autocomplete/contracts/contract_number/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source});
            $('#edit-apt-pin',context).autocomplete({source:'/autocomplete/contracts/apt_pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source});
            $('#edit-pin',context).autocomplete({source:'/autocomplete/contracts/pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source});
            $('#edit-entity-contract-number',context).autocomplete({source:'/autocomplete/contracts/entitycontractnum/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source});
            $('#edit-commodity-line',context).autocomplete({source:'/autocomplete/contracts/commodityline/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source});
            $('#edit-budget-name',context).autocomplete({source:'/autocomplete/contracts/budgetname/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source});
            $('.watch:input', context).each(function () {
                $(this).focusin(function () {
                    var status = $('select[name="df_contract_status"]', context).val();
                    var category = $('#edit-category', context).val();
                    var contract_type = emptyToZero($('#edit-contract-type', context).val());
                    var agency = emptyToZero($('#edit-agency', context).val());
                    var award_method = emptyToZero($('#edit-award-method', context).val());
                    var year = ($('#edit-year', context).attr('disabled')) ? 0 : $('#edit-year', context).val();
                    var data_source = $('input:radio[name=datafeeds-contracts-domain-filter]:checked').val();

                    $('#edit-vendor', context).autocomplete('option', 'source', '/autocomplete/contracts/vendor/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source);
                    $('#edit-contractno', context).autocomplete('option', 'source', '/autocomplete/contracts/contract_number/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source);
                    $('#edit-apt-pin',context).autocomplete('option','source','/autocomplete/contracts/apt_pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source);
                    $('#edit-pin',context).autocomplete('option','source','/autocomplete/contracts/pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source);
                    $('#edit-entity-contract-number',context).autocomplete('option','source','/autocomplete/contracts/entitycontractnum/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source);
                    $('#edit-commodity-line',context).autocomplete('option','source','/autocomplete/contracts/commodityline/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source);
                    $('#edit-budget-name',context).autocomplete('option','source','/autocomplete/contracts/budgetname/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + data_source);
                });
            });
            $('#edit-year',context).change(function(){
               if ($(this).val() == 'ALL'){
                   $('#edit-column-select-expense option[value="Year"]',context).attr('disabled','disabled');
                   $('#edit-column-select-expense option[value="year"]',context).attr('disabled','disabled');
                   $('#edit-column-select-expense', context).multiSelect('refresh');
                   if(!$('#ms-edit-column-select-expense .ms-selection', context).next().is("a")){
                       $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="deselect">Remove All</a>');
                       $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="select">Add All</a>');
                   }
                   $('#ms-edit-column-select-expense a.select', context).click(function () {
                       $('#edit-column-select-expense', context).multiSelect('select_all');
                   });
                   $('#ms-edit-column-select-expense a.deselect', context).click(function () {
                       $('#edit-column-select-expense', context).multiSelect('deselect_all');
                   });
                   $('#edit-column-select-revenue option[value="Year"]',context).attr('disabled','disabled');
                   $('#edit-column-select-revenue option[value="year"]',context).attr('disabled','disabled');
                   $('#edit-column-select-revenue', context).multiSelect('refresh');
                   if(!$('#ms-edit-column-select-revenue .ms-selection', context).next().is("a")){
                       $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="deselect">Remove All</a>');
                       $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="select">Add All</a>');
                   }
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
                   if(!$('#ms-edit-column-select-expense .ms-selection', context).next().is("a")){
                       $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="deselect">Remove All</a>');
                       $('#ms-edit-column-select-expense .ms-selection', context).after('<a class="select">Add All</a>');
                   }
                   $('#ms-edit-column-select-expense a.select', context).click(function () {
                       $('#edit-column-select-expense', context).multiSelect('select_all');
                   });
                   $('#ms-edit-column-select-expense a.deselect', context).click(function () {
                       $('#edit-column-select-expense', context).multiSelect('deselect_all');
                   });
                   $('#edit-column-select-revenue option[value="Year"]',context).attr('disabled','');
                   $('#edit-column-select-revenue option[value="year"]',context).attr('disabled','');
                   $('#edit-column-select-revenue', context).multiSelect('refresh');
                   if(!$('#ms-edit-column-select-revenue .ms-selection', context).next().is("a")){
                       $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="deselect">Remove All</a>');
                       $('#ms-edit-column-select-revenue .ms-selection', context).after('<a class="select">Add All</a>');
                   }
                   $('#ms-edit-column-select-revenue a.select', context).click(function () {
                       $('#edit-column-select-revenue', context).multiSelect('select_all');
                   });
                   $('#ms-edit-column-select-revenue a.deselect', context).click(function () {
                       $('#edit-column-select-revenue', context).multiSelect('deselect_all');
                   });
               }
            });
            fixAutoCompleteWrapping($("#dynamic-filter-data-wrapper").children());
        }
    };

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