<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php require_once(realpath(drupal_get_path('module', 'data_controller')) .'/common/object/converter/handler/PHP2Json.php');
if (module_exists('widget_highcharts')){
    drupal_add_js(drupal_get_path('module', 'widget_highcharts') .'/highcharts/2.1.0/js/highcharts.src.js', array('group'=>JS_LIBRARY,'weight'=>-1));
    drupal_add_js(drupal_get_path('module', 'widget_highcharts') .'/highcharts-globals.js',array('group'=>JS_LIBRARY,'weight'=>0));
} else if (module_exists('widget_highstock')) {
    drupal_add_js(drupal_get_path('module', 'widget_highstock') .'/highstock/1.1.4/js/highstock.js');
    drupal_add_js(drupal_get_path('module', 'widget_highstock') .'/highstock-globals.js');
}

?>

<?php

echo eval($node->widgetConfig->header);
$table_rows = array();
$viewAll = $_REQUEST['viewall'];
$url_path = drupal_get_path_alias($_GET['q']);
$path_params = explode('/', $url_path);
if(in_array("year", $path_params)){
    $yr_index = array_search("year",$path_params);
    $req_year_id = $path_params[$yr_index+1];
}else{
    $req_year_id = _getCurrentYearID();
}

$years = array();

foreach($node->data as $row){
    $years[$row['year_year_year_value']] = 	$row['year_year_year_value'];
}
asort($years);

?>
<h2><?php echo $node->widgetConfig->table_title; ?></h2>
<div class="dataTable dynamicColumns">
<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->html_class ?>">
    <?php
    if (isset($node->widgetConfig->caption_column)) {
        echo '<caption>' . $node->data[0][$node->widgetConfig->caption_column] . '</caption>';
    }
    else if (isset($node->widgetConfig->caption)) {
        echo '<caption>' . $node->widgetConfig->caption . '</caption>';
    }
    ?>
    <thead>
    <tr>
    <th>Name</th>
    <th>Adopted</th>
    <th>Modified</th>
    <?php
    foreach ($years as $year){
        echo "<th>Revenue Collected  FY " . $year . "</th>";
    }
    ?>
    <th>Total Revenue Collected To Date</th>
    </tr>
    </thead>

    <tbody>
    </tbody>
</table>
</div>
<?php
if($node->widgetConfig->viewAll){ ?>
<a class="view-all popup" href="/checkbook/view_all_popup_template/agency_revenue/node/<?= $node->nid ?>?refURL=<?= drupal_get_path_alias($_GET['q']) ?>" >View All</a>

<?php }

foreach($years as $year){
    $node->widgetConfig->dataTableOptions->aoColumns[] = null;
}
$node->widgetConfig->dataTableOptions->aoColumns[] = null;

if ($node->widgetConfig->deferredRendering == TRUE) {
  widget_data_tables_add_js_setting($node);
}
else {
  widget_data_tables_add_js($node);
} ?>