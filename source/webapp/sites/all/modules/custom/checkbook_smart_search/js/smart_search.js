/**
 *  Outputs the auto suggestions for the entered text in the search textbox.
 */
(function ($) {
    $(document).ready(function () {
		$( "#edit-search-box" ).autocomplete({
                        position: { my: "right top", at: "right bottom" },
                        minLength: 0,
                        source: '/smart_search/autocomplete',
                        focus: function (event, ui) {
                                $(event.target).val(ui.item.label);
                                return false;
                        },
                        select: function (event, ui) {
                                $(event.target).val(ui.item.label);
                                window.location = ui.item.url;
                                return false;
                        }
            })
		.data( "autocomplete" )._renderMenu = function( ul, items ){
			var self = this,
				currentCategory = "";
			$.each( items, function( index, item ) {

                if(item.value == 'No matches found'){
                    $( "<li class='ui-menu-item'></li>" ).data( "item.autocomplete", item )
                                    .append(item.label)
                                    .appendTo( ul );
                }
                else{
                    if ( item.category != currentCategory) {
					    ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
					    currentCategory = item.category;
				    }
                    item.url = item.url + encodeURIComponent(item.value);
                    $( "<li></li>" ).data( "item.autocomplete", item )
                                    .append( "<a href='" + item.url + "'>" + htmlEntities(item.label) + "</a>" )
                                    .appendTo( ul );
                }

			});
		}
    });
    Drupal.behaviors.exportSmartSearchTransactions = {
            attach:function (context, settings) {
                $('span.exportSmartSearch').live("click", function () {

                    var dialog = $("#dialog");
                    if ($("#dialog").length == 0) {
                        dialog = $('<div id="dialog" style="display:none"></div>');
                    }
                    var domains = '';
                    $.each($('input[name=fdomainName]:checked'),function(){
                    	domains = domains + "~" + this.value;
                    });   
                    if(domains == '' ){
                    	$.each($('input[name=fdomainName]'),function(){
                        	domains = domains + "~" + this.value;
                        }); 
                    }
                    var dialogUrl = '/exportSmartSearch/form?search_term=' + getParameterByName("search_term") + '&totalRecords=' + $(this).attr("value")
                    					+ '&resultsdomains=' + domains ;
                    // load remote content
                    dialog.load(
                        dialogUrl,
                        {},
                        function (responseText, textStatus, XMLHttpRequest) {
                            dialog.dialog({position:"center",
                                modal:true,
                                title:'Download Search Results',
                                dialogClass:"export",
                                width:700,
                                buttons:{
                                    "Download Data":function () {
                                    	var inputs = "<input type='hidden' name='search_term' value='" +  getParameterByName("search_term") + "'/>"
                                        + "<input type='hidden' name='domain' value='" + $('input[name=domain]:checked').val() + "'/>"                                        
                                    ;
                                    	var url = '/exportSmartSearch/download';
                                        $('<form action="' + url + '" method="get">' + inputs + '</form>').appendTo('body').submit().remove();
                                    },
                                    "Cancel":function () {
                                        $(this).dialog('close');
                                    }
                                }
                            });
                            $('.ui-dialog-buttonpane').append('<div class="exportDialogMessage">*Required Field</div>');
                        }
                    );
                    
                    
                    return false;
                });
            }
        };       
        
     Drupal.behaviors.narrowDownFilters = {
        attach:function (context, settings) {
            var search_term ="";
            search_term  = window.location.href.toString().split(window.location.host)[1];
            //Sets up jQuery UI autocompletes and autocomplete filtering functionality for agency name facet
            $('#autocomplete_fagencyName',context).autocomplete({
                source:"/smart_search/autocomplete/agency/" + search_term,
                focus: function (event, ui) {
                            if(ui.item.label.toLowerCase() == 'no matches found'){
                                return false;
                            }else{
                                $(event.target).val(ui.item.label);
                                return false;
                            }
                        },
                select: function (event, ui) {
                        if(ui.item.label.toLowerCase() == 'no matches found'){
                            return false;
                        }else{
                            var url = getFacetAutocompleteUrl("agency_names",encodeURIComponent(ui.item.value));
                            $(event.target).val(ui.item.label);
                            window.location = url;
                            return false;
                        }
                }
            })

            $('#autocomplete_fogeName',context).autocomplete({
                source:"/smart_search/autocomplete/oge/" + search_term,
                focus: function (event, ui) {
                    if(ui.item.label.toLowerCase() == 'no matches found'){
                        return false;
                    }else{
                        $(event.target).val(ui.item.label);
                        return false;
                    }
                },
                select: function (event, ui) {
                    if(ui.item.label.toLowerCase() == 'no matches found'){
                        return false;
                    }else{
                        var url = getFacetAutocompleteUrl("oge_agency_names",encodeURIComponent(ui.item.value));
                        $(event.target).val(ui.item.label);
                        window.location = url;
                        return false;
                    }
                }
            })
            
            $('#autocomplete_fvendorName',context).autocomplete({
                source:"/smart_search/autocomplete/vendor" + search_term,
                focus: function (event, ui) {
                            if(ui.item.label.toLowerCase() == 'no matches found'){
                                return false;
                            }else{
                                $(event.target).val(ui.item.label);
                                return false;
                            }
                        },
                select: function (event, ui) {
                            if(ui.item.label.toLowerCase() == 'no matches found'){
                                return false;
                            }else{
                                var url = getFacetAutocompleteUrl("vendor_names",encodeURIComponent(ui.item.value));
                                $(event.target).val(ui.item.label);
                                window.location = url;
                                return false;
                            }
                         }
            });
            $('#autocomplete_fexpenseCategoryName',context).autocomplete({
                source:"/smart_search/autocomplete/expensecategory" + search_term,
                focus: function (event, ui) {
                            if(ui.item.label.toLowerCase() == 'no matches found'){
                                return false;
                            }else{
                                $(event.target).val(ui.item.label);
                                return false;
                            }
                        },
                select: function (event, ui) {
                        if(ui.item.label.toLowerCase() == 'no matches found'){
                            return false;
                        }else{
                                var url = getFacetAutocompleteUrl("expense_categories",encodeURIComponent(ui.item.value));
                                $(event.target).val(ui.item.label);
                                window.location = url;
                                return false;
                        }
                    }
            });
            $('#autocomplete_fyear',context).autocomplete({
                source:"/smart_search/autocomplete/fiscalyear" + search_term,
                focus: function (event, ui) {
                            if(ui.item.label.toLowerCase() == 'no matches found'){
                                return false;
                            }else{
                                $(event.target).val(ui.item.label);
                                return false;
                            }
                        },
                select: function (event, ui) {
                        if(ui.item.label.toLowerCase() == 'no matches found'){
                            return false;
                        }else{
                                var url = getFacetAutocompleteUrl("fiscal_years",encodeURIComponent(ui.item.value));
                                $(event.target).val(ui.item.label);
                                window.location = url;
                                return false;
                        }
                     }
            });
        }
    }
    Drupal.behaviors.clear_search = {
		  attach: function(context) {    		    

		    $('#edit-search-box', context).focus(function(){  
      		    if(this.value == this.defaultValue){
  		          $(this).val("");
  		        }
  		    });

		    $('a.pagerItemDisabled').click(function (e) {
                e.preventDefault();
            });
		  }
    }

}(jQuery));

/**
 *  Redirects to the search results page for the given search criteria
 *  Requires 'prepareSearchFilterUrl' function
 */

    function applySearchFilters(){
        jQuery('input[type=checkbox]').attr("disabled", true);
        var cUrl = prepareSearchFilterUrl();
        window.location = cUrl;

    }

/**
 *  Returns the search URL
 *  Requires 'getSearchFilterCriteria' function
 */

    function prepareSearchFilterUrl(){
        var domainNames = getSearchFilterCriteria('fdomainName');
        var ogeAgencyNames = getSearchFilterCriteria('fogeName');
        var agencyNames = getSearchFilterCriteria('fagencyName');
        var vendorNames = getSearchFilterCriteria('fvendorName');
        var expenseCategories = getSearchFilterCriteria('fexpenseCategoryName');
        var revenueCategories = getSearchFilterCriteria('frevenueCategoryName');
        var fiscalYears = getSearchFilterCriteria('fyear');
        var contractCategories = getSearchFilterCriteria('fcontractCatName');
        var contractStatus = getSearchFilterCriteria('fcontractStatus');
        var spendingCategories = getSearchFilterCriteria('fspendingCatName');

        var searchTerm = '';
        var cUrl = null;

        var qsParm = getQuerystringValues();

        if(!qsParm){
            searchTerm = ""
        }else if(qsParm["search_term"]){
            var searchTerms = qsParm["search_term"].split("*|*");
            searchTerm = searchTerms[0];
        }

        cUrl = "?search_term=" + searchTerm + "*|*";

        if(domainNames){
            cUrl += "domains=" + encodeURIComponent(domainNames) + '*|*';
        }
        if(ogeAgencyNames){
            cUrl += "oge_agency_names=" + encodeURIComponent(ogeAgencyNames) + '*|*';
        }
        if(agencyNames){
            cUrl += "agency_names=" + encodeURIComponent(agencyNames) + '*|*';
        }
        if(vendorNames){
            cUrl += "vendor_names=" + encodeURIComponent(vendorNames) + '*|*';
        }
        if(fiscalYears){
            cUrl += "fiscal_years=" + encodeURIComponent(fiscalYears) + '*|*';
        }
        if(expenseCategories){
            cUrl += "expense_categories=" + encodeURIComponent(expenseCategories) + '*|*';
        }
        if(revenueCategories){
            cUrl += "revenue_categories=" + encodeURIComponent(revenueCategories) + '*|*';
        }
        if(domainNames){
            if(contractCategories){
                cUrl += "contract_categories=" + encodeURIComponent(contractCategories) + '*|*';
            }
            if(contractStatus){
                cUrl += "contract_status=" + encodeURIComponent(contractStatus) + '*|*';
            }
            if(spendingCategories){
                cUrl += "spending_categories=" + encodeURIComponent(spendingCategories) + '*|*';
            }
        }
    
        cUrl = cUrl.substring(0, cUrl.length - 3);

        return cUrl;
    }

/**
 *  Returns the selected filter parameters on the form
 * @param filterName
 */
    function getSearchFilterCriteria(filterName){
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
                    filterId = filterId  + '~' + oFilterIds[i].value;
                }else{
                    filterId = oFilterIds[i].value;
                }
            }
        }

        return filterId;
    }

/**
 *  Returns the query string values from the current URL
 *
 */
function getQuerystringValues() {
    var qsParm = new Array();
    var query = window.location.search.substring(1);
    var parms = query.split('&');
    for (var i=0; i<parms.length; i++) {
        var pos = parms[i].indexOf('=');
        if (pos > 0) {
            var key = parms[i].substring(0,pos);
            var val = parms[i].substring(pos+1);
            qsParm[key] = val;
        }
    };
    return qsParm;
}

function getFacetAutocompleteUrl(category, value){
    var searchString = getQuerystringValues();
    var newUrl = '?search_term=';
    var count = 0;

    if(searchString["search_term"]){
        var searchTerms = searchString["search_term"].split("*|*");
        newUrl += searchTerms[0];

        for (var i=1; i<searchTerms.length; i++) {
            var params = searchTerms[i].split('=');
            if(params[0] == category){
                count++;
                params[1] = params[1] + '~' + value;
            }
            newUrl += "*|*" + params[0] + '=' + params[1];
        }

        if(count == 0){
            newUrl += "*|*" + category + '=' + value;
        }
    }else{
        newUrl += "*|*" + category + '=' + value;
    }
    return newUrl;
}


function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}




