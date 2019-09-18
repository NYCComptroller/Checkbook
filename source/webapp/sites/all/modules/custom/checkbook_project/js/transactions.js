    /**
 * Function to get name of filter
 *
 * @param {String} filterName
 */
function getNamedFilterCriteria(filterName){
    var filterId = '';
    var filterUrlValue = '';

    var facetId = document.getElementById(filterName);
    var oFilterIds = document.getElementsByName(filterName);
    if(!eval(oFilterIds)){
        return filterId;
    }

    //Get facet parameter value from URL
    if(facetId) {
        var filterUrlParam = facetId.getAttribute('title');
        var url = oTable.fnSettings().sAjaxSource;
        var urlParts = url.split('/');
        var index = urlParts.indexOf(filterUrlParam);
        if (index >= 0 && index < urlParts.length - 1) {
          filterUrlValue = urlParts[index + 1];
        }
        var filterUrlValues = filterUrlValue.split('~');
    }

    //Get the new facet selected values
    for(var i = 0; i < oFilterIds.length; i++)
    {
        if(oFilterIds[i].checked && filterUrlValues.indexOf(oFilterIds[i].value) == -1)
        {
            if(filterId.length>0){
                filterId = filterId  +'~'+ oFilterIds[i].value;
            }else{
                filterId = oFilterIds[i].value;
            }
        }
    }

    //Append the selected filter values to URL value
    if(filterUrlValue != ''){
      if(filterId != ''){
        filterId = filterUrlValue +'~'+ filterId;
      }
      else{
        return filterUrlValue;
      }
    }
    return filterId;
}

/**
 *
 * Function to apply table filters
 *
 */
function applyTableListFilters(){
	jQuery('input[type=checkbox]').attr("disabled", true);
  var cUrl = prepareTableListFilterUrl();

  oTable.fnSettings().sAjaxSource = cUrl;
  oTable.fnClearTable(0);
  oTable.fnDraw();
  jQuery(document).ajaxComplete(function(event, xhr, settings) {
    var str = '/dashboard_platform/data_tables_list/ajax_data/node/';
    if (settings.url.toLowerCase().indexOf(str) >= 0){
        setTimeout(function(){fnCustomInitCompleteReload();}, 500);}
  });

  reloadSidebar(cUrl);
}

function fnCustomInitCompleteReload() {

    var topSpacing = 0;
    var tableOffsetTop = jQuery('.dataTables_scroll').offset().top;
    var tableHeight = jQuery('.dataTables_scroll').height();
    var docHeight = jQuery(document).height();
    var bottomSpacing = docHeight - (tableOffsetTop + tableHeight)  ;
    jQuery('.dataTables_scrollHead').unstick();
    jQuery('.dataTables_scrollHead').css('overflow','hidden');

    //NYCCHKBK-9146:Enable THEAD scroll bars for NYCHA Transactions Table
    jQuery('#table_979_wrapper .dataTables_scrollHead').css('overflow', 'auto');
    jQuery('#table_1012_wrapper .dataTables_scrollHead').css('overflow', 'auto');
    jQuery('#table_317_wrapper .dataTables_scrollHead').css('overflow', 'auto');

    jQuery('.dataTables_scrollHead').sticky({ 'topSpacing': topSpacing , 'bottomSpacing': bottomSpacing, getWidthFrom:'.dataTables_scroll' });
    jQuery('.dataTables_scrollHead').sticky('update');

    if(jQuery('.DTFC_ScrollWrapper') && jQuery('.DTFC_ScrollWrapper').offset()) {
    	jQuery('.DTFC_LeftHeadWrapper').unstick();
        jQuery('.DTFC_LeftHeadWrapper').sticky({ 'topSpacing': topSpacing , 'bottomSpacing': bottomSpacing, getWidthFrom:'.DTFC_LeftWrapper' });
        jQuery('.DTFC_LeftHeadWrapper').sticky('update');
    }
    jQuery('.dataTables_scrollHead').scrollLeft(0);
    jQuery('.dataTables_scrollBody').scrollLeft(0);
}

function applyTableListFiltersAutocomplete(label, field){
    var cUrl = prepareTableListFilterUrl();
    var value = replaceAllOccurrences('/', '__', label.item.value);
    value = replaceAllOccurrences('%2F', encodeURIComponent('__'), value);
    cUrl = updateURLForTableListFilters(cUrl, jQuery(field).attr("name"), value);

    oTable.fnSettings().sAjaxSource = cUrl;
    oTable.fnClearTable(0);
    oTable.fnDraw();
    reloadSidebar(cUrl);
}

function updateURLForTableListFilters(cUrl, id, value){

	//var reg = /agency\/[^\/]*/
	var reg = new RegExp( id + "\/[^\/]*" );
	var matches = cUrl.match(reg);
	if(matches != null && matches.length > 0  ){
		cUrl = cUrl.replace(reg,matches[0] + '~' + value);
	}else{
		cUrl = cUrl + '/' + id + '/'+value;
	}

	return cUrl;
}

(function ($) {
    $('.styled').live("click",
        function (event) {
            if($(this).is(':checked')){
                $(this).parent().parent().find('.results').addClass('active');
            }else{
                $(this).parent().parent().find('.results').removeClass('active');
            }
        }
     );
}(jQuery));
