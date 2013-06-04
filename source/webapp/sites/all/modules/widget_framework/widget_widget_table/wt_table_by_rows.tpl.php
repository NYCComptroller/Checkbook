<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
            $tag = ($column->header == 'true') ? 'th' : 'td';
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
