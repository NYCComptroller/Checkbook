

(function ($) {
    $(document).ready(function () {
        
        var contracts_div = function (data_source, div_contents) {
            this.div_elements = {
                'agency':'select[name='+data_source+'_contracts_agency]',
                'status':'select[name='+data_source+'_contracts_status]',
                'category':'select[name='+data_source+'_contracts_category]',
                'contract_type':'select[name='+data_source+'_contracts_type]',
                'award_method':'select[name='+data_source+'_contracts_award_method]',
                'year':'select[name="'+data_source+'_contracts_year"]',
                'mwbe_category':'select[name='+data_source+'_contracts_mwbe_category]',
                'industry':'select[name='+data_source+'_contracts_industry]',
                'vendor_name':'input:text[name='+data_source+'_contracts_vendor_name]',
                'contract_id':'input:text[name='+data_source+'_contracts_contract_num]',
                'apt_pin':'input:text[name='+data_source+'_contracts_apt_pin]',
                'pin':'input:text[name='+data_source+'_contracts_pin]',
                'registration_date_from':'input:text[name="'+data_source+'_contracts_registration_date_from[date]"]',
                'registration_date_to':'input:text[name="'+data_source+'_contracts_registration_date_to[date]"]',
                'received_date_from':'input:text[name="'+data_source+'_contracts_received_date_from[date]"]',
                'received_date_to':'input:text[name="'+data_source+'_contracts_received_date_to[date]"]',
                'entity_contract_number':'input:text[name='+data_source+'_contracts_entity_contract_number]',
                'commodity_line':'input:text[name='+data_source+'_contracts_commodity_line]',
                'budget_name':'input:text[name='+data_source+'_contracts_budget_name]',
                'includes_sub_vendors':'select[name="'+data_source+'_contracts_includes_sub_vendors"]',
                'sub_vendor_status':'select[name="'+data_source+'_contracts_sub_vendor_status"]',
                'purpose':'input:text[name='+data_source+'_contracts_purpose]',
                'curremt_amount_from':'input:text[name="'+data_source+'_contracts_current_contract_amount_from"]',
                'curremt_amount_to':'input:text[name="'+data_source+'_contracts_current_contract_amount_to"]'
            };

            this.data_source = data_source;
            this.div_contents = div_contents;
        };
        contracts_div.prototype.contents = function () {
            return this.div_contents;
        };
        contracts_div.prototype.ele = function (element_name) {
            var selector = this.div_elements[element_name];
            return this.div_contents.find(selector);
        };

        var div_contracts_main = $("#contracts-advanced-search");
        var div_checkbook_contracts = new contracts_div('checkbook', div_contracts_main.children('div.checkbook'));
        var div_checkbook_contracts_oge = new contracts_div('checkbook_oge', div_contracts_main.children('div.checkbook-oge'));

        //On change of data source
        $('input:radio[name=contracts_advanced_search_domain_filter]').change(function () {
            onChangeDataSource($('input[name=contracts_advanced_search_domain_filter]:checked').val());
        });
        ///checkbook_advanced_search_clear_button.js sets this value by default
        $('input:radio[name=contracts_advanced_search_domain_filter]').click(function () {
            onChangeDataSource($('input[name=contracts_advanced_search_domain_filter]:checked').val());
        });
        
        showHidePrimeAndSubFields(div_checkbook_contracts);
        function showHidePrimeAndSubFields(div){
            
            //var primeAndSubIcon = "<img class='prime-and-sub' src='/sites/all/themes/checkbook3/images/prime-and-sub.png' />";
            jQuery(".prime-and-sub-note").remove();
            div.ele('status').parent().find('.prime-and-sub').remove();
            div.ele('vendor_name').parent().find('.prime-and-sub').remove();
            div.ele('mwbe_category').parent().find('.prime-and-sub').remove();
            div.ele('curremt_amount_from').parent().parent().parent().find('.prime-and-sub').remove();
            div.ele('category').parent().find('.prime-and-sub').remove();
            div.ele('sub_vendor_status').parent().find('.prime-and-sub').remove();
            div.ele('purpose').parent().find('.prime-and-sub').remove();
            div.ele('industry').parent().find('.prime-and-sub').remove();
            div.ele('year').parent().find('.prime-and-sub').remove();
            
            if((div.ele('status').val() == 'A' || div.ele('status').val() == 'R') 
                    && (div.ele('category').val() == 'expense' || div.ele('category').val() == 'all')  
                    && $('input[name=contracts_advanced_search_domain_filter]:checked').val() == 'checkbook'){
                jQuery("<div class='prime-and-sub-note'>All Fields are searchable by Prime data, unless designated as Prime & Sub (<img src='/sites/all/themes/checkbook3/images/prime-and-sub.png' />).</div>").insertBefore(jQuery("#edit-contracts-advanced-search-domain-filter"));
                jQuery(primeAndSubIcon).insertBefore(div.ele('status').parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(div.ele('vendor_name').parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(div.ele('mwbe_category').parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(div.ele('curremt_amount_from').parent().parent().parent().find('label:first'));
                jQuery(primeAndSubIcon).insertBefore(div.ele('category').parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(div.ele('sub_vendor_status').parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(div.ele('purpose').parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(div.ele('industry').parent().find('label'));
                jQuery(primeAndSubIcon).insertBefore(div.ele('year').parent().find('label'));
            }
        }
        
        function onChangeDataSource(dataSource) {

            /* Reset all the fields for the data source */
            resetFields(div_checkbook_contracts.contents());
            resetFields(div_checkbook_contracts_oge.contents());

            /* Initialize the disabled fields */
            onStatusChange(div_checkbook_contracts);
            onStatusChange(div_checkbook_contracts_oge);
            onCategoryChange(div_checkbook_contracts);

            /* Initialize view by data source */
            switch (dataSource) {
                case "checkbook_oge":
                    initializeContractsView(div_checkbook_contracts_oge);
                    div_checkbook_contracts.contents().hide();
                    div_checkbook_contracts_oge.contents().show();

                    //handle oge attributes
                    div_checkbook_contracts_oge.ele('status').find('option[value=P]').remove();
                    div_checkbook_contracts_oge.ele('category').find('option[value=revenue]').remove();
                    div_checkbook_contracts_oge.ele('category').find('option[value=all]').remove();
                    div_checkbook_contracts_oge.ele('apt_pin').attr('disabled','disabled');
                    div_checkbook_contracts_oge.ele('received_date_from').attr('disabled','disabled');
                    div_checkbook_contracts_oge.ele('received_date_to').attr('disabled','disabled');
                    div_checkbook_contracts_oge.ele('registration_date_from').attr('disabled','disabled');
                    div_checkbook_contracts_oge.ele('registration_date_to').attr('disabled','disabled');
                    break;

                default:
                    initializeContractsView(div_checkbook_contracts);
                    div_checkbook_contracts.contents().show();
                    div_checkbook_contracts_oge.contents().hide();

                    // div_checkbook_contracts.ele('includes_sub_vendors').html('<option value="0" selected>Select Status</option>' +
                    //         '<option value="2">Yes</option>' +
                    //         '<option value="3">No</option>' +
                    //         '<option value="1">No Data Entered</option>');

                    break;
            }
            showHidePrimeAndSubFields(div_checkbook_contracts);
        }

        function autoCompletes(div) {
            var status = div.ele('status').val() ? div.ele('status').val() : 0;
            var category = div.ele('category').val() ? div.ele('category').val() : 0;
            var mwbe_category = div.ele('mwbe_category').val() ? div.ele('mwbe_category').val() : 0;
            var industry = div.ele('industry').val() ? div.ele('industry').val() : 0;
            var contract_type = div.ele('contract_type').val() ? div.ele('contract_type').val() : 0;
            var agency = div.ele('agency').val() ? div.ele('agency').val() : 0;
            var award_method = div.ele('award_method').val() ? div.ele('award_method').val() : 0;
            var year = div.ele('year').val() ? div.ele('year').val() : 0;
            var includes_sub_vendors = div.ele('includes_sub_vendors').val() ? div.ele('includes_sub_vendors').val() : 0;
            var sub_vendor_status = div.ele('sub_vendor_status').val() ? div.ele('sub_vendor_status').val() : 0;
            var data_source = $('input:radio[name=contracts_advanced_search_domain_filter]:checked').val();

            div.ele('vendor_name').autocomplete({
                source:'/advanced-search/autocomplete/contracts/vendor-name/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors+ '/' + sub_vendor_status + '/' + data_source,
                select: function( event, ui ) {
                    $(this).parent().next().val(ui.item.label) ;
                }
            });
            div.ele('contract_id').autocomplete({
                source:'/advanced-search/autocomplete/contracts/contract-num/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors+ '/' + sub_vendor_status + '/' + data_source,
                select: function( event, ui ) {
                    $(this).parent().next().val(ui.item.label) ;
                }
            });
            div.ele('apt_pin').autocomplete({source:'/advanced-search/autocomplete/contracts/apt-pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors+ '/' + sub_vendor_status + '/' + data_source});
            div.ele('pin').autocomplete({source:'/advanced-search/autocomplete/contracts/pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors+ '/' + sub_vendor_status + '/' + data_source});
            div.ele('entity_contract_number').autocomplete({source:'/advanced-search/autocomplete/contracts/entity_contract_number/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors+ '/' + sub_vendor_status + '/' + data_source});
            div.ele('commodity_line').autocomplete({source:'/advanced-search/autocomplete/contracts/commodity_line/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors+ '/' + sub_vendor_status + '/' + data_source});
            div.ele('budget_name').autocomplete({source:'/advanced-search/autocomplete/contracts/budget_name/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year + '/' + mwbe_category + '/' + industry + '/' + includes_sub_vendors+ '/' + sub_vendor_status + '/' + data_source});
        }

        function initializeContractsView(div) {

            autoCompletes(div);

            $('#contracts-advanced-search').each(function () {
                $(this).focusout(function () {
                    autoCompletes(div);
                });
            });
            //prevent the auto-complete from wrapping un-necessarily
            fixAutoCompleteWrapping(div.contents());
        }

        //Prevent the auto-complete from wrapping un-necessarily
        function fixAutoCompleteWrapping(divWrapper) {
            jQuery(divWrapper.children()).find('input.ui-autocomplete-input:text').each(function () {
                $(this).data("autocomplete")._resizeMenu = function () {
                    (this.menu.element).outerWidth('100%');
                }
            });
        }

        function resetFields(divWrapper) {
            jQuery(divWrapper.children()).find(':input').each(function () {
                if (this.type == 'text') {
                    jQuery(this).val('');
                }
                if (this.type == 'select-one') {
                    jQuery(this).val('');
                }
            });
        }

        //On change of "Status"
        div_checkbook_contracts.ele('status').change(function () {
            showHidePrimeAndSubFields(div_checkbook_contracts);
            onStatusChange(div_checkbook_contracts);
        });
        div_checkbook_contracts_oge.ele('status').change(function () {
            onStatusChange(div_checkbook_contracts_oge);
        });
        function onStatusChange(div) {
            var data_source = $('input[name=contracts_advanced_search_domain_filter]:checked').val();
            var contract_status = div.ele('status').val();
            if (contract_status == 'P') {
                if(data_source == 'checkbook') {
                    div.ele('registration_date_from').val('').attr("disabled", "disabled");
                    div.ele('registration_date_to').val('').attr("disabled", "disabled");
                }
                div.ele('year').attr("disabled", "disabled");
                div.ele('received_date_from').removeAttr("disabled");
                div.ele('received_date_to').removeAttr("disabled");
            } else {
                if(data_source == 'checkbook') {
                    div.ele('registration_date_from').removeAttr("disabled");
                    div.ele('registration_date_to').removeAttr("disabled");
                }
                div.ele('year').removeAttr("disabled");
                div.ele('received_date_from').attr("disabled", "disabled");
                div.ele('received_date_to').attr("disabled", "disabled");
                div.ele('received_date_from').val("");
                div.ele('received_date_to').val("");

            }
            updateSubVendorFields(div);
        }

        //On change of "Category"
        div_checkbook_contracts.ele('category').change(function () {
            showHidePrimeAndSubFields(div_checkbook_contracts);
            onCategoryChange(div_checkbook_contracts);
        });
        function onCategoryChange(div) {
            updateSubVendorFields(div);
        }

        function updateSubVendorFields(div) {
            var contract_status = div.ele('status').val();
            var contract_category = div.ele('category').val();

            if (contract_status == 'P' || contract_category == 'revenue') {
                div.ele('includes_sub_vendors').attr("disabled", "disabled");
                //div.ele('includes_sub_vendors').val('');
                div.ele('sub_vendor_status').attr("disabled", "disabled");
                //div.ele('sub_vendor_status').val('');
            } else {
                div.ele('includes_sub_vendors').removeAttr("disabled");
                div.ele('sub_vendor_status').removeAttr("disabled");
            }
        }

        //On change of "Contract Includes Sub Vendors" status - NYCCHKBK-6187
        div_checkbook_contracts.ele('includes_sub_vendors').change(function() {
            onIncludeSubvendorChange(div_checkbook_contracts);
        });
        function onIncludeSubvendorChange(div) {
            updateSubvendorStatusField(div);
        }

        function updateSubvendorStatusField(div) {
            var includes_sub_vendors = div.ele('includes_sub_vendors').val();

            if(includes_sub_vendors == 3 || includes_sub_vendors == 1 || includes_sub_vendors == 4) {
                div.ele('sub_vendor_status').attr("disabled", "disabled");
                // div.ele('sub_vendor_status').val('');
            } else{
                div.ele('sub_vendor_status').removeAttr("disabled");
            }
        }

        //On change of "Sub Vendor Status in PIP" status -  NYCCHKBK-6187
        div_checkbook_contracts.ele('sub_vendor_status').change(function() {
            onSubvendorStatusChange(div_checkbook_contracts);
        });

        function onSubvendorStatusChange(div) {
            updateIncludeSubvendorsField(div);
        }

        function updateIncludeSubvendorsField(div) {
            var sub_vendor_status = div.ele('sub_vendor_status').val();
            var includes_sub_vendors = div.ele('includes_sub_vendors').val();
            
            if(sub_vendor_status == 6 || sub_vendor_status == 1 || sub_vendor_status == 4 || sub_vendor_status == 3 || sub_vendor_status == 2 || sub_vendor_status == 5 ) {
                if(includes_sub_vendors == 2){
                    div.ele('includes_sub_vendors').html('<option value="0">Select Status</option>' +
                        '<option value="2" selected>Yes</option>');
                } else {
                    div.ele('includes_sub_vendors').html('<option value="0" selected>Select Status</option>' +
                        '<option value="2">Yes</option>');
                }
            } else {
                if(includes_sub_vendors == 2){
                    div.ele('includes_sub_vendors').html('<option value="0">Select Status</option>' +
                        '<option value="2" selected>Yes</option>' +
                        '<option value="3">No</option>' +
                        '<option value="1">No Data Entered</option>' +
                        '<option value="4">Not Required</option>');
                } else {
                    div.ele('includes_sub_vendors').html('<option value="0" selected>Select Status</option>' +
                        '<option value="2">Yes</option>' +
                        '<option value="3">No</option>' +
                        '<option value="1">No Data Entered</option>' +
                        '<option value="4">Not Required</option>');
                }
            }
            jQuery("#edit-contracts-clear").click(function() {
                div.ele('includes_sub_vendors').html('<option value="0" selected>Select Status</option>' +
                    '<option value="2">Yes</option>' +
                    '<option value="3">No</option>' +
                    '<option value="1">No Data Entered</option>' +
                    '<option value="4">Not Required</option>');
            });
        }
    })
}(jQuery));