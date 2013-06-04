<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
 

?>

<div id="view-fusioncharts-<?php echo $chartID; ?>"></div>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
var vwFC = new FusionCharts('<?php echo $swf_path; ?>', '<?php echo $chartID; ?>', <?php echo $width; ?>, <?php echo $height; ?>, 0, 1);
vwFC.setTransparent('false');
vwFC.setXMLData('<?php echo $config; ?>');
vwFC.render('view-fusioncharts-<?php echo $chartID; ?>');
//--><!]]>
</script>
