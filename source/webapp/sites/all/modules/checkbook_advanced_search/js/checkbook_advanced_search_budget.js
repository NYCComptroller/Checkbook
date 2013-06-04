(function ($) {
    $(document).ready(function () {
        var fiscal_year, agency, dept, expcategory, budgetcode;
        

        fiscal_year = $('#edit-budget-fiscal-year').val() ? $('#edit-budget-fiscal-year').val() : 0;
        agency = $('#edit-budget-agencies').val() ? $('#edit-budget-agencies').val() : 0;
        dept = $('#edit-budget-department').val() ? $('#edit-budget-department').val() : 0;
        expcategory = $('#edit-budget-expense-category').val() ? $('#edit-budget-expense-category').val() : 0;
        budgetcode = $('#edit-budget-budget-code').val() ? $('#edit-budget-budget-code').val() : 0;

        $('#edit-budget-budget-code').autocomplete({source:'/advanced-search/autocomplete/budget/budgetcode/' + fiscal_year + '/' + agency + '/' + dept + '/' + expcategory});

        $('#budget-advanced-search').each(function () {
            $(this).focusout(function () {
                //set variables for each field's value
                fiscal_year = $('#edit-budget-fiscal-year').val() ? $('#edit-budget-fiscal-year').val() : 0;
                agency = $('#edit-budget-agencies').val() ? $('#edit-budget-agencies').val() : 0;
                dept = $('#edit-budget-department').val() ? $('#edit-budget-department').val() : 0;
                expcategory = $('#edit-budget-expense-category').val() ? $('#edit-budget-expense-category').val() : 0;
                budgetcode = $('#edit-budget-budget-code').val() ? $('#edit-budget-budget-code').val() : 0;

                $('#edit-budget-budget-code').autocomplete({source:'/advanced-search/autocomplete/budget/budgetcode/' + fiscal_year + '/' + agency + '/' + dept + '/' + expcategory});

            });
        });
        
        
        
        $('#edit-budget-agencies, #edit-budget-fiscal-year').change(function(){
            if($('#edit-budget-agencies').val() == 0){
                $('#edit-budget-department').val('0');
	            $('#edit-budget-expense-category').val('0');
            	$('#edit-budget-department').attr("disabled", "disabled");
	            $('#edit-budget-expense-category').attr("disabled", "disabled");
            }
            else{
            	
            	if($('input:radio[name=date_filter]:checked').val() == 0){
                    year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
                }
                agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
                dept = ($('#edit-budget-department').val()) ? ($('#edit-budget-department').val()) : 0;                
                $.ajax({
                		url: '/advanced-search/autocomplete/budget/expcategory/' + year + '/' + agency + '/' + dept.replace(/\//g,"__") 
                		,success: function(data) {
                			var html = '<option select="selected" value="0" >Select Expense Category</option>';
                            if(data[0]){
                                if(data[0]['label'] != 'No Matches Found'){
                                    for (i = 0; i < data.length; i++) {
                                        html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>'
                                    }
                                }
                            }
            		    	$('#edit-budget-expense-category').html(html);
                		  }		
                });
                $.ajax({
            		url: '/advanced-search/autocomplete/budget/department/' + year + '/' + agency
            		,success: function(data) {
            			var html = '<option select="selected" value="0" >Select Department</option>';
                        if(data[0]){
                            if(data[0]['label'] != 'No Matches Found'){
                                for (i = 0; i < data.length; i++) {
                                    html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>'
                                }
                            }
                        }
        		    	$('#edit-budget-department').html(html);
            		  }
                });                
	            $('#edit-budget-department').removeAttr("disabled");
	            $('#edit-budget-expense-category').removeAttr("disabled");
        	}
        });

        $('#edit-budget-department').change(function(){

            if($('input:radio[name=date_filter]:checked').val() == 0){
                year = ($('#edit-budget-fiscal-year').val()) ? $('#edit-budget-fiscal-year').val() : 0;
            }
            agency = ($('#edit-budget-agencies').val()) ? $('#edit-budget-agencies').val() : 0;
            dept = ($('#edit-budget-department').val()) ? ($('#edit-budget-department').val()) : 0;
            $.ajax({
                    url: '/advanced-search/autocomplete/budget/expcategory/' + year + '/' + agency + '/' + dept.replace(/\//g,"__")  
                    ,success: function(data) {
                        var html = '<option select="selected" value="0" >Select Expense Category</option>';
                        if(data[0]){
                            if(data[0]['label'] != 'No Matches Found'){
                                for (i = 0; i < data.length; i++) {
                                    html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>'
                                }
                            }
                        }
                        $('#edit-budget-expense-category').html(html);
                      }
            });
        });
        
        $('#edit-budget-clear').click(function(){
        	$('#edit-budget-expense-category').attr("disabled", "disabled");
        	$('#edit-budget-department').attr("disabled", "disabled");
      	
    	}); 
        
    });
}(jQuery));