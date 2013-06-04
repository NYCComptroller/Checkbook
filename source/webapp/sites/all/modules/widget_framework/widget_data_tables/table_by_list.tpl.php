<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
echo eval($node->widgetConfig->header);
?>
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
      $label = (isset($row->labelAlias))? (WidgetUtil::generateLabelMapping($row->labelAlias)) : $row->label;
      $fn = $row->adjustLabelFunction;
      if(isset($fn) && function_exists($fn)){
          $label = $fn($label);
      }else if(isset($row->evalLabel) && $row->evalLabel){
          $label = eval("return $row->label;");
      }
      $headerClass = ($row->headerClass)? ' class="'.$row->headerClass.'"':'';
      echo "<th$headerClass>" . $label . "</th>";
  }
  echo "</tr>\n";
  ?>
  </thead>

  <tbody>

  <?php
  if (isset($node->data) && is_array($node->data)) {
    foreach ($node->data as $datarow) {
      echo "<tr>";
      foreach ($node->widgetConfig->table_columns as $row) {
        echo '<td class="' . $datarow[$row->classColumn] . '">' . $datarow[$row->column] . '</td>';
      }
      echo "</tr>";
    }

  }
  ?>
  </tbody>
</table>

<?php
echo '<div class="tableFooter">';
  if ($node->widgetConfig->enableExpand == TRUE) {
    if($node->totalDataCount > 5){
        echo '<a href="#" class="expandCollapseWidget"><img src="/' . drupal_get_path('theme',$GLOBALS['theme']) . '/images/open.png"></a>';
        echo '<span class="plus-or" style="display: none;">or</span>';
    }
  }
echo eval($node->widgetConfig->footer);
echo '</div>';
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
