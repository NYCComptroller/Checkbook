<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
    <div id="<?php echo $mapDiv; ?>"></div>
    <script type="text/javascript">    

    $(document).ready(function() {    
    
        //Instantiate the Maps    
        var Maps_FusionMap = new FusionMaps("<?php echo base_path() . drupal_get_path('module', 'views_fusionmaps'); ?>/FusionMaps/<?php echo $options['map']['map_file'];?>", "<?php echo $mapDiv ?>", "<?php echo $options['map']['width']; ?>", "<?php echo $options['map']['height']; ?>", "<?php echo $options['map']['debug_mode']; ?>", "<?php echo $options['map']['register_with_js']; ?>");
        //Provide entire XML data using dataXML method
        Maps_FusionMap.setDataXML("<map showBorder='0' \
                                     showLabels='<?php echo $options['map']['showLabels'];?>' \
                                     showCanvasBorder='0' \
                                     canvasBorderColor='ffffff' \
                                     canvasBorderThickness='0' \
                                     includeNameInLabels='<?php echo $options['map']['includeNameInLabels'];?>' \
                                     includeValueInLabels='<?php echo $options['map']['includeValueInLabels'];?>' \
                                     borderColor='<?php echo $options['map']['borderColor'];?>' \
                                     fillColor='FFFFFF'\
                                     fillAlpha='100' \
                                     showBevel='0'  \
                                     baseFontSize='9' \
                                     formatNumberScale='1' \
                                     showShadow='0' \
                                     showLegend='0' \
                                     legendPosition='Bottom' \
                                     showFCMenuItem='0' \
                                     bgAlpha='0,0' \
                                     animation='0'  \
                                     connectorColor='FFFFFF'  \
                                     <?php if (isset($options['map']['hoverColor']) && !empty($options['map']['hoverColor'])) echo "hovercolor='" . $options['map']['hoverColor'] . "'"; ?>\
                                     connectorAlpha='0'>\
                                     <colorRange>\
                                     <?php for($i = 0; $i < 4; $i++) { if (isset($options['ranges'][$i]['min']) && isset($options['ranges'][$i]['max'])) { echo "<color minValue='" . $options['ranges'][$i]['min'] . "' maxValue='" . $options['ranges'][$i]['max'] . "' color='" . $options['ranges'][$i]['color'] . "'/>\\\n";}}?>
                                     </colorRange>\
                                     \
                                     <data>\
                                          <?php
                                          foreach ($data as $row) {
                                              echo "<entity id='" . $row->$entity_id_col . "'";
                                              if ($row->$entity_val_col == 0) {
                                                  echo " value='0'";
                                              }
                                              else {
                                                  echo " value='" . $row->$entity_val_col . "'";
                                              }
                                              if (isset($row->$entity_displayVal_col)) {
                                                  echo " displayValue='" . $row->$entity_displayVal_col . "'";
                                              }
                                              if (isset($row->$entity_tooltip_col)) {
                                                  echo " toolText='" . urlencode(htmlspecialchars($row->$entity_tooltip_col)) . "'";
                                              }
                                              if (isset($row->$entity_link_col)) {
                                                  echo " link='" . urlencode(htmlspecialchars($row->$entity_link_col)) . "'";
                                              }
                                              echo "/>\\" . "\n";  
                                          }
                                          ?>
                                     </data>\
                                     \
                                     <styles>\
                                          <definition>\
                                               <style name='MyDataPlotStyle' type='bevel' distance='2'  />\
                                               <style name='TTipFont' type='font' isHTML='1' size='9'/>\
                                               <style type='animation' name='animX' param='_xscale' start='0' duration='1' />\
                                               <style type='animation' name='animY' param='_yscale' start='0' duration='1' />\
                                               <style name='BigFont' type='font' face='Georgia'  bold='0' bgColor='FFFFFF'  size='12' />\
                                          </definition>\
                                          \
                                          <application>\
                                               <apply toObject='TOOLTIP' styles='TTipFont,MyDataPlotStyle' />\
                                               <apply toObject='Legend' styles='BigFont' />\
                                          </application>\
                                     </styles>\
                                </map>");
        Maps_FusionMap.render("<?php echo $mapDiv; ?>");

    });
    </script>
