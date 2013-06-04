(function ($) {
	if (typeof Drupal != "undefined") {
	 Drupal.behaviors.trendsFilterDropDown = {
	          attach:function (context, settings) {
	        	  $('select.trends').change(function() {
	        		    var from = jQuery('select#from.trends').val();
	        		    var to = jQuery('select#to.trends').val()
	        		    var columns = oTable.fnSettings().aoColumns;
	        		    for(var i=1; i<columns.length; i++) {
	        		    	if(i > from){
	        		    		oTable.fnSetColumnVis( i, false );	
	        		    	}
	        		    	else if(i < to){
	        		    		//var bVis = oTable.fnSettings().aoColumns[i].bVisible;
	        		    		oTable.fnSetColumnVis( i,false );	
	        		    	}
	        		    	else{
	        		    		oTable.fnSetColumnVis( i,true );
	        		    	}

						}

		           });

	          }
	    };
	}
	 
}(jQuery));


/**
 * Disables the values in the from dropdown which are greater than the selected value in to dropdown
 * @param filterValue
 */
function validateFromYears(filterValue, filterObject){
  var from = document.getElementById(filterObject);
    for (var i=0; i<from.length; i++){
        if(filterValue > from.options[i].text){
            from.options[i].disabled = 'disabled';
        }else{
           from.options[i].disabled = '';
        }
    }

}

/**
 * Disables the values in the to dropdown which are lesser than the selected value in from dropdown
 * @param filterValue
 */
function validateToYears(filterValue, filterObject){
  var to = document.getElementById(filterObject);
    for (var i=0; i<to.length; i++){
        if(filterValue < to.options[i].text){
            to.options[i].disabled = 'disabled';
        }else{
           to.options[i].disabled = '';
        }
    }

}

