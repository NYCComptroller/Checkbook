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
<?php print eval($node->widgetConfig->header);  ?>
<div class="content clearfix highcharts-border">
<div id="node-chart-<?php print widget_unique_identifier($node)?>" class="highstock-wrapper <?php echo $node->widgetConfig->html_class ?>">&nbsp;</div>
</div>
<?php if ($node->widgetConfig->deferredRendering == TRUE){
  widget_highcharts_add_js_setting($node);
} else {?>
  <script type="text/javascript">
    jQuery(document).ready(function() {
        var chart = new Highcharts.StockChart(<?php print widget_mergeJSFunctions($node, $node->widgetConfig->chartConfig); if ($node->widgetConfig->callback){ print ',function(chart){'.$node->widgetConfig->callback.'}';}?>);
        Highcharts.chartarray.push(chart); //array defined in highcharts-globals.js
    });
</script>
<?php } ?>
<?php print eval($node->widgetConfig->footer);  ?>
