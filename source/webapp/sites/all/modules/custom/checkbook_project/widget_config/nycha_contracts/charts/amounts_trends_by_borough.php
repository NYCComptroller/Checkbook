<?php
$query = "SELECT location_borough_name AS borough,
	  release_approved_year AS year,
	  SUM(agreement_total_amount) AS total_amount,
	  SUM(agreement_spend_to_date) AS spent_to_date
FROM all_agreement_transactions
WHERE agreement_type_id = 3 AND location_borough_name IS NOT NULL AND
		release_approved_year BETWEEN 2014 AND 2018
GROUP BY location_borough_name, release_approved_year
ORDER BY 1 ASC, 2 ASC";
$results = _checkbook_project_execute_sql_by_data_source($query, 'checkbook_nycha');
?>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<style>
  #result {
    text-align: right;
    color: gray;
    min-height: 4em;
  }
  #table-sparkline {
    margin: 0 auto;
    border-collapse: collapse;
    font-size:14px;
  }
  th {
    font-weight: bold;
    text-align: left;
  }
  td, th {
    padding: 5px;
    border-bottom: 1px solid silver;
    height: 20px;
  }

  thead th {
    border-top: 2px solid gray;
    border-bottom: 2px solid gray;
  }
  .highcharts-tooltip>span {
    background: white;
    border: 1px solid silver;
    border-radius: 3px;
    box-shadow: 1px 1px 2px #888;
    padding: 8px;
  }
</style>
<div id="result"></div>
<table id="table-sparkline">
  <thead>
  <tr>
    <th>Borough</th>
    <th>CurrentAmount<br/>FY 2018</th>
    <th>Previous<br/>Current Amounts</th>
    <th>Spent to Date<br/>FY 2018</th>
    <th>Previous<br/>Spending</th>
    <th>Trend</th>
  </tr>
  </thead>
  <tbody id="tbody-sparkline">
  <tr>
    <th>Bronx</th>
    <td>$31.6M</td>
    <td data-sparkline="21.0, 15.2, 29.8, 23.1"/>
    <td>$23.2M</td>
    <td data-sparkline="22.1, 14.1, 26.6, 21.1 "/>
    <td data-sparkline="-1.1, 1.1, 2.2, 2.0 ; column"/>
  </tr>
  <tr>
    <th>Brooklyn</th>
    <td>$28.2M</td>
    <td data-sparkline="47.8, 33.1, 43.5, 31.1 "/>
    <td>$19.7M</td>
    <td data-sparkline="45.7, 32.1, 45.7, 32.3 "/>
    <td data-sparkline="3.1, 1.1, -2.2, -1.2 ; column"/>
  </tr>
  <tr>
    <th>Manhattan</th>
    <td>$70.0M</td>
    <td data-sparkline="190.5, 107.5, 76.0, 56.8 "/>
    <td>$49.6M</td>
    <td data-sparkline="189.1, 115.5, 74.2, 55.6 "/>
    <td data-sparkline=" 1.4, -8.0, 2.2, 1.2 ; column"/>
  </tr>
  <tr>
    <th>Queens</th>
    <td>$98.8M</td>
    <td data-sparkline="44.7, 47.2, 123.3,  77.3"/>
    <td>$71.8M</td>
    <td data-sparkline="44.2, 46.8, 122.3, 73.6 "/>
    <td data-sparkline="0.5, 0.4, 1.0, 0.7 ; column"/>
  </tr>
  <tr>
    <th>Staten Island</th>
    <td>$3.8M</td>
    <td data-sparkline="4.3, 4.9, 1.3, 3.6 "/>
    <td>$3.0M</td>
    <td data-sparkline="3.3, 3.1, 2.3, 2.2 "/>
    <td data-sparkline="1.0, 1.8, -1.0, 1.4 ; column"/>
  </tr>
  </tbody>
</table>
<script type="text/javascript">
  Highcharts.SparkLine = function (a, b, c) {
    var hasRenderToArg = typeof a === 'string' || a.nodeName,
      options = arguments[hasRenderToArg ? 1 : 0],
      defaultOptions = {
        chart: {
          renderTo: (options.chart && options.chart.renderTo) || this,
          backgroundColor: null,
          borderWidth: 0,
          type: 'area',
          margin: [2, 0, 2, 0],
          width: 120,
          height: 20,
          style: {
            overflow: 'visible'
          },

          // small optimalization, saves 1-2 ms each sparkline
          skipClone: true
        },
        title: {
          text: ''
        },
        credits: {
          enabled: false
        },
        xAxis: {
          labels: {
            enabled: false
          },
          title: {
            text: null
          },
          startOnTick: false,
          endOnTick: false,
          tickPositions: []
        },
        yAxis: {
          endOnTick: false,
          startOnTick: false,
          labels: {
            enabled: false
          },
          title: {
            text: null
          },
          tickPositions: [0]
        },
        legend: {
          enabled: false
        },
        tooltip: {
          hideDelay: 0,
          outside: true,
          shared: true
        },
        plotOptions: {
          series: {
            animation: false,
            lineWidth: 1,
            shadow: false,
            states: {
              hover: {
                lineWidth: 1
              }
            },
            marker: {
              radius: 1,
              states: {
                hover: {
                  radius: 2
                }
              }
            },
            fillOpacity: 0.25
          },
          column: {
            negativeColor: '#910000',
            borderColor: 'silver'
          }
        }
      };

    options = Highcharts.merge(defaultOptions, options);

    return hasRenderToArg ?
      new Highcharts.Chart(a, options, c) :
      new Highcharts.Chart(options, b);
  };

  var start = +new Date(),
    $tds = $('td[data-sparkline]'),
    fullLen = $tds.length,
    n = 0;

  // Creating 153 sparkline charts is quite fast in modern browsers, but IE8 and mobile
  // can take some seconds, so we split the input into chunks and apply them in timeouts
  // in order avoid locking up the browser process and allow interaction.
  function doChunk() {
    var time = +new Date(),
      i,
      len = $tds.length,
      $td,
      stringdata,
      arr,
      data,
      chart;

    for (i = 0; i < len; i += 1) {
      $td = $($tds[i]);
      stringdata = $td.data('sparkline');
      arr = stringdata.split('; ');
      data = $.map(arr[0].split(', '), parseFloat);
      chart = {};

      if (arr[1]) {
        chart.type = arr[1];
      }
      $td.highcharts('SparkLine', {
        series: [{
          data: data,
          pointStart: 2014
        }],
        tooltip: {
          headerFormat: '<span style="font-size: 11px">' + $td.parent().find('th').html() + ', FY {point.x}:</span><br/>',
          pointFormat: '<b>${point.y}M</b>'
        },
        chart: chart
      });

      n += 1;

      // If the process takes too much time, run a timeout to allow interaction with the browser
      if (new Date() - time > 500) {
        $tds.splice(0, i + 1);
        setTimeout(doChunk, 0);
        break;
      }

      // Print a feedback on the performance
      if (n === fullLen) {
        $('#result').html('Generated ' + fullLen + ' sparklines in ' + (new Date() - start) + ' ms');
      }
    }
  }
  doChunk();
</script>
