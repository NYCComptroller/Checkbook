(function($){
    Drupal.behaviors.advanced_search_module = {
        attach:function(context,settings){
            $('#edit-payroll-clear').click(function(e){
                //$('#checkbook-advanced-search-form')[0].reset(); //this works
                clearInputFields('#payroll-advanced-search','payroll');
                $(this).blur(); /* Remove focus */
                e.preventDefault();
            });
            $('#edit-budget-clear').click(function(e){
                clearInputFields('#budget-advanced-search','budget');
                $(this).blur(); /* Remove focus */
                e.preventDefault();
            });
            $('#edit-revenue-clear').click(function(e){
                clearInputFields('#revenue-advanced-search','revenue');
                $(this).blur(); /* Remove focus */
                e.preventDefault();
            });
            $('div.contracts-submit.checkbook-oge').find('input:submit[value="Clear All"]').click(function(e){
                clearInputFieldByDataSource("#contracts-advanced-search",'contracts','checkbook_oge');
                $(this).blur(); /* Remove focus */
                e.preventDefault();
            });
            $('div.contracts-submit.checkbook').find('input:submit[value="Clear All"]').click(function(e){
                clearInputFieldByDataSource("#contracts-advanced-search",'contracts','checkbook');
                $(this).blur(); /* Remove focus */
                e.preventDefault();
            });
            $('div.spending-submit.checkbook-oge').find('input:submit[value="Clear All"]').click(function(e){
                clearInputFieldByDataSource("#spending-advanced-search",'spending','checkbook_oge');
                $(this).blur(); /* Remove focus */
                e.preventDefault();
            });
            $('div.spending-submit.checkbook').find('input:submit[value="Clear All"]').click(function(e){
                clearInputFieldByDataSource("#spending-advanced-search",'spending','checkbook');
                $(this).blur(); /* Remove focus */
                e.preventDefault();
            });
        }
    }
}(jQuery));

function clearInputFields(enclosingDiv,domain){
    jQuery(enclosingDiv).find(':input').each(function() {
        switch(this.type){
            case 'select-one':
                var defaultoption = jQuery(this).attr('default_selected_value');
                if(defaultoption == null)
                    jQuery(this).find('option:first').attr("selected", "selected");
                else
                    jQuery(this).find('option[value=' + defaultoption + ']').attr("selected", "selected");
                break;
            case 'text':
                jQuery(this).val('');
                break;
            case 'select-multiple':
            case 'password':
            case 'textarea':
                jQuery(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                switch(domain)
                {
                    case 'payroll':
                        jQuery('#edit-payroll-amount-type-0').attr('checked','checked');
                        break;
                }
                break;
        }
    })
    /* Disable the drop-downs by domain */
    switch(domain)
    {
        case 'budget':
            jQuery('#edit-budget-expense-category').attr("disabled", "disabled");
            jQuery('#edit-budget-department').attr("disabled", "disabled");
            break;
    }
}

function clearInputFieldByDataSource(enclosingDiv,domain,dataSource){
    jQuery(enclosingDiv).find(':input').each(function() {
        switch(this.type){
            case 'select-one':
                    jQuery("select#edit-checkbook-contracts-category").val("expense");
                    //jQuery('#edit-spending-fiscal-year').removeAttr("disabled");
            		var defaultoption = jQuery(this).attr('default_selected_value');
                    if(defaultoption == null)
                        jQuery(this).find('option:first').attr("selected", "selected");
                    else
                    	jQuery(this).find('option[value=' + defaultoption + ']').attr("selected", "selected");
                break;
            case 'text':
                jQuery(this).val('');
                break;
            case 'select-multiple':
            case 'password':
            case 'textarea':
                jQuery(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                switch(domain)
                {
                    case 'payroll':
                        jQuery('#edit-payroll-amount-type-0').attr('checked','checked');
                        break;
                    case 'spending':
                        jQuery(':radio[name="spending_advanced_search_domain_filter"][value="'+dataSource+'"]').click();
                        break;
                    case 'contracts':
                        jQuery(':radio[name="contracts_advanced_search_domain_filter"][value="'+dataSource+'"]').click();
                        break;
                }
                break;
        }
    })
}

//Disable Advanced Search Form Fields based on the selection criteria
function disableInputFields(){
    /****************disabling Budget fields*****************/
    if(jQuery('#edit-budget-agencies').val() == 0){
        jQuery('#edit-budget-department').attr("disabled", "disabled");
        jQuery('#edit-budget-expense-category').attr("disabled", "disabled");
    }
    if(jQuery('#edit-budget-department').val() == 0){
        jQuery('#edit-budget-expense-category').attr("disabled", "disabled");
    }
    
    /****************disabling Spending fields*****************/
    //Agency, Department and Expense Category
    var spending_data_source = jQuery('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
    if (jQuery('select[name='+spending_data_source+'_spending_agency]').val() == 0) {
        jQuery('select[name='+spending_data_source+'_spending_department]').attr("disabled", "disabled");
        jQuery('select[name='+spending_data_source+'_spending_expense_category]').attr("disabled", "disabled");
    }
    if(jQuery('select[name='+spending_data_source+'_spending_department]').val() == 0){
       jQuery('select[name='+spending_data_source+'_spending_expense_category]').attr("disabled", "disabled");
    }
    
    //Spending Category, Contract ID and Payee Name
    if (jQuery('select[name='+spending_data_source+'_spending_expense_type]').val() == 2) {
        jQuery('input:text[name='+spending_data_source+'_spending_contract_num]').attr("disabled", "disabled");
        jQuery('input:text[name='+spending_data_source+'_spending_contract_num]').val("");
        jQuery('input:text[name='+spending_data_source+'_spending_payee_name]').attr("disabled", "disabled");
        jQuery('input:text[name='+spending_data_source+'_spending_payee_name]').val("");
    }
    else if (jQuery('select[name='+spending_data_source+'_spending_expense_type]').val() == 4) {
        jQuery('input:text[name='+spending_data_source+'_spending_contract_num]').attr("disabled", "disabled");
        jQuery('input:text[name='+spending_data_source+'_spending_contract_num]').val("");
    }
    
    //Date Filter
    var value = jQuery('input:radio[name='+spending_data_source+'_spending_date_filter]:checked').val();
    if (value == 0) {
        jQuery('select[name="'+spending_data_source+'_spending_fiscal_year"]').attr('disabled', '');
        jQuery('input:text[name="'+spending_data_source+'_spending_issue_date_from[date]"]').attr('disabled', 'disabled');
        jQuery('input:text[name="'+spending_data_source+'_spending_issue_date_to[date]"]').attr('disabled', 'disabled');
    } else if (value == 1) {
        jQuery('select[name="'+spending_data_source+'_spending_fiscal_year"]').attr('disabled', 'disabled');
    }
    
    /****************disabling Contracts fields*****************/
    var contracts_data_source = jQuery('input:radio[name=contracts_advanced_search_domain_filter]:checked').val();
    
    //If the datasource is 'OGE'
    if(contracts_data_source == 'checkbook_oge'){
        jQuery('input:text[name='+contracts_data_source+'_contracts_apt_pin]').attr('disabled','disabled');
        jQuery('input:text[name="'+contracts_data_source+'_contracts_received_date_from[date]"]').attr('disabled','disabled');
        jQuery('input:text[name="'+contracts_data_source+'_contracts_received_date_to[date]"]').attr('disabled','disabled');
        jQuery('input:text[name="'+contracts_data_source+'_contracts_registration_date_from[date]"]').attr('disabled','disabled');
        jQuery('input:text[name="'+contracts_data_source+'_contracts_registration_date_to[date]"]').attr('disabled','disabled');
    }
    
    //upon 'Status' change
    var contract_status = jQuery('select[name='+contracts_data_source+'_contracts_status]').val();
    if (contract_status == 'P') {
        if(contracts_data_source == 'checkbook') {
            jQuery('input:text[name="'+contracts_data_source+'_contracts_registration_date_from[date]"]').attr('disabled','disabled');
            jQuery('input:text[name="'+contracts_data_source+'_contracts_registration_date_to[date]"]').attr('disabled','disabled');
        }
        jQuery('select[name="'+contracts_data_source+'_contracts_year"]').attr("disabled", "disabled");
    } else {
        jQuery('input:text[name="'+contracts_data_source+'_contracts_received_date_from[date]"]').attr('disabled','disabled');
        jQuery('input:text[name="'+contracts_data_source+'_contracts_received_date_to[date]"]').attr('disabled','disabled'); 
    }
    
    //upon 'Incudes Sub Vendor' change
    var includes_sub_vendors = jQuery('select[name="'+contracts_data_source+'_contracts_includes_sub_vendors"]').val();
    if(includes_sub_vendors == 3 || includes_sub_vendors == 1) {
        jQuery('select[name="'+contracts_data_source+'_contracts_sub_vendor_status"]').attr("disabled", "disabled");
    }
    
    //upon 'Category' change
    var contract_category = jQuery('select[name='+contracts_data_source+'_contracts_category]').val();
    if (contract_status == 'P' || contract_category == 'revenue') {
        jQuery('select[name="'+contracts_data_source+'_contracts_includes_sub_vendors"]').attr("disabled", "disabled");
        jQuery('select[name="'+contracts_data_source+'_contracts_sub_vendor_status"]').attr("disabled", "disabled");
    }
}

