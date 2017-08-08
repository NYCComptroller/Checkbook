(function ($) {
    Drupal.behaviors.payrollDataFeeds = {
        attach:function(context,settings){
            $('#edit-column-select',context).multiSelect();
            $('#ms-edit-column-select .ms-selectable',context).after('<a class="deselect">Remove All</a>');
            $('#ms-edit-column-select .ms-selectable',context).after('<a class="select">Add All</a>');
            $('#ms-edit-column-select a.select',context).click(function(){
                $('#edit-column-select',context).multiSelect('select_all');
            });
            $('#ms-edit-column-select a.deselect',context).click(function(){
                $('#edit-column-select',context).multiSelect('deselect_all');
            });
            
            //Sets up jQuery UI datepickers
            $('.datepicker').datepicker({dateFormat:"yy-mm-dd",
                                        changeMonth:true,     
                                        changeYear:true,
                                        yearRange:'-3:+3'});
                                    
            //Sets up autocompletes
            var year = $('#edit-year', context).val();
            var agency = ($('#edit-agency', context).val() == 'Citywide (All Agencies)') ? 0 : encodeURIComponent($('#edit-agency', context).val()) ;
            var payfrequency = ($('#edit-payfrequency',context).val() == 'All Pay Frequencies') ? 0 : $('#edit-payfrequency',context).val();
            $('#edit-title').autocomplete({
                source:'/autocomplete/payroll/title/'+ agency  + '/' + payfrequency + '/' + year,
                select: function( event, ui ) {
                    $(this).parent().next().val(ui.item.label) ;
                }
            });
            $('.watch:input').each(function () {
                $(this).focusin(function () {
                    year = $('#edit-year', context).val();
                    agency = ($('#edit-agency', context).val() == 'Citywide (All Agencies)') ? 0 : encodeURIComponent($('#edit-agency', context).val()) ;
                    payfrequency = ($('#edit-payfrequency',context).val() == 'All Pay Frequencies') ? 0 : $('#edit-payfrequency',context).val();
                    $("#edit-title").autocomplete("option", "source", '/autocomplete/payroll/title/'+ agency + '/' + payfrequency + '/' + year);
                });
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