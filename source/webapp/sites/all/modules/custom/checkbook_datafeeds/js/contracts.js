(function ($) {

    $(document).ready(function () {
        //This is to reset the radio button to citywide if the user refreshes browser
        var data_source = $('input:hidden[name="data_source"]').val();
        var agency_selected = $('#edit-agency').val();

        if (data_source == "checkbook_oge" && agency_selected == 'Citywide (All Agencies)') {
            $('input:radio[name="datafeeds-contracts-domain-filter"][value="checkbook_oge"]').removeAttr('checked').button("refresh");
            $('input:radio[name="datafeeds-contracts-domain-filter"][value="checkbook"]').attr('checked', 'checked').button("refresh");
            $('input:hidden[name="data_source"]').val("checkbook");
        }
        showHidePrimeAndSubIcon();

        //On change of "Sub Vendor Status in PIP" status
        $('#edit-sub_vendor_status_in_pip_id').change(function() {
            var sub_vendor_status = $('#edit-sub_vendor_status_in_pip_id').val();
            var includes_sub_vendors = $('#edit-contract_includes_sub_vendors_id').val();
            var valid_status = [6,1,4,3,2,5];

            if($.inArray(sub_vendor_status, valid_status)) {
                if(includes_sub_vendors == 2){
                    $('#edit-contract_includes_sub_vendors_id').html('<option value="0">Select Status</option>' +
                        '<option value="2" selected>Yes</option>');
                } else {
                    $('#edit-contract_includes_sub_vendors_id').html('<option value="0" selected>Select Status</option>' +
                        '<option value="2">Yes</option>');
                }
            }

            if(sub_vendor_status == 0) {
                if(includes_sub_vendors == 2){
                    $('#edit-contract_includes_sub_vendors_id').html('<option value="0">Select Status</option>' +
                        '<option value="2" selected>Yes</option>' +
                        '<option value="3">No</option>' +
                        '<option value="1">No Data Entered</option>' +
                        '<option value="4">Not Required</option>');
                } else {
                    $('#edit-contract_includes_sub_vendors_id').html('<option value="0" selected>Select Status</option>' +
                        '<option value="2">Yes</option>' +
                        '<option value="3">No</option>' +
                        '<option value="1">No Data Entered</option>' +
                        '<option value="4">Not Required</option>');
                }
            }
        });
    });
    
    function showHidePrimeAndSubIcon(){
        if(jQuery('input:hidden[name=data_source]').val() == 'checkbook'){
            var primeAndSubIcon = "<img class='prime-and-sub-datafeeds' src='/sites/all/themes/checkbook3/images/prime-and-sub.png' />";
            jQuery(".prime-and-sub-note-datafeeds").remove();
            jQuery("select[name=df_contract_status]").parent().find('.prime-and-sub-datafeeds').remove();
            jQuery("input:text[name=vendor]").parent().find('.prime-and-sub-datafeeds').remove();
            jQuery("select[name=mwbe_category]").parent().find('.prime-and-sub-datafeeds').remove();
            jQuery('input:text[name=currentamtfrom]').parent().parent().parent().find('.prime-and-sub-datafeeds').remove();
            jQuery("select[name=category]").parent().find('.prime-and-sub-datafeeds').remove();
            jQuery("select[name=sub_vendor_status_in_pip_id]").parent().find('.prime-and-sub-datafeeds').remove();
            jQuery("input:text[name=purpose]").parent().find('.prime-and-sub-datafeeds').remove();
            jQuery("select[name=industry]").parent().find('.prime-and-sub-datafeeds').remove();
            jQuery("select[name=year]").parent().find('.prime-and-sub-datafeeds').remove();

            if((jQuery("select[name=df_contract_status]").val() == 'active' || jQuery("select[name=df_contract_status]").val() == 'registered') 
                    && (jQuery("select[name=category]").val() == 'expense' || jQuery("select[name=category]").val() == 'all')){
                jQuery("<div class='prime-and-sub-note-datafeeds'><p>All Fields are searchable by Prime data, unless designated as Prime & Sub (<img src='/sites/all/themes/checkbook3/images/prime-and-sub.png' />).</p><br/></div>").insertAfter(jQuery("p.required-message"));
                jQuery(primeAndSubIcon).insertBefore(jQuery("select[name=df_contract_status]").parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(jQuery("input:text[name=vendor]").parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(jQuery("select[name=mwbe_category]").parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(jQuery('input:text[name=currentamtfrom]').parent().parent().parent().find('label:first'));
                jQuery(primeAndSubIcon).insertBefore(jQuery("select[name=category]").parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(jQuery("select[name=sub_vendor_status_in_pip_id]").parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(jQuery("input:text[name=purpose]").parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(jQuery("select[name=industry]").parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(jQuery("select[name=year]").parent().find('label'));
            }
        }
    }

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
        resetSelectedColumns();

        $('#edit-column-select-expense option[value="Year"]').attr('disabled','disabled');
        $('#edit-column-select-expense option[value="year"]').attr('disabled','disabled');

        $('#edit-column-select-expense').multiSelect('refresh');
        if(!$('#ms-edit-column-select-expense .ms-selection').next().is("a")){
            $('#ms-edit-column-select-expense .ms-selection').after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select-expense .ms-selection').after('<a class="select">Add All</a>');
        }
        $('#ms-edit-column-select-expense a.select').click(function () {
            $('#edit-column-select-expense').multiSelect('select_all');
        });
        $('#ms-edit-column-select-expense a.deselect').click(function () {
            $('#edit-column-select-expense').multiSelect('deselect_all');
        });
        $('#edit-column-select-revenue option[value="Year"]').attr('disabled','disabled');
        $('#edit-column-select-revenue option[value="year"]').attr('disabled','disabled');

        $('#edit-column-select-revenue').multiSelect('refresh');
        if(!$('#ms-edit-column-select-revenue .ms-selection').next().is("a")){
            $('#ms-edit-column-select-revenue .ms-selection').after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select-revenue .ms-selection').after('<a class="select">Add All</a>');
        }
        $('#ms-edit-column-select-revenue a.select').click(function () {
            $('#edit-column-select-revenue').multiSelect('select_all');
        });
        $('#ms-edit-column-select-revenue a.deselect').click(function () {
            $('#edit-column-select-revenue').multiSelect('deselect_all');
        });

        $('#edit-column-select-all option[value="Year"]').attr('disabled','disabled');
        $('#edit-column-select-all option[value="year"]').attr('disabled','disabled');

        $('#edit-column-select-expense').multiSelect('refresh');
        if(!$('#ms-edit-column-select-all .ms-selection').next().is("a")){
            $('#ms-edit-column-select-all .ms-selection').after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select-all .ms-selection').after('<a class="select">Add All</a>');
        }
        $('#ms-edit-column-select-all a.select').click(function () {
            $('#edit-column-select-all').multiSelect('select_all');
        });
        $('#ms-edit-column-select-all a.deselect').click(function () {
            $('#edit-column-select-all').multiSelect('deselect_all');
        });
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
                resetSelectedColumns();
                hideShow(csval, catval);
                showHidePrimeAndSubIcon();
            });
            $category.change(function () {
                csval = $('select[name="df_contract_status"]', context).val();
                catval = $('#edit-category', context).val();
                resetSelectedColumns();
                hideShow(csval, catval);
                showHidePrimeAndSubIcon();
            });
            function hideShow(csval, catval) {
                var $expense = $('.form-item-column-select-expense', context);
                var $revenue = $('.form-item-column-select-revenue', context);
                var $pending = $('.form-item-column-select-pending', context);
                var $all = $('.form-item-column-select-all', context);
                var $pending_all = $('.form-item-column-select-pending-all', context);
                if (csval == 'active') {
                    if (catval == 'expense') {
                        $('.form-item-column-select-expense label').html('Columns (Active Expense)<span class="form-required">*</span>');
                        $expense.show();
                        $revenue.hide();
                        $pending.hide();
                        $all.hide();
                        $pending_all.hide();
                    } else if(catval == 'revenue') {
                        $('.form-item-column-select-revenue label').html('Columns (Active Revenue)<span class="form-required">*</span>');
                        $expense.hide();
                        $revenue.show();
                        $pending.hide();
                        $all.hide();
                        $pending_all.hide();
                    } else {
                        $('.form-item-column-select-all label').html('Columns (All Active)<span class="form-required">*</span>');
                        $all.show();
                        $expense.hide();
                        $revenue.hide();
                        $pending.hide();
                        $pending_all.hide();
                    }
                } else if (csval == 'registered'){
                    if (catval == 'expense'){
                        $('.form-item-column-select-expense label').html('Columns (Registered Expense)<span class="form-required">*</span>');
                        $expense.show();
                        $revenue.hide();
                        $pending.hide();
                        $all.hide();
                        $pending_all.hide();
                    } else if (catval == 'revenue'){
                        $('.form-item-column-select-revenue label').html('Columns (Registered Revenue)<span class="form-required">*</span>');
                        $expense.hide();
                        $revenue.show();
                        $pending.hide();
                        $all.hide();
                        $pending_all.hide();
                    } else {
                        $('.form-item-column-select-all label').html('Columns (All Registered)<span class="form-required">*</span>');
                        $expense.hide();
                        $revenue.hide();
                        $pending.hide();
                        $all.show();
                        $pending_all.hide();
                    }
                } else {
                    if(catval == 'expense'){
                        $('.form-item-column-select-pending label').html('Columns (Pending Expense)<span class="form-required">*</span>');
                        $expense.hide();
                        $revenue.hide();
                        $pending.show();
                        $all.hide();
                        $pending_all.hide();
                    } else if (catval == 'revenue') {
                        $('.form-item-column-select-pending label').html('Columns (Pending Revenue)<span class="form-required">*</span>');
                        $expense.hide();
                        $revenue.hide();
                        $pending.show();
                        $all.hide();
                        $pending_all.hide();
                    } else {
                        $('.form-item-column-select-pending-all label').html('Columns (All Pending)<span class="form-required">*</span>');
                        $expense.hide();
                        $revenue.hide();
                        $pending.hide();
                        $all.hide();
                        $pending_all.show();
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
                $('#edit-column-select-all option[value="Year"]',context).attr('disabled','disabled');
                $('#edit-column-select-all option[value="year"]',context).attr('disabled','disabled');
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
            // All Pending
            $('#edit-column-select-pending-all', context).multiSelect();
            if(!$('#ms-edit-column-select-pending-all .ms-selection', context).next().is("a")){
                $('#ms-edit-column-select-pending-all .ms-selection', context).after('<a class="deselect">Remove All</a>');
                $('#ms-edit-column-select-pending-all .ms-selection', context).after('<a class="select">Add All</a>');
            }
            $('#ms-edit-column-select-pending-all a.select', context).click(function () {
                $('#edit-column-select-pending-all', context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-pending-all a.deselect', context).click(function () {
                $('#edit-column-select-pending-all', context).multiSelect('deselect_all');
            });
            // Active all (expense/revenue)
            $('#edit-column-select-all', context).multiSelect();
            if(!$('#ms-edit-column-select-all .ms-selection', context).next().is("a")){
                $('#ms-edit-column-select-all .ms-selection', context).after('<a class="deselect">Remove All</a>');
                $('#ms-edit-column-select-all .ms-selection', context).after('<a class="select">Add All</a>');
            }
            $('#ms-edit-column-select-all a.select', context).click(function () {
                $('#edit-column-select-all', context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-all a.deselect', context).click(function () {
                $('#edit-column-select-all', context).multiSelect('deselect_all');
            });

            var status = $('select[name="df_contract_status"]', context).val();
            var category = $('#edit-category', context).val();
            var contract_type = emptyToZero($('#edit-contract-type', context).val());
            var agency = emptyToZero($('#edit-agency', context).val());
            var award_method = emptyToZero($('#edit-award-method', context).val());
            var year = ($('#edit-year', context).attr('disabled')) ? 0 : $('#edit-year', context).val();
            var mwbecat = ($('#edit-mwbe-category').val()) ? $('#edit-mwbe-category').val() : 0;
            var industry = emptyToZero($('#edit-industry',context).val());
            var data_source = $('input:radio[name=datafeeds-contracts-domain-filter]:checked').val();
            var includes_sub_vendors = emptyToZero($('#edit-contract_includes_sub_vendors_id', context).val());
            var sub_vendor_status = emptyToZero($('#edit-sub_vendor_status_in_pip_id', context).val());

            $('#edit-vendor', context).autocomplete({source:'/autocomplete/contracts/vendor/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source});
            $('#edit-contractno', context).autocomplete({source:'/autocomplete/contracts/contract_number/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source});
            $('#edit-apt-pin',context).autocomplete({source:'/autocomplete/contracts/apt_pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source});
            $('#edit-pin',context).autocomplete({source:'/autocomplete/contracts/pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source});
            $('#edit-entity-contract-number',context).autocomplete({source:'/autocomplete/contracts/entitycontractnum/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source});
            $('#edit-commodity-line',context).autocomplete({source:'/autocomplete/contracts/commodityline/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source});
            $('#edit-budget-name',context).autocomplete({source:'/autocomplete/contracts/budgetname/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source});
            $('.watch:input', context).each(function () {
                $(this).focusin(function () {
                    var status = $('select[name="df_contract_status"]', context).val();
                    var category = $('#edit-category', context).val();
                    var contract_type = emptyToZero($('#edit-contract-type', context).val());
                    var agency = emptyToZero($('#edit-agency', context).val());
                    var award_method = emptyToZero($('#edit-award-method', context).val());
                    var year = ($('#edit-year', context).attr('disabled')) ? 0 : $('#edit-year', context).val();
                    var mwbecat = ($('#edit-mwbe-category').val()) ? $('#edit-mwbe-category').val() : 0;
                    mwbecat = mwbecat == null ? 0 : mwbecat;
                    var industry = emptyToZero($('#edit-industry',context).val());
                    var data_source = $('input:radio[name=datafeeds-contracts-domain-filter]:checked').val();
                    var includes_sub_vendors = emptyToZero($('#edit-contract_includes_sub_vendors_id', context).val());
                    var sub_vendor_status = emptyToZero($('#edit-sub_vendor_status_in_pip_id', context).val());

                    $('#edit-vendor', context).autocomplete('option', 'source', '/autocomplete/contracts/vendor/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source);
                    $('#edit-contractno', context).autocomplete('option', 'source', '/autocomplete/contracts/contract_number/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source);
                    $('#edit-apt-pin',context).autocomplete('option','source','/autocomplete/contracts/apt_pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source);
                    $('#edit-pin',context).autocomplete('option','source','/autocomplete/contracts/pin/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source);
                    $('#edit-entity-contract-number',context).autocomplete('option','source','/autocomplete/contracts/entitycontractnum/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source);
                    $('#edit-commodity-line',context).autocomplete('option','source','/autocomplete/contracts/commodityline/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source);
                    $('#edit-budget-name',context).autocomplete('option','source','/autocomplete/contracts/budgetname/'+ status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbecat + '/' + industry + '/' + includes_sub_vendors + '/'+ sub_vendor_status + '/' + data_source);
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
                   $('#edit-column-select-all option[value="Year"]',context).attr('disabled','disabled');
                   $('#edit-column-select-all option[value="year"]',context).attr('disabled','disabled');
                   $('#edit-column-select-all', context).multiSelect('refresh');
                   if(!$('#ms-edit-column-select-all .ms-selection', context).next().is("a")){
                       $('#ms-edit-column-select-all .ms-selection', context).after('<a class="deselect">Remove All</a>');
                       $('#ms-edit-column-select-all .ms-selection', context).after('<a class="select">Add All</a>');
                   }
                   $('#ms-edit-column-select-all a.select', context).click(function () {
                       $('#edit-column-select-all', context).multiSelect('select_all');
                   });
                   $('#ms-edit-column-select-all a.deselect', context).click(function () {
                       $('#edit-column-select-all', context).multiSelect('deselect_all');
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
                   $('#edit-column-select-all option[value="Year"]',context).attr('disabled','');
                   $('#edit-column-select-all option[value="year"]',context).attr('disabled','');
                   $('#edit-column-select-all', context).multiSelect('refresh');
                   if(!$('#ms-edit-column-select-all .ms-selection', context).next().is("a")){
                       $('#ms-edit-column-select-all .ms-selection', context).after('<a class="deselect">Remove All</a>');
                       $('#ms-edit-column-select-all .ms-selection', context).after('<a class="select">Add All</a>');
                   }
                   $('#ms-edit-column-select-all a.select', context).click(function () {
                       $('#edit-column-select-all', context).multiSelect('select_all');
                   });
                   $('#ms-edit-column-select-all a.deselect', context).click(function () {
                       $('#edit-column-select-all', context).multiSelect('deselect_all');
                   });
               }
            });
            fixAutoCompleteWrapping($("#dynamic-filter-data-wrapper").children());
        }
    };

    //Reset the selected columns
    function resetSelectedColumns() {
        $('#edit-column-select-expense').multiSelect('deselect_all');
        $('#edit-column-select-revenue').multiSelect('deselect_all');
        $('#edit-column-select-pending').multiSelect('deselect_all');
        $('#edit-column-select-pending-all').multiSelect('deselect_all');
        $('#edit-column-select-all').multiSelect('deselect_all');
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