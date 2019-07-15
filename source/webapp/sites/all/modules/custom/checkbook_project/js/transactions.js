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
        var filterUrlParam = facetId.getAttribute('name');
        var url = oTable.fnSettings().sAjaxSource;
        var urlParts = url.split('/');
        var index = urlParts.indexOf(filterUrlParam);
        if (index >= 0 && index < urlParts.length - 1) {
          filterUrlValue = urlParts[index + 1];
        }
        var filterUrlValues = urldecode(filterUrlValue).split('~');
    }

    //Get the new facet selected values
    for(var i = 0; i < oFilterIds.length; i++)
    {
        var value = oFilterIds[i].value;
        value = replaceAllOccurrences('/', '__', value);
        value = replaceAllOccurrences('%2F', encodeURIComponent('__'), value);
        var multiValueExistence = false;

        if(oFilterIds[i].checked && filterUrlValues.indexOf(urldecode(value)) == -1) {
          //When a checkbox has multiple values, check the existence of them in the URL. Eg: Minority Type ID, Payroll Type
          if (urldecode(value).indexOf('~') != -1) {
            multiValueExistence = checkMultivalueExistence(filterUrlValues, urldecode(value));
          }

          if (!multiValueExistence){
            if(filterId.length>0){
              filterId = filterId + '~' + value;
            }else{
              filterId = value;
            }
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

function removeUrlParam(url, urlParam, value){
  value = replaceAllOccurrences('/', '__', value);
  value = replaceAllOccurrences('%2F', encodeURIComponent('__'), value);
  //Exception for Payroll Type Filter
  if((urlParam == 'payrolltype' || urlParam == 'fpayrolltype') && value == "2"){
    value = "2~3";
  }
  var urlParamValue = '';
  var newUrlParamValue = '';
  var urlParts = url.split('/');
  var index = urlParts.indexOf(urlParam);
  if (index >= 0 && index < urlParts.length - 1) {
    urlParamValue = urlParts[index + 1];
  }
  var filterUrlValues = urlParamValue.split('~');
  for(var i = 0; i < filterUrlValues.length; i++)
  {
    var multiValueExistence = false;
    if (urldecode(filterUrlValues[i]).indexOf('~') != -1) {
      multiValueExistence = checkMultivalueExistence(urldecode(value).split('~'), urldecode(filterUrlValues[i]));
    }
    if(!multiValueExistence) {
      if (urldecode(filterUrlValues[i]) != urldecode(value) && newUrlParamValue.length > 0) {
        newUrlParamValue = newUrlParamValue + '~' + filterUrlValues[i];
      } else if (urldecode(filterUrlValues[i]) != urldecode(value)) {
        newUrlParamValue = filterUrlValues[i];
      }
    }
  }
  var reg = new RegExp("/" +  urlParam + "\/[^\/]*");
  var matches = url.match(reg);
  if(matches != null && matches.length > 0){
    url = url.replace(reg,'');
  }
  if(newUrlParamValue.length > 0){
    url = url + '/' + urlParam + '/' +newUrlParamValue;
  }
  return url;
}

function urldecode(str) {
  if (typeof str != "string") {
    return str;
  }
  return decodeURIComponent(str.replace(/\+/g, ' ')).toLowerCase();
}

//Checks the existence of multiple values in URL
function checkMultivalueExistence(filterUrlValues, value){
  var multiValueExistence = false;
  var multiValues = value.split('~');
  var filtered = filterUrlValues.filter(
    function (e) {
      return this.indexOf(e) < 0;
    },
    multiValues
  );
  if (filtered.length >= 0 && filtered.length < filterUrlValues.length) {
    multiValueExistence = true;
  }

  return multiValueExistence;
}

/**
 * 
 * Function to apply table filters
 *
 */
function applyTableListFilters(chkd, val, urlPar){
  var checked = (chkd.length == 0)? null : chkd;
  var value = (val.length == 0)? null : val;
  var urlParam = (urlPar.length == 0)? null : urlPar;
	jQuery('input[type=checkbox]').attr("disabled", true);
    var cUrl = prepareTableListFilterUrl();
    if(checked == false && urlParam !== null && value !== null){
      cUrl = removeUrlParam(cUrl, urlParam, value);
    }
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
