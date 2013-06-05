<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<script>
var intervalId;
var retryCount = 0;

function isChartPresent() {
  var chartPresent = typeof(charts) != 'undefined' && charts.length > 0 && charts[0].series.length > 0;
  if (!chartPresent && retryCount < 15) {
    //keep trying
    retryCount++;
  }
  else if (!chartPresent && retryCount >= 15) {
    //give up
    clearInterval(intervalId);
  }
  else {
    //found it, cancel interval and continue setup
    clearInterval(intervalId);
    actualLegendSetup();
  }
}

function setupLegendAfterChartIsPresent() {
  intervalId = setInterval(isChartPresent, 100);

}

function actualLegendSetup() {

        var series = charts[0].series;
        for(var i = 0; i < series.length; i++) {
            if (series[i].visible) {
                $('div.chart_legend > ul').append('<li><input name="legend" type="checkbox" checked position=' + i + '"/><img src="'+ getLegendColor(i) +'"/> ' + series[i].name + '</li>');
            }
            else {
                $('div.chart_legend > ul').append('<li><input name="legend" type="checkbox" position=' + i + '"/><img src="'+ getLegendColor(i) +'"/> ' + series[i].name + '</li>');
            }
        }
        $('div.chart_legend > ul > li > input:checkbox').click(function() {
            var position = parseInt($(this).attr("position"));
            var hide = true;
            if ($(this).attr("checked")) {
                hide = false;
            }
            for(var i = 0; i < charts.length; i++) {
                if (hide) {
                    charts[i].series[position].hide();
                }
                else {
                    charts[i].series[position].show();
                }

            }
        });

}

function getLegendColor(pos){

     var imagesColor = ['/legend-color-0.png','/legend-color-1.png','/legend-color-2.png',
                        '/legend-color-3.png','/legend-color-4.png','/legend-color-5.png',
                        '/legend-color-6.png','/legend-color-7.png','/legend-color-8.png',
                        '/legend-color-9.png'];

      var path = "<?php echo getImagePath();?>"+imagesColor[pos];

     return path;
}

$(document).ready(function() {
  setupLegendAfterChartIsPresent();
});

</script>
<div class="chart_legend">
<ul>
</ul>
</div>