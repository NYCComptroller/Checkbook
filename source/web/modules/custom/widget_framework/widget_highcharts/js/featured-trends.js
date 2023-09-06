(function ($) {
    $(document).ready(function () {
        $('.chartfilter').click(function () {
            var parentID = '#' + $(this).parent().attr('id');
            var chartNumber = $(this).attr('name');
            var minraw = $(parentID + ' .chartdatefrom').val();
            var maxraw = $(parentID + ' .chartdateto').val();
            // var lastYear = new Date().getFullYear()-1;
            var minYear = Highcharts.dateFormat('%Y',Highcharts.chartarray[0].xAxis[0].getExtremes().dataMin);
            var maxYear = Highcharts.dateFormat('%Y',Highcharts.chartarray[0].xAxis[0].getExtremes().dataMax);
            if (minraw.length > 0 || maxraw.length > 0) {
                validateInput(minraw, maxraw, 0, minYear,maxYear, parentID);
            } else if (minraw.length === 0 && maxraw.length === 0) {
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
    if(slide === '') slide = '0';
    switch(slide){
      case '1':
        $('#breadcrumb span.last').text("Property Tax Levies and Collections");
        break;
      case '2':
        $('#breadcrumb span.last').text("Capital Projects Fund Aid Revenues");
        break;
      case '3':
        $('#breadcrumb span.last').text("Personal Income");
        break;
      case '4':
        $('#breadcrumb span.last').text("Ratios of Outstanding Debt by Type");
        break;
      case '0':
      default:
        $('#breadcrumb span.last').text("General Fund Revenues and General Fund Expenditures");
    }

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
                            return '<div id="debt-ratio" class="last navigation">Ratios of Outstanding Debt<br>by Type</div>';
                            break;
                    }
                }
            }
        );
    });

    $('#featured-trends-thumbnails div.navigation').on("click",function (e) {
        switch(this.id){
            case 'general-fund':
                $('#breadcrumb span.last').text("General Fund Revenues and General Fund Expenditures"); break;
            case 'property-tax':
                $('#breadcrumb span.last').text("Property Tax Levies and Collections"); break;
            case 'capital-projects':
                $('#breadcrumb span.last').text("Capital Projects Fund Aid Revenues"); break;
            case 'personal-income':
                $('#breadcrumb span.last').text("Personal Income"); break;
            case 'debt-ratio':
            default:
                $('#breadcrumb span.last').text("Ratios of Outstanding Debt by Type");
        }
        return true;
    });

  function isValidYear(n, floor) {
    var ceiling = new Date().getFullYear() - 1;
    return !isNaN(parseFloat(n)) && isFinite(n) && n >= floor && n <= ceiling;
  }

  function validateInput(min, max, chartno, floor, ceiling, parentID) {
    min = Number(min);
    max = Number(max);
    var mindate = Date.UTC(min, 0);
    var maxdate = Date.UTC(max, 1);
    var floorDate = Date.UTC(floor, 0);
    var ceilingDate = Date.UTC(ceiling, 1);
    if (min <= max) {
      if (isValidYear(min, floor) && isValidYear(max, floor)) {
        Highcharts.chartarray[chartno].xAxis[0].setExtremes(mindate, maxdate, true);
        $(parentID + ' .chartdatefrom').val(min);
        $(parentID + ' .chartdateto').val(max);
      } else if (isValidYear(min, floor) && !isValidYear(max, floor)) {
        Highcharts.chartarray[chartno].xAxis[0].setExtremes(mindate, ceilingDate, true);
        $(parentID + ' .chartdatefrom').val(min);
        $(parentID + ' .chartdateto').val(ceiling);
      } else if (!isValidYear(min, floor) && isValidYear(max, floor)) {
        Highcharts.chartarray[chartno].xAxis[0].setExtremes(floorDate, maxdate, true);
        $(parentID + ' .chartdatefrom').val(floor);
        $(parentID + ' .chartdateto').val(max);
      }else{
        Highcharts.chartarray[chartno].xAxis[0].setExtremes(floorDate, ceilingDate, true);
        $(parentID + ' .chartdatefrom').val(floor);
        $(parentID + ' .chartdateto').val(ceiling);
      }
    } else {
      $(parentID + ' .chartdatefrom').val(floor);
      $(parentID + ' .chartdateto').val(ceiling);
      Highcharts.chartarray[chartno].xAxis[0].setExtremes(floorDate, ceilingDate, true);
    }
  }

}(jQuery));
