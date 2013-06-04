(function ($) {
    $(document).ready(function () {
        $('.chartfilter').click(function () {
            var parentID = '#' + $(this).parent().attr('id');
            var chartNumber = $(this).attr('name');
            var minraw = $(parentID + ' .chartdatefrom').val();
            var maxraw = $(parentID + ' .chartdateto').val();
            if (minraw.length > 0 || maxraw.length > 0) {
                if (chartNumber == 3) {
                    validateInput(minraw, maxraw, chartNumber, 1980)
                } else {
                    validateInput(minraw, maxraw, chartNumber, 1997)
                }
            } else if (minraw.length == 0 && maxraw.length == 0) {
                var today = new Date().getFullYear();
                var lastYear = today - 1;
                var tenYears = today - 10;
                tenYears = Date.UTC(tenYears, 0, 1);
                lastYear = Date.UTC(lastYear, 0, 1);
                Highcharts.chartarray[chartNumber].xAxis[0].setExtremes(tenYears, lastYear);
            }
        });
        $('body.page-featured-trends .panel-separator').remove();
        $('body.page-featured-trends #block-system-main').after('<div id="featured-trends-thumbnails"></div>');

        var slide = getParameterByName("slide");
        if(slide == '') slide = 0;
        if(slide == 0)
    		$('#breadcrumb span.last').text("General Fund Revenues and General Fund Expenditures");
        else if(slide == 1)
        	$('#breadcrumb span.last').text("Property Tax Levies and Collections");
        else if(slide == 2)
        	$('#breadcrumb span.last').text("Capital Projects Fund Aid Revenues");
        else if(slide == 3)
        	$('#breadcrumb span.last').text("Personal Income");
        else if(slide == 4)
        	$('#breadcrumb span.last').text("Ratios of Outstanding Debt");
        
        $('body.page-featured-trends #featured-trends .inside').cycle(
            {
                slideExpr:'.pane-node',
                height:'635px',
                width:'940px',
                fx:'fade',
                timeout:0,
                pause:true,
                pager:'#featured-trends-thumbnails',
                startingSlide: slide,
                pagerAnchorBuilder:function (i, DOMElement) {
                    switch (i) {
                        case 0:
                            return '<div id="general-fund" class="first navigation">General Fund Revenues and<br>General Fund Expenditures</div>';
                            break;
                        case 1:
                            return '<div id="property-tax" class="navigation">Property Tax Levies<br>and Collections</div>';
                            break;
                        case 2:
                            return '<div id="capital-projects" class="navigation">Capital Projects Fund<br>Aid Revenues</div>';
                            break;
                        case 3:
                            return '<div id="personal-income" class="navigation">Personal Income</div>';
                            break;
                        case 4:
                            return '<div id="debt-ratio" class="last navigation">Ratios of Outstanding Debt</div>';
                            break;
                    }
                }
            }
        );
    });
    
    
    $('#featured-trends-thumbnails div.navigation').live("click",function (e) {
        if(this.id == 'general-fund')
    		$('#breadcrumb span.last').text("General Fund Revenues and General Fund Expenditures");
        else if(this.id == 'property-tax')
        	$('#breadcrumb span.last').text("Property Tax Levies and Collections");
        else if(this.id == 'capital-projects')
        	$('#breadcrumb span.last').text("Capital Projects Fund Aid Revenues");
        else if(this.id == 'personal-income')
        	$('#breadcrumb span.last').text("Personal Income");
        else if(this.id == 'debt-ratio')
        	$('#breadcrumb span.last').text("Ratios of Outstanding Debt");

        return true;

    })    
    
    
    
}(jQuery));

function isValidYear(n, floor) {
    var ceiling = new Date().getFullYear() - 1;
    return !isNaN(parseFloat(n)) && isFinite(n) && n >= floor && n <= ceiling;
}

function validateInput(min, max, chartno, floor) {
    min = Number(min);
    max = Number(max);
    var mindate = Date.UTC(min, 0, 1);
    var maxdate = Date.UTC(max, 0, 31);
    var floorDate = Date.UTC(floor, 0, 1);
    var ceiling = new Date().getFullYear() - 1;
    var ceilingDate = Date.UTC(ceiling, 0, 1);
    if (min <= max) {
        if (isValidYear(min, floor) && isValidYear(max, floor)) {
            Highcharts.chartarray[chartno].xAxis[0].setExtremes(mindate, maxdate, true);
        } else if (isValidYear(min, floor) && !isValidYear(max, floor)) {
            Highcharts.chartarray[chartno].xAxis[0].setExtremes(mindate, ceilingDate, true);
        } else if (!isValidYear(min, floor) && isValidYear(max, floor)) {
            Highcharts.chartarray[chartno].xAxis[0].setExtremes(floorDate, maxdate, true);
        }
    } else {
        Highcharts.chartarray[chartno].xAxis[0].setExtremes(floorDate, ceilingDate, true);
    }
}