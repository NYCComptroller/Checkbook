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
