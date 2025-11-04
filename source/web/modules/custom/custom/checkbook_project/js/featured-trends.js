(function ($) {
    $(document).ready(function () {
        $('.chartfilter').click(function () {
            let parentID = '#' + $(this).parent().attr('id');
            let chartNumber = $(this).attr('name');
            let minraw = $(parentID + ' .chartdatefrom').val();
            let maxraw = $(parentID + ' .chartdateto').val();
            // var lastYear = new Date().getFullYear()-1;
            let minYear = Highcharts.dateFormat('%Y',Highcharts.chartarray[chartNumber].xAxis[0].getExtremes().dataMin);
            let maxYear = Highcharts.dateFormat('%Y',Highcharts.chartarray[chartNumber].xAxis[0].getExtremes().dataMax);
            if (minraw.length > 0 || maxraw.length > 0) {
                validateInput(minraw, maxraw, chartNumber, minYear,maxYear, parentID);
            } else if (minraw.length === 0 && maxraw.length === 0) {
                let today = new Date().getFullYear();
                let lastYear = today - 1;
                let tenYears = today - 10;
                tenYears = Date.UTC(tenYears, 0, 1);
                lastYear = Date.UTC(lastYear, 0, 1);
                Highcharts.chartarray[chartNumber].xAxis[0].setExtremes(tenYears, lastYear);
            }
        });

      let slide = getParameterByName("slide");
      if(slide === '') slide = '0';
      $('article[about="/featured-trends"]').after('<div id="featured-trends-thumbnails"></div>');
      $("article[about=\"/featured-trends\"] .layout__region--content .block-widget-filter-block").wrapAll('<div id="block-widget-filter-block-wrap"></div>');

      $('article[about="/featured-trends"] #block-widget-filter-block-wrap').cycle({
          slideExpr:'.block-widget-filter-block',
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
                  case 1:
                      return '<div id="property-tax" class="navigation">Property Tax Levies<br>and Collections</div>';
                  case 2:
                      return '<div id="capital-projects" class="navigation">Capital Projects Fund<br>Aid Revenues by Agency</div>';
                  case 3:
                      return '<div id="personal-income" class="navigation">Personal Income</div>';
                  case 4:
                      return '<div id="debt-ratio" class="last navigation">Ratios of Outstanding Debt<br>by Type</div>';
              }
          }
      });

      $("div.navigation").on("click", function(event){
         let breadcrumbElement = $('#block-breadcrumbs nav li').last();
         switch(this.id){
          case 'debt-ratio':
            breadcrumbElement.text("Ratios of Outstanding Debt by Type");
            break;
          case 'property-tax':
            breadcrumbElement.last().text("Property Tax Levies and Collections");
            break;
          case 'capital-projects':
            breadcrumbElement.last().text("Capital Projects Fund Aid Revenues by Agency");
            break;
          case 'personal-income':
            breadcrumbElement.text("Personal Income");
            break;
          case 'general-fund':
          default:
            breadcrumbElement.text("General Fund Revenues and General Fund Expenditures");
          }
          return true;
      });
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
