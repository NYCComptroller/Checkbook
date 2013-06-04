<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php require_once(realpath(drupal_get_path('module', 'data_controller')) . '/common/object/converter/handler/PHP2Json.php'); ?>

<?php if (isset($node->widgetConfig->gridConfig->title)) { ?>
<h3><?=$node->widgetConfig->gridConfig->title?></h3>
<h3><?=_getFullYearString()?></h3>
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
  $isGridView = ($node->widgetConfig->displayType == 'gridview');
  foreach ($node->widgetConfig->table_columns as $row) {
    $headerClass = ($row->headerClass)? ' class="'.$row->headerClass.'"':'';
    if ($isGridView) {
      if (isset($row->isWidget) && $row->isWidget) {
        foreach ($row->gridViewColumns as $gridViewColumn) {
          echo "<th$headerClass>" . $gridViewColumn->label . "</th>";
        }
      }
      else {
        echo "<th$headerClass>" . $label . "</th>";
      }
    }
    else {
      echo "<th$headerClass>" . $label . "</th>";
    }
  }
  echo "</tr>\n";
  ?>
  </thead>

  <tbody>

  <?php

  ?>
  </tbody>
</table>

<?php
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
