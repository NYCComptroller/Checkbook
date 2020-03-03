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
<?php require_once(realpath(drupal_get_path('module', 'data_controller')) .'/common/object/converter/handler/PHP2Json.php');
if (module_exists('widget_highcharts')){
    drupal_add_js(drupal_get_path('module', 'widget_highcharts') .'/highcharts/7.1.1/code/highcharts.js', array('group'=>JS_LIBRARY,'weight'=>-1));
    drupal_add_js(drupal_get_path('module', 'widget_highcharts') .'/highcharts-globals.js',array('group'=>JS_LIBRARY,'weight'=>0));
} else if (module_exists('widget_highstock')) {
    drupal_add_js(drupal_get_path('module', 'widget_highstock') .'/highstock/7.1.1/code/highstock.js');
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
    $req_year_id = CheckbookDateUtil::getCurrentFiscalYearId();
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
