(function ($) {
    $.fn.disableStuff = function(){
            if ($('#edit-agency').val() == 'Citywide (All Agencies)'){
                $('select[name="dept"]').val('');
                $('select[name="dept"]').attr('disabled','disabled');
                $('select[name="expense_category"]').val('');
                $('select[name="expense_category"]').attr('disabled','disabled');
            }
    }

    Drupal.behaviors.budgetDataFeeds = {
        attach:function(context,settings){
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
            //Revenue
            $('#edit-column-select-revenue',context).multiSelect();
            $('#ms-edit-column-select-revenue .ms-selectable .ms-list',context).after('<a class="select">Add All</a>');
            $('#ms-edit-column-select-revenue .ms-selection .ms-list',context).after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select-revenue a.select',context).click(function(){
                $('#edit-column-select-revenue',context).multiSelect('select_all');
            });
            $('#ms-edit-column-select-revenue a.deselect',context).click(function(){
                $('#edit-column-select-revenue',context).multiSelect('deselect_all');
            });
            //Sets up jQuery UI autocompletes and autocomplete filtering functionality
            $.fn.disableStuff();
            var year = ($('#edit-fiscal-year',context).val() == 'All Years') ? 0 : $('#edit-fiscal-year',context).val();
            var agency = emptyToZero($('#edit-agency',context).val());
            var dept = emptyToZero($('#department select',context).val());
            var expcategory = emptyToZero($('#expense-category select',context).val());
            //var dept = ($('#department select',context).attr('disabled')) ? 0 : emptyToZero($('#department select',context).val());
            //var expcategory = ($('#expense-category select',context).attr('disabled')) ? 0 : emptyToZero($('#expense-category select',context).val());
            var budgetcode = ($('#edit-budget-code',context).attr('disabled')) ? 0 : emptyToZero($('#edit-budget-code',context).val());
            $('#edit-budget-code',context).autocomplete({source:'/autocomplete/budget/budgetcode/' + agency + '/' + dept + '/' +expcategory+ '/' + budgetcode + '/' + year});
            $('#expense-category select',context).autocomplete({source:'/autocomplete/budget/expcategory/' + agency + '/' + dept + '/' + expcategory + '/' + budgetcode + '/' + year});
            $('#department select',context).autocomplete({source:'/autocomplete/budget/department/' + agency + '/' + dept + '/' + expcategory + '/' + budgetcode + '/' + year});
            $('.watch:input',context).each(function () {
                $(this,context).focusin(function () {
                    //set variables for each field's value
                    year = ($('#edit-fiscal-year',context).val() == 'All Years') ? 0 : $('#edit-fiscal-year',context).val();
                    var agency = emptyToZero($('#edit-agency',context).val());
                    var dept = emptyToZero($('#department select',context).val());
                    var expcategory = emptyToZero($('#expense-category select',context).val());                    //dept = ($('#department select',context).attr('disabled')) ? 0 : emptyToZero($('#department select',context).val());
                    //expcategory = ($('#expense-category select',context).attr('disabled')) ? 0 : emptyToZero($('#expense-category select',context).val());
                    budgetcode = ($('#edit-budget-code',context).attr('disabled')) ? 0 : emptyToZero($('#edit-budget-code',context).val());
                    $("#edit-budget-code",context).autocomplete("option", "source", '/autocomplete/budget/budgetcode/' + agency + '/' + dept + '/' + expcategory + '/' + budgetcode + '/' + year);
                    $("#expense-category select",context).autocomplete("option", "source", '/autocomplete/budget/expcategory/' + agency + '/' + dept + '/' + expcategory + '/' + budgetcode + '/' + year);
                    $('#department select',context).autocomplete("option", "source", '/autocomplete/budget/department/' + agency + '/' + dept + '/' + expcategory + '/' + budgetcode + '/' + year);
                });
            });
            $(':input[name="budgettype"]',context).change(function(){
                $('#edit-column-select-expense',context).multiSelect('deselect_all');
                $('#edit-column-select-revenue',context).multiSelect('deselect_all');
            });
        }
    }
    //Function to retrieve values enclosed in brackets or return zero if none
    function emptyToZero(input) {
        var p = /\[(.*?)\]$/;
        var inputval, output;
        inputval = p.exec(input);
        if (inputval) {
            output = inputval[1];
        } else {
            output = 0;
        }
        return output;
    }
}(jQuery));