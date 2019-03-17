/**
 * Created with JetBrains PhpStorm.
 * User: jrobertson
 * Date: 2/11/13
 * Time: 12:44 PM
 * To change this template use File | Settings | File Templates.
 */

(function ($) {
if (typeof Drupal != "undefined") {
	Drupal.behaviors.narrowDownFiltersTransactions = {
	        attach:function (context, settings) {


	        	$('input.autocomplete',context).live("focus",function (e) {
		            $('input.autocomplete',context).autocomplete({
		                source: function (request,respone){
		                	
		                	var curl = prepareTableListFilterUrl();		                			    	          
		    	            var nid = $(this.element).attr("nodeid");
		    	            var filter_column = jQuery("#node-widget-" + nid).find('input.autocomplete').attr('autocomplete_param_name');
		    	            
		    	            
		    	            var request_term = $.trim(request.term);
		    	            if(request_term === ''){
		    	            	respone(["No Matches Found"]);
		    	            	return true;
		    	            }
		    	            request_term = replaceAllOccurrences('/','__',request_term);
		    	            request_term = replaceAllOccurrences('\\.','^^',request_term);
		    	            curl = '/faceted-search/ajax/autocomplete'  + curl + "/" + filter_column + "/" + encodeURIComponent(request_term) ;

		    	            var p = new RegExp('node\/[0-9]*');
		    	            curl = curl.replace(p,'node/' + nid );
		                	jQuery.ajax({
		                		dataType: "json",
		                        url: curl,
		                        success: function (data, status, xhr){	  
		                        	respone(data);
		                        }
		                    });
		                },
		                search: function( event, ui ) {},
		                focus: function (event, ui) {
		                                $(event.target).val(ui.item.label);
		                                return false;
		                        },
	
		                select: function (event, ui) {
					                	if(ui.item.value === 'No Matches Found'){
					                		$(event.target).val("");
					                		return false;
					                	}else{
					                		applyTableListFiltersAutocomplete(ui,this);
					                		return false;
					                	}
		                        }
		            }).data( "autocomplete" )._renderItem = function( ul, item ) {
	    			return $( "<li></li>" )
	    				.data( "item.autocomplete", item )
	    				.append( "<a value='" + item.value + "'>" + item.label + "</a>" )
	    				.appendTo( ul );
		            };
	        	});
	        	
	        	
	        }
	    }
}

}(jQuery));

function reloadSidebar(curl) {
    var nodes = jQuery('.grid-3 .node');
    nodes.each(function(i, v){
        var progress = jQuery(this).find('.progress');
        var container = jQuery(this).find('.filter-content');
        var id = jQuery(this).attr('id');
        var nid = id.substring(id.length,id.length-4);
        var nodeId = nid.replace('-','');


        if (progress && container){
            // URL that generated this code: -->
            // http://txt2re.com/index-javascript.php3?s=/dashboard_platform/data_tables_list/ajax_data/node/6&-61&15
            var re1='(\\/)';	// Any Single Character 1
            var re2='.*?';	// Non-greedy match on filler
            var re3='(\\d+)';	// Integer Number 1
            var p = new RegExp(re1+re2+re3,["i"]);
            //end generated code
            curl = curl.replace(p,'/faceted-search/ajax/node/'+nodeId);
            jQuery.ajax({
                url: curl,
                beforeSend: function(){
                    progress.show();
                },
                success: function (data, status, xhr){
                    container.html(data);
                },
                complete: function(){
                    progress.hide();
                }
            })
        }
    });    
}

function paginateScroll(nid, page){
    var curl = prepareTableListFilterUrl();
    // URL that generated this code: -->
    // http://txt2re.com/index-javascript.php3?s=/dashboard_platform/data_tables_list/ajax_data/node/6&-61&15
    var re1='(\\/)';	// Any Single Character 1
    var re2='.*?';	// Non-greedy match on filler
    var re3='(\\d+)';	// Integer Number 1
    var p = new RegExp(re1+re2+re3,["i"]);
    //end generated code
    curl = curl.replace(p,'/faceted-search/ajax/pagination/'+nid);
    curl = curl + '?page='+page;
    var progress = jQuery('#node-widget-'+nid+' .progress');
    var container = jQuery('#node-widget-'+nid+' .rows');
    jQuery.ajax({
        url: curl,
        beforeSend: function (){
            progress.show();
            progress.html(page + 1);
        },
        success: function (data, status, xhr){
            container.html(data);
            var d = jQuery('#node-widget-'+nid+' .options .row');
            var l = Math.round(jQuery('#node-widget-'+nid+' .options .row').length / 2);
            var i = nid+'-half';
            jQuery(d.get(l)).attr('id',i);
            jQuery('#node-widget-'+nid+' .options').mCustomScrollbar('update');
            if(page > 0){
            	jQuery('#node-widget-'+nid+' .options').mCustomScrollbar('scrollTo', '#'+i);
            }else{
            	jQuery('#node-widget-'+nid+' .options').mCustomScrollbar('scrollTo', "top");
            }
        },
        complete: function (){
            progress.html('');
            progress.hide();
        }
    });
}

function smartSearchPaginateVendor(page) {
    var curl = '/smart_search/facet/vendor/page/' + page + window.location.search;
    var progress = jQuery('.filter-content-fvendorName .progress');
    var container = jQuery('.filter-content-fvendorName .options .rows');
    jQuery.ajax({
        url:curl,
        beforeSend:function () {
            progress.show();
            progress.html(page + 1);
        },
        success:function (data, status, xhr) {
            container.html(data);
            var d = jQuery('.filter-content-fvendorName .options .row');
            var i = 'vendor-page-break';
            jQuery(d[100]).attr('id', i);
            jQuery('.filter-content-fvendorName .options').mCustomScrollbar('update');
            if (page > 0) {
                jQuery('.filter-content-fvendorName .options').mCustomScrollbar('scrollTo', '#' + i);
            } else {
                jQuery('.filter-content-fvendorName .options').mCustomScrollbar('scrollTo', "top");
            }
        },
        complete:function () {
            progress.html('');
            progress.hide();
        }
    });
}

function smartSearchPaginateExpcat(page) {
    var curl = '/smart_search/facet/expensecategory/page/' + page + window.location.search;
    var progress = jQuery('.filter-content-fexpenseCategoryName .progress');
    var container = jQuery('.filter-content-fexpenseCategoryName .rows');
    jQuery.ajax({
        url:curl,
        beforeSend:function () {
            progress.show();
            progress.html(page + 1);
        },
        success:function (data, status, xhr) {
            container.html(data);
            var d = jQuery('.filter-content-fexpenseCategoryName .options .row');
            var i = 'ec-page-break';
            jQuery(d[100]).attr('id', i);
            jQuery('.filter-content-fexpenseCategoryName .options').mCustomScrollbar('update');
            if (page > 0) {
                jQuery('.filter-content-fexpenseCategoryName .options').mCustomScrollbar('scrollTo', '#' + i);
            } else {
                jQuery('.filter-content-fexpenseCategoryName .options').mCustomScrollbar('scrollTo', "top");
            }
        },
        complete:function () {
            progress.html('');
            progress.hide();
        }
    });
}