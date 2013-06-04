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

    if (isset($node->data)) {
        foreach ($node->data as $row) {
            //following casting is required for static widgets ... other option is to set 2nd variable in php decode to "TRUE".
            $row_formatted = (array)$row;
  //  dsm($row_formatted);
            echo "<tr>";
            foreach ($node->widgetConfig->table_columns as $column) {

                $tag  = ($column->header == 'true') ? 'th' : 'td';
                
                    echo "<$tag class=\"" . $row_formatted[$column->classColumn] . '">' . $row_formatted[$column->column] . "</$tag>";

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
<?php 
if ((!isset($node->widgetConfig->table_sort)) || ($node->widgetConfig->table_sort == 'true')) {
?>
<script type="text/javascript"> 
    $(document).ready(function() 
        { 
        $.tablesorter.addParser({ 
            // set a unique id 
            id: 'big_money', 
            is: function(s) { 
                return false;
            }, 
            format: function(s) { 
                // format your data for normalization 
                s = s.replace('$','');
                s = s.replace(',','');

                if (/^.*[B]$/.test(s)) {
                    s = s.replace(' B','');
                    s = s * 1000;
                }
                else {
                    s = s.replace(' M','');
                }
                return s; 
            }, 
            // set type, either numeric or text 
            type: 'numeric' 
        });

        $.tablesorter.defaults.widgets = ['zebra'];
        
            $('#table_<?php echo widget_unique_identifier($node) ?>').tablesorter( <?php echo json_encode($node->widgetConfig->passThrough) ?>);
        } 
    ); 
  
</script>
<?php 
}
?>
