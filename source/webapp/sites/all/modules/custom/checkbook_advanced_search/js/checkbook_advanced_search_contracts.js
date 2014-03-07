(function($){
    $(document).ready(function(){
        var status, category, contract_type, agency, award_method, year;

        enable_disable_fields_contract_status($('#edit-contract-status').val());
        $('#edit-contract-status').change(function(){
            enable_disable_fields_contract_status($(this).val());
        });

        status = $('#edit-contract-status').val() ? $('#edit-contract-status').val() : 0;
        category = $('#edit-contract-category').val() ? $('#edit-contract-category').val() : 0;
        contract_type = $('#edit-contract-type').val() ? $('#edit-contract-type').val() : 0;
        agency = $('#edit-contract-agency').val() ? $('#edit-contract-agency').val() : 0;
        award_method = $('#edit-contract-award-method').val() ? $('#edit-contract-award-method').val() : 0;
        year = $('#edit-contract-year').val() ? $('#edit-contract-year').val() : 0;

        $('#edit-contract-vendor-name').autocomplete({source:'/advanced-search/autocomplete/contracts/vendor-name/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});
        $('#edit-contract-contract-num').autocomplete({source:'/advanced-search/autocomplete/contracts/contract-num/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});
        $('#edit-contract-apt-pin').autocomplete({source:'/advanced-search/autocomplete/contracts/apt-pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});
        $('#edit-contract-pin').autocomplete({source:'/advanced-search/autocomplete/contracts/pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});


        $('#contracts-advanced-search').each(function(){
            $(this).focusout(function(){
                status = $('#edit-contract-status').val() ? $('#edit-contract-status').val() : 0;
                category = $('#edit-contract-category').val() ? $('#edit-contract-category').val() : 0;
                contract_type = $('#edit-contract-type').val() ? $('#edit-contract-type').val() : 0;
                agency = $('#edit-contract-agency').val() ? $('#edit-contract-agency').val() : 0;
                award_method = $('#edit-contract-award-method').val() ? $('#edit-contract-award-method').val() : 0;
                year = $('#edit-contract-year').val() ? $('#edit-contract-year').val() : 0;

                $('#edit-contract-vendor-name').autocomplete({source:'/advanced-search/autocomplete/contracts/vendor-name/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});
                $('#edit-contract-contract-num').autocomplete({source:'/advanced-search/autocomplete/contracts/contract-num/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});
                $('#edit-contract-apt-pin').autocomplete({source:'/advanced-search/autocomplete/contracts/apt-pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});
                $('#edit-contract-pin').autocomplete({source:'/advanced-search/autocomplete/contracts/pin/' + status + '/' + category + '/' + contract_type + '/' + agency + '/' + award_method + '/' + year});
            });
        });
        
      
        function enable_disable_fields_contract_status(contract_status){
            if(contract_status == 'P'){
                $('#edit-contract-registration-date-from-datepicker-popup-0').val('').attr("disabled", "disabled");
                $('#edit-contract-registration-date-to-datepicker-popup-0').val('').attr("disabled", "disabled");
                $('#edit-contract-year').attr("disabled", "disabled");
        		$('#edit-contract-received-date-from-datepicker-popup-0').removeAttr("disabled");
        		$('#edit-contract-received-date-to-datepicker-popup-0').removeAttr("disabled");                
            }else{
                $('#edit-contract-registration-date-from-datepicker-popup-0').removeAttr("disabled");
                $('#edit-contract-registration-date-to-datepicker-popup-0').removeAttr("disabled");
                $('#edit-contract-year').removeAttr("disabled");
        		$('#edit-contract-received-date-from-datepicker-popup-0').attr("disabled", "disabled");
        		$('#edit-contract-received-date-to-datepicker-popup-0').attr("disabled", "disabled");
        		$('#edit-contract-received-date-from-datepicker-popup-0').val("");
        		$('#edit-contract-received-date-to-datepicker-popup-0').val("");
                
            }
        }

        //Hide/show fields based on data source, default to checkbook data source
        $('input:radio[name=contracts_advanced_search_domain_filter][value="0"]').attr('checked',true);
        changeDataSource($('input:radio[name=contracts_advanced_search_domain_filter]:checked').val());
        $('input:radio[name=contracts_advanced_search_domain_filter]').change(function () {
            changeDataSource($('input[name=contracts_advanced_search_domain_filter]:checked').val());
        });
        function changeDataSource(dataSource) {

            switch (dataSource)
            {
                case "1":
                    $("#contracts-advanced-search").children('div.checkbook').hide();
                    $("#contracts-advanced-search").children('div.checkbook-oge').show();

                    break;

                default:
                    $("#contracts-advanced-search").children('div.checkbook').show();
                    $("#contracts-advanced-search").children('div.checkbook-oge').hide();
                    break;
            }
        }
    })
}(jQuery));

