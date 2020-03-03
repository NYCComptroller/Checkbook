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

    Drupal.behaviors.budgetDataFeeds = {
        attach:function(context,settings){
            $.fn.reloadDepartment();
            $.fn.reloadExpenseCategory();

            $('#edit-agency', context).change(function () {
                $('input:hidden[name="dept_hidden"]', context).val("");
                $('input:hidden[name="expense_category_hidden"]', context).val("");
                $.fn.reloadDepartment();
                $.fn.reloadExpenseCategory();
            });

            $('#edit-dept', context).change(function () {
                $('input:hidden[name="dept_hidden"]', context).val($('#edit-dept', context).val());
                $('input:hidden[name="expense_category_hidden"]', context).val("");
                $.fn.reloadExpenseCategory();
            });

            //Sets up jQuery UI autocompletes and autocomplete filtering functionality
            var year = ($('#edit-fiscal-year',context).val() == 'All Years') ? 0 : $('#edit-fiscal-year',context).val();
            var agency = emptyToZero($('#edit-agency',context).val());
            var dept = ($('#edit-dept',context).val()) ? $('#edit-dept',context).val() : 0;
            var expcategory = ($('#edit-expense-category',context).val()) ? $('#edit-expense-category',context).val() : 0;
            var budgetcode = ($('#edit-budget-code',context).attr('disabled')) ? 0 : emptyToZero($('#edit-budget-code',context).val());

            $('#edit-budget-code',context).autocomplete({source:'/autocomplete/budget/budgetcode/' + agency + '/' + dept + '/' +expcategory+ '/' + budgetcode + '/' + year});
            $('.watch:input',context).each(function () {
                $(this,context).focus(function () {
                    //set variables for each field's value
                    year = ($('#edit-fiscal-year',context).val() == 'All Years') ? 0 : $('#edit-fiscal-year',context).val();
                    var agency = emptyToZero($('#edit-agency',context).val());
                    var dept = ($('#edit-dept',context).val()) ? $('#edit-dept',context).val() : 0;
                    var expcategory = ($('#edit-expense-category',context).val()) ? $('#edit-expense-category',context).val() : 0;
                    budgetcode = ($('#edit-budget-code',context).attr('disabled')) ? 0 : emptyToZero($('#edit-budget-code',context).val());
                    $('#edit-budget-code',context).autocomplete({source:'/autocomplete/budget/budgetcode/' + agency + '/' + dept + '/' +expcategory+ '/' + budgetcode + '/' + year});
                });
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

    //Function to retrieve values enclosed in brackets or return zero if none
    function emptyToZero(input) {
      const p = /\[(.*?)]$/;
      const code = p.exec(input.trim());
      if (code) {
        return code[1];
      }
      return 0;
    }
}(jQuery));
