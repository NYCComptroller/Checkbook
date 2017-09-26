(function ($) {

    $.fn.reloadDepartment = function(){
       var agency = encodeURIComponent($('#edit-agency').val());
       var dept_hidden = $('input:hidden[name="dept_hidden"]').val();
       var year = ($('#edit-fiscal-year').val() === 'All Years') ? 0 : $('#edit-fiscal-year').val();
       if($('#edit-agency').val() !== 'Citywide (All Agencies)'){
            $.ajax({
                url: '/datafeeds/budget/department/' + year + '/' + agency,
                success: function(data) {
                    var html = '<option select="selected" value="" >Select Department</option>';
                    if(data[0]){
                        for (i = 0; i < data.length; i++) {
                            html = html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                        }
                    }
                    $('select[name="dept"]').removeAttr('disabled');
                    $('select[name="dept"]').html(html);
                    if(dept_hidden){
                        $('select[name="dept"]').val(dept_hidden);
                    }
                }
            }); 
        }else{
            $('select[name="dept"]').append('<option value="" selected="selected">Select Department</option>');
            $('select[name="dept"]').attr('disabled','disabled');
        }
    }
    
    $.fn.reloadExpenseCategory = function(){
       var agency = encodeURIComponent($('#edit-agency').val());
       var dept = ($('input:hidden[name="dept_hidden"]').val()) ? encodeURIComponent( $('input:hidden[name="dept_hidden"]').val()) : 0;
       var year = ($('#edit-fiscal-year').val() === 'All Years') ? 0 : $('#edit-fiscal-year').val();
       var expense_category_hidden = $('input:hidden[name="expense_category_hidden"]').val();
       
       if($('#edit-agency').val() !== 'Citywide (All Agencies)'){
            $.ajax({
                url: '/datafeeds/budget/expcat/' + year + '/' + agency + '/' + dept,
                success: function(data) {
                    var html = '<option select="selected" value="" >Select Expense Category</option>';
                    if(data[0]){
                        for (i = 0; i < data.length; i++) {
                            html = html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                        }
                    }
                    $('select[name="expense_category"]').removeAttr('disabled');
                    $('select[name="expense_category"]').html(html);
                    if(expense_category_hidden){
                        $('select[name="expense_category"]').val(expense_category_hidden);
                    }
                }
            }); 
        }else{
            $('select[name="expense_category"]').append('<option value="" selected="selected">Select Expense Category</option>');
            $('select[name="expense_category"]').attr('disabled','disabled');
        }
    }
    
    
    $.fn.reloadBudgetCode = function(){
       var agency = encodeURIComponent($('#edit-agency').val());
       var dept = ($('input:hidden[name="dept_hidden"]').val()) ? encodeURIComponent( $('input:hidden[name="dept_hidden"]').val()) : 0;
       var expCategory = ($('input:hidden[name="expense_category_hidden"]').val()) ? encodeURIComponent( $('input:hidden[name="expense_category_hidden"]').val()) : 0;
       var budgetName = ($('input:hidden[name="budget_name_hidden"]').val()) ? $('input:hidden[name="budget_name_hidden"]').val() : 0;
       budgetName =  encodeURIComponent(budgetName.toString().replace(/\//g, '__'));
       var budgetCode = $('input:hidden[name="budget_code_hidden"]').val();
       var year = ($('#edit-fiscal-year').val() === 'All Years') ? 0 : $('#edit-fiscal-year').val();
       
       $.ajax({
            url: '/datafeeds/budget/budgetcode/' + year + '/' + agency + '/' + dept + '/' + expCategory + '/' + budgetName,
            success: function(data) {
                var html = '<option value="0" >Select Budget Code</option>';
                if(data[0]){
                    for (i = 0; i < data.length; i++) {
                        html = html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                    }
                }
                $('select[name="budget_code"]').html(html);
                $('select[name="budget_code"]').val(budgetCode);
                $('select[name="budget_code"]').trigger("chosen:updated");
            }
        }); 
    }
    
    $.fn.reloadBudgetName = function(){
       var agency = encodeURIComponent($('#edit-agency').val());
       var dept = ($('input:hidden[name="dept_hidden"]').val()) ? encodeURIComponent( $('input:hidden[name="dept_hidden"]').val()) : 0;
       var expCategory = ($('input:hidden[name="expense_category_hidden"]').val()) ? encodeURIComponent( $('input:hidden[name="expense_category_hidden"]').val()) : 0;
       var budgetCode = ($('input:hidden[name="budget_code_hidden"]').val()) ? $('input:hidden[name="budget_code_hidden"]').val() : 0;
       var budgetName = ($('input:hidden[name="budget_name_hidden"]').val() === "") ? 0 : $('input:hidden[name="budget_name_hidden"]').val();
       var year = ($('#edit-fiscal-year').val() === 'All Years') ? 0 : $('#edit-fiscal-year').val();
       $.ajax({
            url: '/datafeeds/budget/budgetname/' + year + '/' + agency + '/' + dept + '/' + expCategory + '/' + budgetCode,
            success: function(data) {
                var html = '<option value="" >Select Budget Name</option>';
                if(data[0]){
                    for (i = 0; i < data.length; i++) {
                        html = html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                    }
                }
                $('select[name="budget_name"]').html(html);
                $('select[name="budget_name"]').val(budgetName);
                $('select[name="budget_name"]').trigger("chosen:updated");
            }
        }); 
    }

    Drupal.behaviors.budgetDataFeeds = {
        attach:function(context,settings){
            $('select[name="budget_code"]').chosen({
                no_results_text: "No matches found",
            });
            $('select[name="budget_name"]').chosen({
                no_results_text: "No matches found",
            });
            
            $('#edit_budget_code_chosen .chosen-search-input', context).attr("placeholder", "Search Budget Code");
            $('#edit_budget_name_chosen .chosen-search-input', context).attr("placeholder", "Search Budget Name");
            
            $.fn.reloadDepartment();
            $.fn.reloadExpenseCategory();
            $.fn.reloadBudgetCode();
            $.fn.reloadBudgetName();
            
            $('#edit-agency', context).change(function () {
                $('input:hidden[name="dept_hidden"]', context).val("");
                $('input:hidden[name="expense_category_hidden"]', context).val("");
                $('input:hidden[name="budget_code_hidden"]', context).val($('#edit-budget-code', context).val());
                $('input:hidden[name="budget_name_hidden"]', context).val($('#edit-budget-name', context).val());
                $.fn.reloadDepartment();
                $.fn.reloadExpenseCategory();
                $.fn.reloadBudgetCode();
                $.fn.reloadBudgetName();
            });
            
            $('#edit-dept', context).change(function () {
                $('input:hidden[name="dept_hidden"]', context).val($('#edit-dept', context).val());
                $('input:hidden[name="expense_category_hidden"]', context).val("");
                $('input:hidden[name="budget_code_hidden"]', context).val($('#edit-budget-code', context).val());
                $('input:hidden[name="budget_name_hidden"]', context).val($('#edit-budget-name', context).val());
                $.fn.reloadExpenseCategory();
                $.fn.reloadBudgetCode();
                $.fn.reloadBudgetName();
            });
            
            $('#edit-expense-category', context).change(function () {
                $('input:hidden[name="expense_category_hidden"]', context).val($('#edit-expense-category', context).val());
                $('input:hidden[name="budget_code_hidden"]', context).val($('#edit-budget-code', context).val());
                $('input:hidden[name="budget_name_hidden"]', context).val($('#edit-budget-name', context).val());
                $.fn.reloadBudgetCode();
                $.fn.reloadBudgetName();
            });
            
            $('#edit-budget-code', context).change(function () {
                $('input:hidden[name="budget_code_hidden"]', context).val($('#edit-budget-code', context).val());
                $('input:hidden[name="budget_name_hidden"]', context).val($('#edit-budget-name', context).val());
                $.fn.reloadBudgetName();
            });
            
            $('#edit-budget-name', context).change(function () {
                $('input:hidden[name="budget_name_hidden"]', context).val($('#edit-budget-name', context).val());
                $('input:hidden[name="budget_name_hidden"]', context).val($('#edit-budget-name', context).val());
                $.fn.reloadBudgetCode();
            });
            
            $('#edit-fiscal-year', context).change(function () {
                $('input:hidden[name="budget_code_hidden"]', context).val($('#edit-budget-code', context).val());
                $('input:hidden[name="budget_name_hidden"]', context).val($('#edit-budget-name', context).val());
                $.fn.reloadBudgetCode();
                $.fn.reloadBudgetName();
            });
            
            // Sets up multi-select/option transfer
            $('#edit-column-select-expense',context).multiSelect();
            $('#ms-edit-column-select-expense .ms-selectable .ms-list',context).after('<a class="select">Add All</a>');
            $('#ms-edit-column-select-expense .ms-selection .ms-list',context).after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select-expense a.select',context).click(function(){
                $('#edit-column-select-expense',context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-expense a.deselect',context).click(function(){
                $('#edit-column-select-expense',context).multiSelect('deselect_all');
            });

            $(':input[name="budgettype"]',context).change(function(){
                $('#edit-column-select-expense',context).multiSelect('deselect_all');
            });
        }
    }  
}(jQuery));