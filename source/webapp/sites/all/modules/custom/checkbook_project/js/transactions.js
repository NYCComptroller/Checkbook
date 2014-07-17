/**
 * Function to get name of filter
 *
 * @param {String} filterName
 */
function getNamedFilterCriteria(filterName){
    var filterId = '';
    var oFilterIds = document.getElementsByName(filterName);
    if(!eval(oFilterIds)){
        return filterId;
    }
    for(var i = 0; i < oFilterIds.length; i++)
    {
        if(oFilterIds[i].checked)
        {
            if(filterId.length>0){
                filterId = filterId  +'~'+ oFilterIds[i].value;
            }else{
                filterId = oFilterIds[i].value;
            }
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
            setTimeout(function(){fnCustomInitCompleteReload();}, 250);}
     });
    reloadSidebar(cUrl);
}


function fnCustomInitCompleteReload() {
    var topSpacing = 66;
    var tableOffsetTop = jQuery('.dataTables_scroll').offset().top;
    var tableHeight = jQuery('.dataTables_scroll').height();
    var docHeight = jQuery(document).height();
    var bottomSpacing = docHeight - (tableOffsetTop + tableHeight)  ;

    jQuery('.dataTables_scrollHead').sticky({ 'topSpacing': topSpacing , 'bottomSpacing': bottomSpacing, getWidthFrom:'.dataTables_scroll' });
    jQuery('.dataTables_scrollHead').sticky('update');

    if(jQuery('.DTFC_ScrollWrapper') && jQuery('.DTFC_ScrollWrapper').offset()) {
        jQuery('.DTFC_LeftHeadWrapper').sticky({ 'topSpacing': topSpacing , 'bottomSpacing': bottomSpacing, getWidthFrom:'.DTFC_LeftWrapper' });
        jQuery('.DTFC_LeftHeadWrapper').sticky('update');
    }
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