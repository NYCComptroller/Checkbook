(function($){
    Drupal.behaviors.advanced_search_module = {
        attach:function(context,settings){
            $('#edit-payroll-clear').click(function(e){
                //$('#checkbook-advanced-search-form')[0].reset(); //this works
                clearInputFields('#payroll-advanced-search','payroll');
                e.preventDefault();
            });
            $('#edit-spending-clear').click(function(e){
                //$('#checkbook-advanced-search-form')[0].reset();
                clearInputFields('#spending-advanced-search','spending');
                e.preventDefault();
            });
            $('#edit-budget-clear').click(function(e){
                clearInputFields('#budget-advanced-search','budget');
                e.preventDefault();
            });
            $('#edit-revenue-clear').click(function(e){
                clearInputFields('#revenue-advanced-search','revenue');
                e.preventDefault();
            });
			$('#edit-contracts-clear').click(function(e){
                clearInputFields('#contracts-advanced-search','contracts');
                e.preventDefault();
            });
        }
    }
}(jQuery));

/* Deprecated to pass data source optionally with default */
function clearInputFields(enclosingDiv,domain){
    clearInputFieldByDataSource(enclosingDiv,domain,'checkbook');
}

function clearInputFieldByDataSource(enclosingDiv,domain,dataSource){
    jQuery(enclosingDiv).find(':input').each(function() {
        switch(this.type){
            case 'select-one':
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



