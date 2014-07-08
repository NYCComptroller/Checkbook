jQuery(document).ready(function ($) {
    if (parseInt($.browser.version, 10) == 7) {
        $("#page").addClass("ie");
    } else {
        $("#page").addClass("not-ie");
    }
});


function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.search);
    if (results == null)
        return "";
    else
        return decodeURIComponent(results[1].replace(/\+/g, " "));
}


//Line Splitter Function
//copyright Stephen Chapman, 19th April 2006
//you may copy this code but please keep the copyright notice as well
function splitLine(st,n) {var b = ''; var s = st;while (s.length > n) {var c = s.substring(0,n);var d = c.lastIndexOf(' ');var e =c.lastIndexOf('\n');if (e != -1) d = e; if (d == -1) d = n; b +=       c.substring(0,d) + '\n';s = s.substring(d+1);}return b+s;}




function fasterSplit(str,len){
	  var ret=[],strlen=str.length,off=0,rem=len
	  do {
		//if()  
	    ret.push(str.substr(off,len)); 
	 off+=len
	  } while (off<strlen)
	  return ret
	  }

function addPaddingToDataCells(table){
	(function ($) {
	$(table).find("th").each(function(i,val){
		if($(this).hasClass("number")){
			var colwidth = $(this).find("span").width();
			var maxDataWidth = 0;
			$(table).find("tr td:nth-child(" + (i+1) + ")").each(
			     function(){
			    	 if( maxDataWidth <  $(this).find("div").width()){
			    		 maxDataWidth = $(this).find("div").width();
			    	 }
			     }
			 );
			if((colwidth - maxDataWidth)/2 > 1){
				$(table).find("tr td:nth-child(" + (i+1) + ") div").css("margin-right",Math.floor((colwidth - maxDataWidth)/2) + "px");
			}
		}
	}
	);
    $(".DTFC_LeftHeadWrapper table").find("th").each(function(i,val){
            if($(this).hasClass("number")){
                var colwidth = $(this).find("div").width();
                var maxDataWidth = 0;
                $(".DTFC_LeftBodyWrapper table").find("tr td:nth-child(" + (i+1) + ")").each(
                    function(){
                        if( maxDataWidth <  $(this).find("div").width()){
                            maxDataWidth = $(this).find("div").width();
                        }
                    }
                );
                if((colwidth - maxDataWidth)/2 > 1){
                    $(".DTFC_LeftBodyWrapper table").find("tr td:nth-child(" + (i+1) + ") div").css("margin-right",Math.floor((colwidth - maxDataWidth)/2) + "px");
                }
            }
        }
    );
    $(".dataTables_scrollHeadInner table").find("th").each(function(i,val){
            if($(this).hasClass("number")){
                var colwidth = $(this).find("div").width();
                var maxDataWidth = 0;
                $(".dataTables_scrollBody table").find("tr td:nth-child(" + (i+1) + ")").each(
                    function(){
                        if( maxDataWidth <  $(this).find("div").width()){
                            maxDataWidth = $(this).find("div").width();
                        }
                    }
                );
                if((colwidth - maxDataWidth)/2 > 1){
                    $(".dataTables_scrollBody table").find("tr td:nth-child(" + (i+1) + ") div").css("margin-right",Math.floor((colwidth - maxDataWidth)/2) + "px");
                }
            }
        }
    );
	}(jQuery));
}



(function ($) {
	
// trendsCenPad Start
	window.trendsCenPad = function(settings){
/*
		//setTimeout("redraw()", 0);
		//setTimeout("redraw()", 600);
		
		var table = settings.selector+'_wrapper';

		if(settings.oInstance){
			table = settings.oInstance.selector+'_wrapper';
		}
		//redraw = function(){
		// Loops through all divs with a class of trendCen and subtracts its width from its parent TH width.
		//	Then divides by two, in order to add padding to the right of the div.

		$(table).find(".trendCen").each(function(i,val){
			var $oThis = $(this),
				$oTableRows = $(table + " tr"),
				colwidth = $oThis.parent().width(),
				textwidth = $oThis.width(),
				maxDataWidth = 0,
				currentWidth = 0,
				padding;

				//grabs TH width and its child div width, subtracts child from parent, then divides by two to get right margin.
				if((colwidth - textwidth)/2 > 1){
					console.log(colwidth);
					console.log(textwidth);
					padding = Math.floor((colwidth - textwidth)/2);
					$oThis.css("margin-right", padding);

					//goes through current column trying to find the widest div width in the column
					for(var j = 0; $oTableRows.length > j; j++){
						$oThis = $($oTableRows[j]);
						currentWidth = $oThis.find(".tdCen").eq(i).width();
			        	if( currentWidth >  maxDataWidth){
			        		var maxDataWidth = currentWidth;
			        	}
					}
					
					// check widest td div width against TH div width, to see which is wider. If th div is wider, then td div gets its own center margin. Otherwise it uses TH div margin.
					if(textwidth > maxDataWidth){
			        	padding = (colwidth - maxDataWidth)/2;
			        }
					for(var j = 0; $oTableRows.length > j; j++){
						$oThis = $($oTableRows[j]);
						$oThis.find(".tdCen").eq(i).css("margin-right", padding);
			        };
			        
			        // checks to see if column contains an ending item, such as a % or a suptag. If it contains one, the entire columns margin is subtracted by that items width
					var endItems = $oTableRows.find('.endItem');
			        for(var j = 0; endItems.length > j; j++){
						$oThis = $(endItems[j]);
						var endItemWidth = $oThis.width();
						var oDiv = $oThis.parent();
						if(endItemWidth < padding){
							oDiv.css('margin-right', (padding - endItemWidth));
						}
					};
				}
			});
		//}
*/
	}

	// trendsCenPad End

	
    //set classes for sortable columns in DataTables
    var newclasses = {
        'sSortAsc':'ui-state-default sorting_asc',
        'sSortDesc':'ui-state-default sorting_desc',
        'sSortable':'ui-state-default sortable',
        'sSortJUI':'css_right ui-icon ui-icon-triangle-2-n-s'
    }

    $.fn.dataTableExt.oJUIClasses = $.extend({},$.fn.dataTableExt.oJUIClasses,newclasses);

    jQuery.fn.toggleText = function (value1, value2) {
        return this.each(function () {
            var $this = $(this),
                text = $this.text();
            if (text.indexOf(value1) > -1)
                $this.text(text.replace(value1, value2));
            else
                $this.text(text.replace(value2, value1));
        });
    };

    // Projects & Actions Table Styling
    Drupal.behaviors.styleOverrides = {
        attach:function (context, settings) {

            // Add active class to current menu item
            if ($('.block-nice-menus li a').hasClass('active')) {
                $('.block-nice-menus li a.active').parent().addClass('active');
            }
            // Make 'Employment' menu item in footer bold
            $('#block-menu-menu-news-room li:nth-child(4)').css('font-weight', 'bold');

            // Year Dropdown
            $('#year_list').chosen({disable_search_threshold:50});

            // Fiscal Year Dropdown
            $('#fiscal_year_list').chosen({disable_search_threshold:50});

            // Column Widths
            $('#node-widget-15 th:first, #node-widget-16 th:first, #node-widget-22 th:first, #node-widget-23 th:first, #node-widget-24 th:first, #node-widget-29 th:first, #node-widget-30 th:first, #node-widget-31 th:first').css('width', '77%');
            $('#node-widget-26 th:first, #node-widget-28 th:first').css('width', '42%');

            // Sidebar
            $('.page-spending-transactions .panel-panel.grid-3 table:first, .page-budget .panel-panel.grid-3 table:first, .page-revenue-transactions .panel-panel.grid-3 table:first').css('margin-top', '0px');

            // Contract Transactions Show/Hide
            $("#node-widget-273 .contract-transactions-toggle").click(function () {
                $(this).parent().parent().parent().parent().next('tbody').toggleClass('show');
                $(this).toggleText("Show", "Hide").next().toggle();
                return false;
            });

            // FitVids
            $(".video-container").fitVids();

            // Charts Slider
            var pager = "<div class='slider-pager'></div>";
            $('#nyc-budget .grid-12 .inside').filter(':first').after(pager);
            $('#nyc-contracts > .grid-12 .inside').filter(":first").after(pager);
            $('#nyc-spending .grid-12 .inside').filter(":first").after(pager);
            $('#agency-budget .grid-12 .inside').filter(':first').after(pager);
            $('#agency-expenditure-categories .grid-12 .inside').filter(':first').after(pager);
            $('#nyc-expenditure-categories .grid-12 .inside').filter(':first').after(pager);
            $('#nyc-contracts-revenue-landing .grid-12 .inside').filter(':first').after(pager);
            $('#nyc-revenue-pending-contracts .grid-12 .inside').filter(':first').after(pager);
            $('#nyc-expense-pending-contracts .grid-12 .inside').filter(':first').after(pager);
            $('#dept-budget .grid-12 .inside').filter(':first').after(pager);
            $('#nyc-payroll .grid-12 .inside').filter(':first').after(pager);
            $('#nyc-revenue .grid-12 .inside').filter(':first').after(pager);

            var highSlides = '#nyc-spending .grid-12 .inside, #nyc-payroll .grid-12 .inside, #nyc-contracts .grid-12 .inside,#nyc-budget .grid-12 .inside, #agency-budget .grid-12 .inside, #agency-expenditure-categories .grid-12 .inside, #nyc-expenditure-categories .grid-12 .inside,#nyc-contracts-revenue-landing .grid-12 .inside,#nyc-revenue-pending-contracts .grid-12 .inside,#nyc-expense-pending-contracts .grid-12 .inside,#dept-budget .grid-12 .inside,#nyc-revenue .grid-12 .inside';

            if ($(highSlides).filter(":first").length > 0) {
                $(highSlides).filter(":first")
                    .once('styleOverrides')
                    .cycle({
                        slideExpr:'.slider-pane',
                        fx:'fade',
                        timeout:45000,
                        height:'315px',
                        width:'100%',
                        fit:1,
                        pause:true,
                        pager:'.slider-pager'
                    });
            }

            $('.chart-title').css("display","block");

            // NYC Budget Total Expenditure
            $('.page-budget .slider-pager a:last').click(function () {
                $('#total-expenditure').fadeIn();
            });
            $('.page-budget .slider-pager a:first').click(function () {
                $('#total-expenditure').fadeOut();
            });

            // Add jQuery UI Theme to Tables
            $(".ui-table th").each(function () {
                $(this).addClass("ui-state-default");
            });
            $(".ui-table td").each(function () {
                $(this).addClass("ui-widget-content");
            });
            $(".ui-table tr").hover(
                function () {
                    $(this).children("td").addClass("ui-state-hover");
                },
                function () {
                    $(this).children("td").removeClass("ui-state-hover");
                }
            );
            $(".ui-table tr").click(function () {
                $(this).children("td").toggleClass("ui-state-highlight");
            });

            // jQuery Dialog
            var $dialog = $('div.status-codes').dialog({autoOpen:false, title:'Status Codes and Messages', width:800, modal:true});
            $('a.status-codes').click(function (e) {
                $dialog.dialog('open');
                // prevent the default action, e.g., following a link
                e.preventDefault();
            });

            //Smart Search Pager
            $('#smart-search-transactions').prev('.item-list').addClass('smart-search-pager');
            $('#smart-search-transactions').next().next().next('.item-list').addClass('smart-search-pager');

            // Advanced Search
            // Hide calendar icon if user has picked a date, show if field is empty
            $('#spending-advanced-search .form-item-spending-issue-date input').change(function () {
                if ($(this).val() != '') {
                    $(this).css('background-image', 'none');
                }
                else {
                    $(this).removeAttr('style');
                }
            });

            var el;
            var longSelects = '#ie #checkbook-datafeeds-data-feed-wizard #edit-agency, #ie #checkbook-datafeeds-data-feed-wizard #edit-dept, ' +
                '#ie #checkbook-datafeeds-data-feed-wizard #edit-expense-category, #ie #checkbook-datafeeds-data-feed-wizard #edit-expense-type, ' +
                '#ie #checkbook-datafeeds-data-feed-wizard #edit-contract-type, #ie #checkbook-datafeeds-data-feed-wizard #edit-award-method';
            $(longSelects)
                .each(function () {
                    el = $(this);
                    el.data("origWidth", el.outerWidth()) // IE 8 can haz padding
                })
                .focusin(function () {
                    $(this).css("width", "auto")
                        .css('position', 'absolute');
                })
                .bind("blur change", function () {
                    el = $(this);
                    el.css("width", el.data("origWidth"))
                        .css('position', 'static');
                });
        }
    };

    /**
     * Toggles Highcharts series on checkbox click
     *
     * Vendors and Agencies pages
     *
     * @see toggleSeries()
     */
    Drupal.behaviors.agencyPage = {
        attach:function (context, settings) {
            $('.togglecheckboxes input').each(function () {
                $(this).click(function () {
                    var input = $(this);
                    toggleSeries(input, Highcharts.chartarray);
                })
            });
        }
    };

    // Agencies Drop Down
    Drupal.behaviors.agenciesDropDown = {
        attach:function (context, settings) {
            $('.agency-list-open span, .agency-list-open div b').once('agenciesDropDown').click(function () {
            	if($(this).attr("id")== "other-agency-list-open"){
            		$('.all-agency-list-content').slideUp(300);            		
            	}else{
            		$('.other-agency-list-content').slideUp(300);
            	}
            	$(this).parent().parent().find('.agency-list-content').slideToggle(300);
                $(this).toggleClass('open');
                $(this).parent().find(' div b').toggleClass('open');
            });

            $('.agency-list-close a').once('agenciesDropDown').click(function () {
                $('.agency-list-content').slideUp(300);
                $('.agency-list-open div b').removeClass('open');
            });

            if ($('#allAgenciesList').length > 0) {
                $('#allAgenciesList')
                    .once('agenciesDropDown')
                    .after('<div id="agency-list-pager1" class="agency-list-pager"></div>')
                    .cycle({
                        fx:'none',
                        speed:1000,
                        timeout:0,
                        pause:true,
                        pauseOnPagerHover:0,
                        pager:'#agency-list-pager1',
                        prev:'#prev1',
                        next:'#next1'
                    });
            }

            if ($('#otherAgenciesList').length > 0) {
                $('#otherAgenciesList')
                    .once('agenciesDropDown')
                    .after('<div id="agency-list-pager2" class="agency-list-pager"></div>')
                    .cycle({
                        fx:'none',
                        speed:1000,
                        timeout:0,
                        pause:true,
                        pauseOnPagerHover:0,
                        pager:'#agency-list-pager2',
                        prev:'#prev2',
                        next:'#next2'
                    });
            }
        }
    };

    // Employee Payroll Transactions scrolling window
    Drupal.behaviors.employeePayrollTransactions = {
        attach:function (context, settings) {
            if ($('#payroll-emp-trans-table > tbody').length > 0) {
                $('#payroll-emp-trans-table > tbody')
                    .cycle({
                        fx:'none',
                        speed:1000,
                        timeout:0,
                        pause:true,
                        pauseOnPagerHover:0,
                        pager:'#payroll-emp-trans-table-pager',
                        prev:'#payroll-emp-trans-table-up',
                        next:'#payroll-emp-trans-table-down'
                    });
            }
        }
    };

    Drupal.behaviors.custompager = {
        attach:function (context, settings) {
            $('.customPager ul li a').live("click",function (e) {
                e.preventDefault();
                var input = $(this);
                urlLink = input.attr('href');
                if(urlLink != undefined){
                    $.ajax({
                        url: urlLink,
                        success: function(data) {
                            $('#contListContainerNew').html('');
                            $('#contListContainerNew').html(data);
                        }
                    });
                }

                return false;

            })
        }
    }

    Drupal.behaviors.viewAllPopup = {
        attach:function (context, settings) {
            $('a.popup').click(function (event) {
                event.preventDefault();
                var url = $(this).attr('href');
                var splitURL = url.split('?');
                splitURL = splitURL[0].split('/');
                var nid = splitURL.pop();
                var dialog = $("#dialog");
                if ($("#dialog").length == 0) {
                    dialog = $('<div id="dialog" style="display:none"></div>').appendTo('body');
                }
                var parentWidth = $(this).prev().width();
                // load remote content
                dialog.load(
                    url,
                    {},
                    function (responseText, textStatus, XMLHttpRequest) {
                        dialog.dialog({
                            position:"top",
                            width:parentWidth,
                            modal:true,
                            open:function (event, ui) { //If there are DataTables with deferredRender = TRUE, render them on open
                                if (Drupal.settings.deferredRender) {
                                    var i = 0;
                                    var deferredRender = Drupal.settings.deferredRender;
                                    for (i; i < deferredRender.length; i++) {
                                        if (deferredRender[i].type == 'datatable' && deferredRender[i].id == nid) {
                                            var options = eval('(' + deferredRender[i].dataTableOptions + ')');
                                            options.bPaginate = true;
                                            options.sPaginationType = "full_numbers";
                                            options.sAjaxSource = "/checkbook/view_all_popup_data/node/" + deferredRender[i].nodeId + "?refURL=" + getParameterByName('refURL');
                                            window['oTablePopup'] = $('#table_' + deferredRender[i].id + '_popup').dataTable(options);
                                        }
                                    }
                                }
                            },
                            close:function (event, ui) { //Destroy a deferredRender table if it exists and remove any script tags created by movescripts()
                                if (oTablePopup) {
                                    oTablePopup.fnDestroy();
                                    $('[id^="movescript"]').remove();
                                }
                            }
                        });
                    }
                );
            });
        }
    };

    Drupal.behaviors.gridViewAllPopup = {
        attach:function (context, settings) {
            newWindow('a.gridpopup');
            newWindow('a.new_window');
            function newWindow(selector) {
                $('body', context).delegate(selector, 'click', function () {
                    var source = $(this).attr('href');
                    var newWindow = window.open(source, '_blank', 'menubar=no,toolbar=no,location=no,resizable=yes,scrollbars=yes,personalbar=no,chrome=yes,height=700,width=980');
                    return false;
                });
            }
        }
    };

    Drupal.behaviors.disableClicks = {
        attach: function(context,settings){
            if ($('body').hasClass('gridview') || $('body').hasClass('newwindow')){
                $('body').delegate('a', 'click', function () {
                    if($(this).hasClass('showHide') || $(this).hasClass('logo') || $(this).attr('rel') == 'home' || $(this).hasClass('enable-link'))
                        return true;
                    else
                        return false;
                });
            }
        }
    }

    Drupal.behaviors.helpPopup = {
        attach:function (context, settings) {
            $('ul#site-overview a', context).click(function () {
                var source = $(this).attr('href');
                var newWindow = window.open(source, '_blank', 'menubar=yes,toolbar=yes,location=yes,resizable=yes,scrollbars=yes,personalbar=no,chrome=yes,height=700,width=980');
                if (newWindow.addEventListener) {
                    newWindow.addEventListener('load', function () {
                        $('body', newWindow.document).addClass('help_window')
                    });
                } else if (newWindow.attachEvent) {
                    newWindow.attachEvent('onload', function () {
                        $('body', newWindow.document).addClass('help_window')
                    });
                }
                return false;
            })
        }
    }

    Drupal.behaviors.alertTransactions = {
        attach:function (context, settings) {
        // The span.alert is the object in Drupal to which you link the click button, I don�t know how it is actually named for the alert
            $('span.alerts').live("click", function () {
                var dialog = $("#dialog");
                if ($("#dialog").length == 0) {
                    dialog = $('<div id="dialog" style="display:none"></div>');
                }

                // This is where you add the alerted table to which you link the output data from '/alert/transactions/form�
                var oSettings = $('#table_'+$(this).attr('alertsid')).dataTable().fnSettings();

                // This is the part where we get the data from to show in the dialogue we open, I don�t know if you process the following parameters  maxPages , record and so on but it won�t hurt if it stayed here
                var dialogUrl = '/alert/transactions/form';

                var validateEmail=function(email) {
                    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    return re.test(email);
                };
                var isNumber=function(value) {
                    if ((undefined === value) || (null === value)) {
                        return false;
                    }
                    if (typeof value == 'number') {
                        return true;
                    }
                    return !isNaN(value - 0);
                }

                // load remote content
                dialog.load(
                    dialogUrl,
                    {},
                    function (responseText, textStatus, XMLHttpRequest) {
                        dialog.dialog({position:"center",
                            modal:true,
                            title:'Alert',
                            dialogClass:"alert",
                            width:700,
                            open:function(){
                            $("input[name='alert_end[date]']").datepicker({"changeMonth":true,"changeYear":true,"autoPopUp":"focus","closeAtTop":false,"speed":"immediate","firstDay":0,"dateFormat":"yy-mm-dd","yearRange":"-113:+487","fromTo":false,"defaultDate":"0y"});
                                                        },
                            buttons:{
                                "Create Alert":function () {
                                    var alertLabel = $('input[name=alert_label]').val();
                                    var alertEmail = $('input[name=alert_email]').val();
                                    var alertMinimumResults = $('input[name=alert_minimum_results]').val();
                                    var alertMinimumDays = $('select[name=alert_minimum_days]').val();
                                    var alertEnd = $("input[name='alert_end[date]']").val();
                                    var dateRegEx = '[0-9]{4,4}-[0-1][0-9]-[0-3][0-9]';

                                    var alertMsgs = [];
                                    if(alertLabel.length<1){
                                      alertMsgs.push("No Description has been set.");
                                    }
                                    if(alertEmail.length<1 || !validateEmail(alertEmail)){
                                      alertMsgs.push("No email is entered.");
                                    }
                                    if(!isNumber(alertMinimumResults) || alertMinimumResults<1){
                                      alertMsgs.push("Minimum results is not a valid number.");
                                    }
                                    if(!isNumber(alertMinimumDays) || alertMinimumDays<1){
                                      alertMsgs.push("Alert frequency is not valid.");
                                    }
                                    if((alertEnd.length > 1 && alertEnd.length != 10) || (alertEnd.length > 1 && !alertEnd.match(dateRegEx))){
                                        alertMsgs.push("Expiration Date is not valid.");
                                    }

                                    if (alertMsgs.length > 0) {
                                        $('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul>' + '<li>' + alertMsgs.join('<li/>') + '</ul></div>');
                                    } else {
                                        $('#errorMessages').html('');

                                        var url = '/alert/transactions';
                                        var data = {
                                          refURL:oSettings.sAjaxSource,
                                          alert_label:alertLabel,
                                          alert_email:alertEmail,
                                          alert_minimum_results:alertMinimumResults,
                                          alert_minimum_days:alertMinimumDays,
                                          alert_end:alertEnd,
                                          userURL:window.location.href
                                        }
                                        $this=$(this);
                                        $.get(url,data,function(data){
                                          data=JSON.parse(data);
                                          if(data.success){
                                            $this.dialog('close');

                                              var dialog = $("#dialog");
                                              if ($("#dialog").length == 0) {
                                                  dialog = $('<div id="dialog" style="display:none"></div>');
                                              }
                                              dialog.html(data.html);
                                              dialog.dialog({position:"center",
                                                  modal:true,
                                                  title:'Alert',
                                                  dialogClass:"alert",
                                                  width:700,
                                                  buttons:{
                                                      "OK":function () {
                                                          $this.dialog('close');
                                                      }
                                                  }
                                              });
                                          }else{
                                            $('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul><li>'+data.errors.join('<li/>')+'</ul></div>');
                                          }
                                        });
                                    }
                                },
                                "Cancel":function () {
                                    $(this).dialog('close');
                                }
                            }
                        });
                    }
                );
                return false;
            });
        }
    };

    Drupal.behaviors.exportTransactions = {
        attach:function (context, settings) {
            $('span.export').live("click", function () {

                var dialog = $("#dialog");
                if ($("#dialog").length == 0) {
                    dialog = $('<div id="dialog" style="display:none"></div>');
                }

                var oSettings = $('#table_'+$(this).attr('exportid')).dataTable().fnSettings();
                var iRecordsTotal = oSettings.fnRecordsTotal();
                var iRecordsDisplay = oSettings.fnRecordsDisplay();
                var iDisplayLength = oSettings._iDisplayLength;
                var iDisplayStart = oSettings._iDisplayStart;

                var maxPages = Math.ceil(iRecordsDisplay / iDisplayLength);
                // var defStartRecord = iDisplayStart;
                //var defRecordLimit = iDisplayLength;


                var dialogUrl = '/export/transactions/form?maxPages=' + maxPages + '&iRecordsTotal=' + iRecordsTotal + '&iRecordsDisplay=' + iRecordsDisplay;

                // load remote content
                dialog.load(
                    dialogUrl,
                    {},
                    function (responseText, textStatus, XMLHttpRequest) {
                        dialog.dialog({position:"center",
                            modal:true,
                            title:'Download Transactions Data',
                            dialogClass:"export",
                            width:700,
                            buttons:{
                                "Download Data":function () {
                                    //current page
                                    var startRecord = iDisplayStart;
                                    var recordLimit = iDisplayLength;

                                    var alertMsgs = [];
                                    var dcfilter = $('input[name=dc]:checked').val();
                                    if (dcfilter == null) {
                                        alertMsgs.push("One of 'Data Selection' option must be selected.");
                                    }

                                    if (dcfilter == 'all') {
                                        startRecord = 0;
                                        recordLimit = iRecordsDisplay;
                                    }

                                    if (dcfilter == 'range') {
                                        var rangefrom = $('input[name=rangefrom]').val();
                                        var rangeto = $('input[name=rangeto]').val();

                                        var validFrom = ((String(rangefrom).search(/^\s*(\+|-)?\d+\s*$/) != -1 ) && (parseFloat(rangefrom) == parseInt(rangefrom)) && parseInt(rangefrom) >= 1 && parseInt(rangefrom) <= maxPages);
                                        var validTo = ((String(rangeto).search(/^\s*(\+|-)?\d+\s*$/) != -1 ) && (parseFloat(rangeto) == parseInt(rangeto)) && parseInt(rangeto) >= 1 && parseInt(rangeto) <= maxPages);

                                        if (!validFrom && !validTo) {
                                            alertMsgs.push('If "Pages" option is selected, page numbers must be integer values between 1 and ' + maxPages);
                                        } else if (rangefrom.length > 0 && !validFrom) {
                                            alertMsgs.push('From page number must be integer value between 1 and ' + maxPages);
                                        } else if (rangeto.length > 0 && !validTo) {
                                            alertMsgs.push('To page number must be integer value between 1 and ' + maxPages);
                                        } else {
                                            rangefrom = !validFrom ? 1 : parseInt(rangefrom);
                                            rangeto = !validTo ? maxPages : parseInt(rangeto);
                                            if (rangefrom > rangeto) {
                                                alertMsgs.push('From page number(' + rangefrom + ') must be less than or equal to ' + rangeto);
                                            } else {
                                                startRecord = (rangefrom - 1) * iDisplayLength;
                                                recordLimit = (rangeto - rangefrom + 1) * iDisplayLength;
                                                if ((startRecord + recordLimit) > iRecordsDisplay) {
                                                    recordLimit = recordLimit - (startRecord + recordLimit - iRecordsDisplay);
                                                }
                                            }
                                        }
                                    } else {
                                        $('input[name=rangefrom]').val(null);
                                        $('input[name=rangeto]').val(null);
                                    }

                                    var frmtfilter = $('input[name=frmt]:checked').val();
                                    if (frmtfilter == null) {
                                        alertMsgs.push('Format must be selected');
                                    }

                                    if (alertMsgs.length > 0) {
                                        $('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul>' + '<li>' + alertMsgs.join('<li/>') + '</ul></div>');
                                    } else {
                                        $('#errorMessages').html('');

                                        var url = '/export/transactions';
                                        var inputs = "<input type='hidden' name='refURL' value='" + oSettings.sAjaxSource + "'/>"
                                                + "<input type='hidden' name='iDisplayStart' value='" + startRecord + "'/>"
                                                + "<input type='hidden' name='iDisplayLength' value='" + recordLimit + "'/>"
                                            ;

                                        if ( oSettings.oFeatures.bSort !== false )
                                        {
                                            var iCounter = 0;

                                            aaSort = ( oSettings.aaSortingFixed !== null ) ?
                                                oSettings.aaSortingFixed.concat( oSettings.aaSorting ) :
                                                oSettings.aaSorting.slice();

                                            for ( i=0 ; i<aaSort.length ; i++ )
                                            {
                                                aDataSort = oSettings.aoColumns[ aaSort[i][0] ].aDataSort;

                                                for ( j=0 ; j<aDataSort.length ; j++ )
                                                {
                                                    inputs = inputs + "<input type='hidden' name='iSortCol_"+ iCounter + "' value='" + aDataSort[j] + "'/>";
                                                    inputs = inputs + "<input type='hidden' name='sSortDir_"+ iCounter + "' value='" + aaSort[i][1] + "'/>";
                                                    iCounter++;
                                                }
                                            }
                                            inputs = inputs + "<input type='hidden' name='iSortingCols' value='" + iCounter + "'/>";
                                        }

                                        $('<form action="' + url + '" method="get">' + inputs + '</form>').appendTo('body').submit().remove();
                                    }
                                },
                                "Cancel":function () {
                                    $(this).dialog('close');
                                }
                            }
                        });
                    }
                );
                return false;
            });
        }
    };

    Drupal.behaviors.exportGridTransactions = {
        attach:function (context, settings) {
            $('span.grid_export').live("click", function () {
        var nodeId = $(this).attr('exportid');
        var oSettings = jQuery('#table_'+nodeId).dataTable().fnSettings();

        var url = '/export/grid/transactions';
        var inputs = "<input type='hidden' name='refURL' value='"+ (oSettings.sAjaxSource != null ? oSettings.sAjaxSource : oSettings.oInit.sAltAjaxSource) +"'/>"
                + "<input type='hidden' name='iDisplayStart' value='"+ oSettings._iDisplayStart +"'/>"
                + "<input type='hidden' name='iDisplayLength' value='"+ oSettings._iDisplayLength +"'/>"
                + "<input type='hidden' name='node' value='" + nodeId + "'/>"
            ;

        if ( oSettings.oFeatures.bSort !== false )
        {
            var iCounter = 0;

            aaSort = ( oSettings.aaSortingFixed !== null ) ?
                oSettings.aaSortingFixed.concat( oSettings.aaSorting ) :
                oSettings.aaSorting.slice();

            for ( i=0 ; i<aaSort.length ; i++ )
            {
                aDataSort = oSettings.aoColumns[ aaSort[i][0] ].aDataSort;

                for ( j=0 ; j<aDataSort.length ; j++ )
                {
                    inputs = inputs + "<input type='hidden' name='iSortCol_"+ iCounter + "' value='" + aDataSort[j] + "'/>";
                    inputs = inputs + "<input type='hidden' name='sSortDir_"+ iCounter + "' value='" + aaSort[i][1] + "'/>";
                    iCounter++;
                }
            }
            inputs = inputs + "<input type='hidden' name='iSortingCols' value='" + iCounter + "'/>";
        }

        jQuery('<form action="' + url + '" method="get">' + inputs + '</form>').appendTo('body').submit().remove();

        });
       }
    };

    Drupal.behaviors.advancedSearchEnterKeyPress = {
            attach:function (context, settings) {
            	$("#block-checkbook-advanced-search-checkbook-advanced-search-form input").bind("keypress", function(e) {
                    if (e.keyCode == 13) {
                    	e.preventDefault();
                    	var id = this.id;
                    	if(id.match(/contract/g) != null){
                    		$('#edit-contracts-submit').click();                    		
                    	}
                    	else if(id.match(/payroll/g) != null){
                    		$('#edit-payroll-submit').click();                    		
                    	}
                    	else if(id.match(/budget/g) != null){
                    		$('#edit-budget-submit').click();
                    	}
                    	else if(id.match(/revenue/g) != null){
                    		$('#edit-revenue-submit').click();
                    	}
                    	else if(id.match(/spending/g) != null){
                    		$('#edit-spending-submit').click();
                    	}
                    }
                    else return true;
              });
            }
        };

    //Uncomment budget and revenue case and change indexes when enabling budget and revenue
    //Also see changes in checkbook_advanced_search.module and checkbook_datafeeds.module
    Drupal.behaviors.advancedSearchAccordions = {
        attach:function (context, settings) {

            $('a.advanced-search').click(function () {

                var href = window.location.href.replace(/(http|https):\/\//, '');
                var n = href.indexOf('?');
                href = href.substring(0, n != -1 ? n : href.length);
                var data_source = (href.indexOf('datasource/checkbook_oge') !== -1) ? "checkbook_oge" : "checkbook";
                var page_clicked_from = this.id ? this.id : href.split('/')[1];
                var active_accordion_window = initializeActiveAccordionWindow(page_clicked_from, data_source);

                $('#block-checkbook-advanced-search-checkbook-advanced-search-form').dialog({
                    title:"Advanced Search",
                    position:['center', 'center'],
                    width:800,
                    modal:true,
                    autoResize:true,
                    resizable: false,
                    dragStart: function(){
                        $(".ui-autocomplete-input").autocomplete("close")
                    },
                    open: function(){
                    },
                    close: function(){
                        $(".ui-autocomplete-input").autocomplete("close")
                    }
                });

                $('.advanced-search-accordion').accordion({
                    autoHeight: false,
                    active: active_accordion_window
                });

                //Remove Create Alert Attributes and styling
                initializeAccordionAttributes('advanced_search');

                /* For oge, Budget, Revenue & Payroll are not applicable and are disabled */
                disableAccordionSections(data_source);

                return false;
            });
        }
    };

    /*
     * This code is used to determine which window in the accordion should be open when users click the "Advanced Search" link, based on the page
     * from where the link has been clicked
     * Eg: if the "Advanced Search" link from spending page is clicked, the URL would be http://checkbook/SPENDING/transactions.....
     * if the "Advanced Search" link from budget page is clicked, the URL would be http://checkbook/BUDGET/transactions.....
     * based on the url param in the caps above, we have to keep the specific window in the accordion open
     * check the code in checkbook_advanced_search.module where we generate the form
     */
    function initializeActiveAccordionWindow(page_clicked_from, data_source) {
        var active_accordion_window = 2;
        switch (page_clicked_from) {
            case "budget":
                active_accordion_window = 0;
                break;
            case "revenue":
                active_accordion_window = 1;
                break;
            case "contracts_revenue_landing":
            case "contracts_landing":
            case "contracts_pending_rev_landing":
            case "contracts_pending_exp_landing":
            case "contracts_pending_landing":
            case "contract":
                active_accordion_window = 3;
                break;
            case "payroll":
                active_accordion_window = 4;
                break;
            default: //spending
                active_accordion_window = 2;
                break;
        }

        clearInputFields("#payroll-advanced-search",'payroll');
        clearInputFieldByDataSource("#contracts-advanced-search",'contracts',data_source);
        clearInputFieldByDataSource("#spending-advanced-search",'spending',data_source);
        clearInputFields("#budget-advanced-search",'budget');
        clearInputFields("#revenue-advanced-search",'revenue');

        return active_accordion_window;
    }

    function initializeAccordionAttributes(accordion_type) {
        $('.create-alert-customize-results').css('display','none');
        $('.create-alert-schedule-alert').css('display','none');
        $('.create-alert-confirmation').css('display','none');
        $('.create-alert-submit').css('display','none');
        switch(accordion_type) {
            case 'advanced_search':
                $('.create-alert-view').css('display','none');
                $('input[name="budget_submit"]').css('display','inline');
                $('input[name="revenue_submit"]').css('display','inline');
                $('input[name="spending_submit"]').css('display','inline');
                $('input[name="contracts_submit"]').css('display','inline');
                $('input[name="payroll_submit"]').css('display','inline');
                $('input[name="budget_next"]').css('display','none');
                $('input[name="revenue_next"]').css('display','none');
                $('input[name="spending_next"]').css('display','none');
                $('input[name="contracts_next"]').css('display','none');
                $('input[name="payroll_next"]').css('display','none');
                $('.advanced-search-accordion').css('display','inline');
                $('.advanced-search-accordion').width('100%');
                $('.ui-dialog .ui-dialog-content').css('padding','0.5em 1em');
                $('#block-checkbook-advanced-search-checkbook-advanced-search-form').css('margin-left','0px');
                $('#block-checkbook-advanced-search-checkbook-advanced-search-form').css('margin-right','0px');
                break;

            case 'advanced_search_create_alerts':
                $('.create-alert-view').css('display','inline');
                $('input[name="budget_submit"]').css('display','none');
                $('input[name="revenue_submit"]').css('display','none');
                $('input[name="spending_submit"]').css('display','none');
                $('input[name="contracts_submit"]').css('display','none');
                $('input[name="payroll_submit"]').css('display','none');
                $('input[name="budget_next"]').css('display','inline');
                $('input[name="revenue_next"]').css('display','inline');
                $('input[name="spending_next"]').css('display','inline');
                $('input[name="contracts_next"]').css('display','inline');
                $('input[name="payroll_next"]').css('display','inline');
                $('.advanced-search-accordion').css('display','inline');
                $('.advanced-search-accordion').width('90%');
                $('.ui-dialog .ui-dialog-content').css('padding','0.5em 0px 0.5em');
                $('#block-checkbook-advanced-search-checkbook-advanced-search-form').css('margin-left','45px');
                $('#block-checkbook-advanced-search-checkbook-advanced-search-form').css('margin-right','45px');
                break;
        }
    }

    /* For oge, Budget, Revenue & Payroll are not applicable and are disabled */
    function disableAccordionSections(data_source) {
        if(data_source == "checkbook_oge") {
            disableAccordionSection('Budget');
            disableAccordionSection('Revenue');
            disableAccordionSection('Payroll');
        }
    }

    /* Function will apply disable the click of the accordian section and apply an attribute for future processing */
    function disableAccordionSection(name) {
        var accordion_section = $("a:contains("+name+")").closest("h3");
        accordion_section.attr("data-enabled","false");
        accordion_section.addClass('ui-state-section-disabled');
        accordion_section.unbind("click");
    }

    Drupal.behaviors.createAlerts = {
        attach:function (context, settings) {
            $('span.advanced-search-create-alert').click(function () {
                var href = window.location.href.replace(/(http|https):\/\//, '');
                var n = href.indexOf('?');
                href = href.substring(0, n != -1 ? n : href.length);
                var data_source = (href.indexOf('datasource/checkbook_oge') !== -1) ? "checkbook_oge" : "checkbook";
                var page_clicked_from = this.id ? this.id : href.split('/')[1];
                var active_accordion_window = initializeActiveAccordionWindow(page_clicked_from, data_source);

                $('#block-checkbook-advanced-search-checkbook-advanced-search-form').dialog({
                    title:"<span class='create-alert-header'><span class='active'>1. Select Criteria</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>2. Customize Results</span><span class='inactive'>&nbsp;|&nbsp;</span><span class='inactive'>3. Schedule Alerts</span></span>",
                    position:['center', 'center'],
                    width:900,
                    modal:true,
                    autoResize:true,
                    resizable: false,
                    dragStart: function(){
                        $(".ui-autocomplete-input").autocomplete("close")
                    },
                    open: function(){
                    },
                    close: function(){
                        $(".ui-autocomplete-input").autocomplete("close")
                    }
                });

                $('.advanced-search-accordion').accordion({
                    autoHeight: false,
                    active: active_accordion_window
                });

                //Add Create Alert Attributes and styling
                initializeAccordionAttributes('advanced_search_create_alerts');

                /* For oge, Budget, Revenue & Payroll are not applicable and are disabled */
                disableAccordionSections(data_source);

                return false;
            });

            $("#checkbook_advanced_search_result_iframe").load(function(){
                if($(this).contents().height() < 330) {
                    $(this).height('auto');
                    $('div.block-checkbook-advanced-search-checkbook-advanced-search-form').css('padding',0);
                    $('div.create-alert-submit #edit-next-submit').css('display','none');
                    $('div.ui-dialog').css('width',900);
                }
                else {
                    $(this).height($(this).contents().height());
                    $('div.create-alert-submit #edit-next-submit').css('display','inline');
                    $('div.ui-dialog').css('width',965);
                }
                $('.create-alert-submit').css('display','block');
            });

            $.fn.onScheduleAlertClick = function () {

                var scheduleAlertDiv = $(".create-alert-schedule-alert");
                var scheduleAlertUrl = '/alert/transactions/form';
                var ajaxReferralUrl = ($('#checkbook_advanced_search_result_iframe')[0]).attributes['src'];

                /* Add hidden field for ajax referral Url */
                $('input:hidden[name="ajax_referral_url"]').val(ajaxReferralUrl);

                /* Load */
                $.ajax({
                    url: scheduleAlertUrl
                    ,success: function(data) {
                        var html = "<div class='create-alert-schedule-alert'>"+data+"</div>";
                        html = html.replace("<span class='alert-required-field'></span>", "<span class='alert-required-field' style='color:red;'>*</span>");
                        html = html.replace("<span class='alert-required-field'></span>", "<span class='alert-required-field' style='color:red;'>*</span>");
                        html = html.replace("id='alert_instructions'", "style='display:none'");
                        scheduleAlertDiv.html(html);
                    }
                });
            }

            $.fn.onScheduleAlertConfirmClick = function (ajaxReferralUrl) {

                var validateEmail=function(email) {
                    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    return re.test(email);
                };

                var isNumber=function(value) {
                    if ((undefined === value) || (null === value)) {
                        return false;
                    }
                    if (typeof value == 'number') {
                        return true;
                    }
                    return !isNaN(value - 0);
                }

                var alertLabel = $('input[name=alert_label]').val();
                var alertEmail = $('input[name=alert_email]').val();
                var alertMinimumResults = $('input[name=alert_minimum_results]').val();
                var alertMinimumDays = $('select[name=alert_minimum_days]').val();
                var alertEnd = $("input[name='alert_end[date]']").val();
                var dateRegEx = '[0-9]{4,4}-[0-1][0-9]-[0-3][0-9]';

                var alertMsgs = [];
                if(alertLabel.length<1){
                    alertMsgs.push("No Description has been set.");
                }
                if(alertEmail.length<1 || !validateEmail(alertEmail)){
                    alertMsgs.push("No email is entered.");
                }
                if(!isNumber(alertMinimumResults) || alertMinimumResults<1){
                    alertMsgs.push("Minimum results is not a valid number.");
                }
                if(!isNumber(alertMinimumDays) || alertMinimumDays<1){
                    alertMsgs.push("Alert frequency is not valid.");
                }
                if((alertEnd.length > 1 && alertEnd.length != 10) || (alertEnd.length > 1 && !alertEnd.match(dateRegEx))){
                    alertMsgs.push("Expiration Date is not valid.");
                }

                if (alertMsgs.length > 0) {
                    $('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul>' + '<li>' + alertMsgs.join('<li/>') + '</ul></div>');
                } else {
                    $('#errorMessages').html('');

                    var url = '/alert/transactions';
                    var data = {
                        refURL:ajaxReferralUrl,
                        alert_label:alertLabel,
                        alert_email:alertEmail,
                        alert_minimum_results:alertMinimumResults,
                        alert_minimum_days:alertMinimumDays,
                        alert_end:alertEnd,
                        userURL:window.location.href,
                        alert_theme_file:'checkbook_alerts_advanced_search_theme'
                    }
                    $this=$(this);
                    $.get(url,data,function(data){
                        data=JSON.parse(data);
                        if(data.success){
                            $('#block-checkbook-advanced-search-checkbook-advanced-search-form').dialog('close');
                            var dialog = $("#dialog");
                            if ($("#dialog").length == 0) {
                                dialog = $('<div id="dialog" style="display:none"></div>');
                            }
                            dialog.html(data.html);
                            dialog.dialog({position:['center', 'top'],
                                modal:true,
                                width:550,
                                height:80,
                                autoResize:true,
                                resizable: false,
                                dialogClass:'noTitleDialog'
                            });
                        } else{
                            $('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul><li>'+data.errors.join('<li/>')+'</ul></div>');
                        }
                    });
                }
            }

            $(window).resize(function() {
                if (inIframe() && document.URL.indexOf("/createalert") >= 0) {
                    $(document).ajaxComplete(function() {
                        $('#checkbook_advanced_search_result_iframe', window.parent.document).css('height', $('#checkbook_advanced_search_result_iframe', window.parent.document).contents().height()-100);
                        $('div.block-checkbook-advanced-search-checkbook-advanced-search-form', window.parent.document).css('height', 800);
                        $('div.create-alert-customize-results', window.parent.document).css('height', 'auto');
                    });
                }
            });
        }
    };

    /*
    * Function to tell if the current window is inside an iFrame
    * Returns true if the window is in an iFrame, else false
    */
    function inIframe () {
        try {
            return window.self !== window.top;
        } catch (e) {
            return true;
        }
    }
    Drupal.behaviors.bottomContainerShowHide = {
        attach:function (context, settings) {

            $('.bottomContainerToggle', context).toggle(
                function (event) {
                    event.preventDefault();
                    if ($('.bottomContainer').html().length <= 10) {
                        var callBackURL = '';
                        var expandBottomContURL = getParameterByName("expandBottomContURL");
                        if (expandBottomContURL){
                        	callBackURL = expandBottomContURL + "?appendScripts=true";
                        } else{
                        	callBackURL = this.href + window.location.pathname + "?appendScripts=true";
                        }


                        $('.bottomContainer').toggle();
                        $('.bottomContainer').html("<img style='float:right' src='/sites/all/themes/checkbook/images/loading_large.gif' title='Loading Data...'/>");
                        $.cookie("showDetails","enable", { path: '/' });
                        $('.bottomContainerToggle').toggle();
                        $.ajax({
                            url:callBackURL,
                            success:function (data) {
                                $('.bottomContainer').html(data);
                                $('.bottomContainerToggle').html("Hide Details &#171;");
                                $('.bottomContainerToggle').toggle();
                            }
                        });
                    } else {
                        $('.bottomContainer').toggle();
                        $('.bottomContainerToggle').html("Hide Details &#171;");
                    }
                },
                function (event) {
                    event.preventDefault();
                    $('.bottomContainer').toggle();
                    $('.bottomContainerToggle').html("Show Details &#187;");
                    $.cookie("showDetails","disable", { path: '/' });
                }

            );
            if (getParameterByName("expandBottomCont") ||getParameterByName("expandBottomContURL") || $.cookie("showDetails") == "enable" ) {
            	$.cookie("showDetails","enable", { path: '/' });
                $('.bottomContainerToggle', context).click();
            }

        }
    };


    $('.bottomContainerReload').live("click",
        function (event) {
            event.preventDefault();
            
            var reloadURL =  window.location.pathname + "?expandBottomContURL=" +  this.getAttribute("href") ;
            window.location = reloadURL;


           /* $('.bottomContainer').html("Loading Data");
            $.ajax({
                url:callBackURL,
                success:function (data) {
                    $('.bottomContainer').html(data);
                    $(".clickOnLoad").click();
                }
            });
*/
        }
    );

    Drupal.behaviors.loadParentWindow = {
        attach:function (context, settings) {
            if (!$('body').hasClass('newwindow')) {
                $('span.loadParentWindow').live("click", function () {
                    var url = $(this).attr('href');
                    var pWin = window.opener;
                    if (pWin && !pWin.closed) {
                        if (url != undefined) {
                            pWin.focus();
                            pWin.location.href = url;
                        }
                        return false;
                    } else {
                        alert('Parent Window is closed.');
                    }
                });
            }
        }
    }

    Drupal.behaviors.viewHide = {
        attach:function (context, settings) {
            $('.bottom-section').hide();
            var deferredRender = settings.deferredRender;
            $('.toggle-visibility', context).click(function (event) {
                event.preventDefault();
                if ($('.bottom-section').css('display') == 'none') {
                    $('.bottom-section').show();
                    $('.toggle-visibility').text('Hide Details');
                    var i = 0;
                    for (i; i < deferredRender.length; i++) {
                        if (deferredRender[i].type == 'datatable') {
                            var options = eval('(' + deferredRender[i].dataTableOptions + ')');
                            window['oTable' + deferredRender[i].id] = $('#table_' + deferredRender[i].id).dataTable(options);
                            window['oTable' + deferredRender[i].id].node_id = deferredRender[i].nodeId;
                            window['oTable' + deferredRender[i].id].initAjaxSource = deferredRender[i].initAjaxSource;
                            eval(deferredRender[i].customJS);
                        } else if (deferredRender[i].type == 'highchart') {
                            var options = eval('(' + deferredRender[i].chartConfig + ')');
                            var callback = deferredRender[i].callback;
                            if (callback) {
                                window['chart' + deferredRender[i].id] = new Highcharts.Chart(options, function (chart) {
                                    callback
                                });
                            } else {
                                window['chart' + deferredRender[i].id] = new Highcharts.Chart(options);
                            }
                        } else if (deferredRender[i].type == 'highstock') {
                            var options = eval('(' + deferredRender[i].chartConfig + ')');
                            var callback = deferredRender[i].callback;
                            if (callback) {
                                window['chart' + deferredRender[i].id] = new Highcharts.StockChart(options, function (chart) {
                                    callback
                                });
                            } else {
                                window['chart' + deferredRender[i].id] = new Highcharts.StockChart(options);
                            }
                        }
                    }
                } else if ($('.bottom-section').css('display') == 'block') {
                    $('.bottom-section').hide();
                    $('.toggle-visibility').text('View Details');
                    var i = 0;
                    for (i; i < deferredRender.length; i++) {
                        if (deferredRender[i].type == 'datatable') {
                            window['oTable' + deferredRender[i].id].fnDestroy();
                            $('[id^="movescript"]').remove();
                        } else if (deferredRender[i].type == 'highchart' || deferredRender[i].type == 'highstock') {
                            window['chart' + deferredRender[i].id].destroy();
                        }
                    }
                }
            });
        }
    }
    
/* prevent click on new window links in header area */
$(".newwindow .contract-details-heading a").click(function (e) {
    e.preventDefault();
});

$('.expandCollapseWidget').live("click",
       function (event) {
           var toggled = $(this).data('toggled');
           $(this).data('toggled', !toggled);

           event.preventDefault();
           oTable = $(this).parent().prev().find('.dataTable').dataTable();
           var text ="";
           if (!toggled) {
                oTable.fnSettings().oInit.expandto150 = true;
                oTable.fnSettings().oInit.expandto5 = false;
                text = "<img src='/sites/all/themes/checkbook/images/close.png'>";
                $(this).parent().parent().find('.hideOnExpand').hide();

           }else{
                oTable.fnSettings().oInit.expandto5 = true;
                oTable.fnSettings().oInit.expandto150 = false;
                text = "<img src='/sites/all/themes/checkbook/images/open.png'>";
                var place = $('#'+oTable.fnSettings().sInstance + '_wrapper').parent().parent().attr('id');
                document.getElementById(place).scrollIntoView();
                $(this).parent().parent().find('.hideOnExpand').show();
           }
            oTable.fnDraw();
            $(this).html(text);
       }
   );

$('.simultExpandCollapseWidget').live("click",
       function (event) {
           var toggled = $(this).data('toggled');
           var oTable22 =  null;
           var oTable23 =  null;
           var oTable29 =  null;
           var oElement22 =  null;
           var oElement23 =  null;
           var oElement29 =  null;

           if($('#node-widget-22 a.simultExpandCollapseWidget').parent().prev().find('.dataTable') != null){
                oTable22 = $('#node-widget-22 a.simultExpandCollapseWidget').parent().prev().find('.dataTable').dataTable() ;
                oElement22 = $('#node-widget-22 a.simultExpandCollapseWidget');
                oElement22.data('toggled', !toggled);
           }
           if($('#node-widget-23 a.simultExpandCollapseWidget').parent().prev().find('.dataTable') != null){
                oTable23 = $('#node-widget-23 a.simultExpandCollapseWidget').parent().prev().find('.dataTable').dataTable();
                oElement23 = $('#node-widget-23 a.simultExpandCollapseWidget');
                oElement23.data('toggled', !toggled);
           }
           if($('#node-widget-29 a.simultExpandCollapseWidget').parent().prev().find('.dataTable') != null){
                oTable29 = $('#node-widget-29 a.simultExpandCollapseWidget').parent().prev().find('.dataTable').dataTable();
                oElement29 = $('#node-widget-29 a.simultExpandCollapseWidget');
                oElement29.data('toggled', !toggled);
           }

           event.preventDefault();
           var text ="";

           if (!toggled) {
               if(oTable22.size() > 0){
                    oTable22.fnSettings().oInit.expandto150 = true;
                    oTable22.fnSettings().oInit.expandto5 = false;
               }
               if(oTable23.size() > 0){
                    oTable23.fnSettings().oInit.expandto150 = true;
                    oTable23.fnSettings().oInit.expandto5 = false;
               }
               if(oTable29.size() > 0){
                    oTable29.fnSettings().oInit.expandto150 = true;
                    oTable29.fnSettings().oInit.expandto5 = false;
               }

                text = "<img src='/sites/all/themes/checkbook/images/close.png'>";
               if(oElement22 != null){
                    oElement22.parent().parent().find('.hideOnExpand').hide();
               }
               if(oElement23 != null){
                    oElement23.parent().parent().find('.hideOnExpand').hide();
               }

               if(oElement29 != null){
                    oElement29.parent().parent().find('.hideOnExpand').hide();
               }

           }else{
               if(oTable22.size() > 0){
                    oTable22.fnSettings().oInit.expandto5 = true;
                    oTable22.fnSettings().oInit.expandto150 = false;
                    var place22 = $('#'+oTable22.fnSettings().sInstance + '_wrapper').parent().parent().attr('id');
                    document.getElementById(place22).scrollIntoView();
               }
               if(oTable23.size() > 0){
                    oTable23.fnSettings().oInit.expandto5 = true;
                    oTable23.fnSettings().oInit.expandto150 = false;
                    var place23 = $('#'+oTable23.fnSettings().sInstance + '_wrapper').parent().parent().attr('id');
                    document.getElementById(place23).scrollIntoView();
               }
               if(oTable29.size() > 0){
                    oTable29.fnSettings().oInit.expandto5 = true;
                    oTable29.fnSettings().oInit.expandto150 = false;
                    var place29 = $('#'+oTable29.fnSettings().sInstance + '_wrapper').parent().parent().attr('id');
                    document.getElementById(place29).scrollIntoView();
               }

                text = "<img src='/sites/all/themes/checkbook/images/open.png'>";
                if(oElement22 != null){
                    oElement22.parent().parent().find('.hideOnExpand').show();
                }

                if(oElement23 != null){
                    oElement23.parent().parent().find('.hideOnExpand').show();
                }

                if(oElement29 != null){
                    oElement29.parent().parent().find('.hideOnExpand').show();
                }

           }
            if(oTable22.size() > 0){
                oTable22.fnDraw();
                oElement22.html(text);
            }
            if(oTable23.size() > 0){
                oTable23.fnDraw();
                oElement23.html(text);
            }
            if(oTable29.size() > 0){
                oTable29.fnDraw();
                oElement29.html(text);
            }
       }
   );




}(jQuery));


/**
 * TO toggle the display
 * @param {String} id  ID
 */
function toggleDisplay(id) {
    if (document.getElementById) {
        obj = document.getElementById(id);
        if (obj.style.display == 'none') {
            obj.style.display = '';
        } else {
            obj.style.display = 'none';
        }
    }
}

/**
 * To change HTML text based on Div Id.
 *
 * @param {String} divId  ID
 * @param {String} divText Search Text
 */
function changeLinkText(divId, divText) {
    var existingText = document.getElementById(divId).innerHTML;
    if (existingText.indexOf('Show Only Top 5') > -1) {
        document.getElementById(divId).innerHTML = 'Show more ' + divText + ' &#187;';
    } else {
        document.getElementById(divId).innerHTML = 'Show Only Top 5 ' + divText + ' &#171;';
    }
}

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
    value = replaceAllOccurrences("/","__", value);
    value = replaceAllOccurrences("%2F",encodeURIComponent("__"), value);
    
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

/** Replacing all occurrences of a pattern in a string
 * @param {String} find pattern to be replaced
 * @param {string} replace new pattern
 * @param {string} str subject
 */
function replaceAllOccurrences(find, replace, str) {
  return str.replace(new RegExp(find, 'g'), replace);
}


function custom_number_format(number) {
    if (number == null || number == '') {
        return '$0.00';
    }

    var decimal_digits = 2;
    var prefix = '$';

    var thousands = 1000;
    var millions = thousands * 1000;
    var billions = millions * 1000;
    var trillions = billions * 1000;
    var formattedNumber = '';

    var absNumber = Math.abs(number);

    if (absNumber >= trillions) {
        formattedNumber = prefix + addCommas((absNumber / trillions).toFixed(decimal_digits)) + 'T';
    }
    else if (absNumber >= billions) {
        formattedNumber = prefix + addCommas((absNumber / billions).toFixed(decimal_digits)) + 'B';
    }
    else if (absNumber >= millions) {
        formattedNumber = prefix + addCommas((absNumber / millions).toFixed(decimal_digits)) + 'M';
    }
    else if (absNumber >= thousands) {
        formattedNumber = prefix + addCommas((absNumber / thousands).toFixed(decimal_digits)) + 'K';
    }
    else {
        formattedNumber = prefix + addCommas(absNumber.toFixed(decimal_digits));
    }
    return (number < 0) ? ('-' + formattedNumber) : formattedNumber;
}

function addCommas(nStr){
    nStr += '';
    c = nStr.split(',');
    nStr = c.join('');
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function reloadExpandCollapseWidget(context,aoData){
    $length = null;
    if(context.fnSettings().oInit.expandto150){
        $length = 150;
    }else if(context.fnSettings().oInit.expandto5){
        $length = 5;
    }

    if($length){
        for (var i=0; i<aoData.length; i++) {
            if (aoData[i].name == "iDisplayLength") {
                aoData[i].value = $length;break;
            }
        }
    }
}

//Disable if zero in 

Drupal.behaviors.disableClickTopNav = {
    attach: function(context, settings){
        jQuery('a.noclick').click(function(){
            return false;
        });
       
    }
}

// end of disabling code


