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
    <thead>
    <?php
    echo "<tr>";
    foreach ($node->widgetConfig->table_columns as $column) {
        $colLabel = trim($column->label);
        $parts = explode(' ', $colLabel);
        $parts[count($parts) - 1] = '<span>' . $parts[count($parts) - 1] . '</span>';
        $colLabel = join(' ', $parts);
        echo "<th>" . $colLabel . "</th>";
    }
    echo "</tr>";
    ?>
    </thead>
    <tbody>
    <?php
    $i = 0;
    if (isset($node->data)) {
        foreach ($node->data as $row) {
            //following casting is required for static widgets ... other option is to set 2nd variable in php decode to "TRUE".
            $row_formatted = (array)$row;
            //dsm($row_formatted);
            echo "<tr>";
            foreach ($node->widgetConfig->table_columns as $column) {
                $tag = ($column->header == 'true') ? 'th' : 'td';
                if ($column->isWidget) {
                    $widgetnode = node_load($column->widgetNid);
                    widget_set_uid($widgetnode,$i);
                    
                    $additionalParams = array();
                    foreach($column->columnParams as $paramColumn){
                    	$additionalParams[$paramColumn] = $row[str_replace(".", "_",$paramColumn)];
                    }

	                widget_add_additional_parameters($widgetnode,$additionalParams);   
             		
					$widgetChart = node_build_content($widgetnode);
					$widgetChart = drupal_render($widgetnode->content);     
                    
                    echo "<$tag class=\"" . $row_formatted[$column->classColumn] . '">' . $widgetChart . "</$tag>";
                } else {
                    echo "<$tag class=\"" . $row_formatted[$column->classColumn] . '">' . $row_formatted[$column->column] . "</$tag>";
                }
                $i++;
            }	
            echo "</tr>";       
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