
/**
 * Functions to adjust url parameters
 *
 * @param {String} cUrl Current URL
 * @param {String} name Parameter name
 * @param {String} value Paramete value
 * @returns {String} Return updated url
 */
function adjustUrlParameter(cUrl, name, value) {
  var cUrlArray = cUrl.split('/');
  var nameIndex = jQuery.inArray(name, cUrlArray);
  value = replaceAllOccurrences("/", "__", value);
  value = replaceAllOccurrences("%2F", encodeURIComponent("__"), value);
  value = replaceAllOccurrences(":", "@Q", value);
  value = replaceAllOccurrences("%3A", encodeURIComponent("@Q"), value);

  if (nameIndex == -1) {//add
    if (value != null && value.length > 0) {
      cUrlArray.splice((cUrlArray.length + 1), 2, name, value);
    }
  } else if (value != null && value.length > 0) {//update
    cUrlArray[(nameIndex + 1)] = value;
  } else if (value == null || value.length == 0) {//remove
    cUrlArray.splice(nameIndex, 1);//name
    cUrlArray.splice(nameIndex, 1);//value
  }
  var newUrl = cUrlArray.join('/');
  return newUrl;
}

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
  if (!eval(oFilterIds)) {
    return filterId;
  }

  // Get facet parameter value from URL.
  if (facetId) {
    var filterUrlParam = facetId.getAttribute('title');
    var url = oTable.fnSettings().sAjaxSource;
    var urlParts = url.split('/');
    var index = urlParts.indexOf(filterUrlParam);
    if (index >= 0 && index < urlParts.length - 1) {
      filterUrlValue = urlParts[index + 1];
    }
    var filterUrlValues = filterUrlValue.split('~');
  }

  // Get the new facet selected values.
  for (var i = 0; i < oFilterIds.length; i++) {
    if (oFilterIds[i].checked && filterUrlValues.indexOf(oFilterIds[i].value) == -1) {
      if (filterId.length > 0) {
        filterId = filterId  + '~' + oFilterIds[i].value;
      } else {
        filterId = oFilterIds[i].value;
      }
    }
  }

  // Append the selected filter values to URL value.
  if (filterUrlValue != '') {
    if (filterId != '') {
      filterId = filterUrlValue +'~'+ filterId;
    } else {
      return filterUrlValue;
    }
  }
  return filterId;
}

function addPaddingToDataCells(table) {
  (function ($) {
    $(table).find("th").each(function (i, val) {
        if ($(this).hasClass("number")) {
          var colwidth = $(this).find("span").width();
          var maxDataWidth = 0;
          $(table).find("tr td:nth-child(" + (i + 1) + ")").each(
            function () {
              if (maxDataWidth < $(this).find("div").width()) {
                maxDataWidth = $(this).find("div").width();
              }
            }
          );
          if ((colwidth - maxDataWidth) / 2 > 1) {
            $(table).find("tr td:nth-child(" + (i + 1) + ") div").css("margin-right", Math.floor((colwidth - maxDataWidth) / 2) + "px");
          }
        }
      }
    );

    $(".DTFC_LeftHeadWrapper table").find("th").each(function (i, val) {
        if ($(this).hasClass("number")) {
          var colwidth = $(this).find("div").width();
          var maxDataWidth = 0;
          $(".DTFC_LeftBodyWrapper table").find("tr td:nth-child(" + (i + 1) + ")").each(
            function () {
              if (maxDataWidth < $(this).find("div").width()) {
                maxDataWidth = $(this).find("div").width();
              }
            }
          );
          if ((colwidth - maxDataWidth) / 2 > 1) {
            $(".DTFC_LeftBodyWrapper table").find("tr td:nth-child(" + (i + 1) + ") div").css("margin-right", Math.floor((colwidth - maxDataWidth) / 2) + "px");
          }
        }
      }
    );

    $(".dataTables_scrollHeadInner table").find("th").each(function (i, val) {
      if ($(this).hasClass("number")) {
        var colwidth = $(this).find("div").width();
        var maxDataWidth = 0;
        $(".dataTables_scrollBody table").find("tr td:nth-child(" + (i + 1) + ")").each(
          function () {
            if (maxDataWidth < $(this).find("div").width()) {
              maxDataWidth = $(this).find("div").width();
            }
          }
        );
        if ((colwidth - maxDataWidth) / 2 > 1) {
          $(".dataTables_scrollBody table").find("tr td:nth-child(" + (i + 1) + ") div").css("margin-right", Math.floor((colwidth - maxDataWidth) / 2) + "px");
        }
      }
    });
  }(jQuery));
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
    // Get Datasource value
    var url = oTable.fnSettings().sAjaxSource;
    var urlParts = url.split('/');
    var index = urlParts.indexOf('datasource');
    var UrlValue = urlParts[index + 1];

    jQuery('.dataTables_scrollHead').unstick();
    jQuery('.dataTables_scrollHead').css('overflow','hidden');

    // Scroll bar only when datasource is checkbook_nycha for payroll
      if (UrlValue != null && UrlValue == 'checkbook_nycha') {
        //NYCCHKBK-9146:Enable THEAD scroll bars for NYCHA Transactions Table
        jQuery('#table_979_wrapper .dataTables_scrollHead').css('overflow', 'auto');
        jQuery('#table_1012_wrapper .dataTables_scrollHead').css('overflow', 'auto');
        jQuery('#table_1034_wrapper .dataTables_scrollHead').css('overflow', 'auto');
        jQuery('#table_1051_wrapper .dataTables_scrollHead').css('overflow', 'auto');
        jQuery('#table_317_wrapper .dataTables_scrollHead').css('overflow', 'auto');
        jQuery('#table_330_wrapper .dataTables_scrollHead').css('overflow', 'auto');
        jQuery('#table_336_wrapper .dataTables_scrollHead').css('overflow', 'auto');
        jQuery('#table_886_wrapper .dataTables_scrollHead').css('overflow', 'auto');
      }


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
    value = replaceAllOccurrences('%3A', encodeURIComponent('@Q'), value);
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
    $(document).on("click", '.styled',
        function (event) {
            if($(this).is(':checked')){
                $(this).parent().parent().find('.results').addClass('active');
            }else{
                $(this).parent().parent().find('.results').removeClass('active');
            }
        }
     );

  $(document).on('ajaxStop', function() {
    if (typeof addExpandBottomContURL == 'function') {
      addExpandBottomContURL();
    }
  })
}(jQuery));

/** Replacing all occurrences of a pattern in a string
 * @param {String} find pattern to be replaced
 * @param {string} replace new pattern
 * @param {string} str subject
 */
function replaceAllOccurrences(find, replace, str) {
  //This function should handle null/empty strings
  if (str == null || str.length == 0)
    return str;
  else
    return str.replace(new RegExp(find, 'g'), replace);
}
