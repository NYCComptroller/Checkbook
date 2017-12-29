(function ($) {
    $(document).ready(function () {
        function reloadDepartment(){
            var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
            var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
            $.ajax({
                url: '/advanced-search/autocomplete/budget/department/' + fiscal_year + '/' + agency,
                success: function(data) {
                    var html = '<option select="selected" value="0" >Select Department</option>';
                    if(data[0]){
                        if(data[0].label !== 'No Matches Found'){
                            for (i = 0; i < data.length; i++) {
                                html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                            }
                        }
                    }
                    $('#edit-budget-department').html(html);
                    $('#edit-budget-department').removeAttr("disabled");
                }
            });  
        }
        
        function reloadExpenseCategory(){
            var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
            var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
            var dept = ($('#edit-budget-department').val()) ? ($('#edit-budget-department').val()) : 0;

            $.ajax({
                url: '/advanced-search/autocomplete/budget/expcategory/' + fiscal_year + '/' + agency + '/' + dept.replace(/\//g,"__"),
                success: function(data) {
                    var html = '<option select="selected" value="0" >Select Expense Category</option>';
                    if(data[0]){
                        if(data[0].label !== 'No Matches Found'){
                            for (i = 0; i < data.length; i++) {
                                html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>';
                            }
                        }
                    }
                    $('#edit-budget-expense-category').html(html);
                    $('#edit-budget-expense-category').removeAttr("disabled");
                }
            });  
        }
        
        function reloadBudgetCode(){
            var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
            var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
            var dept = ($('#edit-budget-department').val()) ? ($('#edit-budget-department').val()) : 0;
            var exp_cat = ($('#edit-budget-expense-category').val()) ? ($('#edit-budget-expense-category').val()) : 0;
            var budget_code = ($('#edit-budget-budget-code').val()) ? $('#edit-budget-budget-code').val() : 0;
            var budget_name = ($('#edit-budget-budget-name').val()) ? $('#edit-budget-budget-name').val() : 0;

            $.ajax({
                url: '/advanced-search/autocomplete/budget/budgetcode/' + fiscal_year + '/' + agency + '/' + dept.replace(/\//g,"__") + '/' + exp_cat.replace(/\//g,"__") + '/' + budget_name.replace(/\//g,"__"),
                success: function(data) {
                    var html = '<option select="selected" value="0" title="">Select Budget Code</option>';
                    if(data[0]){
                        if(data[0].label !== 'No Matches Found'){
                            for (i = 0; i < data.length; i++) {
                                html=html + '<option title="' + data[i] + '" value="' + data[i] + ' ">' + data[i]  + '</option>';
                            }
                        }
                    }
                    $('#edit-budget-budget-code').html(html);
                    $('#edit-budget-budget-code').val(budget_code);
                    $('#edit-budget-budget-code').trigger("chosen:updated");
                    //if(budget_name !== $('#edit-budget-budget-name').val()){
                    //    reloadBudgetCode();
                    //}
                }
            });
        }
        
        function reloadBudgetName(){
            var fiscal_year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
            var agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
            var dept = ($('#edit-budget-department').val()) ? ($('#edit-budget-department').val()) : 0;
            var exp_cat = ($('#edit-budget-expense-category').val()) ? ($('#edit-budget-expense-category').val()) : 0;
            var budget_code = ($('#edit-budget-budget-code').val()) ? $('#edit-budget-budget-code').val() : 0;
            var budget_name = ($('#edit-budget-budget-name').val()) ? $('#edit-budget-budget-name').val() : 0;

            $.ajax({
                url: '/advanced-search/autocomplete/budget/budgetname/' + fiscal_year + '/' + agency + '/' + dept.replace(/\//g,"__") + '/' + exp_cat.replace(/\//g,"__") + '/' + budget_code,
                success: function(data) {
                    var html = '<option select="selected" value="0" title="">Select Budget Name</option>';
                    if(data[0]){
                        if(data[0].label !== 'No Matches Found'){
                            for (i = 0; i < data.length; i++) {
                                html=html + '<option title="'+ data[i].value +'" value="' + data[i].value + ' ">' + data[i].label  + '</option>';
                            }
                        }
                    }
                    $('#edit-budget-budget-name').html(html);
                    $('#edit-budget-budget-name').val(budget_name);
                    $('#edit-budget-budget-name').trigger("chosen:updated");
                    //if(budget_code !== $('#edit-budget-budget-code').val()){
                    //    reloadBudgetName();
                    //}
                }
            });
        }
        
        $('#edit-budget-budget-code').chosen({
            no_results_text: "No matches found"
        });
        $('#edit_budget_budget_code_chosen .chosen-search-input').attr("placeholder", "Search Budget Code");

        $('#edit-budget-budget-name').chosen({
            no_results_text: "No matches found"
        });
        $('#edit_budget_budget_name_chosen .chosen-search-input').attr("placeholder", "Search Budget Name");

        reloadBudgetCode();
        reloadBudgetName();
        
        $('#edit-budget-agencies').change(function(){
            if($('#edit-budget-agencies').val() === "0"){
                $('#edit-budget-department').val('0');
                $('#edit-budget-expense-category').val('0');
                $('#edit-budget-department').attr("disabled", "disabled");
                $('#edit-budget-expense-category').attr("disabled", "disabled");
            }
            else{
                reloadDepartment();
                reloadExpenseCategory();
            }
            reloadBudgetCode();
            reloadBudgetName();
        });
            
        $('#edit-budget-department').change(function(){
            reloadExpenseCategory();
            reloadBudgetCode();
            reloadBudgetName();
        });
        
        $('#edit-budget-expense-category').change(function(){
            reloadBudgetCode();
            reloadBudgetName();
        });

        $('#edit-budget-budget-code').change(function(){
            reloadBudgetName();
        });

        $('#edit-budget-budget-name').change(function(){
            reloadBudgetCode();
        });

        $('#edit-budget-fiscal-year').change(function(){
            reloadBudgetCode();
            reloadBudgetName();
        });

        $('#edit-budget-clear').click(function(){
            $('#edit-budget-expense-category').attr("disabled", "disabled");
            $('#edit-budget-department').attr("disabled", "disabled");
        });

    });
}(jQuery));
