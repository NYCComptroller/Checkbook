<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php print eval($node->widgetConfig->header);  ?>
<div class="content clearfix highcharts-border">
<div id="node-chart-<?php print widget_unique_identifier($node)?>" class="highstock-wrapper <?php echo $node->widgetConfig->html_class ?>">&nbsp</div>
</div>
<?php if ($node->widgetConfig->deferredRendering == TRUE){
  widget_highcharts_add_js_setting($node);
} else {?>
  <script type="text/javascript">
    jQuery(document).ready(function() {
        var chart = new Highcharts.StockChart(<?php print widget_mergeJSFunctions($node, $node->widgetConfig->chartConfig); ?><?php if ($node->widgetConfig->callback){ print ',function(chart){'.$node->widgetConfig->callback.'}';}?>);
        Highcharts.chartarray.push(chart); //array defined in highcharts-globals.js
    });
</script>
<?php } ?>
<?php print eval($node->widgetConfig->footer);  ?>