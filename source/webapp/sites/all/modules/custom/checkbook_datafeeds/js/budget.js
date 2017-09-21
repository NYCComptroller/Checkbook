(function ($) {
    $.fn.disableStuff = function(){
            if ($('#edit-agency').val() == 'Citywide (All Agencies)'){
                $('select[name="dept"]').val('');
                $('select[name="dept"]').attr('disabled','disabled');
                $('select[name="expense_category"]').val('');
                $('select[name="expense_category"]').attr('disabled','disabled');
            }
    }
    
    $.fn.reloadDepartment = function(){
       var agency = $('#edit-agency').val();
       var year = ($('#edit-fiscal-year').val() == 'All Years') ? 0 : $('#edit-fiscal-year').val();
       if(agency != 'Citywide (All Agencies)'){
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
                }
            }); 
        }else{
            $('select[name="dept"]').val('');
            $('select[name="dept"]').attr('disabled','disabled');
        }
    }
    
    $.fn.reloadExpenseCategory = function(){
       var agency = $('#edit-agency').val();
       var dept = $('#department select').val();
       var year = ($('#edit-fiscal-year').val() == 'All Years') ? 0 : $('#edit-fiscal-year').val();
       if(agency != 'Citywide (All Agencies)'){
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
                }
            }); 
        }else{
            $('select[name="expense_category"]').val('');
            $('select[name="expense_category"]').attr('disabled','disabled');
        }
    }
    
    $.fn.reloadBudgetCode = function(){
       var agency = $('#edit-agency').val();
       var dept = $('#edit-dept').val();
       var expCategory = $('#edit-expense-category').val();
       var budgetName = ($('#edit-budget-name').val() == "") ? 0 : $('#edit-budget-name').val();
       var budgetCode = ($('#edit-budget-code').val() == "") ? 0 : $('#edit-budget-code').val();
       var year = ($('#edit-fiscal-year').val() == 'All Years') ? 0 : $('#edit-fiscal-year').val();
       
       $.ajax({
            url: '/datafeeds/budget/budgetcode/' + year + '/' + agency + '/' + dept + '/' + expCategory + '/' + budgetName,
            success: function(data) {
                var html = '<option value="" >Select Budget Code</option>';
                var exists = false;
                if(data[0]){
                    for (i = 0; i < data.length; i++) {
                        if(data[i] == budgetCode)
                            exists = true;
                        html = html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                    }
                }
                $('select[name="budget_code"]').html(html);
                if(exists){
                    $('select[name="budget_code"]').val(budgetCode);
                }
                $('select[name="budget_code"]').trigger("chosen:updated");
            }
        }); 
    }
    
    $.fn.reloadBudgetName = function(){
       var agency = $('#edit-agency').val();
       var dept = $('#edit-dept').val();
       var expCategory = $('#edit-expense-category').val();
       var budgetCode = ($('#edit-budget-code').val() == "") ? 0 : $('#edit-budget-code').val();
       var budgetName = ($('#edit-budget-name').val() == "") ? 0 : $('#edit-budget-name').val();
       var year = ($('#edit-fiscal-year').val() == 'All Years') ? 0 : $('#edit-fiscal-year').val();
       $.ajax({
            url: '/datafeeds/budget/budgetname/' + year + '/' + agency + '/' + dept + '/' + expCategory + '/' + budgetCode,
            success: function(data) {
                var html = '<option value="" >Select Budget Name</option>';
                var exists = false;
                if(data[0]){
                    for (i = 0; i < data.length; i++) {
                        if(data[i] == budgetName)
                            exists = true;
                        html = html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                    }
                }
                $('select[name="budget_name"]').html(html);
                if(exists){
                    $('select[name="budget_name"]').val(budgetName);
                }
                $('select[name="budget_name"]').trigger("chosen:updated");
            }
        }); 
    }

    Drupal.behaviors.budgetDataFeeds = {
        attach:function(context,settings){
            $.fn.reloadBudgetCode();
            $.fn.reloadBudgetName();
            $('select[name="budget_code"]').chosen({
                no_results_text: "No matches found",
            });
            $('select[name="budget_name"]').chosen({
                no_results_text: "No matches found",
            });
            $('#edit-agency', context).change(function () {
                $.fn.reloadDepartment();
                $.fn.reloadExpenseCategory();
                $.fn.reloadBudgetCode();
                $.fn.reloadBudgetName();
            });
            
            $('#edit-dept', context).change(function () {
                $.fn.reloadExpenseCategory();
                $.fn.reloadBudgetCode();
                $.fn.reloadBudgetName();
            });
            
            $('#edit-expense-category', context).change(function () {
                $.fn.reloadBudgetCode();
                $.fn.reloadBudgetName();
            });
            
            $('#edit-budget-code', context).change(function () {
                $.fn.reloadBudgetName();
            });
            
            $('#edit-budget-name', context).change(function () {
                $.fn.reloadBudgetCode();
            });
            
            $('#edit-fiscal-year', context).change(function () {
                $.fn.reloadBudgetCode();
                $.fn.reloadBudgetName();
            });
            
            // Sets up multi-select/option transfer
            //Expense
            $('#edit-column-select-expense',context).multiSelect();
            $('#ms-edit-column-select-expense .ms-selectable .ms-list',context).after('<a class="select">Add All</a>');
            $('#ms-edit-column-select-expense .ms-selection .ms-list',context).after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select-expense a.select',context).click(function(){
                $('#edit-column-select-expense',context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-expense a.deselect',context).click(function(){
                $('#edit-column-select-expense',context).multiSelect('deselect_all');
            });
            
            //Sets up jQuery UI autocompletes and autocomplete filtering functionality
            $.fn.disableStuff();
            
            $(':input[name="budgettype"]',context).change(function(){
                $('#edit-column-select-expense',context).multiSelect('deselect_all');
            });
        }
    }  
}(jQuery));