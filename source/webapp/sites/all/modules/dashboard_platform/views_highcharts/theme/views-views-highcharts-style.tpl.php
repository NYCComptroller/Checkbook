<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<div id='<?php echo $chartDiv ?>' class='test'></div>
<script type="text/javascript">
var charts = typeof(charts) == 'undefined' ? [] : charts;
(function ($) {
$(document).ready(function() {
    var chart = new Highcharts.Chart({
        chart: {
            renderTo    : '<?php echo $chartDiv ?>'
            <?php if (isset($options['chart']['backgroundColor']) && $options['chart']['backgroundColor'] != '') {?>
                , backgroundColor   : '<?php echo $options['chart']['backgroundColor'] ?>'
            <?php } 
                if (isset($options['chart']['height']) && $options['chart']['height'] != '') {?>
                , height   : <?php echo $options['chart']['height'] ?>
            <?php } 
                if (isset($options['chart']['width']) && $options['chart']['width'] != '') {?>
                , width   : <?php echo $options['chart']['width'] ?>
            <?php } 
                if (isset($options['chart']['margin']['top']) && $options['chart']['margin']['top'] != '') {?>
                , marginTop   : <?php echo $options['chart']['margin']['top'] ?>
            <?php } 
                 if (isset($options['chart']['margin']['left']) && $options['chart']['margin']['left'] != '') {?>
                , maginLeft   : <?php echo $options['chart']['margin']['left'] ?>
            <?php } 
                 if (isset($options['chart']['margin']['bottom']) && $options['chart']['margin']['bottom'] != '' ) {?>
                , marginBottom : <?php echo $options['chart']['margin']['bottom'] ?>
            <?php }
                 if (isset($options['chart']['margin']['right']) && $options['chart']['margin']['right'] != '' ) {?>
                , marginRight : <?php echo $options['chart']['margin']['right'] ?>
            <?php } ?>
         },
         credits: {
             enabled: false
         },         
         title: {
            text: '<?php echo $options['chart']['chart_title'] ?>',
            align: '<?php echo $options['chart']['title']['align'] ?>'
         },
         <?php if (isset($options['tooltip']['formatter']) && $options['tooltip']['formatter'] != '') { ?>
         tooltip: {
             formatter: function() {
                 <?php
                     echo $options['tooltip']['formatter'];
                 ?>
              }
         },
         <?php } ?>
          xAxis: {
             type: '<?php echo $options['xaxis']['type'] ?>',
              <?php if (isset($options['xaxis']['startOnTick']) && $options['xaxis']['startOnTick'] != '') { ?>
             startOnTick: <?php echo $options['xaxis']['startOnTick'] ?>,
             <?php } ?>          
             tickLength: 0,
             <?php 
              if (isset($options['xaxis']['maxPadding']) && $options['xaxis']['maxPadding'] != '') { ?>
             maxPadding: <?php echo $options['xaxis']['maxPadding'];?>,
             <?php }          
              if (isset($options['xaxis']['minPadding']) && $options['xaxis']['minPadding'] != '') { ?>
             minPadding: <?php echo $options['xaxis']['minPadding'];?>,
             <?php }                      
             if (isset($options['xaxis']['tickInterval']) && $options['xaxis']['tickInterval'] != '') { ?>
             tickInterval: <?php echo $options['xaxis']['tickInterval'];?>,
             <?php }              
             if (isset($categories) && count($categories) > 0) { ?>
             categories: <?php echo json_encode($categories)?>,
             <?php } ?>
             labels: {
                   align: '<?php echo $options['xaxis']['labels']['align'] ?>',
                   style :{
                           "font-size":'11px', "font-family":"Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif"
                            <?php if (isset($options['xaxis']['labels']['style']) && $options['xaxis']['labels']['style'] != '') { ?>
                            , <?php echo $options['xaxis']['labels']['style'] ?>
                          <?php } ?>
                           },
                   enabled: <?php echo $options['xaxis']['labels']['enabled'] ?>,
                <?php if (isset($options['xaxis']['labels']['x']) && $options['xaxis']['labels']['x'] != '') { ?>
                           x: <?php echo $options['xaxis']['labels']['x'] ?>,
                           y: <?php echo $options['xaxis']['labels']['y'] ?>,
                <?php } ?>
                rotation: '<?php echo $options['xaxis']['labels']['rotation'] ?>'
                <?php if (isset($options['xaxis']['labels']['formatter']) && $options['xaxis']['labels']['formatter'] != '') { ?>
                ,formatter: function() {
                    <?php echo $options['xaxis']['labels']['formatter']; ?>
                }
                <?php } ?>
             },                     
             title: {
                text: '<?php echo $options['xaxis']['title']['text'] ?>',
                align: '<?php echo $options['xaxis']['title']['align'] ?>',
                rotation: '<?php echo $options['xaxis']['title']['rotation'] ?>'
             }
          },                  
          yAxis: {
             min: 0,
             <?php if (isset($options['yaxis']['max']) && $options['yaxis']['max'] != '') { ?>
             max : <?php echo $options['yaxis']['max'] ?>,
             <?php }
              if (isset($options['yaxis']['gridLineWidth']) && $options['yaxis']['gridLineWidth'] != '') { ?>
              gridLineWidth:<?php echo $options['yaxis']['gridLineWidth'] ?>,
             <?php } ?>
             labels: {
                  align: '<?php echo $options['yaxis']['labels']['align'] ?>',
                  enabled: <?php echo $options['yaxis']['labels']['enabled'] ?>,
                rotation: '<?php echo $options['yaxis']['labels']['rotation'] ?>'
                <?php if (isset($options['yaxis']['labels']['style']) && $options['yaxis']['labels']['style'] != '') { ?>
                , style: {
                     <?php echo $options['yaxis']['labels']['style'] ?>
                }
                <?php } ?>
                   <?php if (isset($options['yaxis']['labels']['formatter']) && $options['yaxis']['labels']['formatter'] != '') { ?>
                , formatter: function() {
                         <?php echo $options['yaxis']['labels']['formatter']; ?>
                      }
                <?php } ?>
             },             
             title: {
                 text: '<?php echo $options['yaxis']['title']['text'] ?>',
                  align: '<?php echo $options['yaxis']['title']['align'] ?>',
                  <?php if (isset($options['yaxis']['title']['margin']) && $options['yaxis']['title']['margin'] != '') { ?>
                  margin: <?php echo $options['yaxis']['title']['margin'] ?>,
                <?php } ?>
                rotation: '<?php echo $options['yaxis']['title']['rotation'] ?>'
                <?php if (isset($options['yaxis']['title']['style']) && $options['yaxis']['title']['style'] != '') { ?>
                , style: {
                     <?php echo $options['yaxis']['title']['style'] ?>
                }
                <?php } ?>
                
             }
          },
          legend: {
             backgroundColor: '#FFFFFF',
             enabled: <?php echo $options['legend']['enabled'] ?>, 
             layout: '<?php echo $options['legend']['layout'] ?>',             
             align: '<?php echo $options['legend']['align'] ?>',
             verticalAlign: '<?php echo $options['legend']['verticalAlign'] ?>',
             x: <?php echo $options['legend']['x'] ?>,
             y: <?php echo $options['legend']['y'] ?>
          },
          plotOptions: {
             series: {
                 borderWidth: 1,
                 minPointLength: 0,
                 shadow: false
                 <?php if (isset($options['plotOptions']['series']['events']) && $options['plotOptions']['series']['events'] != '') { ?>
                 , cursor: '<?php echo $options['plotOptions']['series']['cursor'] ?>'
                 , events:{
                     click: function(event) {
                     <?php echo $options['plotOptions']['series']['events'] ?>
                     }
                 }
                 <?php }
                  if (isset($options['plotOptions']['series']['pointWidth']) && $options['plotOptions']['series']['pointWidth'] != '') { ?>
                 , pointWidth: <?php echo $options['plotOptions']['series']['pointWidth'] ?>
                 <?php }                    
                  if (isset($options['plotOptions']['series']['stacking']) && $options['plotOptions']['series']['stacking'] != '') { ?>
                 , stacking: '<?php echo $options['plotOptions']['series']['stacking'] ?>'
                 <?php
                 }
                 if (isset($options['plotOptions']['series']['colorByPoint']) && $options['plotOptions']['series']['colorByPoint'] == 1 ) { 
                 ?>
                     , colorByPoint: <?php echo $options['plotOptions']['series']['colorByPoint'] ?>
                 <?php } ?>
               }
          },
          series: [
              <?php for($i=0; $i < $seriesCount; $i++) {
                  if (!isset($data[$i])) {continue;}
              ?> 
                  {
                 type: '<?php echo $options['series'][$i]['chart_type']?>',
                 name: '<?php echo $options['series'][$i]['name']?>',
                 <?php if (isset($color)){ ?>                     
                     color: '<?php echo $color[$i]; ?>',
                 <?php } ?>
                 <?php if (isset($options['series'][$i]['visible']) && $options['series'][$i]['visible'] == 0) { ?>    
                 visible : false,
                 <?php } ?>                     
                 data: <?php echo json_encode($data[$i])?>
                 }
             <?php if ($i != $seriesCount -1) {echo ',';} }?>],
             <?php if (isset($options['exporting']['enabled']) && $options['exporting']['enabled'] == 0 ) {?>
                 exporting: {
                      enabled: false
                 }
             <?php }else{ ?>
                 exporting: {
                      filename: '<?php echo $options['exporting']['filename'] ?>'
                 }
                 
             <?php    
             }?>
     });
     charts.push(chart);
});
// All your code here
})(jQuery);

</script>