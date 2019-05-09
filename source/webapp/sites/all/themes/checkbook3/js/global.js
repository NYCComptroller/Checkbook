jQuery(document).ready(function ($) {
    //New Features Menu
    $('#nice-menu-1 li.menu-path-node-975 a').addClass('gridpopup');
    $.ajax({
        url: '/new-features/get_status',
        success: function (data) {
            if (data !== null)
                setNewFeaturesMenuColor(data.toString());
        }
    });
    $("#new_features").click(function () {
        var status = '';
        if ($("#new_features").val() === 'Enable New Features Link')
            status = 'enable';
        else
            status = 'disable';
        $.ajax({
            url: '/new-features/' + status,
            success: function (data) {
                if (data !== null)
                    setNewFeaturesMenuColor(data.toString());
            }
        });
    });

    // remove all empty # hrefs
    $('a').each(function(){if('#' === $(this).attr('href')){jQuery(this).removeAttr("href");}});

    function setNewFeaturesMenuColor(status) {
        if (status === 'enable') {
            $('#nice-menu-1 li.menu-path-node-975 a').removeClass('disabled');
            $('#nice-menu-1 li.menu-path-node-975 a').addClass('enabled');
            $("#new_features").val('Disable New Features Link');
        } else {
            $('#nice-menu-1 li.menu-path-node-975 a').removeClass('enabled');
            $('#nice-menu-1 li.menu-path-node-975 a').addClass('disabled');
            $("#new_features").val('Enable New Features Link');
        }
    }

    //Altering CSS for slider pager for pie charts on contracts page
    if (jQuery(".slider-pager a").length == 2) {
        jQuery("div.slider-pager").addClass('pieSlider');
    } else {
        jQuery("div.slider-pager").removeClass('pieSlider');
    }

    if (!getParameterByName("expandBottomCont") && !getParameterByName("expandBottomContURL")) {
        jQuery('.bottomContainerToggle').click();
        jQuery('.bottomContainer').show();
    }
    if (parseInt($.browser.version, 10) == 7) {
        $("#page").addClass("ie");
    } else {
        $("#page").addClass("not-ie");
    }

    //create tool tops for featured dashboard title
    var featureddashboard = function () {
        // add processed class to all anchors after ajax
        $(".featured-dashboard-title a").each(function () {
            if (!$(this).hasClass('processed')) {
                $('<div class="toolTip">' + $(this).attr('alt') + '<div class="bottom"></div></div>').insertBefore(this);
                $(this).addClass('processed');
            }
        });
        $('.featured-dashboard-title').hover(
            function () {
                $(this).find('.toolTip').css('display', 'block')
            },
            function () {
                $(this).find('.toolTip').css('display', 'none');
            }
        );
    }


    //check if ajax is being fired on the page or not
    if ($.active > 0) {
        $(document).ajaxComplete(function () {
            featureddashboard();
        });
    }
    else {
        featureddashboard();
    }
});


(function ($) {

//hover show/hide list for mwbe menu item
    Drupal.behaviors.hoveOverMWBE = {
        attach: function (context, settings) {
            $(".drop-down-menu-triangle").hover(function () {
                $(this).closest(".mwbe").addClass("hover");
                $(this).closest(".mwbe").find('.main-nav-drop-down').css('display', 'block');

            }, function () {
                $(this).closest(".mwbe").removeClass("hover");
                $(this).closest(".mwbe").find('.main-nav-drop-down').css('display', 'none');
            });
        }
    };


    //set classes for sortable columns in DataTables
    var newclasses = {
        'sSortAsc': 'ui-state-default sorting_asc',
        'sSortDesc': 'ui-state-default sorting_desc',
        'sSortable': 'ui-state-default sortable',
        'sSortJUI': 'css_right ui-icon ui-icon-triangle-2-n-s'
    };

    $.fn.dataTableExt.oJUIClasses = $.extend({}, $.fn.dataTableExt.oJUIClasses, newclasses);

    $.fn.toggleText = function (value1, value2) {
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
        attach: function (context, settings) {

            // Add active class to current menu item
            if ($('.block-nice-menus li a').hasClass('active')) {
                $('.block-nice-menus li a.active').parent().addClass('active');
            }
            // Make 'Employment' menu item in footer bold
            $('#block-menu-menu-news-room li:nth-child(4)').css('font-weight', 'bold');

            // Year Dropdown
            $('#year_list').chosen({disable_search_threshold: 50});

            // Fiscal Year Dropdown
            $('#fiscal_year_list').chosen({disable_search_threshold: 50});

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
            $('#nycha-contracts-landing .grid-12 .inside').filter(':first').after(pager);
            $('#nycha-spending-landing .grid-12 .inside').filter(':first').after(pager);

            var highSlides = '#nycha-spending-landing .grid-12 .inside, #nycha-contracts-landing .grid-12 .inside, #nyc-spending .grid-12 .inside, #nyc-payroll .grid-12 .inside, #nyc-contracts .grid-12 .inside,#nyc-budget .grid-12 .inside, #agency-budget .grid-12 .inside, #agency-expenditure-categories .grid-12 .inside, #nyc-expenditure-categories .grid-12 .inside,#nyc-contracts-revenue-landing .grid-12 .inside,#nyc-revenue-pending-contracts .grid-12 .inside,#nyc-expense-pending-contracts .grid-12 .inside,#dept-budget .grid-12 .inside,#nyc-revenue .grid-12 .inside';

            if ($(highSlides).filter(":first").length > 0) {
            $(highSlides).filter(":first")
              .once('styleOverrides')
              .cycle({
                slideExpr: '.slider-pane',
                fx: 'fade',
                timeout: 45000,
                height: '315px',
                width: '100%',
                fit: 1,
                pause: true,
                pager: '.slider-pager'
              });
          }

            $('.chart-title').css("display", "block");

            //Spotlight Videos
            if ($('#video-list-pager').children().length == 0)
                if ($('#allVideoList').length > 0) {
                    $('#allVideoList')
                        .after('<div id="video-list-pager" class="spotlight-video-pager"></div>')
                        .cycle({
                            fx: 'fade',
                            timeout: 45000,
                            height: '315px',
                            width: '100%',
                            fit: 1,
                            speed: 1000,
                            pager: '#video-list-pager',
                            prev: '#prev1',
                            next: '#next1'
                        });
                }

            var iframeClick = function () {
                var windowLostBlur = function () {

                    //if the current iframe is hovered, flag it as blurred and pause the slider
                    if ($('#allVideoList div.mouseenter').length > 0) {
                        $(window).focus();
                        $('#allVideoList div.mouseenter').each(function () {
                            $(this).removeClass('mouseenter');
                            $(this).addClass('blur');
                            $('#allVideoList').cycle('pause');
                        });
                        $(window).blur();
                    }
                };
                $(window).focus();
                $('div.video-container iframe').mouseenter(function () {

                    //if the current iframe is not blurred, flag it as mouseenter and pause the slider
                    if (!$(this).closest('div.video-container').hasClass('blur')) {
                        $(this).closest('div.video-container').removeClass('mouseleave');
                        $(this).closest('div.video-container').addClass('mouseenter');
                        $('#allVideoList').cycle('pause');
                    }
                });
                $('div.video-container iframe').mouseleave(function () {

                    //if the current iframe is not blurred, flag it as mouseleave and resume the slider
                    if (!$(this).closest('div.video-container').hasClass('blur')) {
                        $(this).closest('div.video-container').removeClass('mouseenter');
                        $(this).closest('div.video-container').addClass('mouseleave');
                        $('#allVideoList').cycle('resume');
                    }
                });
                $(window).blur(function () {
                    windowLostBlur();
                });
            };
            iframeClick();

            //if non-active sliders are clicked, resume the sliders & reset the iframe/video states
            $('#video-list-pager a').click(function () {
                $('#allVideoList').cycle('resume');
                $('div.video-container').each(function () {
                    $(this).removeClass('mouseenter');
                    $(this).removeClass('mouseleave');
                    if ($(this).hasClass('blur')) {
                        $(this).removeClass('blur');
                        var video = $(this).find('iframe');
                        resetVideo(video);
                    }
                });
            });

            /*
             Given an iframe with a video, this function will reset the video by resetting the src
             */
            var resetVideo = function (video) {
                var video_source = $(video).attr("src");
                $(video).attr("src", "");
                $(video).attr("src", video_source);
            };

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
            var $dialog = $('div.status-codes').dialog({
                autoOpen: false,
                title: 'Status Codes and Messages',
                width: 800,
                modal: true
            });
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
        attach: function (context, settings) {
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
        attach: function (context, settings) {
            $('.agency-list-open span, .agency-list-open div b').once('agenciesListOpen').click(function () {
                if ($(this).attr("id") == "other-agency-list-open") {
                    $('.all-agency-list-content').slideUp(300);
                } else {
                    $('.other-agency-list-content').slideUp(300);
                }
                $(this).parent().parent().find('.agency-list-content').slideToggle(300);
                $(this).toggleClass('open');
                $(this).parent().find(' div b').toggleClass('open');
            });

            $('.agency-list-close a').once('agenciesListClose').click(function () {
                $('.agency-list-content').slideUp(300);
                $('.agency-list-open div b').removeClass('open');
            });

            if ($('#agency-list-pager1').children().length == 0)
                if ($('#allAgenciesList').length > 0) {
                    $('#allAgenciesList')
                        .after('<div id="agency-list-pager1" class="agency-list-pager"></div>')
                        .cycle({
                            fx: 'none',
                            speed: 1000,
                            timeout: 0,
                            pause: true,
                            pauseOnPagerHover: 0,
                            pager: '#agency-list-pager1',
                            prev: '#prev1',
                            next: '#next1'
                        });
                }

            if ($('#agency-list-pager2').children().length == 0)
                if ($('#otherAgenciesList').length > 0) {
                    $('#otherAgenciesList')
                        .after('<div id="agency-list-pager2" class="agency-list-pager"></div>')
                        .cycle({
                            fx: 'none',
                            speed: 1000,
                            timeout: 0,
                            pause: true,
                            pauseOnPagerHover: 0,
                            pager: '#agency-list-pager2',
                            prev: '#prev2',
                            next: '#next2'
                        });
                }
        }
    };

    // Employee Payroll Transactions scrolling window
    Drupal.behaviors.employeePayrollTransactions = {
        attach: function (context, settings) {
            if ($('#payroll-emp-trans-table > tbody').length > 0) {
                $('#payroll-emp-trans-table > tbody')
                    .cycle({
                        fx: 'none',
                        speed: 1000,
                        timeout: 0,
                        pause: true,
                        pauseOnPagerHover: 0,
                        pager: '#payroll-emp-trans-table-pager',
                        prev: '#payroll-emp-trans-table-up',
                        next: '#payroll-emp-trans-table-down'
                    });
            }
        }
    };

    Drupal.behaviors.custompager = {
        attach: function (context, settings) {
            $('.customPager ul li a').live("click", function (e) {
                e.preventDefault();
                var input = $(this);
                urlLink = input.attr('href');
                if (urlLink != undefined) {
                    $.ajax({
                        url: urlLink,
                        success: function (data) {
                            $('#contListContainerNew').html('');
                            $('#contListContainerNew').html(data);
                        }
                    });
                }

                return false;

            })
        }
    };

    Drupal.behaviors.viewAllPopup = {
        attach: function (context, settings) {
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
                            position: "top",
                            width: parentWidth,
                            modal: true,
                            open: function (event, ui) { //If there are DataTables with deferredRender = TRUE, render them on open
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
                            close: function (event, ui) { //Destroy a deferredRender table if it exists and remove any script tags created by movescripts()
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
        attach: function (context, settings) {
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
        attach: function (context, settings) {
            if ($('body').hasClass('gridview') || ($('body').hasClass('newwindow') && !($('body').hasClass('page-new-features')))) {
                $('body').delegate('a', 'click', function () {
                    if ($(this).hasClass('subContractViewAll') || $(this).hasClass('showHide') || $(this).hasClass('logo') || $(this).attr('rel') == 'home' || $(this).hasClass('enable-link'))
                        return true;
                    else
                        return false;
                });
            }
        }
    }

    Drupal.behaviors.helpPopup = {
        attach: function (context, settings) {
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

    Drupal.behaviors.exportTransactions = {
        attach: function (context, settings) {
            $('span.export').die().live("click", function () {

                var dialog = $("#dialog");
                if ($("#dialog").length == 0) {
                    dialog = $('<div id="dialog" style="display:none"></div>');
                }


                var oSettings = $('#table_' + $(this).attr('exportid')).dataTable().fnSettings();
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
                        dialog.dialog({
                            position: "center",
                            modal: true,
                            title: 'Download Transactions Data',
                            dialogClass: "export",
                            width: 700,
                            resizable: false,
                            buttons: {
                                "Download Data": function () {
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

                                        var validFrom = ((String(rangefrom).search(/^\s*(\+|-)?\d+\s*$/) != -1) && (parseFloat(rangefrom) == parseInt(rangefrom)) && parseInt(rangefrom) >= 1 && parseInt(rangefrom) <= maxPages);
                                        var validTo = ((String(rangeto).search(/^\s*(\+|-)?\d+\s*$/) != -1) && (parseFloat(rangeto) == parseInt(rangeto)) && parseInt(rangeto) >= 1 && parseInt(rangeto) <= maxPages);

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


                                    // var frmtfilter = $('input[name=frmt]:checked').val();
                                    // if (frmtfilter == null) {
                                    //     alertMsgs.push('Format must be selected');
                                    // }

                                    if (alertMsgs.length > 0) {
                                        $('#errorMessages').html('Below errors must be corrected:<div class="error-message"><ul>' + '<li>' + alertMsgs.join('<li/>') + '</ul></div>');
                                    } else {
                                        $('#errorMessages').html('');

                                        var url = '/export/transactions';
                                        var inputs = "<input type='hidden' name='refURL' value='" + oSettings.sAjaxSource + "'/>"
                                            + "<input type='hidden' name='iDisplayStart' value='" + startRecord + "'/>"
                                            + "<input type='hidden' name='iDisplayLength' value='" + recordLimit + "'/>"
                                        ;

                                        if (oSettings.oFeatures.bSort !== false) {
                                            var iCounter = 0;

                                            aaSort = (oSettings.aaSortingFixed !== null) ?
                                                oSettings.aaSortingFixed.concat(oSettings.aaSorting) :
                                                oSettings.aaSorting.slice();

                                            for (i = 0; i < aaSort.length; i++) {
                                                aDataSort = oSettings.aoColumns[aaSort[i][0]].aDataSort;

                                                for (j = 0; j < aDataSort.length; j++) {
                                                    inputs = inputs + "<input type='hidden' name='iSortCol_" + iCounter + "' value='" + aDataSort[j] + "'/>";
                                                    inputs = inputs + "<input type='hidden' name='sSortDir_" + iCounter + "' value='" + aaSort[i][1] + "'/>";
                                                    iCounter++;
                                                }
                                            }
                                            inputs = inputs + "<input type='hidden' name='iSortingCols' value='" + iCounter + "'/>";
                                        }

                                        $('<form action="' + url + '" method="get">' + inputs + '</form>').appendTo('body').submit().remove();

                                        $('#dialog #export-message').addClass('disable_me');
                                        $('.ui-dialog-titlebar').addClass('disable_me');
                                        $('.ui-dialog-buttonset').addClass('disable_me');
                                        $('#dialog #dialog').addClass('disable_me');
                                        $('#loading_gif').show();
                                        $('#loading_gif').addClass('loading_bigger_gif');

                                        setTimeout(function () {
                                            $('#dialog #export-message').removeClass('disable_me');
                                            $('.ui-widget-header').removeClass('disable_me');
                                            $('.ui-dialog-buttonset').removeClass('disable_me');
                                            $('#dialog #dialog').removeClass('disable_me');
                                            $('#loading_gif').hide();
                                            $('#loading_gif').removeClass('loading_bigger_gif');
                                        }, 3000);
                                    }
                                },
                                "Cancel": function () {
                                    $(this).dialog('close');
                                }
                            },
                            open: function(){
                              $('.export-range-input').click(function(){
                                $('#export-dc-range').attr("checked", "checked").trigger("click");
                              });
                            }
                        });
                    }
                );
                return false;
            });
        }
    };

    Drupal.behaviors.exportGridTransactions = {
        attach: function (context, settings) {
            $('span.grid_export').die().live("click", function () {
                var nodeId = $(this).attr('exportid');
                var oSettings = $('#table_' + nodeId).dataTable().fnSettings();

                var url = '/export/grid/transactions/'+ nodeId;
                var inputs = "<input type='hidden' name='refURL' value='" + (oSettings.sAjaxSource != null ? oSettings.sAjaxSource : oSettings.oInit.sAltAjaxSource) + "'/>"
                    + "<input type='hidden' name='iDisplayStart' value='" + oSettings._iDisplayStart + "'/>"
                    + "<input type='hidden' name='iDisplayLength' value='" + oSettings._iDisplayLength + "'/>"
                    + "<input type='hidden' name='node' value='" + nodeId + "'/>"
                ;

                if (oSettings.oFeatures.bSort !== false) {
                    var iCounter = 0;

                    aaSort = (oSettings.aaSortingFixed !== null) ?
                        oSettings.aaSortingFixed.concat(oSettings.aaSorting) :
                        oSettings.aaSorting.slice();

                    for (i = 0; i < aaSort.length; i++) {
                        aDataSort = oSettings.aoColumns[aaSort[i][0]].aDataSort;

                        for (j = 0; j < aDataSort.length; j++) {
                            inputs = inputs + "<input type='hidden' name='iSortCol_" + iCounter + "' value='" + aDataSort[j] + "'/>";
                            inputs = inputs + "<input type='hidden' name='sSortDir_" + iCounter + "' value='" + aaSort[i][1] + "'/>";
                            iCounter++;
                        }
                    }
                    inputs = inputs + "<input type='hidden' name='iSortingCols' value='" + iCounter + "'/>";
                }
                $('<form action="' + url + '" method="get">' + inputs + '</form>').appendTo('body').submit().remove();

            });
        }
    };

    // MWBE Agency Summary export sorting
    Drupal.behaviors.exportmwbeAgencySummary = {
        attach: function (context, settings) {
            $('span.summary_export').die().live("click", function () {

                var oSettings = $('#grading_table').dataTable().fnSettings();
                var url = '';
                var url_path = location.pathname.split("/");
                for (var i = 0; i < url_path.length; i++) {
                    if (url_path[i] == 'mwbe_agency_grading') {
                        url += 'mwbe_agency_grading_csv/';
                    }
                    else {
                        url += url_path[i] + '/';
                    }
                }

                var inputs = "<input type='hidden' name='iDisplayStart' value='" + oSettings._iDisplayStart + "'/>"
                    + "<input type='hidden' name='iDisplayLength' value='" + oSettings._iDisplayLength + "'/>"
                ;

                if (oSettings.oFeatures.bSort !== false) {
                    var iCounter = 0;

                    aaSort = (oSettings.aaSortingFixed !== null) ?
                        oSettings.aaSortingFixed.concat(oSettings.aaSorting) :
                        oSettings.aaSorting.slice();

                    for (i = 0; i < aaSort.length; i++) {
                        aDataSort = oSettings.aoColumns[aaSort[i][0]].aDataSort;

                        for (j = 0; j < aDataSort.length; j++) {
                            inputs = inputs + "<input type='hidden' name='iSortCol_" + iCounter + "' value='" + aDataSort[j] + "'/>";
                            inputs = inputs + "<input type='hidden' name='sSortDir_" + iCounter + "' value='" + aaSort[i][1] + "'/>";
                            iCounter++;
                        }
                    }
                    inputs = inputs + "<input type='hidden' name='iSortingCols' value='" + iCounter + "'/>";
                }
                $('<form action="' + url + '" method="get">' + inputs + '</form>').appendTo('body').submit().remove();

            });
        }
    };

    Drupal.behaviors.bottomContainerShowHide = {
        attach: function (context, settings) {

            $('.bottomContainerToggle', context).toggle(
                function (event) {
                    event.preventDefault();
                    if ($('.bottomContainer').html().length <= 10) {
                        var callBackURL = '';
                        var expandBottomContURL = getParameterByName("expandBottomContURL");
                        if (expandBottomContURL) {
                            callBackURL = expandBottomContURL + "?appendScripts=true";
                        } else {
                            callBackURL = this.href + window.location.pathname + "?appendScripts=true";
                        }

                        $('.bottomContainer').toggle();
                        $('.bottomContainer').html("<img style='float:right' src='/sites/all/themes/checkbook/images/loading_large.gif' title='Loading Data...'/>");
                        $('.bottomContainerToggle').toggle();
                        $.ajax({
                            url: callBackURL,
                            success: function (data) {
                                $('.bottomContainer').html(data);
                                // $('.bottomContainerToggle').html("Hide Details &#171;");
                                $('.bottomContainerToggle').html("");
                                $('.bottomContainerToggle').toggle();
                                $('.first-item').trigger('click');
                            }
                        });
                    } else {
                        $('.bottomContainer').toggle();
                        // $('.bottomContainerToggle').html("Hide Details &#171;");
                        $('.bottomContainerToggle').html("");
                    }
                },
                function (event) {
                    event.preventDefault();
                    $('.bottomContainer').toggle();
                    //  $('.bottomContainerToggle').html("Show Details &#187;");
                    $('.bottomContainerToggle').html("");
                }
            );
            if (getParameterByName("expandBottomCont") || getParameterByName("expandBottomContURL")) {
                $('.bottomContainerToggle', context).click();
            }

        }
    };

    Drupal.behaviors.bottomContainerReload = {
      attach: function (context, settings) {
        $(document).ajaxStop(function() {
          $('.bottomContainerReload')
            .not('.altered')
            .each(function(){
              $(this).addClass('altered');
              $(this).attr('href', window.location.pathname + "?expandBottomContURL=" + $(this).attr('href'));
            });
        })
      }
    };

    Drupal.behaviors.loadParentWindow = {
        attach: function (context, settings) {
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
    };

    Drupal.behaviors.viewHide = {
        attach: function (context, settings) {
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
    };

//Disable if zero in
    Drupal.behaviors.disableClickTopNav = {
        attach: function (context, settings) {
            $('a.noclick').click(function () {
                return false;
            });

        }
    };

// end of disabling code


//Datafeeds form freeze while loading
    Drupal.behaviors.datafeedspagefreeze = {
        attach: function (context, settings) {
            function formfreeze_datafeeds(e) {
                setTimeout(function () {
                    $("#checkbook-datafeeds-data-feed-wizard").addClass('transparent');
                    $(".data-feeds-sidebar").addClass('transparent');
                    $("#checkbook-datafeeds-data-feed-wizard").addClass('disable_me');
                    $("#checkbook-datafeeds-tracking-form").addClass('disable_me');
                    $('.data-feeds-wizard a').addClass('disable_me');
                    $('.data-feeds-wizard li').addClass('disable_me');
                    $("#checkbook-datafeeds-data-feed-wizard :input").attr("disabled", "disabled");
                    $("#checkbook-datafeeds-tracking-form :input").attr("disabled", "disabled");
                }, 1);
            }

            function gif_rotator(e) {
                setTimeout(function () {
                    $("#rotator").css('display', 'block');
                    $("#rotator").addClass('loading_bigger_gif');
                }, 1);
            }

            // Datafeeds form disable
            $("#edit-type-next").click(formfreeze_datafeeds);
            $("#edit-prev").click(formfreeze_datafeeds);
            $("#edit-feeds-revenue-next").click(formfreeze_datafeeds);
            $("#edit-feeds-payroll-next").click(formfreeze_datafeeds);
            $("#edit-feeds-spending-next").click(formfreeze_datafeeds);
            $("#edit-feeds-contract-next").click(formfreeze_datafeeds);
            $("#edit-feeds-budget-next").click(formfreeze_datafeeds);
            $("#edit-confirm").click(formfreeze_datafeeds);
            $("#edit-cancel").click(formfreeze_datafeeds);

            //loading gif
            $("#edit-type-next").click(gif_rotator);
            $("#edit-prev").click(gif_rotator);
            $("#edit-feeds-revenue-next").click(gif_rotator);
            $("#edit-feeds-payroll-next").click(gif_rotator);
            $("#edit-feeds-spending-next").click(gif_rotator);
            $("#edit-feeds-contract-next").click(gif_rotator);
            $("#edit-feeds-budget-next").click(gif_rotator);
            $("#edit-confirm").click(gif_rotator);
        }
    };


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
            var text = "";
            if (!toggled) {
                oTable.fnSettings().oInit.expandto150 = true;
                oTable.fnSettings().oInit.expandto5 = false;
                text = "<img src='/sites/all/themes/checkbook/images/close.png'>";
                $(this).parent().parent().find('.hideOnExpand').hide();

            } else {
                oTable.fnSettings().oInit.expandto5 = true;
                oTable.fnSettings().oInit.expandto150 = false;
                text = "<img src='/sites/all/themes/checkbook/images/open.png'>";
                var place = $('#' + oTable.fnSettings().sInstance + '_wrapper').parent().parent().attr('id');
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
            var nodes = ['node-widget-spending_by_expense_categories_view', 'node-widget-spending_by_agencies_view',
                'node-widget-spending_by_departments_view', 'node-widget-oge_spending_by_expense_categories_view',
                'node-widget-oge_spending_by_departments_view', 'node-widget-mwbe_spending_by_agencies_view',
                'node-widget-mwe_spending_expense_categories_view', 'node-widget-mwbe_spending_by_departments_view'];
            $.each(nodes, function (index, value) {
                var oTable = null;
                var oElement = null;
                var nodeId = '#' + value + ' a.simultExpandCollapseWidget';

                if ($(nodeId).parent().prev().find('.dataTable') != null) {
                    oTable = $(nodeId).parent().prev().find('.dataTable').dataTable();
                    oElement = $(nodeId);
                    oElement.data('toggled', !toggled);

                    event.preventDefault();
                    var text = "";

                    if (!toggled) {
                        if (oTable.size() > 0) {
                            oTable.fnSettings().oInit.expandto150 = true;
                            oTable.fnSettings().oInit.expandto5 = false;
                        }

                        text = "<img src='/sites/all/themes/checkbook/images/close.png'>";
                        if (oElement != null) {
                            oElement.parent().parent().find('.hideOnExpand').hide();
                        }
                    } else {
                        if (oTable.size() > 0) {
                            oTable.fnSettings().oInit.expandto5 = true;
                            oTable.fnSettings().oInit.expandto150 = false;
                            var placeTable = $('#' + oTable.fnSettings().sInstance + '_wrapper').parent().parent().attr('id');
                            document.getElementById(placeTable).scrollIntoView();
                        }

                        text = "<img src='/sites/all/themes/checkbook/images/open.png'>";
                        if (oElement != null) {
                            oElement.parent().parent().find('.hideOnExpand').show();
                        }
                    }

                    if (oTable.size() > 0) {
                        oTable.fnDraw();
                        oElement.html(text);
                    }
                }
            });
        }
    );

    //Instructional Videos
    var instructionalVideos = '.instructional-video-toggle, .instructional-video-filter-highlight';
    $(instructionalVideos).live("click",
        function (event) {
            $(this).parent().parent().find('.instructional-video-content').slideUp(300);
            if (!$(this).parent().find('.instructional-video-toggle').hasClass('open')) {
                $(this).parent().parent().find('.instructional-video-content').slideToggle(300);
            }
            $(this).parent().find('.instructional-video-toggle').toggleClass('open');
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
    value = replaceAllOccurrences("/", "__", value);
    value = replaceAllOccurrences("%2F", encodeURIComponent("__"), value);

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
    //This function should handle null/empty strings
    if (str == null || str.length == 0)
        return str;
    else
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

function addCommas(nStr) {
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

function reloadExpandCollapseWidget(context, aoData) {
    $length = null;
    if (context.fnSettings().oInit.expandto150) {
        $length = 150;
    } else if (context.fnSettings().oInit.expandto5) {
        $length = 5;
    }

    if ($length) {
        for (var i = 0; i < aoData.length; i++) {
            if (aoData[i].name == "iDisplayLength") {
                aoData[i].value = $length;
                break;
            }
        }
    }
}

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
function splitLine(st, n) {
    var b = '';
    var s = st;
    while (s.length > n) {
        var c = s.substring(0, n);
        var d = c.lastIndexOf(' ');
        var e = c.lastIndexOf('\n');
        if (e != -1) d = e;
        if (d == -1) d = n;
        b += c.substring(0, d) + '\n';
        s = s.substring(d + 1);
    }
    return b + s;
}


function fasterSplit(str, len) {
    var ret = [], strlen = str.length, off = 0, rem = len
    do {
        //if()
        ret.push(str.substr(off, len));
        off += len
    } while (off < strlen)
    return ret
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
            }
        );
    }(jQuery));
}
