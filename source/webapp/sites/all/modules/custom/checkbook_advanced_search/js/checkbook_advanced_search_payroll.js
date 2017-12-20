(function($){
    $(document).ready(function(){
        var employee_name, agency, pay_frequency, year;

       // employee_name = ($('#edit-payroll-employee-name')).val() ? $('#edit-payroll-employee-name').val() : 0;
         pay_frequency = ($('#edit-payroll-pay-frequency').val()) ? $('#edit-payroll-pay-frequency').val() : 0;
        agency = ($('#edit-payroll-agencies').val()) ? $('#edit-payroll-agencies').val() : 0;
        year = ($('#edit-payroll-year').val()) ? $('#edit-payroll-year').val() : 0;

        $('#edit-payroll-employee-name').autocomplete({
            source:'/advanced-search/autocomplete/payroll/employee-name/' + pay_frequency + '/' + agency + '/' + year,
            select: function( event, ui ) {
                $(this).parent().next().val(ui.item.label) ;
            }
        });
        $('#payroll-advanced-search').each(function(){
            $(this).focusout(function(){
           // employee_name = ($('#edit-payroll-employee-name')).val() ? $('#edit-payroll-employee-name').val() : 0;
                 pay_frequency = ($('#edit-payroll-pay-frequency').val()) ? $('#edit-payroll-pay-frequency').val() : 0;
                 agency = ($('#edit-payroll-agencies').val()) ? $('#edit-payroll-agencies').val() : 0;
                 year = ($('#edit-payroll-year').val()) ? $('#edit-payroll-year').val() : 0;
                $('#edit-payroll-employee-name').autocomplete({source:'/advanced-search/autocomplete/payroll/employee-name/' + pay_frequency + '/' + agency + '/' + year});
            });
        });
    })
}(jQuery));

