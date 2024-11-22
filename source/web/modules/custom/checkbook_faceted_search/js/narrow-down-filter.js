(function ($) {
if (typeof Drupal != "undefined") {
	Drupal.behaviors.narrowDownFiltersTransactions = {
	        attach:function (context, settings) {
	        	$('input.autocomplete',context).on("focus",function (e) {
              filterWidgetAutocomplete(context);
	        	});
	        }
	    }
}

}(jQuery));

function reloadSidebar(curl) {
  let nodes = jQuery('.grid-3 .node');
  nodes.each(function(i, v){
    let progress = jQuery(this).find('.progress');
    let container = jQuery(this).find('.filter-content');
    let id = jQuery(this).attr('id');
    let nid = id.substring(id.length,id.length-4);
    let nodeId = nid.replace('-','');

    if (progress && container){
      // URL that generated this code: -->
      // http://txt2re.com/index-javascript.php3?s=/dashboard_platform/data_tables_list/ajax_data/node/6&-61&15
      let re1='(\\/)';	// Any Single Character 1
      let re2='.*?';	// Non-greedy match on filler
      let re3='(\\d+)';	// Integer Number 1
      let p = new RegExp(re1+re2+re3,["i"]);
      //end generated code
      curl = curl.replace(p,'/faceted-search/ajax/widget/'+nodeId);

      jQuery.ajax({
        url: curl,
        beforeSend: function(){
          progress.show();
        },
        success: function (data, status, xhr){
          //since container is at .filter-content node, we cannot add data as is. Need to go down to same node in data as well.
          container.html(jQuery(data).find('.filter-content:first').html());
          // Attach js to the new markup
          jQuery(once('reload_sidebar_once',container))
            .on('click', '.filter-title', function(event) {
              if (jQuery(this).next().css('display') === 'block') {
                jQuery(this).next().css('display', 'none');
                jQuery(this).children('span').removeClass('open');
              }
              else {
                jQuery(this).next().css('display', 'block');
                jQuery(this).children('span').addClass('open');
                jQuery('div.facet-content .options', this).mCustomScrollbar("destroy");
                jQuery('div.facet-content .options', this).mCustomScrollbar({
                  horizontalScroll: false,
                  scrollButtons: {
                    enable: false
                  },
                  theme: 'dark',
                  setHeight: true
                });
              }
            })
            .on('focus', 'input.autocomplete', function (e) {
              filterWidgetAutocomplete(container);
            });
        },
        complete: function(){
          progress.hide();
        }
      })
    }
  });
}

function filterWidgetAutocomplete(container) {
  jQuery('input.autocomplete',container).autocomplete({
    source: function (request,respone){
      let curl = prepareTableListFilterUrl();
      let removeNid = new RegExp('/node\/[0-9]*');
      // Remove unwanted url parameters
      curl = curl.replace(removeNid,'');
      curl = curl.replace('dashboard_platform/data_tables_list/ajax_data/','');
      let nid = jQuery(this.element).attr("nodeid");
      let filter_column = jQuery("#node-widget-" + nid).find('input.autocomplete').attr('autocomplete_param_name');

      let request_term = jQuery.trim(request.term);
      if(request_term === ''){
        respone(["No Matches Found"]);
        return true;
      }
      request_term = replaceAllOccurrences('/','__',request_term);
      request_term = replaceAllOccurrences('\\.','^^',request_term);
      curl = '/faceted-search/ajax/autocomplete/node' + "/" + curl +"/" + filter_column + "/" + encodeURIComponent(request_term) ;

      let p = new RegExp('node\/[0-9]*');
      curl = curl.replace(p,'node/' + nid );
      //console.log("curl is " + curl);
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
      jQuery(event.target).val(ui.item.label);
      return false;
    },

    select: function (event, ui) {
      if(ui.item.value === 'No Matches Found'){
        jQuery(event.target).val("");
        return false;
      }else{
        let original_value = jQuery(event.target).val();
        jQuery('#' + event.target.id + '_orig').val(original_value);
        applyTableListFiltersAutocomplete(ui,this);
        return false;
      }
    }
  }).focusout(function(event, ui) {
    jQuery(event.target).val(jQuery('#' + event.target.id + '_orig').val());
  }).on('input',function(event, ui){
    let original_value = jQuery(event.target).val();
    jQuery('#' + event.target.id + '_orig').val(original_value);
  })._renderItem = function( ul, item ) {
    return jQuery( "<li></li>" )
      .data( "item.autocomplete", item )
      .append( "<a value='" + item.value + "'>" + item.label + "</a>" )
      .appendTo( ul );
  };
}

function paginateScroll(nid, page){
    let curl = prepareTableListFilterUrl();
    // URL that generated this code: -->
    // http://txt2re.com/index-javascript.php3?s=/dashboard_platform/data_tables_list/ajax_data/node/6&-61&15
    let re1='(\\/)';	// Any Single Character 1
    let re2='.*?';	// Non-greedy match on filler
    let re3='(\\d+)';	// Integer Number 1
    let p = new RegExp(re1+re2+re3,["i"]);
    //end generated code
    curl = curl.replace(p,'/faceted-search/ajax/pagination/'+nid);
    curl = curl + '?page='+page;
    let progress = jQuery('#node-widget-'+nid+' .progress');
    let container = jQuery('#node-widget-'+nid+' .rows');
    jQuery.ajax({
        url: curl,
        beforeSend: function (){
            progress.show();
            progress.html(page + 1);
        },
        success: function (data, status, xhr){
            container.html(data);
            let d = jQuery('#node-widget-'+nid+' .options .row');
            let l = Math.round(jQuery('#node-widget-'+nid+' .options .row').length / 2);
            let i = nid+'-half';
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
