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
    reloadSidebar(cUrl);
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