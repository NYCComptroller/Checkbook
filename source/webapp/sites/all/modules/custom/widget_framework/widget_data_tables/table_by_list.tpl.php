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

echo eval($node->widgetConfig->header);
if(isset($node->widgetConfig->headerConfig)){
    $headerConfig = eval($node->widgetConfig->headerConfig);
}
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
    $datasource = RequestUtilities::getRequestParamValue('datasource');
    foreach ($node->widgetConfig->table_columns as $row) {
      if(check_node_flag_visibilty($row->visibility_flag, $node)){
        if(!isset($row->datasource) || (isset($row->datasource) && ($row->datasource == RequestUtilities::get('datasource')))){
          $label = (isset($row->labelAlias))? (WidgetUtil::generateLabelMapping($row->labelAlias)) : $row->label;
            $fn = $row->adjustLabelFunction;
            if(isset($fn) && function_exists($fn)){
                $label = $fn($label);
            }else if(isset($row->evalLabel) && $row->evalLabel){
                $label = eval("return $row->label;");
            }
            if ('checkbook_nycha' === $datasource ) {
            if (preg_match("/Tax/",$label)){$label = "<div><span>Withheld<br/>Amount</span></div>";}}
            $headerClass = ($row->headerClass)? ' class="'.$row->headerClass.'"':'';
            $th .= "<th$headerClass>" . $label . "</th>";
        }
      }
    }

    if(isset($headerConfig)){
        foreach($headerConfig as $header=>$colSpan){
            $th1 .= "<th class='doubleHeader' colspan='$colSpan'>" . $header . "</th>";
        }
        echo "<tr>".$th1."</tr>\n";
    }
    echo "<tr>".$th."</tr>\n";
  ?>
  </thead>
  <tbody>
  <?php
  if (isset($node->data) && is_array($node->data)) {
    foreach ($node->data as $datarow) {
      echo "<tr>";
      foreach ($node->widgetConfig->table_columns as $row) {
        if(!isset($row->datasource) || (isset($row->datasource) && ($row->datasource == RequestUtilities::get('datasource')))){
          echo '<td class="' . $datarow[$row->classColumn] . '">' . $datarow[$row->column] . '</td>';
        }
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
        echo '<a class="expandCollapseWidget"><img src="/' . drupal_get_path('theme',$GLOBALS['theme']) . '/images/open.png"></a>';
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
