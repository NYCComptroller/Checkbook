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
<?php require_once(realpath(drupal_get_path('module', 'data_controller')) . '/common/object/converter/handler/PHP2Json.php');
if (module_exists('widget_highcharts')) {
  drupal_add_js(drupal_get_path('module', 'widget_highcharts') . '/highcharts/7.1.1/code/highcharts.js', array(
    'group' => JS_LIBRARY,
    'weight' => -1
  ));
  drupal_add_js(drupal_get_path('module', 'widget_highcharts') . '/highcharts-globals.js', array(
    'group' => JS_LIBRARY,
    'weight' => 0
  ));
}
else {
  if (module_exists('widget_highstock')) {
    drupal_add_js(drupal_get_path('module', 'widget_highstock') . '/highstock/7.1.1/code/highstock.js');
    drupal_add_js(drupal_get_path('module', 'widget_highstock') . '/highstock-globals.js');
  }
}

?>


<?php if (!$node->widgetConfig->disableViewAll && isset($node->widgetConfig->gridConfig->link)) {
  echo eval($node->widgetConfig->gridConfig->link);
}
?>

<?php if (isset($node->widgetConfig->table_title)) { ?>
<?php if ($node->widgetConfig->table_title){print '<div class="tableHeader"><h2>'.$node->widgetConfig->table_title.'</h2></div>';}?>
<?php } ?>
<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->html_class ?>">
  <?php
  if (isset($node->widgetConfig->caption_column)) {
    echo '<caption>' . $node->data[0][$node->widgetConfig->caption_column] . '</caption>';
  }
  else {
    if (isset($node->widgetConfig->caption)) {
      echo '<caption>' . $node->widgetConfig->caption . '</caption>';
    }
  }
  ?>
  <thead>
  <?php
  echo "<tr>";
  foreach ($node->widgetConfig->table_columns as $row) {
      $label = $row->label;
      $fn = $row->adjustLabelFunction;
      if(isset($fn) && function_exists($fn)){
          $label = $fn($label);
      }
    $headerClass = ($row->headerClass)? ' class="'.$row->headerClass.'"':'';
    echo "<th$headerClass>" . $label . "</th>";
  }
  echo "</tr>\n";
  ?>
  </thead>

  <tbody>

  <?php
  if (isset($node->data)) {
    $i = 0;
    foreach ($node->data as $datarow) {
      echo "<tr>";
      foreach ($node->widgetConfig->table_columns as $row) {
        if ($row->isWidget) {
          $widgetnode = node_load($row->widgetNid);
          widget_set_uid($widgetnode, $i);
          /*$additionalParams = array();
          foreach($row->columnParams as $paramColumn){
              $additionalParams[$paramColumn] = $row[str_replace(".", "_",$paramColumn)];
          }
          widget_add_additional_parameters($widgetnode,$additionalParams);*/
          $widgetChart = node_build_content($widgetnode);
          $widgetChart = drupal_render($widgetnode->content);
          echo '<td class="' . $datarow[$row->classColumn] . '">' . $widgetChart . '</td>';
        }
        else {
          echo '<td class="' . $datarow[$row->classColumn] . '">' . $datarow[$row->column] . '</td>';
        }
      }
      echo "</tr>";
      $i++;
    }

  }
  ?>
  </tbody>
</table>
<?php if (!$node->widgetConfig->disableViewAll) { ?>
<a class="view-all popup"
   href="/checkbook/view_all_popup/node/<?= $node->nid ?>?refURL=<?= drupal_get_path_alias($_GET['q']) ?>">View All</a>
<?php
}
if ($node->widgetConfig->deferredRendering == TRUE) {
  widget_data_tables_add_js_setting($node);
}
else {
  widget_data_tables_add_js($node);
}
if (isset($node->widgetConfig->table_footnote_column)) {
  echo $node->data[0][$node->widgetConfig->table_footnote_column];
}
else {
  if (isset($node->widgetConfig->table_footnote)) {
    echo $node->widgetConfig->table_footnote;
  }
}
?>
