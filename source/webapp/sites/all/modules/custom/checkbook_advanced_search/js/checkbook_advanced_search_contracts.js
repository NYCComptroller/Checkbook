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
                'vendor_name':'input:text[name='+data_source+'_contracts_vendor_name]',
                'contract_id':'input:text[name='+data_source+'_contracts_contract_num]',
                'apt_pin':'input:text[name='+data_source+'_contracts_apt_pin]',
                'pin':'input:text[name='+data_source+'_contracts_pin]',
                'registration_date_from':'input:text[name="'+data_source+'_contracts_registration_date_from[date]"]',
                'registration_date_to':'input:text[name="'+data_source+'_contracts_registration_date_to[date]"]',
                'received_date_from':'input:text[name="'+data_source+'_contracts_received_date_from[date]"]',
                'received_date_to':'input:text[name="'+data_source+'_contracts_received_date_to[date]"]',
                'entity_contract_num':'input:text[name='+data_source+'_contracts_entity_contract_num]',
                'commodity_line':'input:text[name='+data_source+'_contracts_commodity_line]',
                'budget_name':'input:text[name='+data_source+'_contracts_budget_name]'
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

        function getAutoCompletePath(div) {
            var status = div.ele('status').val() ? div.ele('status').val() : 0;
            var category = div.ele('category').val() ? div.ele('category').val() : 0;
            var contract_type = div.ele('contract_type').val() ? div.ele('contract_type').val() : 0;
            var agency = div.ele('agency').val() ? div.ele('agency').val() : 0;
            var award_method = div.ele('award_method').val() ? div.ele('award_method').val() : 0;
            var year = div.ele('year').val() ? div.ele('year').val() : 0;
            var entity_contract_num = div.ele('entity_contract_num').val() ? div.ele('entity_contract_num').val() : 0;
            var commodity_line = div.ele('commodity_line').val() ? div.ele('commodity_line').val() : 0;
            var budget_name = div.ele('budget_name').val() ? div.ele('budget_name').val() : 0;

            var domain = 'contracts';
            var data_source = $('input:radio[name=contracts_advanced_search_domain_filter]:checked').val();
            var params = ['status=>'+status,
                'category=>'+category,
                'contract_type=>'+contract_type,
                'agency=>'+agency,
                'award_method=>'+award_method,
                'year=>'+year];
            return domain + '/' + data_source + '/' + params;
        }

        function onChangeDataSource(dataSource) {

            /* Reset all the fields for the data source */
           resetFields(div_checkbook_contracts.contents());
           resetFields(div_checkbook_contracts_oge.contents());

            /* Initialize the disabled fields */
            onStatusChange(div_checkbook_contracts);
            onStatusChange(div_checkbook_contracts_oge);

            /* Initialize view by data source */
            switch (dataSource) {
                case "checkbook_oge":
                    initializeContractsView(div_checkbook_contracts_oge);
                    div_checkbook_contracts.contents().hide();
                    div_checkbook_contracts_oge.contents().show();

                    //handle oge attributes
                    div_checkbook_contracts_oge.ele('status').find('option[value=P]').remove();
                    div_checkbook_contracts_oge.ele('category').find('option[value=revenue]').remove();
                    div_checkbook_contracts_oge.ele('apt_pin').attr('disabled','disabled');
                    div_checkbook_contracts_oge.ele('received_date_from').attr('disabled','disabled');
                    div_checkbook_contracts_oge.ele('received_date_to').attr('disabled','disabled');
                    break;

                default:
                    initializeContractsView(div_checkbook_contracts);
                    div_checkbook_contracts.contents().show();
                    div_checkbook_contracts_oge.contents().hide();
                    break;
            }
        }

        function initializeContractsView(div) {

            var path = getAutoCompletePath(div);

            div.ele('vendor_name').autocomplete({source:'/advanced-search/generic/autocomplete/pathcontracts/vendor/' + path});
            div.ele('contract_id').autocomplete({source:'/advanced-search/generic/autocomplete/contracts/contract_id/' + path});
            div.ele('apt_pin').autocomplete({source:'/advanced-search/generic/autocomplete/contracts/apt_pin/' + path});
            div.ele('pin').autocomplete({source:'/advanced-search/generic/autocomplete/contracts/pin/' + path});
            div.ele('entity_contract_num').autocomplete({source:'/advanced-search/generic/autocomplete/contracts/entity_contract_num/' + path});
            div.ele('commodity_line').autocomplete({source:'/advanced-search/generic/autocomplete/contracts/commodity_line/' + path});
            div.ele('budget_name').autocomplete({source:'/advanced-search/generic/autocomplete/contracts/budget_name/' + path});

            $('#contracts-advanced-search').each(function () {
                $(this).focusout(function () {
                    path = getAutoCompletePath(div);

                    div.ele('vendor_name').autocomplete({source:'/advanced-search/generic/autocomplete/pathcontracts/vendor/' + path});
                    div.ele('contract_id').autocomplete({source:'/advanced-search/generic/autocomplete/contracts/contract_id/' + path});
                    div.ele('apt_pin').autocomplete({source:'/advanced-search/generic/autocomplete/contracts/apt_pin/' + path});
                    div.ele('pin').autocomplete({source:'/advanced-search/generic/autocomplete/contracts/pin/' + path});
                    div.ele('entity_contract_num').autocomplete({source:'/advanced-search/generic/autocomplete/contracts/entity_contract_num/' + path});
                    div.ele('commodity_line').autocomplete({source:'/advanced-search/autocomplete/generic/contracts/commodity_line/' + path});
                    div.ele('budget_name').autocomplete({source:'/advanced-search/autocomplete/generic/contracts/budget_name/' + path});
                });
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
            onStatusChange(div_checkbook_contracts);
        });
        div_checkbook_contracts_oge.ele('status').change(function () {
            onStatusChange(div_checkbook_contracts_oge);
        });
        function onStatusChange(div) {
            var contract_status = div.ele('status').val();
            if (contract_status == 'P') {
                div.ele('registration_date_from').val('').attr("disabled", "disabled");
                div.ele('registration_date_to').val('').attr("disabled", "disabled");
                div.ele('year').attr("disabled", "disabled");
                div.ele('received_date_from').removeAttr("disabled");
                div.ele('received_date_to').removeAttr("disabled");
            } else {
                div.ele('registration_date_from').removeAttr("disabled");
                div.ele('registration_date_to').removeAttr("disabled");
                div.ele('year').removeAttr("disabled");
                div.ele('received_date_from').attr("disabled", "disabled");
                div.ele('received_date_to').attr("disabled", "disabled");
                div.ele('received_date_from').val("");
                div.ele('received_date_to').val("");

            }
        }
    })
}(jQuery));

