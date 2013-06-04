(function($){
    $(document).ready(function(){
        var p = /\[(.*?)\]/;
        var year, dept, agency, exptype, expcategory;

        if($('input:radio[name=date_filter]:checked').val() == 0){
            year = ($('#edit-spending-fiscal-year').val()) ? $('#edit-spending-fiscal-year').val() : 0;
        }
		agency = ($('#edit-spending-agencies').val()) ? $('#edit-spending-agencies').val() : 0;
        dept = ($('#edit-spending-department').val()) ? ($('#edit-spending-department').val()) : 0;
        expcategory = ($('#edit-spending-expense-category').val()) ? ($('#edit-spending-expense-category').val()) : 0;
        exptype = ($('#edit-spending-expense-type').val()) ? ($('#edit-spending-expense-type').val()) : 0;

        $('#edit-spending-payee-name').autocomplete({
        	source:'/advanced-search/autocomplete/spending/payee/' + year + '/' + agency + '/' + expcategory + '/' + dept.replace(/\//g,"__") + '/' + exptype,
        	 select: function( event, ui ) {
        		 $(this).parent().next().val(ui.item.label) ;
        	 }        	 
        });
        $('#edit-spending-contract-num').autocomplete({
        	source:'/advanced-search/autocomplete/spending/contractno/'  + year + '/' + agency + '/' + expcategory + '/' + dept.replace(/\//g,"__") + '/' + exptype,
		   	 select: function( event, ui ) {
		   		 $(this).parent().next().val(ui.item.label) ;
		   	 }        	 
    	});
        $('#edit-spending-capital-project').autocomplete({
        	source:'/advanced-search/autocomplete/spending/capitalproject/'  + year + '/' + agency + '/' + expcategory + '/' + dept.replace(/\//g,"__") + '/' + exptype,
	       	 select: function( event, ui ) {
	    		 $(this).parent().next().val(ui.item.label) ;
	    	 }        	         	
        });
        $('#edit-spending-document-id').autocomplete({
        	source:'/advanced-search/autocomplete/spending/expenseid/'  + year + '/' + agency + '/' + expcategory + '/' + dept.replace(/\//g,"__") + '/' + exptype,
	       	 select: function( event, ui ) {
	    		 $(this).parent().next().val(ui.item.label) ;
	    	 }        	         	
        });

        $('#spending-advanced-search').each(function(){
            $(this).focusout(function(){
            if($('input:radio[name=date_filter]:checked').val() == 0){
                year = ($('#edit-spending-fiscal-year').val()) ? $('#edit-spending-fiscal-year').val() : 0;
            }
            agency = ($('#edit-spending-agencies').val()) ? $('#edit-spending-agencies').val() : 0;
            dept = ($('#edit-spending-department').val()) ? ($('#edit-spending-department').val()) : 0;
            expcategory = ($('#edit-spending-expense-category').val()) ? ($('#edit-spending-expense-category').val()) : 0;
            exptype = ($('#edit-spending-expense-type').val()) ? ($('#edit-spending-expense-type').val()) : 0;

            $('#edit-spending-payee-name').autocomplete({source:'/advanced-search/autocomplete/spending/payee/' + year + '/' + agency + '/' + expcategory + '/' + dept.replace(/\//g,"__") + '/' + exptype});
            $('#edit-spending-contract-num').autocomplete({source:'/advanced-search/autocomplete/spending/contractno/'  + year + '/' + agency + '/' + expcategory + '/' + dept.replace(/\//g,"__") + '/' + exptype});
            $('#edit-spending-capital-project').autocomplete({source:'/advanced-search/autocomplete/spending/capitalproject/'  + year + '/' + agency + '/' + expcategory + '/' + dept.replace(/\//g,"__") + '/' + exptype});
            $('#edit-spending-document-id').autocomplete({source:'/advanced-search/autocomplete/spending/expenseid/'  + year + '/' + agency + '/' + expcategory + '/' + dept.replace(/\//g,"__") + '/' + exptype});
            });
        });
        
        $('#edit-spending-agencies').change(function(){
            if($(this).val() == 0){
                $('#edit-spending-department').val('0');
	            $('#edit-spending-expense-category').val('0');
            	$('#edit-spending-department').attr("disabled", "disabled");
	            $('#edit-spending-expense-category').attr("disabled", "disabled");
            }
            else{
            	
            	if($('input:radio[name=date_filter]:checked').val() == 0){
                    year = ($('#edit-spending-fiscal-year').val()) ? $('#edit-spending-fiscal-year').val() : 0;
                }
                agency = ($('#edit-spending-agencies').val()) ? $('#edit-spending-agencies').val() : 0;
                dept = ($('#edit-spending-department').val()) ? ($('#edit-spending-department').val()) : 0;
                exptype = ($('#edit-spending-expense-type').val()) ? ($('#edit-spending-expense-type').val()) : 0;
                $.ajax({
                		url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept.replace(/\//g,"__") +'/' + exptype
                		,success: function(data) {
                			var html = '<option select="selected" value="0" >Select Expense Category</option>';
                            if(data[0]){
                                if(data[0]['label'] != 'No Matches Found'){
                                    for (i = 0; i < data.length; i++) {
                                        html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>'
                                    }
                                }
                            }
            		    	$('#edit-spending-expense-category').html(html);
                		  }		
                });
                $.ajax({
            		url: '/advanced-search/autocomplete/spending/department/' + year + '/' + agency + '/' + exptype
            		,success: function(data) {
            			var html = '<option select="selected" value="0" >Select Department</option>';
                        if(data[0]){
                            if(data[0]['label'] != 'No Matches Found'){
                                for (i = 0; i < data.length; i++) {
                                    html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>'
                                }
                            }
                        }
        		    	$('#edit-spending-department').html(html);
            		  }
                });                
	            $('#edit-spending-department').removeAttr("disabled");
	            $('#edit-spending-expense-category').removeAttr("disabled");
        	}
        });

        $('#edit-spending-department').change(function(){

            if($('input:radio[name=date_filter]:checked').val() == 0){
                year = ($('#edit-spending-fiscal-year').val()) ? $('#edit-spending-fiscal-year').val() : 0;
            }
            agency = ($('#edit-spending-agencies').val()) ? $('#edit-spending-agencies').val() : 0;
            dept = ($('#edit-spending-department').val()) ? ($('#edit-spending-department').val()) : 0;
            exptype = ($('#edit-spending-expense-type').val()) ? ($('#edit-spending-expense-type').val()) : 0;
            $.ajax({
                    url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept.replace(/\//g,"__") +'/' + exptype
                    ,success: function(data) {
                        var html = '<option select="selected" value="0" >Select Expense Category</option>';
                        if(data[0]){
                            if(data[0]['label'] != 'No Matches Found'){
                                for (i = 0; i < data.length; i++) {
                                    html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>'
                                }
                            }
                        }
                        $('#edit-spending-expense-category').html(html);
                      }
            });
        });
        
        $('#edit-spending-expense-type').change(function(){
        	if($(this).val() == 2 ){
        		$('#edit-spending-contract-num').attr("disabled", "disabled");
        		$('#edit-spending-contract-num').val("");
        		$('#edit-spending-payee-name').attr("disabled", "disabled");
        		$('#edit-spending-payee-name').val("");
        	}else if($(this).val() == 4 ){
        		$('#edit-spending-contract-num').attr("disabled", "disabled");
        		$('#edit-spending-contract-num').val("");
        	}
        	else{
        		$('#edit-spending-contract-num').removeAttr("disabled");
        		$('#edit-spending-payee-name').removeAttr("disabled");
        	}


            if($('input:radio[name=date_filter]:checked').val() == 0){
                year = ($('#edit-spending-fiscal-year').val()) ? $('#edit-spending-fiscal-year').val() : 0;
            }
            agency = ($('#edit-spending-agencies').val()) ? $('#edit-spending-agencies').val() : 0;
            dept = ($('#edit-spending-department').val()) ? ($('#edit-spending-department').val()) : 0;
            exptype = ($('#edit-spending-expense-type').val()) ? ($('#edit-spending-expense-type').val()) : 0;
            $.ajax({
                    url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept.replace(/\//g,"__") +'/' + exptype
                    ,success: function(data) {
                        var html = '<option select="selected" value="0" >Select Expense Category</option>';
                        if(data[0]){
                            if(data[0]['label'] != 'No Matches Found'){
                                for (i = 0; i < data.length; i++) {
                                    html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>'
                                }
                            }
                        }
                        $('#edit-spending-expense-category').html(html);
                      }
            });
            $.ajax({
                url: '/advanced-search/autocomplete/spending/department/' + year + '/' + agency + '/' + exptype
                ,success: function(data) {
                    var html = '<option select="selected" value="0" >Select Department</option>';
                    if(data[0]){
                        if(data[0]['label'] != 'No Matches Found'){
                            for (i = 0; i < data.length; i++) {
                                html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>'
                            }
                        }
                    }
                    $('#edit-spending-department').html(html);
                  }
            });
    	});

        $('#edit-spending-fiscal-year').change(function(){

            if($('input:radio[name=date_filter]:checked').val() == 0){
                year = ($('#edit-spending-fiscal-year').val()) ? $('#edit-spending-fiscal-year').val() : 0;
            }
            agency = ($('#edit-spending-agencies').val()) ? $('#edit-spending-agencies').val() : 0;
            dept = ($('#edit-spending-department').val()) ? ($('#edit-spending-department').val()) : 0;
            exptype = ($('#edit-spending-expense-type').val()) ? ($('#edit-spending-expense-type').val()) : 0;
            $.ajax({
                    url: '/advanced-search/autocomplete/spending/expcategory/' + year + '/' + agency + '/' + dept.replace(/\//g,"__") +'/' + exptype
                    ,success: function(data) {
                        var html = '<option select="selected" value="0" >Select Expense Category</option>';
                        if(data[0]){
                            if(data[0]['label'] != 'No Matches Found'){
                                for (i = 0; i < data.length; i++) {
                                    html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>'
                                }
                            }
                        }
                        $('#edit-spending-expense-category').html(html);
                      }
            });
            $.ajax({
                url: '/advanced-search/autocomplete/spending/department/' + year + '/' + agency + '/' + exptype
                ,success: function(data) {
                    var html = '<option select="selected" value="0" >Select Department</option>';
                    if(data[0]){
                        if(data[0]['label'] != 'No Matches Found'){
                            for (i = 0; i < data.length; i++) {
                                html=html + '<option value="' + data[i] + ' ">' + data[i]  + '</option>'
                            }
                        }
                    }
                    $('#edit-spending-department').html(html);
                  }
            });
    	});
        
        $('#edit-spending-clear').click(function(){
        	$('#edit-spending-expense-category').attr("disabled", "disabled");
        	$('#edit-spending-department').attr("disabled", "disabled");
      	
    	});
        if ($('input:radio[name=date_filter]:checked').val() == 0){
            $("#edit-spending-issue-date-from-datepicker-popup-0").attr('disabled','disabled');
            $("#edit-spending-issue-date-to-datepicker-popup-0").attr('disabled','disabled');
        }
        $('input:radio[name=date_filter]').click(function() {
            var value = $(this).val();
            if (value == 0){
                $("#edit-spending-fiscal-year").attr('disabled','');
                $("#edit-spending-issue-date-from-datepicker-popup-0").attr('disabled','disabled');
                $("#edit-spending-issue-date-to-datepicker-popup-0").attr('disabled','disabled');
            } else if (value == 1) {
                $("#edit-spending-fiscal-year").attr('disabled','disabled');
                $("#edit-spending-issue-date-from-datepicker-popup-0").attr('disabled','');
                $("#edit-spending-issue-date-to-datepicker-popup-0").attr('disabled','');
            }
        });
        
        
        function emptyToZero(input) {
            var inputval, output;
            inputval = p.exec(input);
            if (inputval) {
                output = inputval[1];
            } else {
                output = 0;
            }
            return output;
        }
    })
}(jQuery));

