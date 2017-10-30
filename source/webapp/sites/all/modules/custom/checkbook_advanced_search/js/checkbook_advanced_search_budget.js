(function ($) {
    $.fn.reloadDepartment = function(){
        var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
        var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
        $.ajax({
            url: '/advanced-search/autocomplete/budget/department/' + fiscal_year + '/' + agency,
            success: function(data) {
                var html = '<option select="selected" value="0" >Select Department</option>';
                if(data[0]){
                    if(data[0]['label'] !== 'No Matches Found'){
                        for (i = 0; i < data.length; i++) {
                            html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                        }
                    }
                }
                $('#edit-budget-department').html(html);
                $('#edit-budget-department').removeAttr("disabled");
            }
        }); 
    };
    
    $.fn.reloadExpenseCategory = function(){
        var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
        var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
        var dept = ($('#edit-budget-department').val()) ? ($('#edit-budget-department').val()) : 0;

        $.ajax({
            url: '/advanced-search/autocomplete/budget/expcategory/' + fiscal_year + '/' + agency + '/' + dept.replace(/\//g,"__"),
            success: function(data) {
                var html = '<option select="selected" value="0" >Select Expense Category</option>';
                if(data[0]){
                    if(data[0]['label'] !== 'No Matches Found'){
                        for (i = 0; i < data.length; i++) {
                            html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                        }
                    }
                }
                $('#edit-budget-expense-category').html(html);
                $('#edit-budget-expense-category').removeAttr("disabled");
            }
        });                    
    };
    
    $.fn.reloadBudgetCode = function(){
        var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
        var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
        var dept = ($('#edit-budget-department').val()) ? ($('#edit-budget-department').val()) : 0;
        var exp_cat = ($('#edit-budget-expense-category').val()) ? ($('#edit-budget-expense-category').val()) : 0;
        var budget_code = ($('#edit-budget-budget-code').val()) ? $('#edit-budget-budget-code').val() : 0;
        var budget_name = ($('#edit-budget-budget-name').val()) ? $('#edit-budget-budget-name').val() : 0;

        $.ajax({
            url: '/advanced-search/autocomplete/budget/budgetcode/' + fiscal_year + '/' + agency + '/' + dept.replace(/\//g,"__") + '/' + exp_cat.replace(/\//g,"__") + '/' + budget_name.replace(/\//g,"__"),
            success: function(data) {
                var html = '<option select="selected" value="0" >Select Budget Code</option>';
                if(data[0]){
                    if(data[0]['label'] !== 'No Matches Found'){
                        for (i = 0; i < data.length; i++) {
                            html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                        }
                    }
                }
                $('#edit-budget-budget-code').html(html);
                $('#edit-budget-budget-code').val(budget_code);
                $('#edit-budget-budget-code').trigger("chosen:updated");
            }
        });                    
    };
    
    $.fn.reloadBudgetName = function(){
        var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
        var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
        var dept = ($('#edit-budget-department').val()) ? ($('#edit-budget-department').val()) : 0;
        var exp_cat = ($('#edit-budget-expense-category').val()) ? ($('#edit-budget-expense-category').val()) : 0;
        var budget_code = ($('#edit-budget-budget-code').val()) ? $('#edit-budget-budget-code').val() : 0;
        var budget_name = ($('#edit-budget-budget-name').val()) ? $('#edit-budget-budget-name').val() : 0;

        $.ajax({
            url: '/advanced-search/autocomplete/budget/budgetname/' + fiscal_year + '/' + agency + '/' + dept.replace(/\//g,"__") + '/' + exp_cat.replace(/\//g,"__") + '/' + budget_code,
            success: function(data) {
                var html = '<option select="selected" value="0" >Select Budget Name</option>';
                if(data[0]){
                    if(data[0]['label'] !== 'No Matches Found'){
                        for (i = 0; i < data.length; i++) {
                            html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                        }
                    }
                }
                $('#edit-budget-budget-name').html(html);
                $('#edit-budget-budget-name').val(budget_name);
                $('#edit-budget-budget-name').trigger("chosen:updated");
            }
        });                    
    };
    
    Drupal.behaviors.budgetDataFeeds = {
        attach:function(context, settings){

            if($('#budget-advanced-search').hasClass('ui-accordion-content-active')){
                $('select[name="budget_budget_code"]', context).chosen({
                    no_results_text: "No matches found"
                });
                $('select[name="budget_budget_name"]', context).chosen({
                    no_results_text: "No matches found"
                });
                $.fn.reloadBudgetCode();
                $.fn.reloadBudgetName();
            }
            
            $('#edit-budget-agencies', context).change(function(){
                if($('#edit-budget-agencies', context).val() === 0){
                    $('#edit-budget-department', context).val('0');
                    $('#edit-budget-expense-category').val('0');
                    $('#edit-budget-department', context).attr("disabled", "disabled");
                    $('#edit-budget-expense-category', context).attr("disabled", "disabled");
                }
                else{
                    $.fn.reloadDepartment();
                    $.fn.reloadExpenseCategory();
                    $.fn.reloadBudgetCode();
                    $.fn.reloadBudgetName();
                }
            });
            
            $('#edit-budget-department', context).change(function(){
                $.fn.reloadExpenseCategory();
                $.fn.reloadBudgetCode();
                $.fn.reloadBudgetName();
            });
            
            $('#edit-budget-budget-code', context).change(function(){
                $.fn.reloadBudgetName();
            });
            
            $('#edit-budget-budget-name', context).change(function(){
                $.fn.reloadBudgetCode();
            });
            
            $('#edit-budget-fiscal-year', context).change(function(){
                $.fn.reloadBudgetCode();
                $.fn.reloadBudgetName();
            });
            
            $('#edit-budget-clear', context).click(function(){
                $('#edit-budget-expense-category', context).attr("disabled", "disabled");
                $('#edit-budget-department', context).attr("disabled", "disabled");
                $(".chosen-select").val('0');
            });
        }
    };
    
}(jQuery));