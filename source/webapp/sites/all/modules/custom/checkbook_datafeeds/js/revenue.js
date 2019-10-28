(function ($) {
    $(document).ready(function () {
        // Sets up multi-select/option transfer
        $('#edit-column-select').multiSelect();
        $('.ms-selectable .ms-list').after('<a class="select">Add All</a>');
        $('.ms-selection .ms-list').after('<a class="deselect">Remove All</a>');
        $('a.select').click(function(){
            $('#edit-column-select').multiSelect('select_all');
        });
        $('a.deselect').click(function(){
            $('#edit-column-select').multiSelect('deselect_all');
        });
        //Sets up autocompletes and autocomplete filtering functionality
        var year = ($('#edit-fiscal-year').val() === 'All Years') ? 0 : $('#edit-fiscal-year').val();
        var fundclass = emptyToZero($('#edit-fund-class').val());
        var agency = emptyToZero($('#edit-agency').val());
        var budgetyear = ($('#edit-budget-fiscal-year').val() === 'All Years') ? 0 : $('#edit-budget-fiscal-year').val();
        var revcat = emptyToZero($('#edit-revenue-category').val());
        var revclass = emptyToZero($('#edit-revenue-class').val());
        var revsrc = emptyToZero($('#edit-revenue-source').val());
        var fundingsrc = emptyToZero($('#edit-funding-class').val());
        $('#edit-revenue-class').autocomplete({source:'/autocomplete/revenue/revenueclass/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revsrc + '/' + fundingsrc});
        $('#edit-revenue-source').autocomplete({source:'/autocomplete/revenue/revenuesource/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revclass + '/' + fundingsrc});
        $('.watch:input').each(function () {
            $(this).focusin(function () {
                year = ($('#edit-fiscal-year').val() === 'All Years') ? 0 : $('#edit-fiscal-year').val();
                fundclass = emptyToZero($('#edit-fund-class').val());
                agency = emptyToZero($('#edit-agency').val());
                budgetyear = ($('#edit-budget-fiscal-year').val() === 'All Years') ? 0 : $('#edit-budget-fiscal-year').val();
                revcat = emptyToZero($('#edit-revenue-category').val());
                revclass = emptyToZero($('#edit-revenue-class').val());
                revsrc = emptyToZero($('#edit-revenue-source').val());
                fundingsrc = emptyToZero($('#edit-funding-class').val());
                $("#edit-revenue-class").autocomplete("option", "source", '/autocomplete/revenue/revenueclass/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revsrc + '/' + fundingsrc);
                $("#edit-revenue-source").autocomplete("option", "source", '/autocomplete/revenue/revenuesource/' + year + '/' + fundclass + '/' + agency + '/' + budgetyear + '/' + revcat + '/' + revclass + '/' + fundingsrc);
            });
        });
        //Function to retrieve values enclosed in brackets or return zero if none
        function emptyToZero(input) {
          const p = /\[(.*?)]$/;
          const code = p.exec(input.trim());
          if (code) {
            return code[1];
          }
          return 0;
        }
    })
}(jQuery));
