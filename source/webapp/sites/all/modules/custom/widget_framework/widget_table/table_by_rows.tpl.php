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
<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->html_class ?>">
    <?php 
      if (isset($node->widgetConfig->caption_column)) {
          echo '<caption>' . $node->data[0][$node->widgetConfig->caption_column] . '</caption>';
      }
      else if (isset($node->widgetConfig->caption)) {
        echo '<caption>' . $node->widgetConfig->caption . '</caption>';
      }
    ?>
    <tbody>
<?php
if (isset($node->data)) {
    foreach ($node->widgetConfig->table_rows as $row) {
        echo "<tr>";
        if (isset($row->label)) {
            echo "<th>" . $row->label . "</th>";
        }
        foreach ($node->data as $datarow) {
            $tag = (($column->header??0) == 'true') ? 'th' : 'td';
            echo '<' . $tag . ' class="' . $datarow[$row->classColumn] . '">' . $datarow[$row->column] . '</' . $tag . '>';
        }
        echo "</tr>\n";
    }
}
?>
    </tbody>
</table>

<?php 
if (isset($node->widgetConfig->table_footnote_column)) {
    echo $node->data[0][$node->widgetConfig->table_footnote_column];
}
else if (isset($node->widgetConfig->table_footnote)) {
    echo $node->widgetConfig->table_footnote;
}
?>
