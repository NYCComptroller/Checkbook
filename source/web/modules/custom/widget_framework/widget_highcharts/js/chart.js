jQuery(document).ready(
  function() {
    var chart = new Highcharts.Chart(function(chart) {
      'drupalSettings.widget_highcharts.chart.widgetConfig_callback'
    }
    );
    Highcharts.chartarray.push(chart);
  }
);
