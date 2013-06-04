Highcharts.setOptions({
	   global: {
	      useUTC: false
	   },
	   credits: {
		   enabled: false
	   },
	   plotOptions: {
	        pie: {
	            dataLabels: {
		            enabled: false
	            }
	       }
	   },	   
	   exporting: {
	        buttons: {
	            exportButton: {
	                menuItems: [{
	                    text: 'Export to JPEG',
	                    onclick: function() {
	                        this.exportChart({
	                            width: 250,
	                            type: 'image/jpeg'
	                        });
	                    }
	                }, {
	                    text: 'Export to PDF',
	                    onclick: function() {
	                        this.exportChart({
	                        	type: 'application/pdf'
	                        }); // 800px by default
	                    }
	                },
	                null,
	                null
	                ]
	            }
	        }
	    }
	});

function abbrNum(number, decPlaces) {
    // 2 decimal places => 100, 3 => 1000, etc
    decPlaces = Math.pow(10,decPlaces);

    // Enumerate number abbreviations
    var abbrev = [ "K", "M", "B", "T" ];

    // Go through the array backwards, so we do the largest first
    for (var i=abbrev.length-1; i>=0; i--) {

        // Convert array index to "1000", "1000000", etc
        var size = Math.pow(10,(i+1)*3);

        // If the number is bigger or equal do the abbreviation
        if(size <= number) {
             // Here, we multiply by decPlaces, round, and then divide by decPlaces.
             // This gives us nice rounding to a particular decimal place.
             number = Math.round(number*decPlaces/size)/decPlaces;

             // Add the letter for the abbreviation
             number += abbrev[i];

             // We are done... stop
             break;
        }
    }

    return number;
}
