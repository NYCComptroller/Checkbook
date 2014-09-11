(function ($) {

//hover show/hide list for mwbe menu item
    Drupal.behaviors.agency_grading = {
            attach:function (context, settings) {
            	$(".checkbox-grading-legend .legend_entry").click(function () {                    
                    var filter = getNamedFilterCriteria("mwbe_right_filter");
                    window.location = "/mwbe_agency_grading/year/115/yeartype/B/mwbe_filter/" + filter;
                });

            }
        };
    
    
}(jQuery));    