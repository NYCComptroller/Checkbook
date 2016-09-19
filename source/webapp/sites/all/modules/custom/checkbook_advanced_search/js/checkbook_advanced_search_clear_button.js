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
    //disable Budget fields
    if(jQuery('#edit-budget-agencies').val() == 0){
        jQuery('#edit-budget-department').attr("disabled", "disabled");
        jQuery('#edit-budget-expense-category').attr("disabled", "disabled");
    }
    if(jQuery('#edit-budget-department').val() == 0){
        jQuery('#edit-budget-expense-category').attr("disabled", "disabled");
    }
    
    /****************disable Spending fields*****************/
    //Agency, Department and Expense Category
    var data_source = jQuery('input:radio[name=spending_advanced_search_domain_filter]:checked').val();
    if (jQuery('select[name='+data_source+'_spending_agency]').val() == 0) {
        jQuery('select[name='+data_source+'_spending_department]').attr("disabled", "disabled");
        jQuery('select[name='+data_source+'_spending_expense_category]').attr("disabled", "disabled");
    }
    if(jQuery('select[name='+data_source+'_spending_department]').val() == 0){
       jQuery('select[name='+data_source+'_spending_expense_category]').attr("disabled", "disabled");
    }
    
    //Spending Category, Contract ID and Payee Name
    if (jQuery('select[name='+data_source+'_spending_expense_type]').val() == 2) {
        jQuery('input:text[name='+data_source+'_spending_contract_num]').attr("disabled", "disabled");
        jQuery('input:text[name='+data_source+'_spending_contract_num]').val("");
        jQuery('input:text[name='+data_source+'_spending_payee_name]').attr("disabled", "disabled");
        jQuery('input:text[name='+data_source+'_spending_payee_name]').val("");
    }
    else if (jQuery('select[name='+data_source+'_spending_expense_type]').val() == 4) {
        jQuery('input:text[name='+data_source+'_spending_contract_num]').attr("disabled", "disabled");
        jQuery('input:text[name='+data_source+'_spending_contract_num]').val("");
    }
    
    //Date Filter
    var value = jQuery('input:radio[name='+data_source+'_spending_date_filter]:checked').val();
    if (value == 0) {
        jQuery('select[name="'+data_source+'_spending_fiscal_year"]').attr('disabled', '');
        jQuery('input:text[name="'+data_source+'_spending_issue_date_from[date]"]').attr('disabled', 'disabled');
        jQuery('input:text[name="'+data_source+'_spending_issue_date_to[date]"]').attr('disabled', 'disabled');
    } else if (value == 1) {
        jQuery('select[name="'+data_source+'_spending_fiscal_year"]').attr('disabled', 'disabled');
    }
    
}

