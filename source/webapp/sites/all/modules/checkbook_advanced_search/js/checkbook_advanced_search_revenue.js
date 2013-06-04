(function($){
    $(document).ready(function(){
        var p = /\[(.*?)\]/;
        var year, fundclass, agency, budgetyear, revcat, revclass, revsrc, fundingsrc, from_advanced_search;

        //year = ($('#edit-revenue-fiscal-year').val()) ? $('#edit-revenue-fiscal-year').val() : 0;
        year = 0; //do not change, this is a needed for the new change
        fundclass = ($('#edit-revenue-fund-class').val()) ? $('#edit-revenue-fund-class').val() : 0;
        agency = ($('#edit-revenue-agencies').val()) ? $('#edit-revenue-agencies').val() : 0;
        budgetyear = ($('#edit-revenue-budget-fiscal-year').val()) ? $('#edit-revenue-budget-fiscal-year').val() : 0;
        revcat = ($('#edit-revenue-revenue-category').val()) ? $('#edit-revenue-revenue-category').val() : 0;
        revclass = $('#edit-revenue-revenue-class').val() ? $('#edit-revenue-revenue-class').val().replace('/','~') : 0;
        revsrc = $('#edit-revenue-revenue-source').val() ? $('#edit-revenue-revenue-source').val().replace('/','~') : 0;
        fundingsrc = $('#edit-revenue-funding-source').val() ? $('#edit-revenue-funding-source').val() : 0;

        $('#edit-revenue-revenue-class').autocomplete({
        	source:'/advanced-search/autocomplete/revenue/revenueclass/'+year+'/'+fundclass+'/'+agency+'/'+budgetyear+'/'+revcat+'/'+revsrc+'/'+fundingsrc,
	       	 select: function( event, ui ) {
	    		 $(this).parent().next().val(ui.item.label) ;
	    	 }        	         		
        });
        $('#edit-revenue-revenue-source').autocomplete({
        	source:'/advanced-search/autocomplete/revenue/revenuesource/'+year+'/'+fundclass+'/'+agency+'/'+budgetyear+'/'+revcat+'/'+revclass+'/'+fundingsrc,
	       	 select: function( event, ui ) {
	    		 $(this).parent().next().val(ui.item.label) ;
	    	 }        	         	
        	});
        $('#revenue-advanced-search').each(function(){
            $(this).focusout(function(){
                //year = ($('#edit-revenue-fiscal-year').val()) ? $('#edit-revenue-fiscal-year').val() : 0;
                year = 0; //do not change, this is a needed for the new change
                fundclass = ($('#edit-revenue-fund-class').val()) ? $('#edit-revenue-fund-class').val() : 0;
                agency = ($('#edit-revenue-agencies').val()) ? $('#edit-revenue-agencies').val() : 0;
                budgetyear = ($('#edit-revenue-budget-fiscal-year').val()) ? $('#edit-revenue-budget-fiscal-year').val() : 0;
                revcat = ($('#edit-revenue-revenue-category').val()) ? $('#edit-revenue-revenue-category').val() : 0;
                revclass = $('#edit-revenue-revenue-class').val() ? $('#edit-revenue-revenue-class').val().replace('/','~') : 0;
                revsrc = $('#edit-revenue-revenue-source').val() ? $('#edit-revenue-revenue-source').val().replace('/','~') : 0;
                fundingsrc = $('#edit-revenue-funding-source').val() ? $('#edit-revenue-funding-source').val() : 0;
                $('#edit-revenue-revenue-class').autocomplete({source:'/advanced-search/autocomplete/revenue/revenueclass/'+year+'/'+fundclass+'/'+agency+'/'+budgetyear+'/'+revcat+'/'+revsrc+'/'+fundingsrc});
                $('#edit-revenue-revenue-source').autocomplete({source:'/advanced-search/autocomplete/revenue/revenuesource/'+year+'/'+fundclass+'/'+agency+'/'+budgetyear+'/'+revcat+'/'+revclass+'/'+fundingsrc});
            });
        });
        function emptyToZero(input) {
            var inputval, output;
            inputval = p.exec(input);
            if (inputval){
                output = inputval[1];
            } else {
                output = 0;
            }
            return output;
        }
    })
}(jQuery));